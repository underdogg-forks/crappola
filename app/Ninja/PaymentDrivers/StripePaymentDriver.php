<?php

namespace App\Ninja\PaymentDrivers;

use App\Models\GatewayType;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentType;
use Exception;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentDriver extends BasePaymentDriver
{
    public $canRefundPayments = true;

    protected $customerReferenceParam = 'customerReference';

    public function gatewayTypes(): array
    {
        $types = [
            GATEWAY_TYPE_CREDIT_CARD,
            GATEWAY_TYPE_TOKEN,
        ];

        if ($gateway = $this->accountGateway) {
            $achEnabled = $gateway->getAchEnabled();
            $sofortEnabled = $gateway->getSofortEnabled();
            if ($achEnabled && $sofortEnabled) {
                if ($this->invitation) {
                    $country = ($this->client() && $this->client()->country) ? $this->client()->country->iso_3166_3 : ($this->account()->country ? $this->account()->country->iso_3166_3 : false);
                    // https://stripe.com/docs/sources/sofort
                    if ($country && in_array($country, ['AUT', 'BEL', 'DEU', 'ITA', 'NLD', 'ESP'])) {
                        $types[] = GATEWAY_TYPE_SOFORT;
                    } else {
                        $types[] = GATEWAY_TYPE_BANK_TRANSFER;
                    }
                } else {
                    $types[] = GATEWAY_TYPE_BANK_TRANSFER;
                    $types[] = GATEWAY_TYPE_SOFORT;
                }
            } elseif ($achEnabled) {
                $types[] = GATEWAY_TYPE_BANK_TRANSFER;
            } elseif ($sofortEnabled) {
                $types[] = GATEWAY_TYPE_SOFORT;
            }

            if ($gateway->getSepaEnabled()) {
                $types[] = GATEWAY_TYPE_SEPA;
            }
            if ($gateway->getBitcoinEnabled()) {
                $types[] = GATEWAY_TYPE_BITCOIN;
            }
            if ($gateway->getAlipayEnabled()) {
                $types[] = GATEWAY_TYPE_ALIPAY;
            }
            if ($gateway->getApplePayEnabled()) {
                $types[] = GATEWAY_TYPE_APPLE_PAY;
            }
        }

        return $types;
    }

    /**
     * Returns a setup intent that allows the user to enter card details without initiating a transaction.
     *
     * @return \Stripe\SetupIntent
     */
    public function getSetupIntent()
    {
        $this->prepareStripeAPI();

        return \Stripe\SetupIntent::create();
    }

    public function tokenize()
    {
        return $this->accountGateway->getPublishableKey();
    }

    public function rules()
    {
        $rules = parent::rules();

        if ($this->isGatewayType(GATEWAY_TYPE_APPLE_PAY)) {
            return ['sourceToken' => 'required'];
        }

        if ($this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER)) {
            $rules['authorize_ach'] = 'required';
        }

        return $rules;
    }

    public function isValid()
    {
        $result = $this->makeStripeCall(
            'GET',
            'charges',
            'limit=1'
        );

        if (\Illuminate\Support\Arr::get($result, 'object') == 'list') {
            return true;
        }

        return $result;
    }

    public function shouldUseSource(): bool
    {
        return in_array($this->gatewayType, [GATEWAY_TYPE_ALIPAY, GATEWAY_TYPE_SOFORT, GATEWAY_TYPE_BITCOIN]);
    }

    public function isTwoStep(): bool
    {
        return $this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER) && empty($this->input['plaidPublicToken']);
    }

    /**
     * @param bool $input
     * @param bool $paymentMethod
     * @param bool $offSession    true if this payment is being made automatically rather than manually initiated by the user
     *
     * @return bool|mixed
     *
     * @throws PaymentActionRequiredException when further interaction is required from the user
     */
    public function completeOnsitePurchase($input = false, $paymentMethod = false, $offSession = false)
    {
        $data = $this->prepareOnsitePurchase($input, $paymentMethod);

        if ( ! $data && request()->capture) {
            // We only want to save the payment details, not actually charge the card.
            $real_data = $this->paymentDetails($paymentMethod);

            if ( ! empty($real_data['payment_method'])) {
                // Attach the payment method to the existing customer.
                $this->prepareStripeAPI();
                $payment_method = \Stripe\PaymentMethod::retrieve($real_data['payment_method']);
                $payment_method = $payment_method->attach(['customer' => $this->getCustomerID()]);
                $this->tokenResponse = $payment_method;
                parent::createToken();

                return $payment_method;
            }
        }

        if ( ! $data) {
            // No payment method to charge against yet; probably a 2-step or capture-only transaction.
            return null;
        }

        if ( ! empty($data['payment_method']) || ! empty($data['payment_intent']) || ! empty($data['token'])) {
            // Need to use Stripe's new Payment Intent API.
            $this->prepareStripeAPI();

            // Get information about the currency we're using.
            $currency = \Illuminate\Support\Facades\Cache::get('currencies')->where('code', mb_strtoupper($data['currency']))->first();

            if ( ! empty($data['payment_intent'])) {
                // Find the existing payment intent.
                $intent = PaymentIntent::retrieve($data['payment_intent']);
                if ( ! $intent->amount == $data['amount'] * 10 ** $currency['precision']) {
                    // Make sure that the provided payment intent matches the invoice amount.
                    throw new Exception('Incorrect PaymentIntent amount.');
                }
                $intent->confirm();
            } elseif ( ! empty($data['token']) || ! empty($data['payment_method'])) {
                $params = [
                    'amount'              => $data['amount'] * 10 ** $currency['precision'],
                    'currency'            => $data['currency'],
                    'confirmation_method' => 'manual',
                    'confirm'             => true,
                ];

                if ($offSession) {
                    $params['off_session'] = true;
                }

                if ( ! empty($data['description'])) {
                    $params['description'] = $data['description'];
                }

                if ( ! empty($data['payment_method'])) {
                    $params['payment_method'] = $data['payment_method'];

                    if ($this->shouldCreateToken()) {
                        // Tell Stripe to save the payment method for future usage.
                        $params['setup_future_usage'] = 'off_session';
                        $params['save_payment_method'] = true;
                        $params['customer'] = $this->getCustomerID();
                    }
                } elseif ( ! empty($data['token'])) {
                    // Use a stored payment method.
                    $params['payment_method'] = $data['token'];
                    $params['customer'] = $this->getCustomerID();

                    if (mb_substr($data['token'], 0, 3) === 'ba_') {
                        // The PaymentIntent API doesn't seem to work with saved Bank Accounts.
                        // For now, just use the old API.
                        return $this->doOmnipayOnsitePurchase($data, $paymentMethod);
                    }
                }
                $intent = PaymentIntent::create($params);
            }

            if (empty($intent)) {
                throw new Exception('PaymentIntent not found.');
            }
            if (($intent->status == 'requires_source_action' || $intent->status == 'requires_action') &&
                      $intent->next_action->type == 'use_stripe_sdk') {
                // Throw an exception that can either be logged or be handled by getting further interaction from the user.
                throw new PaymentActionRequiredException(['payment_intent' => $intent]);
            }
            if ($intent->status == 'succeeded') {
                $ref = empty($intent->charges->data) ? null : $intent->charges->data[0]->id;

                $payment = $this->createPayment($ref, $paymentMethod);

                if ($this->invitation->invoice->account->isNinjaAccount()) {
                    \Illuminate\Support\Facades\Session::flash('trackEventCategory', '/account');
                    \Illuminate\Support\Facades\Session::flash('trackEventAction', '/buy_pro_plan');
                    \Illuminate\Support\Facades\Session::flash('trackEventAmount', $payment->amount);
                }

                if ($intent->setup_future_usage == 'off_session') {
                    // Save the payment method ID.
                    $payment_method = \Stripe\PaymentMethod::retrieve($intent->payment_method);
                    $this->tokenResponse = $payment_method;
                    parent::createToken();
                }

                return $payment;
            }
            throw new Exception('Invalid PaymentIntent status: ' . $intent->status);
        }

        return $this->doOmnipayOnsitePurchase($data, $paymentMethod);
    }

    public function getCustomerID()
    {
        // if a customer already exists link the token to it
        if ($customer = $this->customer()) {
            return $customer->token;
        }
        // otherwise create a new czustomer
        $invoice = $this->invitation->invoice;
        $client = $invoice->client;

        $response = $this->gateway()->createCustomer([
            'description' => $client->getDisplayName(),
            'email'       => $this->contact()->email,
        ])->send();

        return $response->getCustomerReference();
    }

    public function createToken()
    {
        $invoice = $this->invitation->invoice;
        $client = $invoice->client;

        $data = $this->paymentDetails();

        if ( ! empty($data['payment_method']) || ! empty($data['payment_intent'])) {
            // Using the PaymentIntent API; we'll save the details later.
            return null;
        }

        $data['description'] = $client->getDisplayName();
        $data['customerReference'] = $this->getCustomerID();

        if ( ! empty($data['plaidPublicToken'])) {
            $plaidResult = $this->getPlaidToken($data['plaidPublicToken'], $data['plaidAccountId']);
            unset($data['plaidPublicToken'], $data['plaidAccountId']);

            $data['token'] = $plaidResult['stripe_bank_account_token'];
        }

        $tokenResponse = $this->gateway()
            ->createCard($data)
            ->send();

        if ($tokenResponse->isSuccessful()) {
            $this->tokenResponse = $tokenResponse->getData();

            return parent::createToken();
        }
        throw new Exception($tokenResponse->getMessage());
    }

    public function creatingCustomer($customer)
    {
        if (isset($this->tokenResponse['customer'])) {
            $customer->token = $this->tokenResponse['customer'];
        } else {
            $customer->token = $this->tokenResponse['id'];
        }

        return $customer;
    }

    public function removePaymentMethod($paymentMethod): void
    {
        parent::removePaymentMethod($paymentMethod);

        if ( ! $paymentMethod->relationLoaded('account_gateway_token')) {
            $paymentMethod->load('account_gateway_token');
        }

        $response = $this->gateway()->deleteCard([
            'customerReference' => $paymentMethod->account_gateway_token->token,
            'cardReference'     => $paymentMethod->source_reference,
        ])->send();

        if ($response->isSuccessful()) {
            return true;
        }
        throw new Exception($response->getMessage());
    }

    public function verifyBankAccount($client, $publicId, $amount1, $amount2): void
    {
        $customer = $this->customer($client->id);
        $paymentMethod = PaymentMethod::clientId($client->id)
            ->wherePublicId($publicId)
            ->firstOrFail();

        // Omnipay doesn't support verifying payment methods
        // Also, it doesn't want to urlencode without putting numbers inside the brackets
        $result = $this->makeStripeCall(
            'POST',
            'customers/' . $customer->token . '/sources/' . $paymentMethod->source_reference . '/verify',
            'amounts[]=' . (int) $amount1 . '&amounts[]=' . (int) $amount2
        );

        if (is_string($result) && $result !== 'This bank account has already been verified.') {
            return $result;
        }

        $paymentMethod->status = PAYMENT_METHOD_STATUS_VERIFIED;
        $paymentMethod->save();

        if ( ! $customer->default_payment_method_id) {
            $customer->default_payment_method_id = $paymentMethod->id;
            $customer->save();
        }

        return true;
    }

    public function createSource()
    {
        $amount = (int) ($this->invoice()->getRequestedAmount() * 100);
        $invoiceNumber = $this->invoice()->invoice_number;
        $currency = $this->client()->getCurrencyCode();
        $email = $this->contact()->email;
        $gatewayType = GatewayType::getAliasFromId($this->gatewayType);
        $redirect = url("/complete_source/{$this->invitation->invitation_key}/{$gatewayType}");
        $country = $this->client()->country ? $this->client()->country->iso_3166_2 : ($this->account()->country ? $this->account()->country->iso_3166_2 : '');
        $extra = '';

        if ($this->gatewayType == GATEWAY_TYPE_ALIPAY) {
            if ( ! $this->accountGateway->getAlipayEnabled()) {
                throw new Exception('Alipay is not enabled');
            }
            $type = 'alipay';
        } elseif ($this->gatewayType == GATEWAY_TYPE_BITCOIN) {
            if ( ! $this->accountGateway->getBitcoinEnabled()) {
                throw new Exception('Bitcoin is not enabled');
            }
            $type = 'bitcoin';
            $extra = "&owner[email]={$email}";
        } else {
            if ( ! $this->accountGateway->getSofortEnabled()) {
                throw new Exception('Sofort is not enabled');
            }
            $type = 'sofort';
            $extra = "&sofort[country]={$country}&statement_descriptor={$invoiceNumber}";
        }

        $data = "type={$type}&amount={$amount}&currency={$currency}&redirect[return_url]={$redirect}{$extra}";
        $response = $this->makeStripeCall('POST', 'sources', $data);

        if (is_array($response) && isset($response['id'])) {
            $this->invitation->transaction_reference = $response['id'];
            $this->invitation->save();

            if ($this->gatewayType == GATEWAY_TYPE_BITCOIN) {
                return view('payments/stripe/bitcoin', [
                    'client'        => $this->client(),
                    'account'       => $this->account(),
                    'invitation'    => $this->invitation,
                    'invoiceNumber' => $invoiceNumber,
                    'amount'        => $this->invoice()->getRequestedAmount(),
                    'source'        => $response,
                ]);
            }

            return redirect($response['redirect']['url']);
        }
        throw new Exception($response);
    }

    public function makeStripeCall(string $method, $url, $body = null)
    {
        $apiKey = $this->accountGateway->getConfig()->apiKey;

        if ( ! $apiKey) {
            return 'No API key set';
        }

        try {
            $options = [
                'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                'auth'    => [$apiKey, ''],
            ];

            if ($body) {
                $options['body'] = $body;
            }

            $response = (new \GuzzleHttp\Client(['base_uri' => 'https://api.stripe.com/v1/']))->request(
                $method,
                $url,
                $options
            );

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();

            $body = json_decode($response->getBody(), true);
            if ($body && $body['error'] && $body['error']['type'] == 'invalid_request_error') {
                return $body['error']['message'];
            }

            return $e->getMessage();
        }
    }

    public function handleWebHook($input): void
    {
        $eventId = \Illuminate\Support\Arr::get($input, 'id');
        $eventType = \Illuminate\Support\Arr::get($input, 'type');

        $accountGateway = $this->accountGateway;
        $accountId = $accountGateway->account_id;

        if ( ! $eventId) {
            throw new Exception('Missing event id');
        }

        if ( ! $eventType) {
            throw new Exception('Missing event type');
        }

        $supportedEvents = [
            'charge.failed',
            'charge.succeeded',
            'charge.refunded',
            'customer.source.updated',
            'customer.source.deleted',
            'source.chargeable',
        ];

        if ( ! in_array($eventType, $supportedEvents)) {
            return ['message' => 'Ignoring event'];
        }

        // Fetch the event directly from Stripe for security
        $eventDetails = $this->makeStripeCall('GET', 'events/' . $eventId);

        if (is_string($eventDetails) || ! $eventDetails) {
            return false;
        }

        if ($eventType != $eventDetails['type']) {
            return false;
        }

        if ( ! $eventDetails['pending_webhooks']) {
            return false;
        }

        $source = $eventDetails['data']['object'];
        $sourceRef = $source['id'];

        if ($eventType == 'charge.failed' || $eventType == 'charge.succeeded' || $eventType == 'charge.refunded') {
            $payment = Payment::scope(false, $accountId)->where('transaction_reference', '=', $sourceRef)->first();

            if ( ! $payment) {
                return false;
            }

            if ($payment->is_deleted || $payment->invoice->is_deleted) {
                return false;
            }

            if ($eventType == 'charge.failed') {
                if ( ! $payment->isFailed()) {
                    $payment->markFailed($source['failure_message']);

                    $userMailer = app(\App\Ninja\Mailers\UserMailer::class);
                    $userMailer->sendNotification($payment->user, $payment->invoice, 'payment_failed', $payment);
                }
            } elseif ($eventType == 'charge.succeeded') {
                $payment->markComplete();
            } elseif ($eventType == 'charge.refunded') {
                $payment->recordRefund($source['amount_refunded'] / 100 - $payment->refunded);
            }
        } elseif ($eventType == 'customer.source.updated' || $eventType == 'customer.source.deleted') {
            $paymentMethod = PaymentMethod::scope(false, $accountId)->where('source_reference', '=', $sourceRef)->first();

            if ( ! $paymentMethod) {
                return false;
            }

            if ($eventType == 'customer.source.deleted') {
                $paymentMethod->delete();
            } elseif ($eventType == 'customer.source.updated') {
                //$this->paymentService->convertPaymentMethodFromStripe($source, null, $paymentMethod)->save();
            }
        } elseif ($eventType == 'source.chargeable') {
            $this->invitation = Invitation::scope(false, $accountId)->where('transaction_reference', '=', $sourceRef)->first();
            if ( ! $this->invitation) {
                return false;
            }
            $data = sprintf('amount=%d&currency=%s&source=%s', $source['amount'], $source['currency'], $source['id']);
            $this->purchaseResponse = $response = $this->makeStripeCall('POST', 'charges', $data);
            $this->gatewayType = GatewayType::getIdFromAlias($source['type']);
            if (is_array($response) && isset($response['id'])) {
                $this->createPayment($response['id']);
            }
        }

        return 'Processed successfully';
    }

    protected function prepareStripeAPI(): void
    {
        Stripe::setApiKey($this->accountGateway->getConfigField('apiKey'));
    }

    protected function checkCustomerExists($customer): bool
    {
        $response = $this->gateway()
            ->fetchCustomer(['customerReference' => $customer->token])
            ->send();

        return (bool) $response->isSuccessful();

        /*
        $this->tokenResponse = $response->getData();

        // import Stripe tokens created before payment methods table was added
        if (! $customer->payment_methods->count()) {
            if ($paymentMethod = $this->createPaymentMethod($customer)) {
                $customer->default_payment_method_id = $paymentMethod->id;
                $customer->save();
                $customer->load('payment_methods');
            }
        }
        */
    }

    protected function paymentDetails($paymentMethod = false)
    {
        $data = parent::paymentDetails($paymentMethod);

        // Stripe complains if the email field is set
        unset($data['email']);

        if ( ! empty($this->input['paymentIntentID'])) {
            // If we're completing a previously initiated payment intent, use that ID first.
            $data['payment_intent'] = $this->input['paymentIntentID'];
            unset($data['card']);

            return $data;
        }

        if ($paymentMethod) {
            return $data;
        }

        if ( ! empty($this->input['paymentMethodID'])) {
            // We're using an existing payment method.
            $data['payment_method'] = $this->input['paymentMethodID'];
            unset($data['card']);
        } elseif ( ! empty($this->input['sourceToken'])) {
            $data['token'] = $this->input['sourceToken'];
            unset($data['card']);
        }

        if ( ! empty($this->input['plaidPublicToken'])) {
            $data['plaidPublicToken'] = $this->input['plaidPublicToken'];
            $data['plaidAccountId'] = $this->input['plaidAccountId'];
            unset($data['card']);
        }

        return $data;
    }

    protected function creatingPaymentMethod($paymentMethod)
    {
        $data = $this->tokenResponse;
        $source = false;

        if ( ! empty($data['object']) && ($data['object'] == 'card' || $data['object'] == 'bank_account')) {
            $source = $data;
        } elseif ( ! empty($data['object']) && $data['object'] == 'customer') {
            $sources = empty($data['sources']) ? $data['cards'] : $data['sources'];
            $source = reset($sources['data']);
        } elseif ( ! empty($data['source'])) {
            $source = $data['source'];
        } elseif ( ! empty($data['card'])) {
            $source = $data['card'];
        }

        if ( ! $source) {
            return false;
        }

        if ( ! empty($source['id'])) {
            $paymentMethod->source_reference = $source['id'];
        } elseif ( ! empty($data['id'])) {
            // Find an ID on the payment method instead of the card.
            $paymentMethod->source_reference = $data['id'];
        }
        $paymentMethod->last4 = $source['last4'];

        // For older users the Stripe account may just have the customer token but not the card version
        // In that case we'd use GATEWAY_TYPE_TOKEN even though we're creating the credit card
        if ($this->isGatewayType(GATEWAY_TYPE_CREDIT_CARD)
            || $this->isGatewayType(GATEWAY_TYPE_APPLE_PAY)
            || $this->isGatewayType(GATEWAY_TYPE_TOKEN)) {
            if (isset($source['exp_year'], $source['exp_month'])) {
                $paymentMethod->expiration = $source['exp_year'] . '-' . $source['exp_month'] . '-01';
            }
            if (isset($source['brand'])) {
                $paymentMethod->payment_type_id = PaymentType::parseCardType($source['brand']);
            }
        } elseif ($this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER)) {
            $paymentMethod->routing_number = $source['routing_number'];
            $paymentMethod->payment_type_id = PAYMENT_TYPE_ACH;
            $paymentMethod->status = $source['status'];
            $currency = \Illuminate\Support\Facades\Cache::get('currencies')->where('code', mb_strtoupper($source['currency']))->first();

            if ($currency) {
                $paymentMethod->currency_id = $currency->id;
                $paymentMethod->setRelation('currency', $currency);
            }
        }

        return $paymentMethod;
    }

    protected function creatingPayment($payment, $paymentMethod)
    {
        $isBank = $this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER, $paymentMethod);
        $isAlipay = $this->isGatewayType(GATEWAY_TYPE_ALIPAY, $paymentMethod);
        $isSofort = $this->isGatewayType(GATEWAY_TYPE_SOFORT, $paymentMethod);
        $isBitcoin = $this->isGatewayType(GATEWAY_TYPE_BITCOIN, $paymentMethod);

        if ($isBank || $isAlipay || $isSofort || $isBitcoin) {
            $payment->payment_status_id = $this->purchaseResponse['status'] == 'succeeded' ? PAYMENT_STATUS_COMPLETED : PAYMENT_STATUS_PENDING;
            if ($isAlipay) {
                $payment->payment_type_id = PAYMENT_TYPE_ALIPAY;
            } elseif ($isSofort) {
                $payment->payment_type_id = PAYMENT_TYPE_SOFORT;
            } elseif ($isBitcoin) {
                $payment->payment_type_id = PAYMENT_TYPE_BITCOIN;
            }
        } elseif ( ! $paymentMethod && $this->isGatewayType(GATEWAY_TYPE_CREDIT_CARD) && ! strcmp($this->purchaseResponse['payment_method_details']['type'], 'card')) {
            $card = $this->purchaseResponse['payment_method_details']['card'];
            $payment->last4 = $card['last4'];
            $payment->expiration = $card['exp_year'] . '-' . $card['exp_month'] . '-01';
            $payment->payment_type_id = PaymentType::parseCardType($card['brand']);
        }

        return $payment;
    }

    private function getPlaidToken($publicToken, $accountId): mixed
    {
        $clientId = $this->accountGateway->getPlaidClientId();
        $secret = $this->accountGateway->getPlaidSecret();

        if ( ! $clientId) {
            throw new Exception('plaid client id not set'); // TODO use text strings
        }

        if ( ! $secret) {
            throw new Exception('plaid secret not set');
        }

        try {
            $subdomain = $this->accountGateway->getPlaidEnvironment() == 'production' ? 'api' : 'tartan';
            $response = (new \GuzzleHttp\Client(['base_uri' => "https://{$subdomain}.plaid.com"]))->request(
                'POST',
                'exchange_token',
                [
                    'allow_redirects' => false,
                    'headers'         => ['content-type' => 'application/x-www-form-urlencoded'],
                    'body'            => http_build_query([
                        'client_id'    => $clientId,
                        'secret'       => $secret,
                        'public_token' => $publicToken,
                        'account_id'   => $accountId,
                    ]),
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody(), true);

            if ($body && ! empty($body['message'])) {
                throw new Exception($body['message'], $e->getCode(), $e);
            }
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}

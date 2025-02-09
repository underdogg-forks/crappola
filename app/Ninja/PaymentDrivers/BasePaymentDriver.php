<?php

namespace App\Ninja\PaymentDrivers;

use App\Libraries\Utils;
use App\Models\Account;
use App\Models\AccountGatewaySettings;
use App\Models\AccountGatewayToken;
use App\Models\GatewayType;
use App\Models\License;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Ninja\Repositories\InvoiceRepository;
use CreditCard;
use DateTime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Omnipay;
use Omnipay\Common\Item;
use Session;
use URL;

class BasePaymentDriver
{
    public $invitation;

    public $accountGateway;

    public $canRefundPayments = false;

    protected $gatewayType;

    protected $gateway;

    protected $customer;

    protected $sourceId;

    protected $input;

    protected $customerResponse;

    protected $tokenResponse;

    protected $purchaseResponse;

    protected $sourceReferenceParam = 'token';

    protected $customerReferenceParam;

    protected $transactionReferenceParam;

    public function __construct($accountGateway = false, $invitation = false, $gatewayType = false)
    {
        $this->accountGateway = $accountGateway;
        $this->invitation = $invitation;
        $this->gatewayType = $gatewayType ?: $this->gatewayTypes()[0];
    }

    public function isGateway($gatewayId): bool
    {
        return $this->accountGateway->gateway_id == $gatewayId;
    }

    public function isValid(): string|bool
    {
        return true;
    }

    public function gatewayTypes(): array
    {
        return [
            GATEWAY_TYPE_CREDIT_CARD,
        ];
    }

    public function handles($type): bool
    {
        return in_array($type, $this->gatewayTypes());
    }

    // when set to true we won't pass the card details with the form
    public function tokenize()
    {
        return false;
    }

    // set payment method as pending until confirmed
    public function isTwoStep(): bool
    {
        return false;
    }

    public function providerName(): string
    {
        return mb_strtolower($this->accountGateway->gateway->provider);
    }

    public function startPurchase($input = false, $sourceId = false)
    {
        $this->input = $input;
        $this->sourceId = $sourceId;

        \Illuminate\Support\Facades\Session::put('invitation_key', $this->invitation->invitation_key);
        \Illuminate\Support\Facades\Session::put($this->invitation->id . 'gateway_type', $this->gatewayType);
        \Illuminate\Support\Facades\Session::put($this->invitation->id . 'payment_ref', $this->invoice()->id . '_' . uniqid());

        $gateway = $this->accountGateway->gateway;

        if ( ! $this->meetsGatewayTypeLimits($this->gatewayType)) {
            // The customer must have hacked the URL
            \Illuminate\Support\Facades\Session::flash('error', trans('texts.limits_not_met'));

            return redirect()->to('view/' . $this->invitation->invitation_key);
        }

        if ( ! $this->isGatewayType(GATEWAY_TYPE_TOKEN)) {
            // apply gateway fees
            $invoiceRepo = app(InvoiceRepository::class);
            $invoiceRepo->setGatewayFee($this->invoice(), $this->gatewayType);
        }

        // For these gateway types we use the API directrly rather than Omnipay
        if ($this->shouldUseSource()) {
            return;
        }

        if ($this->isGatewayType(GATEWAY_TYPE_TOKEN) || $gateway->is_offsite) {
            if (\Illuminate\Support\Facades\Session::has('error')) {
                \Illuminate\Support\Facades\Session::reflash();
            } else {
                try {
                    $this->completeOnsitePurchase();
                } catch (PaymentActionRequiredException $exception) {
                    return $this->startStepTwo($exception->getData(), $sourceId);
                }

                if ($redirectUrl = session('redirect_url:' . $this->invitation->invitation_key)) {
                    $separator = str_contains($redirectUrl, '?') ? '&' : '?';

                    return redirect()->to($redirectUrl . $separator . 'invoice_id=' . $this->invoice()->public_id);
                }

                \Illuminate\Support\Facades\Session::flash('message', trans('texts.applied_payment'));
            }

            return redirect()->to('view/' . $this->invitation->invitation_key);
        }

        $url = 'payment/' . $this->invitation->invitation_key;
        if (request()->capture) {
            $url .= '?capture=true';
        }

        $data = [
            'details'                 => empty($input['details']) ? false : json_decode($input['details']),
            'accountGateway'          => $this->accountGateway,
            'acceptedCreditCardTypes' => $this->accountGateway->getCreditcardTypes(),
            'gateway'                 => $gateway,
            'showBreadcrumbs'         => false,
            'url'                     => $url,
            'amount'                  => $this->invoice()->getRequestedAmount(),
            'invoiceNumber'           => $this->invoice()->invoice_number,
            'client'                  => $this->client(),
            'contact'                 => $this->invitation->contact,
            'invitation'              => $this->invitation,
            'gatewayType'             => $this->gatewayType,
            'currencyId'              => $this->client()->getCurrencyId(),
            'currencyCode'            => $this->client()->getCurrencyCode(),
            'account'                 => $this->account(),
            'sourceId'                => $sourceId,
            'tokenize'                => $this->tokenize(),
            'driver'                  => $this,
            'transactionToken'        => $this->createTransactionToken(),
        ];

        return view($this->paymentView(), $data);
    }

    public function startStepTwo($data = null, $sourceId = false)
    {
        $url = 'payment/' . $this->invitation->invitation_key;

        if ($sourceId) {
            $url .= '/token/' . $sourceId;
        }

        if (request()->capture) {
            $url .= '?capture=true';
        }

        $data = [
            'step2_details'           => $data,
            'url'                     => $url,
            'showBreadcrumbs'         => false,
            'accountGateway'          => $this->accountGateway,
            'gateway'                 => $this->accountGateway->gateway,
            'acceptedCreditCardTypes' => $this->accountGateway->getCreditcardTypes(),
            'amount'                  => $this->invoice()->getRequestedAmount(),
            'invoiceNumber'           => $this->invoice()->invoice_number,
            'client'                  => $this->client(),
            'contact'                 => $this->invitation->contact,
            'invitation'              => $this->invitation,
            'gatewayType'             => $this->gatewayType,
            'currencyId'              => $this->client()->getCurrencyId(),
            'currencyCode'            => $this->client()->getCurrencyCode(),
            'account'                 => $this->account(),
            'tokenize'                => $this->tokenize(),
            'transactionToken'        => $this->createTransactionToken(),
        ];

        return view($this->stepTwoView(), $data);
    }

    // check if a custom partial exists for this provider
    public function partialView(): string|false
    {
        $file = sprintf('%s/views/payments/%s/partial.blade.php', resource_path(), $this->providerName());

        if (file_exists($file)) {
            return sprintf('payments.%s.partial', $this->providerName());
        }

        return false;
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->isGatewayType(GATEWAY_TYPE_CREDIT_CARD)) {
            $rules = array_merge($rules, [
                'first_name' => 'required',
                'last_name'  => 'required',
            ]);

            // TODO check this is always true
            if ( ! $this->tokenize()) {
                $rules = array_merge($rules, [
                    'card_number'      => 'required',
                    'expiration_month' => 'required',
                    'expiration_year'  => 'required',
                    'cvv'              => 'required',
                ]);
            }

            if ($this->accountGateway->show_address) {
                $rules = array_merge($rules, [
                    'address1'    => 'required',
                    'city'        => 'required',
                    'postal_code' => 'required',
                    'country_id'  => 'required',
                ]);

                if ($this->account()->requiresAddressState()) {
                    $rules['state'] = 'required';
                }
            }
        }

        return $rules;
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

        if ( ! $data) {
            // No payment method to charge against yet; probably a 2-step or capture-only transaction.
            return;
        }

        return $this->doOmnipayOnsitePurchase($data, $paymentMethod);
    }

    public function paymentDetailsFromClient(): array
    {
        $invoice = $this->invoice();
        $client = $this->client();
        $contact = $this->invitation->contact ?: $client->contacts()->first();
        $hasShippingAddress = $this->accountGateway->show_shipping_address;

        return [
            'email'            => $contact->email,
            'company'          => $client->getDisplayName(),
            'firstName'        => $contact->first_name,
            'lastName'         => $contact->last_name,
            'billingAddress1'  => $client->address1,
            'billingAddress2'  => $client->address2,
            'billingCity'      => $client->city,
            'billingPostcode'  => $client->postal_code,
            'billingState'     => $client->state,
            'billingCountry'   => $client->country ? $client->country->iso_3166_2 : '',
            'billingPhone'     => $contact->phone,
            'shippingAddress1' => $client->shipping_address1 ?: $client->address1,
            'shippingAddress2' => $client->shipping_address1 ?: $client->address2,
            'shippingCity'     => $client->shipping_address1 ?: $client->city,
            'shippingPostcode' => $client->shipping_address1 ?: $client->postal_code,
            'shippingState'    => $client->shipping_address1 ?: $client->state,
            'shippingCountry'  => $client->shipping_address1 ? ($client->shipping_country ? $client->shipping_country->iso_3166_2 : '') : ($client->country ? $client->country->iso_3166_2 : ''),
            'shippingPhone'    => $contact->phone,
        ];
    }

    public function shouldUseSource(): bool
    {
        // Use Omnipay by default
        return false;
    }

    /*
    protected function tokenDetails()
    {
        $details = [];

        if ($customer = $this->customer()) {
            $details['customerReference'] = $customer->token;
        }

        return $details;
    }
    */

    public function customer($clientId = false)
    {
        if ($this->customer) {
            return $this->customer;
        }

        if ( ! $clientId) {
            $clientId = $this->client()->id;
        }

        $this->customer = AccountGatewayToken::clientAndGateway($clientId, $this->accountGateway->id)
            ->with('payment_methods')
            ->orderBy('id', 'desc')
            ->first();

        if ($this->customer && $this->invitation) {
            $this->customer = $this->checkCustomerExists($this->customer) ? $this->customer : null;
        }

        return $this->customer;
    }

    public function verifyBankAccount($client, $publicId, $amount1, $amount2)
    {
        throw new Exception('verifyBankAccount not implemented');
    }

    public function removePaymentMethod($paymentMethod)
    {
        return $paymentMethod->delete() ?? true;
    }

    // Some gateways (ie, Checkout.com and Braintree) require generating a token before paying for the invoice
    public function createTransactionToken() {}

    public function createToken()
    {
        $account = $this->account();

        if ( ! $customer = $this->customer()) {
            $customer = new AccountGatewayToken();
            $customer->account_id = $account->id;
            $customer->contact_id = $this->invitation->contact_id;
            $customer->account_gateway_id = $this->accountGateway->id;
            $customer->client_id = $this->client()->id;
            $customer = $this->creatingCustomer($customer);
            $customer->save();
        }

        // archive the old payment method
        $paymentMethod = PaymentMethod::clientId($this->client()->id)
            ->isBankAccount($this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER))
            ->first();

        if ($paymentMethod) {
            $paymentMethod->delete();
        }

        $paymentMethod = $this->createPaymentMethod($customer);

        if ($paymentMethod) {
            $customer->default_payment_method_id = $paymentMethod->id;
            $customer->save();
        }

        return $paymentMethod;
    }

    public function createPaymentMethod($customer)
    {
        $paymentMethod = PaymentMethod::createNew($this->invitation);
        $paymentMethod->contact_id = $this->contact()->id;
        $paymentMethod->ip = Request::ip();
        $paymentMethod->account_gateway_token_id = $customer->id;
        $paymentMethod->setRelation('account_gateway_token', $customer);
        $paymentMethod = $this->creatingPaymentMethod($paymentMethod);

        if ($paymentMethod) {
            // archive the old payment method
            $oldPaymentMethod = PaymentMethod::clientId($this->client()->id)
                ->wherePaymentTypeId($paymentMethod->payment_type_id)
                ->first();

            if ($oldPaymentMethod) {
                $oldPaymentMethod->delete();
            }

            $paymentMethod->save();
        }

        return $paymentMethod;
    }

    public function deleteToken() {}

    public function createPayment($ref = false, $paymentMethod = null)
    {
        $account = $this->account();
        $invitation = $this->invitation;
        $invoice = $this->invoice();
        if ( ! $invoice->canBePaid()) {
            return false;
        }

        $invoice->markSentIfUnsent();

        $payment = Payment::createNew($invitation);
        $payment->invitation_id = $invitation->id;
        $payment->account_gateway_id = $this->accountGateway->id;
        $payment->invoice_id = $invoice->id;
        $payment->amount = $invoice->getRequestedAmount();
        $payment->client_id = $invoice->client_id;
        $payment->contact_id = $invitation->contact_id;
        $payment->transaction_reference = $ref;
        $payment->payment_date = $account->getDateTime()->format('Y-m-d');
        $payment->ip = Request::ip();

        //Laravel 6 upgrade - uncommented this line as it was causing a failure
        // $payment = $this->creatingPayment($payment, $paymentMethod);

        if ($paymentMethod) {
            $payment->last4 = $paymentMethod->last4;
            $payment->expiration = $paymentMethod->expiration;
            $payment->routing_number = $paymentMethod->routing_number;
            $payment->payment_type_id = $paymentMethod->payment_type_id;
            $payment->email = $paymentMethod->email;
            $payment->bank_name = $paymentMethod->bank_name;
            $payment->payment_method_id = $paymentMethod->id;
        }

        $payment->save();

        $accountKey = $invoice->account->account_key;

        if ($accountKey == env('NINJA_LICENSE_ACCOUNT_KEY')) {
            $this->createLicense($payment);
            // TODO move this code
            // enable pro plan for hosted users
        } elseif ($invoice->account->isNinjaAccount()) {
            foreach ($invoice->invoice_items as $invoice_item) {
                // Hacky, but invoices don't have meta fields to allow us to store this easily
                if (1 == preg_match('/^Plan - (.+) \((.+)\)$/', $invoice_item->product_key, $matches)) {
                    $plan = mb_strtolower($matches[1]);
                    $term = mb_strtolower($matches[2]);
                    $price = $invoice_item->cost;
                    if ($plan === PLAN_ENTERPRISE) {
                        preg_match('/###[\d]* [\w]* (\d*)/', $invoice_item->notes, $numUserMatches);
                        $numUsers = count($numUserMatches) ? $numUserMatches[1] : 5;
                    } else {
                        $numUsers = 1;
                    }
                }
            }

            if ($plan !== '' && $plan !== '0') {
                $account = Account::with('users')->find($invoice->client->public_id);
                $company = $account->company;

                if (
                    $company->plan != $plan
                    || DateTime::createFromFormat('Y-m-d', $account->company->plan_expires) <= date_create('-7 days')
                ) {
                    // Either this is a different plan, or the subscription expired more than a week ago
                    // Reset any grandfathering
                    $company->plan_started = date_create()->format('Y-m-d');
                }

                if (
                    $company->plan == $plan
                    && $company->plan_term == $term
                    && DateTime::createFromFormat('Y-m-d', $company->plan_expires) >= date_create()
                ) {
                    // This is a renewal; mark it paid as of when this term expires
                    $company->plan_paid = $company->plan_expires;
                } else {
                    $company->plan_paid = date_create()->format('Y-m-d');
                }

                $company->payment_id = $payment->id;
                $company->plan = $plan;
                $company->plan_term = $term;
                $company->plan_price = $price;
                $company->num_users = $numUsers;
                $company->plan_expires = DateTime::createFromFormat('Y-m-d', $account->company->plan_paid)
                    ->modify($term === PLAN_TERM_MONTHLY ? '+1 month' : '+1 year')->format('Y-m-d');

                if ($company->hasActivePromo()) {
                    $company->discount_expires = date_create()->modify('1 year')->format('Y-m-d');
                    $company->promo_expires = null;
                }

                $company->save();
            }
        }

        return $payment;
    }

    public function refundPayment($payment, $amount = 0)
    {
        $amount = $amount ? min($amount, $payment->getCompletedAmount()) : $payment->getCompletedAmount();

        if ($payment->is_deleted) {
            return false;
        }

        if ( ! $amount) {
            return false;
        }

        if ($payment->payment_type_id == PAYMENT_TYPE_CREDIT) {
            return $payment->recordRefund($amount);
        }

        $details = $this->refundDetails($payment, $amount);
        $response = $this->gateway()->refund($details)->send();

        if ($response->isSuccessful()) {
            return $payment->recordRefund($amount);
        }

        if ($this->attemptVoidPayment($response, $payment, $amount)) {
            $details = ['transactionReference' => $payment->transaction_reference];
            $response = $this->gateway->void($details)->send();
            if ($response->isSuccessful()) {
                return $payment->markVoided();
            }
        }

        return false;
    }

    public function completeOffsitePurchase($input)
    {
        $this->input = $input;
        $transRef = Arr::get($this->input, 'token') ?: $this->invitation->transaction_reference;

        if (method_exists($this->gateway(), 'completePurchase')) {
            $details = $this->paymentDetails();
            $response = $this->gateway()->completePurchase($details)->send();
            $paymentRef = $response->getTransactionReference() ?: $transRef;

            if ($response->isCancelled()) {
                return false;
            }

            if ( ! $response->isSuccessful()) {
                throw new Exception($response->getMessage());
            }
        } else {
            $paymentRef = $transRef;
        }

        $this->updateClientFromOffsite($transRef, $paymentRef);

        // check invoice still has balance
        if ((float) ($this->invoice()->balance) === 0.0) {
            throw new Exception(trans('texts.payment_error_code', ['code' => 'NB']));
        }

        // check this isn't a duplicate transaction reference
        if (Payment::whereAccountId($this->invitation->account_id)
            ->whereTransactionReference($paymentRef)
            ->first()) {
            throw new Exception(trans('texts.payment_error_code', ['code' => 'DT']));
        }

        return $this->createPayment($paymentRef);
    }

    public function tokenLinks(): array
    {
        if ( ! $this->customer()) {
            return [];
        }

        $paymentMethods = $this->customer()->payment_methods;
        $links = [];

        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod->payment_type_id == PAYMENT_TYPE_ACH && $paymentMethod->status != PAYMENT_METHOD_STATUS_VERIFIED) {
                continue;
            }

            if ( ! $this->meetsGatewayTypeLimits($paymentMethod->payment_type->gateway_type_id)) {
                continue;
            }

            $url = \Illuminate\Support\Facades\URL::to(sprintf('/payment/%s/token/', $this->invitation->invitation_key) . $paymentMethod->public_id);

            if ($paymentMethod->payment_type_id == PAYMENT_TYPE_ACH) {
                $label = $paymentMethod->bank_name ? $paymentMethod->bank_name : trans('texts.use_bank_on_file');
            } elseif ($paymentMethod->payment_type_id == PAYMENT_TYPE_PAYPAL) {
                $label = 'PayPal: ' . $paymentMethod->email;
            } else {
                $label = trans('texts.payment_type_on_file', ['type' => $paymentMethod->payment_type->name]);
            }

            $label .= $this->invoice()->present()->gatewayFee($paymentMethod->payment_type->gateway_type_id);

            $links[] = [
                'url'   => $url,
                'label' => $label,
            ];
        }

        return $links;
    }

    /**
     * @return array<mixed, array<'gatewayTypeId'|'label'|'url', mixed>>
     */
    public function paymentLinks(): array
    {
        $links = [];

        foreach ($this->gatewayTypes() as $gatewayTypeId) {
            if ($gatewayTypeId === GATEWAY_TYPE_TOKEN) {
                continue;
            }

            if ( ! $this->meetsGatewayTypeLimits($gatewayTypeId)) {
                continue;
            }

            $gatewayTypeAlias = GatewayType::getAliasFromId($gatewayTypeId);

            if ($gatewayTypeId == GATEWAY_TYPE_CUSTOM1) {
                $url = 'javascript:showCustom1Modal();';
                $label = e($this->accountGateway->getConfigField('name'));
            } elseif ($gatewayTypeId == GATEWAY_TYPE_CUSTOM2) {
                $url = 'javascript:showCustom2Modal();';
                $label = e($this->accountGateway->getConfigField('name'));
            } elseif ($gatewayTypeId == GATEWAY_TYPE_CUSTOM3) {
                $url = 'javascript:showCustom3Modal();';
                $label = e($this->accountGateway->getConfigField('name'));
            } else {
                $url = $this->paymentUrl($gatewayTypeAlias);
                $label = ($custom = $this->account()->getLabel($gatewayTypeAlias)) ? $custom : trans('texts.' . $gatewayTypeAlias);
            }

            $label .= $this->invoice()->present()->gatewayFee($gatewayTypeId);

            $links[] = [
                'gatewayTypeId' => $gatewayTypeId,
                'url'           => $url,
                'label'         => $label,
            ];
        }

        return $links;
    }

    public function supportsGatewayType($gatewayTypeId): bool
    {
        return in_array($gatewayTypeId, $this->gatewayTypes());
    }

    public function handleWebHook($input)
    {
        throw new Exception('Unsupported gateway');
    }

    // optionally pass a paymentMethod to determine the type from the token
    protected function isGatewayType($gatewayType, $paymentMethod = false): bool
    {
        if ($paymentMethod) {
            return $paymentMethod->gatewayType() == $gatewayType;
        }

        return $this->gatewayType === $gatewayType;
    }

    protected function invoice()
    {
        return $this->invitation->invoice;
    }

    protected function contact()
    {
        return $this->invitation->contact;
    }

    protected function client()
    {
        return $this->invoice()->client;
    }

    protected function account()
    {
        return $this->client()->account;
    }

    protected function stepTwoView(): string
    {
        $file = sprintf('%s/views/payments/%s/step2.blade.php', resource_path(), $this->providerName());

        if (file_exists($file)) {
            return sprintf('payments.%s/step2', $this->providerName());
        }

        return 'payments.step2';
    }

    // check if a custom view exists for this provider
    protected function paymentView(): string
    {
        $gatewayTypeAlias = GatewayType::getAliasFromId($this->gatewayType);

        $file = sprintf('%s/views/payments/%s/%s.blade.php', resource_path(), $this->providerName(), $gatewayTypeAlias);

        if (file_exists($file)) {
            return sprintf('payments.%s/%s', $this->providerName(), $gatewayTypeAlias);
        }

        return sprintf('payments.%s', $gatewayTypeAlias);
    }

    protected function gateway()
    {
        if ($this->gateway) {
            return $this->gateway;
        }

        $this->gateway = Omnipay::create($this->accountGateway->gateway->provider);
        $this->gateway->initialize((array) $this->accountGateway->getConfig());

        return $this->gateway;
    }

    protected function prepareOnsitePurchase($input = false, $paymentMethod = false)
    {
        $this->input = $input && count($input) ? $input : false;

        if ($input) {
            $this->updateClient();
        }

        // load or create token
        if ($this->isGatewayType(GATEWAY_TYPE_TOKEN)) {
            if ( ! $paymentMethod) {
                $paymentMethod = PaymentMethod::clientId($this->client()->id)
                    ->wherePublicId($this->sourceId)
                    ->firstOrFail();
            }

            $invoiceRepo = app(InvoiceRepository::class);
            $invoiceRepo->setGatewayFee($this->invoice(), $paymentMethod->payment_type->gateway_type_id);

            if ( ! $this->meetsGatewayTypeLimits($paymentMethod->payment_type->gateway_type_id)) {
                // The customer must have hacked the URL
                \Illuminate\Support\Facades\Session::flash('error', trans('texts.limits_not_met'));

                return redirect()->to('view/' . $this->invitation->invitation_key);
            }
        } else {
            if ($this->shouldCreateToken()) {
                $paymentMethod = $this->createToken();
            }

            if ( ! $this->meetsGatewayTypeLimits($this->gatewayType)) {
                // The customer must have hacked the URL
                \Illuminate\Support\Facades\Session::flash('error', trans('texts.limits_not_met'));

                return redirect()->to('view/' . $this->invitation->invitation_key);
            }
        }

        if ($this->isTwoStep() || request()->capture) {
            return;
        }

        // prepare and process payment
        return $this->paymentDetails($paymentMethod);
    }

    protected function doOmnipayOnsitePurchase($data, $paymentMethod = false)
    {
        $gateway = $this->gateway();

        // TODO move to payment driver class
        if ($this->isGateway(GATEWAY_SAGE_PAY_DIRECT) || $this->isGateway(GATEWAY_SAGE_PAY_SERVER)) {
            $items = null;
        } elseif ($this->account()->send_item_details) {
            $items = $this->paymentItems();
        } else {
            $items = null;
        }

        $response = $gateway->purchase($data)
            ->setItems($items)
            ->send();
        $this->purchaseResponse = (array) $response->getData();

        // parse the transaction reference
        if ($this->transactionReferenceParam) {
            if ( ! empty($this->purchaseResponse[$this->transactionReferenceParam])) {
                $ref = $this->purchaseResponse[$this->transactionReferenceParam];
            } else {
                throw new Exception($response->getMessage() ?: trans('texts.payment_error'));
            }
        } else {
            $ref = $response->getTransactionReference();
        }

        // wrap up
        if ($response->isSuccessful() && $ref) {
            $payment = $this->createPayment($ref, $paymentMethod);

            // TODO move this to stripe driver
            if ($this->invitation->invoice->account->isNinjaAccount()) {
                \Illuminate\Support\Facades\Session::flash('trackEventCategory', '/account');
                \Illuminate\Support\Facades\Session::flash('trackEventAction', '/buy_pro_plan');
                \Illuminate\Support\Facades\Session::flash('trackEventAmount', $payment->amount);
            }

            return $payment;
        }

        if ($response->isRedirect()) {
            $this->invitation->transaction_reference = $ref;
            $this->invitation->save();
            //Session::put('transaction_reference', $ref);
            \Illuminate\Support\Facades\Session::save();
            $response->redirect();
        } else {
            throw new Exception($response->getMessage() ?: trans('texts.payment_error'));
        }
    }

    protected function paymentDetails($paymentMethod = false): array
    {
        $invoice = $this->invoice();
        $gatewayTypeAlias = $this->gatewayType == GATEWAY_TYPE_TOKEN ? $this->gatewayType : GatewayType::getAliasFromId($this->gatewayType);
        $completeUrl = $this->invitation->getLink('complete', true) . '/' . $gatewayTypeAlias;

        $data = [
            'amount'          => $invoice->getRequestedAmount(),
            'currency'        => $invoice->getCurrencyCode(),
            'returnUrl'       => $completeUrl,
            'cancelUrl'       => $this->invitation->getLink(),
            'description'     => trans('texts.' . $invoice->getEntityType()) . (' ' . $invoice->invoice_number),
            'transactionId'   => $invoice->invoice_number,
            'transactionType' => 'Purchase',
            'clientIp'        => Request::getClientIp(),
        ];

        if ($paymentMethod) {
            if ($this->customerReferenceParam) {
                $data[$this->customerReferenceParam] = $paymentMethod->account_gateway_token->token;
            }

            $data[$this->sourceReferenceParam] = $paymentMethod->source_reference;
        } elseif ($this->input) {
            $data['card'] = new CreditCard($this->paymentDetailsFromInput($this->input));
        } else {
            $data['card'] = new CreditCard($this->paymentDetailsFromClient());
        }

        return $data;
    }

    protected function shouldCreateToken()
    {
        if ($this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER)) {
            return true;
        }

        if ( ! $this->handles(GATEWAY_TYPE_TOKEN)) {
            return false;
        }

        if ($this->account()->token_billing_type_id == TOKEN_BILLING_ALWAYS) {
            return true;
        }

        return (bool) (Arr::get($this->input, 'token_billing'));
    }

    protected function checkCustomerExists($customer): bool
    {
        return true;
    }

    protected function creatingCustomer($customer)
    {
        return $customer;
    }

    protected function creatingPaymentMethod($paymentMethod)
    {
        return $paymentMethod;
    }

    protected function createLicense($payment)
    {
        // TODO parse invoice to determine license
        if ($payment->amount == WHITE_LABEL_PRICE) {
            $affiliateId = 4;
            $productId = PRODUCT_WHITE_LABEL;
        } else {
            $affiliateId = 1;
            $productId = PRODUCT_ONE_CLICK_INSTALL;
        }

        $license = new License();
        $license->first_name = $this->contact()->first_name;
        $license->last_name = $this->contact()->last_name;
        $license->email = $this->contact()->email;
        $license->transaction_reference = $payment->transaction_reference;
        $license->license_key = Utils::generateLicense();
        $license->affiliate_id = $affiliateId;
        $license->product_id = $productId;
        $license->is_claimed = 0;
        $license->save();

        // Add the license key to the invoice content
        $invoiceItem = $payment->invoice->invoice_items->first();
        $invoiceItem->notes .= '

#' . $license->license_key;
        $invoiceItem->save();
    }

    protected function creatingPayment($payment, $paymentMethod)
    {
        return $payment;
    }

    protected function refundDetails($payment, $amount): array
    {
        return [
            'amount'               => $amount,
            'transactionReference' => $payment->transaction_reference,
            'currency'             => $payment->client->getCurrencyCode(),
        ];
    }

    protected function attemptVoidPayment($response, $payment, $amount): bool
    {
        // Partial refund not allowed for unsettled transactions
        return $amount == $payment->amount;
    }

    protected function createLocalPayment($payment)
    {
        return $payment;
    }

    protected function updateClientFromOffsite($transRef, $paymentRef)
    {
        // do nothing
    }

    protected function meetsGatewayTypeLimits($gatewayTypeId): bool
    {
        if ( ! $gatewayTypeId) {
            return true;
        }

        $accountGatewaySettings = AccountGatewaySettings::scope(false, $this->invitation->account_id)
            ->where('account_gateway_settings.gateway_type_id', '=', $gatewayTypeId)->first();

        if ($accountGatewaySettings) {
            $invoice = $this->invoice();

            if ($accountGatewaySettings->min_limit !== null && $invoice->balance < $accountGatewaySettings->min_limit) {
                return false;
            }

            if ($accountGatewaySettings->max_limit !== null && $invoice->balance > $accountGatewaySettings->max_limit) {
                return false;
            }
        }

        return true;
    }

    protected function paymentUrl($gatewayTypeAlias)
    {
        $account = $this->account();
        $url = \Illuminate\Support\Facades\URL::to(sprintf('/payment/%s/%s', $this->invitation->invitation_key, $gatewayTypeAlias));

        return $url;
    }

    private function paymentItems()
    {
        $invoice = $this->invoice();
        $items = [];
        $total = 0;

        foreach ($invoice->invoice_items as $invoiceItem) {
            // Some gateways require quantity is an integer
            if ((float) ($invoiceItem->qty) != (int) ($invoiceItem->qty)) {
                return;
            }

            $item = new Item([
                'name'        => $invoiceItem->product_key,
                'description' => mb_substr($invoiceItem->notes, 0, 100),
                'price'       => $invoiceItem->cost,
                'quantity'    => $invoiceItem->qty,
            ]);

            $items[] = $item;

            $total += $invoiceItem->cost * $invoiceItem->qty;
        }

        if ($total != $invoice->getRequestedAmount()) {
            $item = new Item([
                'name'        => trans('texts.taxes_and_fees'),
                'description' => '',
                'price'       => $invoice->getRequestedAmount() - $total,
                'quantity'    => 1,
            ]);

            $items[] = $item;
        }

        return $items;
    }

    private function updateClient()
    {
        if ( ! $this->isGatewayType(GATEWAY_TYPE_CREDIT_CARD)) {
            return;
        }

        // update the contact info
        if ( ! $this->contact()->getFullName()) {
            $this->contact()->first_name = $this->input['first_name'];
            $this->contact()->last_name = $this->input['last_name'];
        }

        if ( ! $this->contact()->email) {
            $this->contact()->email = $this->input['email'];
        }

        if ($this->contact()->isDirty()) {
            $this->contact()->save();
        }

        // update the address info
        $client = $this->client();

        if ($this->accountGateway->show_address && $this->accountGateway->update_address) {
            $client->address1 = trim($this->input['address1']);
            $client->address2 = trim($this->input['address2']);
            $client->city = trim($this->input['city']);
            $client->state = trim($this->input['state']);
            $client->postal_code = trim($this->input['postal_code']);
            $client->country_id = trim($this->input['country_id']);
        }

        if ($this->accountGateway->show_shipping_address) {
            $client->shipping_address1 = trim($this->input['shipping_address1']);
            $client->shipping_address2 = trim($this->input['shipping_address2']);
            $client->shipping_city = trim($this->input['shipping_city']);
            $client->shipping_state = trim($this->input['shipping_state']);
            $client->shipping_postal_code = trim($this->input['shipping_postal_code']);
            $client->shipping_country_id = trim($this->input['shipping_country_id']);
        }

        if ($client->isDirty()) {
            $client->save();
        }
    }

    private function paymentDetailsFromInput($input): array
    {
        $invoice = $this->invoice();
        $client = $this->client();

        $data = [
            'company'     => $client->getDisplayName(),
            'firstName'   => $input['first_name'] ?? null,
            'lastName'    => $input['last_name'] ?? null,
            'email'       => $input['email'] ?? null,
            'number'      => $input['card_number'] ?? null,
            'expiryMonth' => $input['expiration_month'] ?? null,
            'expiryYear'  => $input['expiration_year'] ?? null,
        ];

        // allow space until there's a setting to disable
        if (isset($input['cvv']) && $input['cvv'] != ' ') {
            $data['cvv'] = $input['cvv'];
        }

        if (isset($input['address1'])) {
            $hasShippingAddress = $this->accountGateway->show_shipping_address;
            $country = Utils::getFromCache($input['country_id'], 'countries');
            $shippingCountry = $hasShippingAddress ? Utils::getFromCache($input['shipping_country_id'], 'countries') : $country;

            $data = array_merge($data, [
                'billingAddress1'  => trim($input['address1']),
                'billingAddress2'  => trim($input['address2']),
                'billingCity'      => trim($input['city']),
                'billingState'     => trim($input['state']),
                'billingPostcode'  => trim($input['postal_code']),
                'billingCountry'   => $country->iso_3166_2,
                'shippingAddress1' => $hasShippingAddress ? trim($this->input['shipping_address1']) : trim($input['address1']),
                'shippingAddress2' => $hasShippingAddress ? trim($this->input['shipping_address2']) : trim($input['address2']),
                'shippingCity'     => $hasShippingAddress ? trim($this->input['shipping_city']) : trim($input['city']),
                'shippingState'    => $hasShippingAddress ? trim($this->input['shipping_state']) : trim($input['state']),
                'shippingPostcode' => $hasShippingAddress ? trim($this->input['shipping_postal_code']) : trim($input['postal_code']),
                'shippingCountry'  => $hasShippingAddress ? $shippingCountry->iso_3166_2 : $country->iso_3166_2,
            ]);
        }

        return $data;
    }
}

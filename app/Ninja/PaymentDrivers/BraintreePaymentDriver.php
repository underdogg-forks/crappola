<?php

namespace App\Ninja\PaymentDrivers;

use App\Libraries\Utils;
use App\Models\GatewayType;
use App\Models\PaymentType;
use Braintree\Customer;
use Exception;
use Illuminate\Support\Facades\Session;

class BraintreePaymentDriver extends BasePaymentDriver
{
    public $canRefundPayments = true;

    protected $customerReferenceParam = 'customerId';

    protected $sourceReferenceParam = 'paymentMethodToken';

    public function gatewayTypes()
    {
        $types = [
            GATEWAY_TYPE_CREDIT_CARD,
            GATEWAY_TYPE_TOKEN,
        ];

        if ($this->accountGateway && $this->accountGateway->getPayPalEnabled()) {
            $types[] = GATEWAY_TYPE_PAYPAL;
        }

        return $types;
    }

    public function tokenize()
    {
        return true;
    }

    public function startPurchase($input = false, $sourceId = false)
    {
        $data = parent::startPurchase($input, $sourceId);

        if ($this->isGatewayType(GATEWAY_TYPE_PAYPAL)) {
            /*
            if ( ! $sourceId || empty($input['device_data'])) {
                throw new Exception();
            }

            Session::put($this->invitation->id . 'device_data', $input['device_data']);
            */

            $data['details'] = ! empty($input['device_data']) ? json_decode($input['device_data']) : false;
        }

        return $data;
    }

    public function createToken()
    {
        $data = $this->paymentDetails();

        if ($customer = $this->customer()) {
            $customerReference = $customer->token;
        } else {
            $tokenResponse = $this->gateway()->createCustomer(['customerData' => $this->customerData()])->send();
            if ($tokenResponse->isSuccessful()) {
                $customerReference = $tokenResponse->getCustomerData()->id;
            } else {
                Utils::logError('Failed to create Braintree customer: ' . $tokenResponse->getMessage());

                return false;
            }
        }

        if ($customerReference) {
            $data['customerId'] = $customerReference;

            if ($this->isGatewayType(GATEWAY_TYPE_PAYPAL)) {
                $data['paymentMethodNonce'] = $this->input['sourceToken'];
            }

            $tokenResponse = $this->gateway->createPaymentMethod($data)->send();
            if ($tokenResponse->isSuccessful()) {
                $this->tokenResponse = $tokenResponse->getData()->paymentMethod;
            } else {
                Utils::logError('Failed to create Braintree token: ' . $tokenResponse->getMessage());

                return false;
            }
        }

        return parent::createToken();
    }

    public function creatingCustomer($customer)
    {
        $customer->token = $this->tokenResponse->customerId;

        return $customer;
    }

    public function removePaymentMethod($paymentMethod)
    {
        parent::removePaymentMethod($paymentMethod);

        $response = $this->gateway()->deletePaymentMethod([
            'token' => $paymentMethod->source_reference,
        ])->send();

        if ($response->isSuccessful()) {
            return true;
        }
        throw new Exception($response->getMessage());
    }

    public function createTransactionToken()
    {
        return $this->gateway()
            ->clientToken()
            ->send()
            ->getToken();
    }

    public function isValid()
    {
        try {
            $this->createTransactionToken();

            return true;
        } catch (Exception $exception) {
            return get_class($exception);
        }
    }

    protected function checkCustomerExists($customer)
    {
        if ( ! parent::checkCustomerExists($customer)) {
            return false;
        }

        $customer = $this->gateway()->findCustomer($customer->token)
            ->send()
            ->getData();

        return $customer instanceof Customer;
    }

    protected function paymentUrl($gatewayTypeAlias)
    {
        $url = parent::paymentUrl($gatewayTypeAlias);

        if (GatewayType::getIdFromAlias($gatewayTypeAlias) === GATEWAY_TYPE_PAYPAL) {
            $url .= '#braintree_paypal';
        }

        return $url;
    }

    protected function paymentDetails($paymentMethod = false)
    {
        $data = parent::paymentDetails($paymentMethod);

        $deviceData = array_get($this->input, 'device_data') ?: Session::get($this->invitation->id . 'device_data');

        if ($deviceData) {
            $data['device_data'] = $deviceData;
        }

        if ($this->isGatewayType(GATEWAY_TYPE_PAYPAL, $paymentMethod)) {
            $data['ButtonSource'] = 'InvoiceNinja_SP';
        }

        if ( ! $paymentMethod && ! empty($this->input['sourceToken'])) {
            $data['token'] = $this->input['sourceToken'];
        }

        return $data;
    }

    protected function creatingPaymentMethod($paymentMethod)
    {
        $response = $this->tokenResponse;

        $paymentMethod->source_reference = $response->token;

        if ($this->isGatewayType(GATEWAY_TYPE_CREDIT_CARD)) {
            $paymentMethod->payment_type_id = PaymentType::parseCardType($response->cardType);
            $paymentMethod->last4 = $response->last4;
            $paymentMethod->expiration = $response->expirationYear . '-' . $response->expirationMonth . '-01';
        } elseif ($this->isGatewayType(GATEWAY_TYPE_PAYPAL)) {
            $paymentMethod->email = $response->email;
            $paymentMethod->payment_type_id = PAYMENT_TYPE_PAYPAL;
        } else {
            return;
        }

        return $paymentMethod;
    }

    protected function attemptVoidPayment($response, $payment, $amount)
    {
        if ( ! parent::attemptVoidPayment($response, $payment, $amount)) {
            return false;
        }

        $data = $response->getData();

        if ($data instanceof \Braintree\Result\Error) {
            $error = $data->errors->deepAll()[0];
            if ($error && $error->code == 91506) {
                return true;
            }
        }

        return false;
    }

    private function customerData()
    {
        return [
            'firstName' => array_get($this->input, 'first_name') ?: $this->contact()->first_name,
            'lastName'  => array_get($this->input, 'last_name') ?: $this->contact()->last_name,
            'company'   => $this->client()->name,
            'email'     => $this->contact()->email,
            'phone'     => $this->contact()->phone,
            'website'   => $this->client()->website,
        ];
    }
}

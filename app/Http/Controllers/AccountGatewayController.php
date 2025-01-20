<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\AccountGateway;
use App\Models\AccountGatewaySettings;
use App\Models\Gateway;
use App\Services\AccountGatewayService;
use Auth;
use File;
use Illuminate\Http\RedirectResponse;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Session;
use stdClass;
use URL;
use App\Libraries\Utils;
use Validator;
use View;
use WePay;
use WePayException;

class AccountGatewayController extends BaseController
{
    protected $companyGatewayService;

    public function __construct(AccountGatewayService $companyGatewayService)
    {
        //parent::__construct();

        $this->accountGatewayService = $companyGatewayService;
    }

    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_PAYMENTS);
    }

    public function getDatatable()
    {
        return $this->accountGatewayService->getDatatable(Auth::user()->company_id);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("gateways/$publicId/edit");
    }

    public function edit($publicId)
    {
        $companyGateway = AccountGateway::scope($publicId)->firstOrFail();
        $config = $companyGateway->getConfig();

        if (!$companyGateway->isCustom()) {
            foreach ($config as $field => $value) {
                $config->$field = str_repeat('*', strlen($value));
            }
        }

        $data = self::getViewModel($companyGateway);
        $data['url'] = 'gateways/' . $publicId;
        $data['method'] = 'PUT';
        $data['title'] = trans('texts.edit_gateway') . ' - ' . $companyGateway->gateway->name;
        $data['config'] = $config;
        $data['hiddenFields'] = Gateway::$hiddenFields;
        $data['selectGateways'] = Gateway::where('id', '=', $companyGateway->gateway_id)->get();

        return View::make('companies.account_gateway', $data);
    }

    private function getViewModel($companyGateway = false)
    {
        $selectedCards = $companyGateway ? $companyGateway->accepted_credit_cards : 0;
        $user = Auth::user();
        $company = $user->company;

        $creditCardsArray = unserialize(CREDIT_CARDS);
        $creditCards = [];
        foreach ($creditCardsArray as $card => $name) {
            if ($selectedCards > 0 && ($selectedCards & $card) == $card) {
                $creditCards['<div>' . $name['text'] . '</div>'] = ['value' => $card, 'data-imageUrl' => asset($name['card']), 'checked' => 'checked'];
            } else {
                $creditCards['<div>' . $name['text'] . '</div>'] = ['value' => $card, 'data-imageUrl' => asset($name['card'])];
            }
        }

        $company->load('account_gateways');
        $currentGateways = $company->account_gateways;
        $gateways = Gateway::where('payment_library_id', '=', 1)->orderBy('name')->get();

        if ($companyGateway) {
            $companyGateway->fields = [];
        }

        foreach ($gateways as $gateway) {
            $fields = $gateway->getFields();
            if (!$gateway->isCustom()) {
                asort($fields);
            }
            $gateway->fields = $gateway->id == GATEWAY_WEPAY ? [] : $fields;
            if ($companyGateway && $companyGateway->gateway_id == $gateway->id) {
                $companyGateway->fields = $gateway->fields;
            }
        }

        return [
            'company' => $company,
            'user' => $user,
            'accountGateway' => $companyGateway,
            'config' => false,
            'gateways' => $gateways,
            'creditCardTypes' => $creditCards,
            'countGateways' => $currentGateways->count(),
        ];
    }

    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * Stores new company.
     *
     * @param mixed $companyGatewayPublicId
     */
    public function save($companyGatewayPublicId = false)
    {
        $gatewayId = $request->get('primary_gateway_id') ?: $request->get('secondary_gateway_id');
        $gateway = Gateway::findOrFail($gatewayId);

        $rules = [];
        $fields = $gateway->getFields();
        $optional = array_merge(Gateway::$hiddenFields, Gateway::$optionalFields);

        if ($gatewayId == GATEWAY_DWOLLA) {
            $optional = array_merge($optional, ['key', 'secret']);
        } elseif ($gatewayId == GATEWAY_PAYMILL) {
            $rules['publishable_key'] = 'required';
        } elseif ($gatewayId == GATEWAY_STRIPE) {
            if (Utils::isNinjaDev()) {
                // do nothing - we're unable to acceptance test with StripeJS
            } else {
                $rules['publishable_key'] = 'required';
                $rules['enable_ach'] = 'boolean';
            }
        }

        if ($gatewayId != GATEWAY_WEPAY) {
            foreach ($fields as $field => $details) {
                if (!in_array($field, $optional)) {
                    if (strtolower($gateway->name) == 'beanstream') {
                        if (in_array($field, ['merchant_id', 'passCode'])) {
                            $rules[$gateway->id . '_' . $field] = 'required';
                        }
                    } else {
                        $rules[$gateway->id . '_' . $field] = 'required';
                    }
                }
            }
        }

        $creditcards = $request->get('creditCardTypes');
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            $url = $companyGatewayPublicId ? "/gateways/{$companyGatewayPublicId}/edit" : 'gateways/create?other_providers=' . ($gatewayId == GATEWAY_WEPAY ? 'false' : 'true');

            return Redirect::to($url)
                ->withErrors($validator)
                ->withInput();
        }
        $company = company::with('account_gateways')->findOrFail(Auth::user()->company_id);
        $oldConfig = null;

        if ($companyGatewayPublicId) {
            $companyGateway = AccountGateway::scope($companyGatewayPublicId)->firstOrFail();
            $oldConfig = $companyGateway->getConfig();
        } else {
            // check they don't already have an active gateway for this provider
            // TODO complete this
            $companyGateway = AccountGateway::scope()
                ->whereGatewayId($gatewayId)
                ->first();
            if ($companyGateway) {
                Session::flash('error', trans('texts.gateway_exists'));

                return Redirect::to("gateways/{$companyGateway->public_id}/edit");
            }

            $companyGateway = AccountGateway::createNew();
            $companyGateway->gateway_id = $gatewayId;

            if ($gatewayId == GATEWAY_WEPAY) {
                if (!$this->setupWePay($companyGateway, $wepayResponse)) {
                    return $wepayResponse;
                }
                $oldConfig = $companyGateway->getConfig();
            }
        }

        $config = new stdClass();

        if ($gatewayId != GATEWAY_WEPAY) {
            foreach ($fields as $field => $details) {
                $value = trim($request->get($gateway->id . '_' . $field));
                // if the new value is masked use the original value
                if ($oldConfig && $value && $value === str_repeat('*', strlen($value))) {
                    $value = $oldConfig->$field;
                }
                if (!$value && in_array($field, ['testMode', 'developerMode', 'sandbox'])) {
                    // do nothing
                } else {
                    $config->$field = $value;
                }
            }
        } elseif ($oldConfig) {
            $config = clone $oldConfig;
        }

        $publishableKey = trim($request->get('publishable_key'));
        if ($publishableKey = str_replace('*', '', $publishableKey)) {
            $config->publishableKey = $publishableKey;
        } elseif ($oldConfig && property_exists($oldConfig, 'publishableKey')) {
            $config->publishableKey = $oldConfig->publishableKey;
        }

        $plaidClientId = trim($request->get('plaid_client_id'));
        if (!$plaidClientId || $plaidClientId = str_replace('*', '', $plaidClientId)) {
            $config->plaidClientId = $plaidClientId;
        } elseif ($oldConfig && property_exists($oldConfig, 'plaidClientId')) {
            $config->plaidClientId = $oldConfig->plaidClientId;
        }

        $plaidSecret = trim($request->get('plaid_secret'));
        if (!$plaidSecret || $plaidSecret = str_replace('*', '', $plaidSecret)) {
            $config->plaidSecret = $plaidSecret;
        } elseif ($oldConfig && property_exists($oldConfig, 'plaidSecret')) {
            $config->plaidSecret = $oldConfig->plaidSecret;
        }

        $plaidPublicKey = trim($request->get('plaid_public_key'));
        if (!$plaidPublicKey || $plaidPublicKey = str_replace('*', '', $plaidPublicKey)) {
            $config->plaidPublicKey = $plaidPublicKey;
        } elseif ($oldConfig && property_exists($oldConfig, 'plaidPublicKey')) {
            $config->plaidPublicKey = $oldConfig->plaidPublicKey;
        }

        if ($gatewayId == GATEWAY_STRIPE) {
            $config->enableAlipay = boolval($request->get('enable_alipay'));
            $config->enableSofort = boolval($request->get('enable_sofort'));
            $config->enableSepa = boolval($request->get('enable_sepa'));
            $config->enableBitcoin = boolval($request->get('enable_bitcoin'));
            $config->enableApplePay = boolval($request->get('enable_apple_pay'));

            if ($config->enableApplePay && $uploadedFile = request()->file('apple_merchant_id')) {
                $config->appleMerchantId = File::get($uploadedFile);
            } elseif ($oldConfig && !empty($oldConfig->appleMerchantId)) {
                $config->appleMerchantId = $oldConfig->appleMerchantId;
            }
        }

        if ($gatewayId == GATEWAY_STRIPE || $gatewayId == GATEWAY_WEPAY) {
            $config->enableAch = boolval($request->get('enable_ach'));
        }

        if ($gatewayId == GATEWAY_BRAINTREE) {
            $config->enablePayPal = boolval($request->get('enable_paypal'));
        }

        $cardCount = 0;
        if ($creditcards) {
            foreach ($creditcards as $card => $value) {
                $cardCount += intval($value);
            }
        }

        $companyGateway->accepted_credit_cards = $cardCount;
        $companyGateway->show_address = $request->get('show_address') ? true : false;
        $companyGateway->show_shipping_address = $request->get('show_shipping_address') ? true : false;
        $companyGateway->update_address = $request->get('update_address') ? true : false;
        $companyGateway->setConfig($config);

        if ($companyGatewayPublicId) {
            $companyGateway->save();
        } else {
            $company->account_gateways()->save($companyGateway);
        }

        if (isset($wepayResponse)) {
            return $wepayResponse;
        }
        $this->testGateway($companyGateway);

        if ($companyGatewayPublicId) {
            $message = trans('texts.updated_gateway');
            Session::flash('message', $message);

            return Redirect::to("gateways/{$companyGateway->public_id}/edit");
        }
        $message = trans('texts.created_gateway');
        Session::flash('message', $message);

        return Redirect::to('/settings/online_payments');
    }

    protected function setupWePay($companyGateway, &$response)
    {
        $user = Auth::user();
        $company = $user->company;

        $rules = [
            'company_name' => 'required',
            'tos_agree' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required|in:US,CA,GB',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('gateways/create')
                ->withErrors($validator)
                ->withInput();
        }

        if (!$user->email) {
            $user->email = trim($request->get('email'));
            $user->first_name = trim($request->get('first_name'));
            $user->last_name = trim($request->get('last_name'));
            $user->save();
        }

        try {
            $wepay = Utils::setupWePay();

            $userDetails = [
                'client_id' => WEPAY_CLIENT_ID,
                'client_secret' => WEPAY_CLIENT_SECRET,
                'email' => $request->get('email'),
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'original_ip' => request()->getClientIp(true),
                'original_device' => Request::server('HTTP_USER_AGENT'),
                'tos_acceptance_time' => time(),
                'redirect_uri' => URL::to('gateways'),
                'scope' => 'manage_accounts,collect_payments,view_user,preapprove_payments,send_money',
            ];

            $wepayUser = $wepay->request('user/register/', $userDetails);

            $accessToken = $wepayUser->access_token;
            $accessTokenExpires = $wepayUser->expires_in ? (time() + $wepayUser->expires_in) : null;

            $wepay = new WePay($accessToken);

            $companyDetails = [
                'name' => $request->get('company_name'),
                'description' => trans('texts.wepay_account_description'),
                'theme_object' => json_decode(WEPAY_THEME),
                'callback_uri' => $companyGateway->getWebhookUrl(),
                'rbits' => $company->present()->rBits,
                'country' => $request->get('country'),
            ];

            if ($request->get('country') == 'CA') {
                $companyDetails['currencies'] = ['CAD'];
                $companyDetails['country_options'] = ['debit_opt_in' => boolval($request->get('debit_cards'))];
            } elseif ($request->get('country') == 'GB') {
                $companyDetails['currencies'] = ['GBP'];
            }

            $wepayAccount = $wepay->request('company/create/', $companyDetails);

            try {
                $wepay->request('user/send_confirmation/', []);
                $confirmationRequired = true;
            } catch (WePayException $ex) {
                if ($ex->getMessage() == 'This access_token is already approved.') {
                    $confirmationRequired = false;
                } else {
                    throw $ex;
                }
            }

            $companyGateway->gateway_id = GATEWAY_WEPAY;
            $companyGateway->setConfig([
                'userId' => $wepayUser->user_id,
                'accessToken' => $accessToken,
                'tokenType' => $wepayUser->token_type,
                'tokenExpires' => $accessTokenExpires,
                'accountId' => $wepayAccount->company_id,
                'state' => $wepayAccount->state,
                'testMode' => WEPAY_ENVIRONMENT == WEPAY_STAGE,
                'country' => $request->get('country'),
            ]);

            if ($confirmationRequired) {
                Session::flash('message', trans('texts.created_wepay_confirmation_required'));
            } else {
                $updateUri = $wepay->request('/company/get_update_uri', [
                    'company_id' => $wepayAccount->company_id,
                    'redirect_uri' => URL::to('gateways'),
                ]);

                $response = Redirect::to($updateUri->uri);

                return true;
            }

            $response = Redirect::to("gateways/{$companyGateway->public_id}/edit");

            return true;
        } catch (WePayException $e) {
            Session::flash('error', $e->getMessage());
            $response = Redirect::to('gateways/create')
                ->withInput();

            return false;
        }
    }

    private function testGateway($companyGateway): void
    {
        $paymentDriver = $companyGateway->paymentDriver();
        $result = $paymentDriver->isValid();

        if ($result !== true) {
            Session::flash('error', $result . ' - ' . trans('texts.gateway_config_error'));
        }
    }

    public function store()
    {
        return $this->save();
    }

    /**
     * Displays the form for company creation.
     */
    public function create()
    {
        if (!Request::secure() && !Utils::isNinjaDev()) {
            Session::now('warning', trans('texts.enable_https'));
        }

        $company = Auth::user()->company;
        $companyGatewaysIds = $company->gatewayIds();
        $wepay = $request->get('wepay');

        $data = self::getViewModel();
        $data['url'] = 'gateways';
        $data['method'] = 'POST';
        $data['title'] = trans('texts.add_gateway');

        if ($wepay) {
            return View::make('companies.account_gateway_wepay', $data);
        }
        $availableGatewaysIds = $company->availableGatewaysIds();
        $data['primaryGateways'] = Gateway::primary($availableGatewaysIds)->orderBy('sort_order')->get();
        $data['secondaryGateways'] = Gateway::secondary($availableGatewaysIds)->orderBy('name')->get();
        $data['hiddenFields'] = Gateway::$hiddenFields;
        $data['accountGatewaysIds'] = $companyGatewaysIds;

        return View::make('companies.account_gateway', $data);
    }

    public function bulk()
    {
        $action = $request->get('bulk_action');
        $ids = $request->get('bulk_public_id');
        $count = $this->accountGatewayService->bulk($ids, $action);

        Session::flash('message', trans("texts.{$action}d_account_gateway"));

        return Redirect::to('settings/' . ACCOUNT_PAYMENTS);
    }

    public function resendConfirmation($publicId = false)
    {
        $companyGateway = AccountGateway::scope($publicId)->firstOrFail();

        if ($companyGateway->gateway_id == GATEWAY_WEPAY) {
            try {
                $wepay = Utils::setupWePay($companyGateway);
                $wepay->request('user/send_confirmation', []);

                Session::flash('message', trans('texts.resent_confirmation_email'));
            } catch (WePayException $e) {
                Session::flash('error', $e->getMessage());
            }
        }

        return Redirect::to("gateways/{$companyGateway->public_id}/edit");
    }

    /**
     * @return RedirectResponse
     */
    public function savePaymentGatewayLimits()
    {
        $gateway_type_id = intval($request->get('gateway_type_id'));
        $gateway_settings = AccountGatewaySettings::scope()->where('gateway_type_id', '=', $gateway_type_id)->first();

        if (!$gateway_settings) {
            $gateway_settings = AccountGatewaySettings::createNew();
            $gateway_settings->gateway_type_id = $gateway_type_id;
        }

        $gateway_settings->min_limit = $request->get('limit_min_enable') ? intval($request->get('limit_min')) : null;
        $gateway_settings->max_limit = $request->get('limit_max_enable') ? intval($request->get('limit_max')) : null;

        if ($gateway_settings->max_limit !== null && $gateway_settings->min_limit > $gateway_settings->max_limit) {
            $gateway_settings->max_limit = $gateway_settings->min_limit;
        }

        $gateway_settings->fill(Input::all());
        $gateway_settings->save();

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_PAYMENTS);
    }

    protected function getWePayUpdateUri($companyGateway)
    {
        if ($companyGateway->gateway_id != GATEWAY_WEPAY) {
            return;
        }

        $wepay = Utils::setupWePay($companyGateway);

        $update_uri_data = $wepay->request('company/get_update_uri', [
            'company_id' => $companyGateway->getConfig()->accountId,
            'mode' => 'iframe',
            'redirect_uri' => URL::to('/gateways'),
        ]);

        return $update_uri_data->uri;
    }
}

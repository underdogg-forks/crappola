<?php

namespace App\Http\Controllers;

use App\Libraries\CurlUtils;
use App\Libraries\Utils;
use App\Models\Affiliate;
use App\Models\Country;
use App\Models\License;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Repositories\AccountRepository;
use Cache;
use CreditCard;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Omnipay;
use URL;
use Illuminate\Support\Facades\Validator;

class NinjaController extends BaseController
{
    /**
     * @var AccountRepository
     */
    protected $companyRepo;

    protected ContactMailer $contactMailer;

    /**
     * NinjaController constructor.
     */
    public function __construct(AccountRepository $companyRepo, ContactMailer $contactMailer)
    {
        $this->accountRepo = $companyRepo;
        $this->contactMailer = $contactMailer;
    }

    /**
     * @return $this|\Illuminate\Contracts\View\View
     */
    public function show_license_payment()
    {
        if (request()->has('return_url')) {
            session(['return_url' => $request->get('return_url')]);
        }

        if (request()->has('affiliate_key')) {
            if ($affiliate = Affiliate::where('affiliate_key', '=', $request->get('affiliate_key'))->first()) {
                session(['affiliate_id' => $affiliate->id]);
            }
        }

        if (request()->has('product_id')) {
            session(['product_id' => $request->get('product_id')]);
        } elseif (! Session::has('product_id')) {
            session(['product_id' => PRODUCT_ONE_CLICK_INSTALL]);
        }

        if (! Session::get('affiliate_id')) {
            return Utils::fatalError();
        }

        if (Utils::isNinjaDev() && request()->has('test_mode')) {
            session(['test_mode' => $request->get('test_mode')]);
        }

        $company = $this->accountRepo->getNinjaAccount();
        $company->load('account_gateways.gateway');
        $companyGateway = $company->getGatewayByType(GATEWAY_TYPE_CREDIT_CARD);
        $gateway = $companyGateway->gateway;
        $acceptedCreditCardTypes = $companyGateway->getCreditcardTypes();

        $affiliate = Affiliate::find(Session::get('affiliate_id'));

        $data = [
            'showBreadcrumbs'         => false,
            'hideHeader'              => true,
            'url'                     => 'license',
            'amount'                  => $affiliate->price,
            'client'                  => false,
            'contact'                 => false,
            'gateway'                 => $gateway,
            'company'                 => $company,
            'accountGateway'          => $companyGateway,
            'acceptedCreditCardTypes' => $acceptedCreditCardTypes,
            'countries'               => Cache::get('countries'),
            'currencyId'              => 1,
            'currencyCode'            => 'USD',
            'paymentTitle'            => $affiliate->payment_title,
            'paymentSubtitle'         => $affiliate->payment_subtitle,
            'showAddress'             => true,
        ];

        return View::make('payments.stripe.credit_card', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function do_license_payment()
    {
        $testMode = Session::get('test_mode') === 'true';

        $rules = [
            'first_name'       => 'required',
            'last_name'        => 'required',
            'email'            => 'required',
            'card_number'      => 'required',
            'expiration_month' => 'required',
            'expiration_year'  => 'required',
            'cvv'              => 'required',
            'address1'         => 'required',
            'city'             => 'required',
            'state'            => 'required',
            'postal_code'      => 'required',
            'country_id'       => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return redirect()->to('license')
                ->withErrors($validator)
                ->withInput();
        }

        $company = $this->accountRepo->getNinjaAccount();
        $company->load('account_gateways.gateway');
        $companyGateway = $company->getGatewayByType(GATEWAY_TYPE_CREDIT_CARD);

        try {
            $affiliate = Affiliate::find(Session::get('affiliate_id'));

            if ($testMode) {
                $ref = 'TEST_MODE';
            } else {
                $details = self::getLicensePaymentDetails(Input::all(), $affiliate);

                $gateway = Omnipay::create($companyGateway->gateway->provider);
                $gateway->initialize((array) $companyGateway->getConfig());
                $response = $gateway->purchase($details)->send();

                $ref = $response->getTransactionReference();

                if (! $response->isSuccessful() || ! $ref) {
                    $this->error('License', $response->getMessage(), $companyGateway);

                    return redirect()->to('license')->withInput();
                }
            }

            $licenseKey = Utils::generateLicense();

            $license = new License();
            $license->first_name = $request->get('first_name');
            $license->last_name = $request->get('last_name');
            $license->email = $request->get('email');
            $license->transaction_reference = $ref;
            $license->license_key = $licenseKey;
            $license->affiliate_id = Session::get('affiliate_id');
            $license->product_id = Session::get('product_id');
            $license->save();

            $data = [
                'message'    => $affiliate->payment_subtitle,
                'license'    => $licenseKey,
                'hideHeader' => true,
                'productId'  => $license->product_id,
                'price'      => $affiliate->price,
            ];

            $name = "{$license->first_name} {$license->last_name}";
            $this->contactMailer->sendLicensePaymentConfirmation($name, $license->email, $affiliate->price, $license->license_key, $license->product_id);

            if (Session::has('return_url')) {
                $data['redirectTo'] = Session::get('return_url') . "?license_key={$license->license_key}&product_id=" . Session::get('product_id');
                $data['message'] = 'Redirecting to ' . Session::get('return_url');
            }

            return View::make('public.license', $data);
        } catch (Exception $e) {
            $this->error('License-Uncaught', false, $companyGateway, $e);

            return redirect()->to('license')->withInput();
        }
    }

    /**
     * @return array{amount: mixed, card: \CreditCard, currency: string, returnUrl: mixed, cancelUrl: mixed}
     */
    private function getLicensePaymentDetails(array $input, Affiliate $affiliate): array
    {
        $country = Country::find($input['country_id']);

        $data = [
            'firstName'        => $input['first_name'],
            'lastName'         => $input['last_name'],
            'email'            => $input['email'],
            'number'           => $input['card_number'],
            'expiryMonth'      => $input['expiration_month'],
            'expiryYear'       => $input['expiration_year'],
            'cvv'              => $input['cvv'],
            'billingAddress1'  => $input['address1'],
            'billingAddress2'  => $input['address2'],
            'billingCity'      => $input['city'],
            'billingState'     => $input['state'],
            'billingPostcode'  => $input['postal_code'],
            'billingCountry'   => $country->iso_3166_2,
            'shippingAddress1' => $input['address1'],
            'shippingAddress2' => $input['address2'],
            'shippingCity'     => $input['city'],
            'shippingState'    => $input['state'],
            'shippingPostcode' => $input['postal_code'],
            'shippingCountry'  => $country->iso_3166_2,
        ];

        $card = new CreditCard($data);

        return [
            'amount'    => $affiliate->price,
            'card'      => $card,
            'currency'  => 'USD',
            'returnUrl' => URL::to('license_complete'),
            'cancelUrl' => URL::to('/'),
        ];
    }

    private function error(string $type, $error, $companyGateway = false, $exception = false): void
    {
        $message = '';
        if ($companyGateway && $companyGateway->gateway) {
            $message = $companyGateway->gateway->name . ': ';
        }
        $message .= $error ?: trans('texts.payment_error');
        Session::flash('error', $message);
        Utils::logError("Payment Error [{$type}]: " . ($exception ? Utils::getErrorString($exception) : $message), 'PHP', true);
    }

    /**
     * @return string
     */
    public function claim_license()
    {
        $licenseKey = $request->get('license_key');
        $productId = $request->get('product_id', PRODUCT_ONE_CLICK_INSTALL);

        // add in dashes
        if (strlen($licenseKey) == 20) {
            $licenseKey = sprintf(
                '%s-%s-%s-%s-%s',
                substr($licenseKey, 0, 4),
                substr($licenseKey, 4, 4),
                substr($licenseKey, 8, 4),
                substr($licenseKey, 12, 4),
                substr($licenseKey, 16, 4)
            );
        }

        $license = License::where('license_key', '=', $licenseKey)
            ->where('is_claimed', '<', 10)
            ->where('product_id', '=', $productId)
            ->first();

        if ($license) {
            if ($license->transaction_reference != 'TEST_MODE') {
                $license->is_claimed = $license->is_claimed + 1;
                $license->save();
            }

            if ($productId == PRODUCT_INVOICE_DESIGNS) {
                return file_get_contents(storage_path() . '/invoice_designs.txt');
            }

            return $license->created_at->format('Y-m-d');
        }

        return RESULT_FAILURE;
    }

    public function hideWhiteLabelMessage()
    {
        $user = Auth::user();
        $companyPlan = $user->company->companyPlan;

        $companyPlan->plan = null;
        $companyPlan->save();

        return RESULT_SUCCESS;
    }

    public function purchaseWhiteLabel()
    {
        if (Utils::isNinja()) {
            return redirect('/');
        }

        $user = Auth::user();
        $company = $user->company;
        $url = NINJA_APP_URL . '/buy_now';
        $contactKey = $user->primaryAccount()->account_key;

        $data = [
            'account_key' => NINJA_LICENSE_ACCOUNT_KEY,
            'contact_key' => $contactKey,
            'product_id'  => PRODUCT_WHITE_LABEL,
            'first_name'  => $user->first_name,
            'last_name'   => $user->last_name,
            'email'       => $user->email,
            'name'        => $company->name,
            'address1'    => $company->address1,
            'address2'    => $company->address2,
            'city'        => $company->city,
            'state'       => $company->state,
            'postal_code' => $company->postal_code,
            'country_id'  => $company->country_id,
            'vat_number'  => $company->vat_number,
            'return_link' => true,
        ];

        if ($url = CurlUtils::post($url, $data)) {
            return redirect($url);
        }

        return redirect()->back()->withError(trans('texts.error_refresh_page'));
    }
}

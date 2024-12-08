<?php

namespace App\Http\Controllers;

use App\Libraries\CurlUtils;
use App\Models\Affiliate;
use App\Models\Country;
use App\Models\License;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Repositories\AccountRepository;
use CreditCard;
use Exception;
use Omnipay;
use Utils;

class NinjaController extends BaseController
{
    protected \App\Ninja\Repositories\AccountRepository $accountRepo;

    protected \App\Ninja\Mailers\ContactMailer $contactMailer;

    /**
     * NinjaController constructor.
     *
     * @param AccountRepository $accountRepo
     * @param ContactMailer     $contactMailer
     */
    public function __construct(AccountRepository $accountRepo, ContactMailer $contactMailer)
    {
        $this->accountRepo = $accountRepo;
        $this->contactMailer = $contactMailer;
    }

    /**
     * @return $this|\Illuminate\Contracts\View\View
     */
    public function show_license_payment()
    {
        if (\Illuminate\Support\Facades\Request::has('return_url')) {
            session(['return_url' => \Illuminate\Support\Facades\Request::input('return_url')]);
        }

        if (\Illuminate\Support\Facades\Request::has('affiliate_key') && ($affiliate = Affiliate::where('affiliate_key', '=', \Illuminate\Support\Facades\Request::input('affiliate_key'))->first())) {
            session(['affiliate_id' => $affiliate->id]);
        }

        if (\Illuminate\Support\Facades\Request::has('product_id')) {
            session(['product_id' => \Illuminate\Support\Facades\Request::input('product_id')]);
        } elseif ( ! \Illuminate\Support\Facades\Session::has('product_id')) {
            session(['product_id' => PRODUCT_ONE_CLICK_INSTALL]);
        }

        if ( ! \Illuminate\Support\Facades\Session::get('affiliate_id')) {
            return Utils::fatalError();
        }

        if (Utils::isNinjaDev() && \Illuminate\Support\Facades\Request::has('test_mode')) {
            session(['test_mode' => \Illuminate\Support\Facades\Request::input('test_mode')]);
        }

        $account = $this->accountRepo->getNinjaAccount();
        $account->load('account_gateways.gateway');

        $accountGateway = $account->getGatewayByType(GATEWAY_TYPE_CREDIT_CARD);
        $gateway = $accountGateway->gateway;
        $acceptedCreditCardTypes = $accountGateway->getCreditcardTypes();

        $affiliate = Affiliate::find(\Illuminate\Support\Facades\Session::get('affiliate_id'));

        $data = [
            'showBreadcrumbs'         => false,
            'hideHeader'              => true,
            'url'                     => 'license',
            'amount'                  => $affiliate->price,
            'client'                  => false,
            'contact'                 => false,
            'gateway'                 => $gateway,
            'account'                 => $account,
            'accountGateway'          => $accountGateway,
            'acceptedCreditCardTypes' => $acceptedCreditCardTypes,
            'countries'               => \Illuminate\Support\Facades\Cache::get('countries'),
            'currencyId'              => 1,
            'currencyCode'            => 'USD',
            'paymentTitle'            => $affiliate->payment_title,
            'paymentSubtitle'         => $affiliate->payment_subtitle,
            'showAddress'             => true,
        ];

        return \Illuminate\Support\Facades\View::make('payments.stripe.credit_card', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function do_license_payment()
    {
        $testMode = \Illuminate\Support\Facades\Session::get('test_mode') === 'true';

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

        $validator = \Illuminate\Support\Facades\Validator::make(\Illuminate\Support\Facades\Request::all(), $rules);

        if ($validator->fails()) {
            return redirect()->to('license')
                ->withErrors($validator)
                ->withInput();
        }

        $account = $this->accountRepo->getNinjaAccount();
        $account->load('account_gateways.gateway');

        $accountGateway = $account->getGatewayByType(GATEWAY_TYPE_CREDIT_CARD);

        try {
            $affiliate = Affiliate::find(\Illuminate\Support\Facades\Session::get('affiliate_id'));

            if ($testMode) {
                $ref = 'TEST_MODE';
            } else {
                $details = self::getLicensePaymentDetails(\Illuminate\Support\Facades\Request::all(), $affiliate);

                $gateway = Omnipay::create($accountGateway->gateway->provider);
                $gateway->initialize((array) $accountGateway->getConfig());
                $response = $gateway->purchase($details)->send();

                $ref = $response->getTransactionReference();

                if ( ! $response->isSuccessful() || ! $ref) {
                    $this->error('License', $response->getMessage(), $accountGateway);

                    return redirect()->to('license')->withInput();
                }
            }

            $licenseKey = Utils::generateLicense();

            $license = new License();
            $license->first_name = \Illuminate\Support\Facades\Request::input('first_name');
            $license->last_name = \Illuminate\Support\Facades\Request::input('last_name');
            $license->email = \Illuminate\Support\Facades\Request::input('email');
            $license->transaction_reference = $ref;
            $license->license_key = $licenseKey;
            $license->affiliate_id = \Illuminate\Support\Facades\Session::get('affiliate_id');
            $license->product_id = \Illuminate\Support\Facades\Session::get('product_id');
            $license->save();

            $data = [
                'message'    => $affiliate->payment_subtitle,
                'license'    => $licenseKey,
                'hideHeader' => true,
                'productId'  => $license->product_id,
                'price'      => $affiliate->price,
            ];

            $name = sprintf('%s %s', $license->first_name, $license->last_name);
            $this->contactMailer->sendLicensePaymentConfirmation($name, $license->email, $affiliate->price, $license->license_key, $license->product_id);

            if (\Illuminate\Support\Facades\Session::has('return_url')) {
                $data['redirectTo'] = \Illuminate\Support\Facades\Session::get('return_url') . sprintf('?license_key=%s&product_id=', $license->license_key) . \Illuminate\Support\Facades\Session::get('product_id');
                $data['message'] = 'Redirecting to ' . \Illuminate\Support\Facades\Session::get('return_url');
            }

            return \Illuminate\Support\Facades\View::make('public.license', $data);
        } catch (Exception $exception) {
            $this->error('License-Uncaught', false, $accountGateway, $exception);

            return redirect()->to('license')->withInput();
        }
    }

    public function claim_license()
    {
        $licenseKey = \Illuminate\Support\Facades\Request::input('license_key');
        $productId = \Illuminate\Support\Facades\Request::input('product_id', PRODUCT_ONE_CLICK_INSTALL);

        // add in dashes
        if (mb_strlen($licenseKey) == 20) {
            $licenseKey = sprintf(
                '%s-%s-%s-%s-%s',
                mb_substr($licenseKey, 0, 4),
                mb_substr($licenseKey, 4, 4),
                mb_substr($licenseKey, 8, 4),
                mb_substr($licenseKey, 12, 4),
                mb_substr($licenseKey, 16, 4)
            );
        }

        $license = License::where('license_key', '=', $licenseKey)
            ->where('is_claimed', '<', 10)
            ->where('product_id', '=', $productId)
            ->first();

        if ($license) {
            if ($license->transaction_reference != 'TEST_MODE') {
                $license->is_claimed += 1;
                $license->save();
            }

            if ($productId == PRODUCT_INVOICE_DESIGNS) {
                return file_get_contents(storage_path() . '/invoice_designs.txt');
            }

            return $license->created_at->format('Y-m-d');
        }

        return RESULT_FAILURE;
    }

    public function hideWhiteLabelMessage(): string
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $company = $user->account->company;

        $company->plan = null;
        $company->save();

        return RESULT_SUCCESS;
    }

    public function purchaseWhiteLabel()
    {
        if (Utils::isNinja()) {
            return redirect('/');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $account = $user->account;
        $url = NINJA_APP_URL . '/buy_now';
        $contactKey = $user->primaryAccount()->account_key;

        $data = [
            'account_key' => NINJA_LICENSE_ACCOUNT_KEY,
            'contact_key' => $contactKey,
            'product_id'  => PRODUCT_WHITE_LABEL,
            'first_name'  => $user->first_name,
            'last_name'   => $user->last_name,
            'email'       => $user->email,
            'name'        => $account->name,
            'address1'    => $account->address1,
            'address2'    => $account->address2,
            'city'        => $account->city,
            'state'       => $account->state,
            'postal_code' => $account->postal_code,
            'country_id'  => $account->country_id,
            'vat_number'  => $account->vat_number,
            'return_link' => true,
        ];

        if ($url = CurlUtils::post($url, $data)) {
            return redirect($url);
        }

        return redirect()->back()->withError(trans('texts.error_refresh_page'));
    }

    /**
     * @param array     $input
     * @param Affiliate $affiliate
     *
     * @return array
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
            'returnUrl' => \Illuminate\Support\Facades\URL::to('license_complete'),
            'cancelUrl' => \Illuminate\Support\Facades\URL::to('/'),
        ];
    }

    private function error(string $type, $error, $accountGateway = false, $exception = false): void
    {
        $message = '';
        if ($accountGateway && $accountGateway->gateway) {
            $message = $accountGateway->gateway->name . ': ';
        }

        $message .= $error ?: trans('texts.payment_error');
        \Illuminate\Support\Facades\Session::flash('error', $message);
        Utils::logError(sprintf('Payment Error [%s]: ', $type) . ($exception ? Utils::getErrorString($exception) : $message), 'PHP', true);
    }
}

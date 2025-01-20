<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOnlinePaymentRequest;
use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Company;
use App\Models\GatewayType;
use App\Models\Invitation;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\PaymentDrivers\PaymentActionRequiredException;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Carbon;
use Crawler;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use URL;
use Illuminate\Support\Facades\Validator;

/**
 * Class OnlinePaymentController.
 */
class OnlinePaymentController extends BaseController
{
    protected PaymentService $paymentService;

    protected UserMailer $userMailer;

    protected InvoiceRepository $invoiceRepo;

    /**
     * OnlinePaymentController constructor.
     */
    public function __construct(PaymentService $paymentService, UserMailer $userMailer, InvoiceRepository $invoiceRepo)
    {
        $this->paymentService = $paymentService;
        $this->userMailer = $userMailer;
        $this->invoiceRepo = $invoiceRepo;
    }

    /**
     * @param bool  $gatewayType
     * @param bool  $sourceId
     * @param mixed $gatewayTypeAlias
     *
     * @return RedirectResponse
     */
    public function showPayment($invitationKey, $gatewayTypeAlias = false, $sourceId = false)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return response()->view('error', [
                'error'      => trans('texts.invoice_not_found'),
                'hideHeader' => true,
            ]);
        }

        if (! request()->capture && ! $invitation->invoice->canBePaid()) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        $invitation = $invitation->load('invoice.client.company.account_gateways.gateway');
        $company = $invitation->company;

        if (! request()->capture && $company->requiresAuthorization($invitation->invoice) && ! session('authorized:' . $invitation->invitation_key)) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        $company->loadLocalizationSettings($invitation->invoice->client);

        if (! $gatewayTypeAlias) {
            $gatewayTypeId = Session::get($invitation->id . 'gateway_type');
        } elseif ($gatewayTypeAlias != GATEWAY_TYPE_TOKEN) {
            $gatewayTypeId = GatewayType::getIdFromAlias($gatewayTypeAlias);
        } else {
            $gatewayTypeId = $gatewayTypeAlias;
        }

        $paymentDriver = $company->paymentDriver($invitation, $gatewayTypeId);

        if (! $paymentDriver) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        // add a delay check for token links
        if ($gatewayTypeId == GATEWAY_TYPE_TOKEN) {
            $key = 'payment_token:' . $invitation->invitation_key;
            if (cache($key)) {
                return redirect()->to('view/' . $invitation->invitation_key);
            }
            cache([$key => true], Carbon::now()->addSeconds(10));
        }

        try {
            return $paymentDriver->startPurchase(Input::all(), $sourceId);
        } catch (Exception $exception) {
            return $this->error($paymentDriver, $exception);
        }
    }

    /**
     * @return RedirectResponse
     */
    private function error($paymentDriver, $exception, bool $showPayment = false)
    {
        if (is_string($exception)) {
            $displayError = $exception;
            $logError = $exception;
        } else {
            $displayError = $exception->getMessage();
            $logError = Utils::getErrorString($exception);
        }

        $message = sprintf('%s: %s', ucwords($paymentDriver->providerName()), $displayError);
        Session::flash('error', $message);

        $message = sprintf('Payment Error [%s]: %s', $paymentDriver->providerName(), $logError);
        Utils::logError($message, 'PHP', true);

        $route = $showPayment ? 'payment/' : 'view/';

        return redirect()->to($route . $paymentDriver->invitation->invitation_key);
    }

    /**
     * @return RedirectResponse
     */
    public function doPayment(
        CreateOnlinePaymentRequest $request,
        $invitationKey,
        $gatewayTypeAlias = false,
        $sourceId = false
    ) {
        $invitation = $request->invitation;

        if ($gatewayTypeAlias == GATEWAY_TYPE_TOKEN) {
            $gatewayTypeId = $gatewayTypeAlias;
        } elseif ($gatewayTypeAlias) {
            $gatewayTypeId = GatewayType::getIdFromAlias($gatewayTypeAlias);
        } else {
            $gatewayTypeId = Session::get($invitation->id . 'gateway_type');
        }

        $paymentDriver = $invitation->company->paymentDriver($invitation, $gatewayTypeId);

        if (! $invitation->invoice->canBePaid() && ! request()->capture) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        try {
            // Load the payment method to charge.
            // Currently only hit for saved cards that still require 3D secure verification.
            $paymentMethod = null;
            if ($sourceId) {
                $paymentMethod = PaymentMethod::clientId($invitation->invoice->client_id)
                    ->wherePublicId($sourceId)
                    ->firstOrFail();
            }

            $paymentDriver->completeOnsitePurchase($request->all(), $paymentMethod);
            if (request()->capture) {
                return redirect('/client/dashboard')->withMessage(trans('texts.updated_payment_details'));
            }

            if ($paymentDriver->isTwoStep()) {
                Session::flash('warning', trans('texts.bank_account_verification_next_steps'));
            } else {
                Session::flash('message', trans('texts.applied_payment'));
            }

            return $this->completePurchase($invitation);
        } catch (PaymentActionRequiredException $exception) {
            return $paymentDriver->startStepTwo($exception->getData());
        } catch (Exception $exception) {
            return $this->error($paymentDriver, $exception, true);
        }
    }

    private function completePurchase($invitation, bool $isOffsite = false)
    {
        if (request()->wantsJson()) {
            return response()->json(RESULT_SUCCESS);
        }
        if ($redirectUrl = session('redirect_url:' . $invitation->invitation_key)) {
            $separator = strpos($redirectUrl, '?') === false ? '?' : '&';

            return redirect()->to($redirectUrl . $separator . 'invoice_id=' . $invitation->invoice->public_id);
        }
        // Allow redirecting to iFrame for offsite payments
        if ($isOffsite) {
            return redirect()->to($invitation->getLink());
        }

        return redirect()->to('view/' . $invitation->invitation_key);
    }

    /**
     * @param bool  $invitationKey
     * @param mixed $gatewayTypeAlias
     *
     * @return RedirectResponse
     */
    public function offsitePayment($invitationKey = false, $gatewayTypeAlias = false)
    {
        if (Crawler::isCrawler()) {
            return redirect()->to(NINJA_WEB_URL, 301);
        }

        $invitationKey = $invitationKey ?: Session::get('invitation_key');
        $invitation = Invitation::with('invoice.invoice_items', 'invoice.client.currency', 'invoice.client.company.account_gateways.gateway')
            ->where('invitation_key', '=', $invitationKey)->firstOrFail();

        if (! $gatewayTypeAlias) {
            $gatewayTypeId = Session::get($invitation->id . 'gateway_type');
        } elseif ($gatewayTypeAlias != GATEWAY_TYPE_TOKEN) {
            $gatewayTypeId = GatewayType::getIdFromAlias($gatewayTypeAlias);
        } else {
            $gatewayTypeId = $gatewayTypeAlias;
        }

        $paymentDriver = $invitation->company->paymentDriver($invitation, $gatewayTypeId);

        if ($error = $request->get('error_description') ?: $request->get('error')) {
            return $this->error($paymentDriver, $error);
        }

        try {
            if ($paymentDriver->completeOffsitePurchase(Input::all())) {
                Session::flash('message', trans('texts.applied_payment'));
            }

            return $this->completePurchase($invitation, true);
        } catch (Exception $exception) {
            return $this->error($paymentDriver, $exception);
        }
    }

    public function completeSource($invitationKey, $gatewayType)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return response()->view('error', [
                'error'      => trans('texts.invoice_not_found'),
                'hideHeader' => true,
            ]);
        }

        return redirect()->to('view/' . $invitation->invitation_key);
    }

    public function getBankInfo($routingNumber)
    {
        if (strlen($routingNumber) != 9) {
            return response()->json([
                'message' => 'Invalid routing number',
            ], 400);
        }
        if (! preg_match('/\d{9}/', $routingNumber)) {
            return response()->json([
                'message' => 'Invalid routing number',
            ], 400);
        }
        $data = PaymentMethod::lookupBankData($routingNumber);
        if (is_string($data)) {
            return response()->json([
                'message' => $data,
            ], 500);
        }

        if (! empty($data)) {
            return response()->json($data);
        }

        return response()->json([
            'message' => 'Bank not found',
        ], 404);
    }

    public function handlePaymentWebhook($companyKey, $gatewayId)
    {
        $gatewayId = intval($gatewayId);

        $company = Company::where('companies.account_key', '=', $companyKey)->first();

        if (! $company) {
            return response()->json([
                'message' => 'Unknown company',
            ], 404);
        }

        $companyGateway = $company->getGatewayConfig(intval($gatewayId));

        if (! $companyGateway) {
            return response()->json([
                'message' => 'Unknown gateway',
            ], 404);
        }

        $paymentDriver = $companyGateway->paymentDriver();

        try {
            $result = $paymentDriver->handleWebHook(Input::all());

            return response()->json(['message' => $result]);
        } catch (Exception $exception) {
            if (! Utils::isNinjaProd()) {
                Utils::logError($exception->getMessage(), 'HOOK');
            }

            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function handleBuyNow(ClientRepository $clientRepo, InvoiceService $invoiceService, $gatewayTypeAlias = false)
    {
        if (Crawler::isCrawler()) {
            return redirect()->to(NINJA_WEB_URL, 301);
        }

        $company = Company::whereAccountKey($request->get('account_key'))->first();
        $redirectUrl = $request->get('redirect_url');
        $failureUrl = URL::previous();
        if (! $company) {
            return redirect()->to("{$failureUrl}/?error=invalid company");
        }
        if (! $company->enable_buy_now_buttons) {
            return redirect()->to("{$failureUrl}/?error=invalid company");
        }
        if (! $company->hasFeature(FEATURE_BUY_NOW_BUTTONS)) {
            return redirect()->to("{$failureUrl}/?error=invalid company");
        }

        Auth::onceUsingId($company->users[0]->id);
        $company->loadLocalizationSettings();
        $product = Product::scope($request->get('product_id'))->first();

        if (! $product) {
            return redirect()->to("{$failureUrl}/?error=invalid product");
        }

        // check for existing client using contact_key
        $client = false;
        if ($contactKey = $request->get('contact_key')) {
            $client = Client::scope()->whereHas('contacts', function ($query) use ($contactKey): void {
                $query->where('contact_key', $contactKey);
            })->first();
        }
        if (! $client) {
            $rules = [
                'first_name' => 'string|max:100',
                'last_name'  => 'string|max:100',
                'email'      => 'email|string|max:100',
            ];

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return redirect()->to("{$failureUrl}/?error=" . $validator->errors()->first());
            }

            $data = request()->all();
            $data['currency_id'] = $company->currency_id;
            $data['custom_value1'] = request()->custom_client1;
            $data['custom_value2'] = request()->custom_client2;
            $data['contact'] = request()->all();
            $data['contact']['custom_value1'] = request()->custom_contact1;
            $data['contact']['custom_value2'] = request()->custom_contact2;

            if (request()->currency_code) {
                $data['currency_code'] = request()->currency_code;
            }
            if (request()->country_code) {
                $data['country_code'] = request()->country_code;
            }
            $client = $clientRepo->save($data, $client);
        }

        $data = [
            'client_id'          => $client->id,
            'is_recurring'       => filter_var($request->get('is_recurring'), FILTER_VALIDATE_BOOLEAN),
            'is_public'          => filter_var($request->get('is_recurring'), FILTER_VALIDATE_BOOLEAN),
            'frequency_id'       => $request->get('frequency_id'),
            'auto_bill_id'       => $request->get('auto_bill_id'),
            'start_date'         => $request->get('start_date', date('Y-m-d')),
            'tax_rate1'          => $company->tax_rate1,
            'tax_name1'          => $company->tax_name1 ?: '',
            'tax_rate2'          => $company->tax_rate2,
            'tax_name2'          => $company->tax_name2 ?: '',
            'custom_text_value1' => $request->get('custom_invoice1'),
            'custom_text_value2' => $request->get('custom_invoice2'),
            'invoice_items'      => [[
                'product_key'   => $product->product_key,
                'notes'         => $product->notes,
                'cost'          => $product->cost,
                'qty'           => request()->quantity ?: (request()->qty ?: 1),
                'tax_rate1'     => $product->tax_rate1,
                'tax_name1'     => $product->tax_name1 ?: '',
                'tax_rate2'     => $product->tax_rate2,
                'tax_name2'     => $product->tax_name2 ?: '',
                'custom_value1' => $request->get('custom_product1') ?: $product->custom_value1,
                'custom_value2' => $request->get('custom_product2') ?: $product->custom_value2,
            ]],
        ];
        $invoice = $invoiceService->save($data);
        if ($invoice->is_recurring) {
            $invoice = $this->invoiceRepo->createRecurringInvoice($invoice->fresh());
        }
        $invitation = $invoice->invitations[0];
        $link = $invitation->getLink();

        if ($redirectUrl) {
            session(['redirect_url:' . $invitation->invitation_key => $redirectUrl]);
        }

        $link = $gatewayTypeAlias ? $invitation->getLink('payment') . "/{$gatewayTypeAlias}" : $invitation->getLink();

        if (filter_var($request->get('return_link'), FILTER_VALIDATE_BOOLEAN)) {
            return $link;
        }

        return redirect()->to($link);
    }

    public function showAppleMerchantId(): void
    {
        if (Utils::isNinja()) {
            $subdomain = Utils::getSubdomain(Request::server('HTTP_HOST'));
            if (! $subdomain || $subdomain == 'app') {
                exit('Invalid subdomain');
            }
            $company = Company::whereSubdomain($subdomain)->first();
        } else {
            $company = Company::first();
        }

        if (! $company) {
            exit('company not found');
        }

        $companyGateway = $company->account_gateways()
            ->whereGatewayId(GATEWAY_STRIPE)->first();

        if (! $company) {
            exit('Apple merchant id not set');
        }

        echo $companyGateway->getConfigField('appleMerchantId');
        exit;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Ninja\Datatables\PaymentDatatable;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Repositories\PaymentRepository;
use App\Services\PaymentService;
use DropdownButton;
use Utils;

class PaymentController extends BaseController
{
    /**
     * @var string
     */
    public $entityType = ENTITY_PAYMENT;

    protected \App\Ninja\Repositories\PaymentRepository $paymentRepo;

    protected \App\Ninja\Mailers\ContactMailer $contactMailer;

    protected \App\Services\PaymentService $paymentService;

    /**
     * PaymentController constructor.
     *
     * @param PaymentRepository $paymentRepo
     * @param ContactMailer     $contactMailer
     * @param PaymentService    $paymentService
     */
    public function __construct(
        PaymentRepository $paymentRepo,
        ContactMailer $contactMailer,
        PaymentService $paymentService
    ) {
        $this->paymentRepo = $paymentRepo;
        $this->contactMailer = $contactMailer;
        $this->paymentService = $paymentService;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return \Illuminate\Support\Facades\View::make('list_wrapper', [
            'entityType' => ENTITY_PAYMENT,
            'datatable'  => new PaymentDatatable(),
            'title'      => trans('texts.payments'),
        ]);
    }

    /**
     * @param null $clientPublicId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable($clientPublicId = null)
    {
        return $this->paymentService->getDatatable($clientPublicId, \Illuminate\Support\Facades\Request::input('sSearch'));
    }

    /**
     * @param PaymentRequest $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(PaymentRequest $request)
    {
        $user = auth()->user();
        $account = $user->account;

        $invoices = Invoice::scope()
            ->invoices()
            ->where('invoices.invoice_status_id', '!=', INVOICE_STATUS_PAID)
            ->with('client', 'invoice_status')
            ->orderBy('invoice_number')->get();

        $clientPublicId = \Illuminate\Support\Facades\Request::old('client') ?: ($request->client_id ?: 0);
        $invoicePublicId = \Illuminate\Support\Facades\Request::old('invoice') ?: ($request->invoice_id ?: 0);

        $totalCredit = false;
        if ($clientPublicId && $client = Client::scope($clientPublicId)->first()) {
            $totalCredit = $account->formatMoney($client->getTotalCredit(), $client);
        } elseif ($invoicePublicId && $invoice = Invoice::scope($invoicePublicId)->first()) {
            $totalCredit = $account->formatMoney($invoice->client->getTotalCredit(), $client);
        }

        $data = [
            'account'         => \Illuminate\Support\Facades\Auth::user()->account,
            'clientPublicId'  => $clientPublicId,
            'invoicePublicId' => $invoicePublicId,
            'invoice'         => null,
            'invoices'        => $invoices,
            'payment'         => null,
            'method'          => 'POST',
            'url'             => 'payments',
            'title'           => trans('texts.new_payment'),
            'paymentTypeId'   => \Illuminate\Support\Facades\Request::input('paymentTypeId'),
            'clients'         => Client::scope()->with('contacts')->orderBy('name')->get(),
            'totalCredit'     => $totalCredit,
        ];

        return \Illuminate\Support\Facades\View::make('payments.edit', $data);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($publicId)
    {
        \Illuminate\Support\Facades\Session::reflash();

        return redirect()->to(sprintf('payments/%s/edit', $publicId));
    }

    /**
     * @param PaymentRequest $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(PaymentRequest $request)
    {
        $payment = $request->entity();
        $payment->payment_date = Utils::fromSqlDate($payment->payment_date);

        $actions = [];
        if ($payment->invoiceJsonBackup()) {
            $actions[] = ['url' => url(sprintf('/invoices/invoice_history/%s?payment_id=%s', $payment->invoice->public_id, $payment->public_id)), 'label' => trans('texts.view_invoice')];
        }

        $actions[] = ['url' => url(sprintf('/invoices/%s/edit', $payment->invoice->public_id)), 'label' => trans('texts.edit_invoice')];
        $actions[] = DropdownButton::DIVIDER;
        $actions[] = ['url' => 'javascript:submitAction("email")', 'label' => trans('texts.email_payment')];

        if ($payment->canBeRefunded()) {
            $actions[] = ['url' => sprintf('javascript:showRefundModal(%s, "%s", "%s", "%s")', $payment->public_id, $payment->getCompletedAmount(), $payment->present()->completedAmount, $payment->present()->currencySymbol), 'label' => trans('texts.refund_payment')];
        }

        $actions[] = DropdownButton::DIVIDER;
        if ( ! $payment->trashed()) {
            $actions[] = ['url' => 'javascript:submitAction("archive")', 'label' => trans('texts.archive_payment')];
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans('texts.delete_payment')];
        } else {
            $actions[] = ['url' => 'javascript:submitAction("restore")', 'label' => trans('texts.restore_expense')];
        }

        $data = [
            'account'  => \Illuminate\Support\Facades\Auth::user()->account,
            'client'   => null,
            'invoice'  => null,
            'invoices' => Invoice::scope()
                ->invoices()
                ->whereIsPublic(true)
                ->with('client', 'invoice_status')
                ->orderBy('invoice_number')->get(),
            'payment'      => $payment,
            'entity'       => $payment,
            'method'       => 'PUT',
            'url'          => 'payments/' . $payment->public_id,
            'title'        => trans('texts.edit_payment'),
            'actions'      => $actions,
            'paymentTypes' => \Illuminate\Support\Facades\Cache::get('paymentTypes'),
            'clients'      => Client::scope()->with('contacts')->orderBy('name')->get(),
        ];

        return \Illuminate\Support\Facades\View::make('payments.edit', $data);
    }

    /**
     * @param CreatePaymentRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreatePaymentRequest $request)
    {
        // check payment has been marked sent
        $request->invoice->markSentIfUnsent();
        $input = $request->input();
        $amount = Utils::parseFloat($input['amount']);
        $credit = false;

        // if the payment amount is more than the balance create a credit
        if ($amount > $request->invoice->balance) {
            $credit = true;
        }

        $payment = $this->paymentService->save($input, null, $request->invoice);

        if (\Illuminate\Support\Facades\Request::input('email_receipt')) {
            $this->contactMailer->sendPaymentConfirmation($payment);
            \Illuminate\Support\Facades\Session::flash('message', trans($credit ? 'texts.created_payment_and_credit_emailed_client' : 'texts.created_payment_emailed_client'));
        } else {
            \Illuminate\Support\Facades\Session::flash('message', trans($credit ? 'texts.created_payment_and_credit' : 'texts.created_payment'));
        }

        return url($payment->client->getRoute());
    }

    /**
     * @param UpdatePaymentRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePaymentRequest $request)
    {
        if (in_array($request->action, ['archive', 'delete', 'restore', 'refund', 'email'])) {
            return self::bulk();
        }

        $payment = $this->paymentRepo->save($request->input(), $request->entity());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_payment'));

        return redirect()->to($payment->getRoute());
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('action');
        $ids = \Illuminate\Support\Facades\Request::input('public_id') ?: \Illuminate\Support\Facades\Request::input('ids');

        if ($action === 'email') {
            $payment = Payment::scope($ids)->withArchived()->first();
            $this->contactMailer->sendPaymentConfirmation($payment);
            \Illuminate\Support\Facades\Session::flash('message', trans('texts.emailed_payment'));
        } else {
            $count = $this->paymentService->bulk($ids, $action, [
                'refund_amount' => \Illuminate\Support\Facades\Request::input('refund_amount'),
                'refund_email'  => \Illuminate\Support\Facades\Request::input('refund_email'),
            ]);
            if ($count > 0) {
                $message = Utils::pluralize($action == 'refund' ? 'refunded_payment' : $action . 'd_payment', $count);
                \Illuminate\Support\Facades\Session::flash('message', $message);
            }
        }

        return $this->returnBulk(ENTITY_PAYMENT, $action, $ids);
    }
}

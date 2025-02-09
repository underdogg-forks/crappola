<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentTermRequest;
use App\Http\Requests\UpdatePaymentTermRequest;
use App\Libraries\Utils;
use App\Models\PaymentTerm;
use App\Services\PaymentTermService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class PaymentTermController extends BaseController
{
    protected PaymentTermService $paymentTermService;

    /**
     * PaymentTermController constructor.
     */
    public function __construct(PaymentTermService $paymentTermService)
    {
        //parent::__construct();

        $this->paymentTermService = $paymentTermService;
    }

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_PAYMENT_TERMS);
    }

    /**
     * @return JsonResponse
     */
    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;

        return $this->paymentTermService->getDatatable($accountId);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $publicId)
    {
        $data = [
            'paymentTerm' => PaymentTerm::scope($publicId)->firstOrFail(),
            'method'      => 'PUT',
            'url'         => 'payment_terms/' . $publicId,
            'title'       => trans('texts.edit_payment_term'),
        ];

        return View::make('accounts.payment_term', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $data = [
            'paymentTerm' => null,
            'method'      => 'POST',
            'url'         => 'payment_terms',
            'title'       => trans('texts.create_payment_term'),
        ];

        return View::make('accounts.payment_term', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function store(CreatePaymentTermRequest $request)
    {
        return $this->save();
    }

    /**
     * @param $publicId
     *
     * @return RedirectResponse
     */
    public function update(UpdatePaymentTermRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = Request::input('bulk_action');
        $ids = Request::input('bulk_public_id');
        $this->paymentTermService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_payment_term'));

        return Redirect::to('settings/' . ACCOUNT_PAYMENT_TERMS);
    }

    /**
     * @param bool $publicId
     *
     * @return RedirectResponse
     */
    private function save($publicId = false)
    {
        if ($publicId) {
            $paymentTerm = PaymentTerm::scope($publicId)->firstOrFail();
        } else {
            $paymentTerm = PaymentTerm::createNew();
        }

        $paymentTerm->num_days = Utils::parseInt(Request::input('num_days'));
        $paymentTerm->name = 'Net ' . $paymentTerm->num_days;
        $paymentTerm->save();

        $message = $publicId ? trans('texts.updated_payment_term') : trans('texts.created_payment_term');
        Session::flash('message', $message);

        return Redirect::to('settings/' . ACCOUNT_PAYMENT_TERMS);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxRateRequest;
use App\Http\Requests\UpdateTaxRateRequest;
use App\Models\TaxRate;
use App\Ninja\Repositories\TaxRateRepository;
use App\Services\TaxRateService;

class TaxRateController extends BaseController
{
    protected \App\Services\TaxRateService $taxRateService;

    protected \App\Ninja\Repositories\TaxRateRepository $taxRateRepo;

    public function __construct(TaxRateService $taxRateService, TaxRateRepository $taxRateRepo)
    {
        //parent::__construct();

        $this->taxRateService = $taxRateService;
        $this->taxRateRepo = $taxRateRepo;
    }

    public function index()
    {
        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_TAX_RATES);
    }

    public function getDatatable()
    {
        return $this->taxRateService->getDatatable(\Illuminate\Support\Facades\Auth::user()->account_id);
    }

    public function edit(string $publicId)
    {
        $data = [
            'taxRate' => TaxRate::scope($publicId)->firstOrFail(),
            'method'  => 'PUT',
            'url'     => 'tax_rates/' . $publicId,
            'title'   => trans('texts.edit_tax_rate'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.tax_rate', $data);
    }

    public function create()
    {
        $data = [
            'taxRate' => null,
            'method'  => 'POST',
            'url'     => 'tax_rates',
            'title'   => trans('texts.create_tax_rate'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.tax_rate', $data);
    }

    public function store(CreateTaxRateRequest $request)
    {
        $this->taxRateRepo->save($request->input());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.created_tax_rate'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_TAX_RATES);
    }

    public function update(UpdateTaxRateRequest $request, $publicId)
    {
        $this->taxRateRepo->save($request->input(), $request->entity());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_tax_rate'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_TAX_RATES);
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('bulk_action');
        $ids = \Illuminate\Support\Facades\Request::input('bulk_public_id');
        $count = $this->taxRateService->bulk($ids, $action);

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.archived_tax_rate'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_TAX_RATES);
    }
}

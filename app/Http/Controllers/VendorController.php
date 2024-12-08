<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Requests\VendorRequest;
use App\Models\Account;
use App\Models\Vendor;
use App\Ninja\Datatables\VendorDatatable;
use App\Ninja\Repositories\VendorRepository;
use App\Services\VendorService;
use Utils;

class VendorController extends BaseController
{
    public $entityType = ENTITY_VENDOR;

    protected \App\Services\VendorService $vendorService;

    protected \App\Ninja\Repositories\VendorRepository $vendorRepo;

    public function __construct(VendorRepository $vendorRepo, VendorService $vendorService)
    {
        //parent::__construct();

        $this->vendorRepo = $vendorRepo;
        $this->vendorService = $vendorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \Illuminate\Support\Facades\View::make('list_wrapper', [
            'entityType' => 'vendor',
            'datatable'  => new VendorDatatable(),
            'title'      => trans('texts.vendors'),
        ]);
    }

    public function getDatatable()
    {
        return $this->vendorService->getDatatable(\Illuminate\Support\Facades\Request::input('sSearch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CreateVendorRequest $request)
    {
        $vendor = $this->vendorService->save($request->input());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.created_vendor'));

        return redirect()->to($vendor->getRoute());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(VendorRequest $request)
    {
        $vendor = $request->entity();

        $actionLinks = [
            ['label' => trans('texts.new_vendor'), 'url' => \Illuminate\Support\Facades\URL::to('/vendors/create/' . $vendor->public_id)],
        ];

        $data = [
            'actionLinks'          => $actionLinks,
            'showBreadcrumbs'      => false,
            'vendor'               => $vendor,
            'title'                => trans('texts.view_vendor'),
            'hasRecurringInvoices' => false,
            'hasQuotes'            => false,
            'hasTasks'             => false,
        ];

        return \Illuminate\Support\Facades\View::make('vendors.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(VendorRequest $request)
    {
        if (Vendor::scope()->count() > \Illuminate\Support\Facades\Auth::user()->getMaxNumVendors()) {
            return \Illuminate\Support\Facades\View::make('error', ['hideHeader' => true, 'error' => "Sorry, you've exceeded the limit of " . \Illuminate\Support\Facades\Auth::user()->getMaxNumVendors() . ' vendors']);
        }

        $data = [
            'vendor' => null,
            'method' => 'POST',
            'url'    => 'vendors',
            'title'  => trans('texts.new_vendor'),
        ];

        $data = array_merge($data, $this->getViewModel());

        return \Illuminate\Support\Facades\View::make('vendors.edit', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit(VendorRequest $request)
    {
        $vendor = $request->entity();

        $data = [
            'vendor' => $vendor,
            'method' => 'PUT',
            'url'    => 'vendors/' . $vendor->public_id,
            'title'  => trans('texts.edit_vendor'),
        ];

        $data = array_merge($data, $this->getViewModel());

        if (\Illuminate\Support\Facades\Auth::user()->account->isNinjaAccount() && ($account = Account::whereId($client->public_id)->first())) {
            $data['planDetails'] = $account->getPlanDetails(false, false);
        }

        return \Illuminate\Support\Facades\View::make('vendors.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update(UpdateVendorRequest $request)
    {
        $vendor = $this->vendorService->save($request->input(), $request->entity());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_vendor'));

        return redirect()->to($vendor->getRoute());
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('action');
        $ids = \Illuminate\Support\Facades\Request::input('public_id') ?: \Illuminate\Support\Facades\Request::input('ids');
        $count = $this->vendorService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_vendor', $count);
        \Illuminate\Support\Facades\Session::flash('message', $message);

        return $this->returnBulk($this->entityType, $action, $ids);
    }

    private function getViewModel(): array
    {
        return [
            'data'    => \Illuminate\Support\Facades\Request::old('data'),
            'account' => \Illuminate\Support\Facades\Auth::user()->account,
        ];
    }
}

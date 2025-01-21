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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Utils;

class VendorController extends BaseController
{
    public $entityType = ENTITY_VENDOR;

    protected VendorService $vendorService;

    protected VendorRepository $vendorRepo;

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
        return View::make('list_wrapper', [
            'entityType' => 'vendor',
            'datatable'  => new VendorDatatable(),
            'title'      => trans('texts.vendors'),
        ]);
    }

    public function getDatatable()
    {
        return $this->vendorService->getDatatable(Request::input('sSearch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CreateVendorRequest $request)
    {
        $vendor = $this->vendorService->save($request->input());

        Session::flash('message', trans('texts.created_vendor'));

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
            ['label' => trans('texts.new_vendor'), 'url' => URL::to('/vendors/create/' . $vendor->public_id)],
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

        return View::make('vendors.show', $data);
    }

    public function create(VendorRequest $request)
    {
        if (Vendor::scope()->count() > Auth::user()->getMaxNumVendors()) {
            return View::make('error', ['hideHeader' => true, 'error' => "Sorry, you've exceeded the limit of " . Auth::user()->getMaxNumVendors() . ' vendors']);
        }

        $data = [
            'vendor' => null,
            'method' => 'POST',
            'url'    => 'vendors',
            'title'  => trans('texts.new_vendor'),
        ];

        $data = array_merge($data, $this->getViewModel());

        return View::make('vendors.edit', $data);
    }

    public function edit(VendorRequest $request): \Illuminate\Contracts\View\View
    {
        $vendor = $request->entity();

        $data = [
            'vendor' => $vendor,
            'method' => 'PUT',
            'url'    => 'vendors/' . $vendor->public_id,
            'title'  => trans('texts.edit_vendor'),
        ];

        $data = array_merge($data, $this->getViewModel());

        $client = null;
        if (Auth::user()->account->isNinjaAccount() && ($account = Account::whereId($client?->public_id)->first())) {
            $data['planDetails'] = $account->getPlanDetails(false, false);
        }

        return View::make('vendors.edit', $data);
    }

    public function update(UpdateVendorRequest $request)
    {
        $vendor = $this->vendorService->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_vendor'));

        return redirect()->to($vendor->getRoute());
    }

    public function bulk()
    {
        $action = Request::input('action');
        $ids = Request::input('public_id') ?: Request::input('ids');
        $count = $this->vendorService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_vendor', $count);
        Session::flash('message', $message);

        return $this->returnBulk($this->entityType, $action, $ids);
    }

    private function getViewModel(): array
    {
        return [
            'data'    => Request::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}

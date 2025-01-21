<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProposalRequest;
use App\Http\Requests\ProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Jobs\ConvertProposalToPdf;
use App\Jobs\SendInvoiceEmail;
use App\Models\Invoice;
use App\Models\ProposalTemplate;
use App\Ninja\Datatables\ProposalDatatable;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Repositories\ProposalRepository;
use App\Services\ProposalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ProposalController extends BaseController
{
    protected ProposalRepository $proposalRepo;

    protected ProposalService $proposalService;

    protected ContactMailer $contactMailer;

    protected $entityType = ENTITY_PROPOSAL;

    public function __construct(ProposalRepository $proposalRepo, ProposalService $proposalService, ContactMailer $contactMailer)
    {
        $this->proposalRepo = $proposalRepo;
        $this->proposalService = $proposalService;
        $this->contactMailer = $contactMailer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PROPOSAL,
            'datatable'  => new ProposalDatatable(),
            'title'      => trans('texts.proposals'),
        ]);
    }

    public function getDatatable($expensePublicId = null)
    {
        $search = $request->get('sSearch');
        //$userId = Auth::user()->filterId();
        $userId = Auth::user()->filterIdByEntity(ENTITY_PROPOSAL);

        return $this->proposalService->getDatatable($search, $userId);
    }

    public function create(ProposalRequest $request)
    {
        $data = array_merge($this->getViewmodel(), [
            'proposal'         => null,
            'method'           => 'POST',
            'url'              => 'proposals',
            'title'            => trans('texts.new_proposal'),
            'invoices'         => Invoice::scope()->with('client.contacts', 'client.country')->unapprovedQuotes()->orderBy('id')->get(),
            'invoicePublicId'  => $request->invoice_id,
            'templatePublicId' => $request->proposal_template_id,
        ]);

        return View::make('proposals.edit', $data);
    }

    /**
     * @return array{templates: mixed, company: mixed}
     */
    private function getViewmodel($proposal = false): array
    {
        $company = auth()->user()->company;
        $templates = ProposalTemplate::whereCompanyPlanId($company->id)->withActiveOrSelected($proposal ? $proposal->proposal_template_id : false)->orderBy('name')->get();

        if (! $templates->count()) {
            $templates = ProposalTemplate::whereNull('company_id')->orderBy('name')->get();
        }

        return [
            'templates' => $templates,
            'company'   => $company,
        ];
    }

    public function show($publicId)
    {
        Session::reflash();

        return redirect("proposals/$publicId/edit");
    }

    public function edit(ProposalRequest $request)
    {
        $proposal = $request->entity();

        $data = array_merge($this->getViewmodel($proposal), [
            'proposal'         => $proposal,
            'entity'           => $proposal,
            'method'           => 'PUT',
            'url'              => 'proposals/' . $proposal->public_id,
            'title'            => trans('texts.edit_proposal'),
            'invoices'         => Invoice::scope()->with('client.contacts', 'client.country')->withActiveOrSelected($proposal->invoice_id)->unapprovedQuotes($proposal->invoice_id)->orderBy('id')->get(),
            'invoicePublicId'  => $proposal->invoice ? $proposal->invoice->public_id : null,
            'templatePublicId' => $proposal->proposal_template ? $proposal->proposal_template->public_id : null,
        ]);

        return View::make('proposals.edit', $data);
    }

    public function store(CreateProposalRequest $request)
    {
        $proposal = $this->proposalService->save($request->input());
        $action = $request->get('action');

        if ($action == 'email') {
            $this->dispatch(new SendInvoiceEmail($proposal->invoice, auth()->user()->id, false, false, $proposal));
            Session::flash('message', trans('texts.emailed_proposal'));
        } else {
            Session::flash('message', trans('texts.created_proposal'));
        }

        return redirect()->to($proposal->getRoute());
    }

    public function update(UpdateProposalRequest $request)
    {
        $proposal = $this->proposalService->save($request->input(), $request->entity());
        $action = $request->get('action');

        if (in_array($action, ['archive', 'delete', 'restore'])) {
            return self::bulk();
        }

        if ($action == 'email') {
            $this->dispatch(new SendInvoiceEmail($proposal->invoice, auth()->user()->id, false, false, $proposal));
            Session::flash('message', trans('texts.emailed_proposal'));
        } else {
            Session::flash('message', trans('texts.updated_proposal'));
        }

        return redirect()->to($proposal->getRoute());
    }

    public function bulk()
    {
        $action = $request->get('bulk_action') ?: $request->get('action');
        $ids = $request->get('bulk_public_id') ?: ($request->get('public_id') ?: $request->get('ids'));

        $count = $this->proposalService->bulk($ids, $action);

        if ($count > 0) {
            $field = $count == 1 ? "{$action}d_proposal" : "{$action}d_proposals";
            $message = trans("texts.$field", ['count' => $count]);
            Session::flash('message', $message);
        }

        return redirect()->to('/proposals');
    }

    public function download(ProposalRequest $request): void
    {
        $proposal = $request->entity();

        $pdf = dispatch_now(new ConvertProposalToPdf($proposal));

        $this->downloadResponse($proposal->getFilename(), $pdf);
    }
}

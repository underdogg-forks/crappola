<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertProposalToPdf;
use App\Models\Company;
use App\Models\Document;
use App\Models\Invitation;
use App\Ninja\Repositories\ProposalRepository;

class ClientPortalProposalController extends BaseController
{
    private $invoiceRepo;

    private $paymentRepo;

    private $documentRepo;

    private ProposalRepository $propoosalRepo;

    public function __construct(ProposalRepository $propoosalRepo)
    {
        $this->propoosalRepo = $propoosalRepo;
    }

    public function viewProposal($invitationKey)
    {
        if (! $invitation = $this->propoosalRepo->findInvitationByKey($invitationKey)) {
            return $this->returnError(trans('texts.proposal_not_found'));
        }

        $company = $invitation->company;
        $proposal = $invitation->proposal;
        $invoiceInvitation = Invitation::whereContactId($invitation->contact_id)
            ->whereInvoiceId($proposal->invoice_id)
            ->firstOrFail();

        $data = [
            'proposal'           => $proposal,
            'company'            => $company,
            'invoiceInvitation'  => $invoiceInvitation,
            'proposalInvitation' => $invitation,
        ];

        if (request()->phantomjs) {
            return $proposal->present()->htmlDocument;
        }

        return view('invited.proposal', $data);
    }

    public function downloadProposal($invitationKey)
    {
        if (! $invitation = $this->propoosalRepo->findInvitationByKey($invitationKey)) {
            return $this->returnError(trans('texts.proposal_not_found'));
        }

        $proposal = $invitation->proposal;

        $pdf = dispatch_now(new ConvertProposalToPdf($proposal));

        $this->downloadResponse($proposal->getFilename(), $pdf);
    }

    public function getProposalImage($companyKey, $documentKey)
    {
        $company = Company::whereAccountKey($companyKey)
            ->firstOrFail();

        $document = Document::whereCompanyPlanId($company->id)
            ->whereDocumentKey($documentKey)
            ->whereIsProposal(true)
            ->firstOrFail();

        return DocumentController::getDownloadResponse($document);
    }
}

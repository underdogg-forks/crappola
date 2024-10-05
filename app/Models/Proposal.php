<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class Proposal extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ProposalPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'private_notes',
        'html',
        'css',
    ];

    /**
     * @var string
     */
    //protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return "/proposals/{$this->public_id}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function invitations()
    {
        return $this->hasMany(\App\Models\ProposalInvitation::class)->orderBy('proposal_invitations.contact_id');
    }

    /**
     * @return mixed
     */
    public function proposal_invitations()
    {
        return $this->hasMany(\App\Models\ProposalInvitation::class)->orderBy('proposal_invitations.contact_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal_template()
    {
        return $this->belongsTo(\App\Models\ProposalTemplate::class)->withTrashed();
    }

    public function getDisplayName()
    {
        return $this->invoice->invoice_number;
    }

    public function getLink($forceOnsite = false, $forcePlain = false)
    {
        $invitation = $this->invitations->first();

        return $invitation->getLink('proposal', $forceOnsite, $forcePlain);
    }

    public function getHeadlessLink(): string
    {
        return sprintf('%s?phantomjs=true&phantomjs_secret=%s', $this->getLink(true, true), env('PHANTOMJS_SECRET'));
    }

    public function getFilename(string $extension = 'pdf'): string
    {
        $entityType = $this->getEntityType();

        return trans('texts.proposal') . '_' . $this->invoice->invoice_number . '.' . $extension;
    }

    /**
     * @return string
     */
    public function getCustomMessageType(): string
    {
        if ($this->invoice->quote_invoice_id) {
            return CUSTOM_MESSAGE_APPROVED_PROPOSAL;
        }

        return CUSTOM_MESSAGE_UNAPPROVED_PROPOSAL;
    }
}

Proposal::creating(function ($project): void {
    $project->setNullValues();
});

Proposal::updating(function ($project): void {
    $project->setNullValues();
});

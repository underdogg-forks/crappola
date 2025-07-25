<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class Proposal extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ProposalPresenter';

    protected $fillable = [
        'private_notes',
        'html',
        'css',
    ];

    /**
     * @var string
     */
    //protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

    public function getEntityType()
    {
        return ENTITY_PROPOSAL;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/proposals/{$this->public_id}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function invitations()
    {
        return $this->hasMany('App\Models\ProposalInvitation')->orderBy('proposal_invitations.contact_id');
    }

    public function proposal_invitations()
    {
        return $this->hasMany('App\Models\ProposalInvitation')->orderBy('proposal_invitations.contact_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal_template()
    {
        return $this->belongsTo('App\Models\ProposalTemplate')->withTrashed();
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

    public function getHeadlessLink()
    {
        return sprintf('%s?phantomjs=true&phantomjs_secret=%s', $this->getLink(true, true), env('PHANTOMJS_SECRET'));
    }

    public function getFilename($extension = 'pdf')
    {
        $entityType = $this->getEntityType();

        return trans('texts.proposal') . '_' . $this->invoice->invoice_number . '.' . $extension;
    }

    /**
     * @return string
     */
    public function getCustomMessageType()
    {
        if ($this->invoice->quote_invoice_id) {
            return CUSTOM_MESSAGE_APPROVED_PROPOSAL;
        }

        return CUSTOM_MESSAGE_UNAPPROVED_PROPOSAL;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Proposal::creating(function ($project) {
    $project->setNullValues();
});

Proposal::updating(function ($project) {
    $project->setNullValues();
});

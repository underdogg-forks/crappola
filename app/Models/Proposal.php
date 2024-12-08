<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                                                                           $id
 * @property int                                                                           $account_id
 * @property int                                                                           $user_id
 * @property \Illuminate\Support\Carbon|null                                               $created_at
 * @property \Illuminate\Support\Carbon|null                                               $updated_at
 * @property \Illuminate\Support\Carbon|null                                               $deleted_at
 * @property int                                                                           $is_deleted
 * @property int                                                                           $invoice_id
 * @property int|null                                                                      $proposal_template_id
 * @property string                                                                        $private_notes
 * @property string                                                                        $html
 * @property string                                                                        $css
 * @property int                                                                           $public_id
 * @property \App\Models\Account                                                           $account
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProposalInvitation> $invitations
 * @property int|null                                                                      $invitations_count
 * @property \App\Models\Invoice                                                           $invoice
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProposalInvitation> $proposal_invitations
 * @property int|null                                                                      $proposal_invitations_count
 * @property \App\Models\ProposalTemplate|null                                             $proposal_template
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereProposalTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Proposal withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Proposal extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

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

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @var string
     */
    //protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL;
    }

    public function getRoute(): string
    {
        return '/proposals/' . $this->public_id;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    public function invitations()
    {
        return $this->hasMany(\App\Models\ProposalInvitation::class)->orderBy('proposal_invitations.contact_id');
    }

    public function proposal_invitations()
    {
        return $this->hasMany(\App\Models\ProposalInvitation::class)->orderBy('proposal_invitations.contact_id');
    }

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

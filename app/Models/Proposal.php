<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                                 $id
 * @property int                                 $account_id
 * @property int                                 $user_id
 * @property Carbon|null                         $created_at
 * @property Carbon|null                         $updated_at
 * @property Carbon|null                         $deleted_at
 * @property int                                 $is_deleted
 * @property int                                 $invoice_id
 * @property int|null                            $proposal_template_id
 * @property string                              $private_notes
 * @property string                              $html
 * @property string                              $css
 * @property int                                 $public_id
 * @property Account                             $account
 * @property Collection<int, ProposalInvitation> $invitations
 * @property int|null                            $invitations_count
 * @property Invoice                             $invoice
 * @property Collection<int, ProposalInvitation> $proposal_invitations
 * @property int|null                            $proposal_invitations_count
 * @property ProposalTemplate|null               $proposal_template
 *
 * @method static Builder|Proposal newModelQuery()
 * @method static Builder|Proposal newQuery()
 * @method static Builder|Proposal onlyTrashed()
 * @method static Builder|Proposal query()
 * @method static Builder|Proposal scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Proposal whereAccountId($value)
 * @method static Builder|Proposal whereCreatedAt($value)
 * @method static Builder|Proposal whereCss($value)
 * @method static Builder|Proposal whereDeletedAt($value)
 * @method static Builder|Proposal whereHtml($value)
 * @method static Builder|Proposal whereId($value)
 * @method static Builder|Proposal whereInvoiceId($value)
 * @method static Builder|Proposal whereIsDeleted($value)
 * @method static Builder|Proposal wherePrivateNotes($value)
 * @method static Builder|Proposal whereProposalTemplateId($value)
 * @method static Builder|Proposal wherePublicId($value)
 * @method static Builder|Proposal whereUpdatedAt($value)
 * @method static Builder|Proposal whereUserId($value)
 * @method static Builder|Proposal withActiveOrSelected($id = false)
 * @method static Builder|Proposal withArchived()
 * @method static Builder|Proposal withTrashed()
 * @method static Builder|Proposal withoutTrashed()
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
    protected $presenter = ProposalPresenter::class;

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
        return $this->belongsTo(Account::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function invitations()
    {
        return $this->hasMany(ProposalInvitation::class)->orderBy('proposal_invitations.contact_id');
    }

    public function proposal_invitations()
    {
        return $this->hasMany(ProposalInvitation::class)->orderBy('proposal_invitations.contact_id');
    }

    public function proposal_template()
    {
        return $this->belongsTo(ProposalTemplate::class)->withTrashed();
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Proposal::creating(function ($project): void {
    $project->setNullValues();
});

Proposal::updating(function ($project): void {
    $project->setNullValues();
});

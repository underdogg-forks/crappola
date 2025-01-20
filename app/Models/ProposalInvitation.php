<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Invitation.
 *
 * @property int          $id
 * @property int          $account_id
 * @property int          $user_id
 * @property int          $contact_id
 * @property int          $proposal_id
 * @property string       $invitation_key
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property string|null  $sent_date
 * @property string|null  $viewed_date
 * @property string|null  $opened_date
 * @property string|null  $message_id
 * @property string|null  $email_error
 * @property int          $public_id
 * @property Account|null $account
 * @property Contact      $contact
 * @property Proposal     $proposal
 * @property User         $user
 *
 * @method static Builder|ProposalInvitation newModelQuery()
 * @method static Builder|ProposalInvitation newQuery()
 * @method static Builder|ProposalInvitation onlyTrashed()
 * @method static Builder|ProposalInvitation query()
 * @method static Builder|ProposalInvitation scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|ProposalInvitation whereAccountId($value)
 * @method static Builder|ProposalInvitation whereContactId($value)
 * @method static Builder|ProposalInvitation whereCreatedAt($value)
 * @method static Builder|ProposalInvitation whereDeletedAt($value)
 * @method static Builder|ProposalInvitation whereEmailError($value)
 * @method static Builder|ProposalInvitation whereId($value)
 * @method static Builder|ProposalInvitation whereInvitationKey($value)
 * @method static Builder|ProposalInvitation whereMessageId($value)
 * @method static Builder|ProposalInvitation whereOpenedDate($value)
 * @method static Builder|ProposalInvitation whereProposalId($value)
 * @method static Builder|ProposalInvitation wherePublicId($value)
 * @method static Builder|ProposalInvitation whereSentDate($value)
 * @method static Builder|ProposalInvitation whereUpdatedAt($value)
 * @method static Builder|ProposalInvitation whereUserId($value)
 * @method static Builder|ProposalInvitation whereViewedDate($value)
 * @method static Builder|ProposalInvitation withActiveOrSelected($id = false)
 * @method static Builder|ProposalInvitation withArchived()
 * @method static Builder|ProposalInvitation withTrashed()
 * @method static Builder|ProposalInvitation withoutTrashed()
 *
 * @mixin \Eloquent
 */
class ProposalInvitation extends EntityModel
{
    use Inviteable;
    use SoftDeletes;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_PROPOSAL_INVITATION;
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class)->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

ProposalInvitation::creating(function ($invitation): void {
    LookupProposalInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

ProposalInvitation::updating(function ($invitation): void {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupProposalInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

ProposalInvitation::deleted(function ($invitation): void {
    if ($invitation->forceDeleting) {
        LookupProposalInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});

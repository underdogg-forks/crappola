<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Invitation.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int                             $contact_id
 * @property int                             $proposal_id
 * @property string                          $invitation_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null                     $sent_date
 * @property string|null                     $viewed_date
 * @property string|null                     $opened_date
 * @property string|null                     $message_id
 * @property string|null                     $email_error
 * @property int                             $public_id
 * @property \App\Models\Account|null        $account
 * @property \App\Models\Contact             $contact
 * @property \App\Models\Proposal            $proposal
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereEmailError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereInvitationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereOpenedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation whereViewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProposalInvitation withoutTrashed()
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
        return $this->belongsTo(\App\Models\Proposal::class)->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
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

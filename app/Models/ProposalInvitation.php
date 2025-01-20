<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Invitation.
 */
class ProposalInvitation extends EntityModel
{
    use Inviteable;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PROPOSAL_INVITATION;
    }

    /**
     * @return mixed
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

ProposalInvitation::creating(function ($invitation): void {
    LookupProposalInvitation::createNew($invitation->company->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

ProposalInvitation::updating(function ($invitation): void {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupProposalInvitation::updateInvitation($invitation->company->account_key, $invitation);
    }
});

ProposalInvitation::deleted(function ($invitation): void {
    if ($invitation->forceDeleting) {
        LookupProposalInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});

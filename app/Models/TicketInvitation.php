<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Invitation.
 */
class TicketInvitation extends EntityModel
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
        return ENTITY_TICKET_INVITATION;
    }

    /**
     * @return mixed
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class)->withTrashed();
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

TicketInvitation::creating(function ($invitation): void {
    LookupTicketInvitation::createNew($invitation->company->account_key, [
        'invitation_key' => $invitation->invitation_key,
        'ticket_hash'    => $invitation->ticket_hash,
    ]);
});

TicketInvitation::updating(function ($invitation): void {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupTicketInvitation::updateInvitation($invitation->company->account_key, $invitation);
    }
});

TicketInvitation::deleted(function ($invitation): void {
    if ($invitation->forceDeleting) {
        LookupTicketInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});

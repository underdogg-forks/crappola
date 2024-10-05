<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Utils;

/**
 * Class Invitation.
 */
class Invitation extends EntityModel
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
    public function getEntityType(): string
    {
        return ENTITY_INVITATION;
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
    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function signatureDiv(): false|string
    {
        if ( ! $this->signature_base64) {
            return false;
        }

        return sprintf('<img src="data:image/svg+xml;base64,%s"></img><p/>%s: %s', $this->signature_base64, trans('texts.signed'), Utils::fromSqlDateTime($this->signature_date));
    }
}

Invitation::creating(function ($invitation): void {
    LookupInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

Invitation::updating(function ($invitation): void {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

Invitation::deleted(function ($invitation): void {
    if ($invitation->forceDeleting) {
        LookupInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});

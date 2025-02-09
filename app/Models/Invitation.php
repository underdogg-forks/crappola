<?php

namespace App\Models;

use App\Libraries\Utils;
use App\Models\Traits\Inviteable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Invitation.
 */
class Invitation extends EntityModel
{
    use Inviteable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function getEntityType()
    {
        return ENTITY_INVITATION;
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo('App\Models\Contact')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function signatureDiv()
    {
        if ( ! $this->signature_base64) {
            return false;
        }

        return sprintf('<img src="data:image/svg+xml;base64,%s"></img><p/>%s: %s', $this->signature_base64, trans('texts.signed'), Utils::fromSqlDateTime($this->signature_date));
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Invitation::creating(function ($invitation) {
    LookupInvitation::createNew($invitation->account->account_key, [
        'invitation_key' => $invitation->invitation_key,
    ]);
});

Invitation::updating(function ($invitation) {
    $dirty = $invitation->getDirty();
    if (array_key_exists('message_id', $dirty)) {
        LookupInvitation::updateInvitation($invitation->account->account_key, $invitation);
    }
});

Invitation::deleted(function ($invitation) {
    if ($invitation->forceDeleting) {
        LookupInvitation::deleteWhere([
            'invitation_key' => $invitation->invitation_key,
        ]);
    }
});

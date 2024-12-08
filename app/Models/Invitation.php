<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Utils;

/**
 * Class Invitation.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int                             $contact_id
 * @property int                             $invoice_id
 * @property string                          $invitation_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null                     $transaction_reference
 * @property string|null                     $sent_date
 * @property string|null                     $viewed_date
 * @property int                             $public_id
 * @property string|null                     $opened_date
 * @property string|null                     $message_id
 * @property string|null                     $email_error
 * @property string|null                     $signature_base64
 * @property string|null                     $signature_date
 * @property \App\Models\Account|null        $account
 * @property \App\Models\Contact             $contact
 * @property \App\Models\Invoice             $invoice
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereEmailError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereInvitationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereOpenedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereSignatureBase64($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereSignatureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereViewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Invitation withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Invitation extends EntityModel
{
    use Inviteable;
    use SoftDeletes;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_INVITATION;
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
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

<?php

namespace App\Models;

use App\Models\Traits\Inviteable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Utils;

/**
 * Class Invitation.
 *
 * @property int          $id
 * @property int          $account_id
 * @property int          $user_id
 * @property int          $contact_id
 * @property int          $invoice_id
 * @property string       $invitation_key
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property string|null  $transaction_reference
 * @property string|null  $sent_date
 * @property string|null  $viewed_date
 * @property int          $public_id
 * @property string|null  $opened_date
 * @property string|null  $message_id
 * @property string|null  $email_error
 * @property string|null  $signature_base64
 * @property string|null  $signature_date
 * @property Account|null $account
 * @property Contact      $contact
 * @property Invoice      $invoice
 * @property User         $user
 *
 * @method static Builder|Invitation newModelQuery()
 * @method static Builder|Invitation newQuery()
 * @method static Builder|Invitation onlyTrashed()
 * @method static Builder|Invitation query()
 * @method static Builder|Invitation scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Invitation whereAccountId($value)
 * @method static Builder|Invitation whereContactId($value)
 * @method static Builder|Invitation whereCreatedAt($value)
 * @method static Builder|Invitation whereDeletedAt($value)
 * @method static Builder|Invitation whereEmailError($value)
 * @method static Builder|Invitation whereId($value)
 * @method static Builder|Invitation whereInvitationKey($value)
 * @method static Builder|Invitation whereInvoiceId($value)
 * @method static Builder|Invitation whereMessageId($value)
 * @method static Builder|Invitation whereOpenedDate($value)
 * @method static Builder|Invitation wherePublicId($value)
 * @method static Builder|Invitation whereSentDate($value)
 * @method static Builder|Invitation whereSignatureBase64($value)
 * @method static Builder|Invitation whereSignatureDate($value)
 * @method static Builder|Invitation whereTransactionReference($value)
 * @method static Builder|Invitation whereUpdatedAt($value)
 * @method static Builder|Invitation whereUserId($value)
 * @method static Builder|Invitation whereViewedDate($value)
 * @method static Builder|Invitation withActiveOrSelected($id = false)
 * @method static Builder|Invitation withArchived()
 * @method static Builder|Invitation withTrashed()
 * @method static Builder|Invitation withoutTrashed()
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
        return $this->belongsTo(Invoice::class)->withTrashed();
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

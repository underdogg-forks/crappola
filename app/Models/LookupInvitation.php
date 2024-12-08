<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 *
 * @property int                       $id
 * @property int                       $lookup_account_id
 * @property string                    $invitation_key
 * @property string|null               $message_id
 * @property \App\Models\LookupAccount $lookupAccount
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation whereInvitationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation whereLookupAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupInvitation whereMessageId($value)
 *
 * @mixin \Eloquent
 */
class LookupInvitation extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'lookup_account_id',
        'invitation_key',
        'message_id',
    ];

    public static function updateInvitation($accountKey, $invitation): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        if ( ! $invitation->message_id) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)
            ->firstOrFail();

        $lookupInvitation = self::whereLookupAccountId($lookupAccount->id)
            ->whereInvitationKey($invitation->invitation_key)
            ->firstOrFail();

        $lookupInvitation->message_id = $invitation->message_id;
        $lookupInvitation->save();

        config(['database.default' => $current]);
    }
}

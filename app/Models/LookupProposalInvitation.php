<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;

/**
 * Class ExpenseCategory.
 *
 * @property int         $id
 * @property int         $lookup_account_id
 * @property string      $invitation_key
 * @property string|null $message_id
 *
 * @method static Builder|LookupProposalInvitation newModelQuery()
 * @method static Builder|LookupProposalInvitation newQuery()
 * @method static Builder|LookupProposalInvitation query()
 * @method static Builder|LookupProposalInvitation whereId($value)
 * @method static Builder|LookupProposalInvitation whereInvitationKey($value)
 * @method static Builder|LookupProposalInvitation whereLookupAccountId($value)
 * @method static Builder|LookupProposalInvitation whereMessageId($value)
 *
 * @property LookupAccount $lookupAccount
 *
 * @mixin \Eloquent
 */
class LookupProposalInvitation extends LookupModel
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;

use DateTimeInterface;

/**
 * Class ExpenseCategory.
 */
class LookupProposalInvitation extends LookupModel
{
    protected $fillable = [
        'lookup_account_id',
        'invitation_key',
        'message_id',
    ];

    public static function updateInvitation($accountKey, $invitation)
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

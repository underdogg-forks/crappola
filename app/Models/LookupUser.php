<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 *
 * @property int         $id
 * @property int         $lookup_account_id
 * @property string|null $email
 * @property string|null $confirmation_code
 * @property int         $user_id
 * @property string|null $oauth_user_key
 * @property string|null $referral_code
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereLookupAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereOauthUserKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupUser whereUserId($value)
 *
 * @property \App\Models\LookupAccount $lookupAccount
 *
 * @mixin \Eloquent
 */
class LookupUser extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'lookup_account_id',
        'email',
        'user_id',
        'confirmation_code',
        'oauth_user_key',
        'referral_code',
    ];

    public static function updateUser($accountKey, $user): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)
            ->firstOrFail();

        $lookupUser = self::whereLookupAccountId($lookupAccount->id)
            ->whereUserId($user->id)
            ->firstOrFail();

        $lookupUser->email = $user->email;
        $lookupUser->confirmation_code = $user->confirmation_code ?: null;
        $lookupUser->oauth_user_key = ($user->oauth_provider_id && $user->oauth_user_id) ? ($user->oauth_provider_id . '-' . $user->oauth_user_id) : null;
        $lookupUser->referral_code = $user->referral_code;
        $lookupUser->save();

        config(['database.default' => $current]);
    }

    public static function validateField($field, $value, $user = false)
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return true;
        }

        $current = config('database.default');
        $accountKey = $user ? $user->account->account_key : false;

        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupUser = self::where($field, '=', $value)->first();

        if ($user) {
            $lookupAccount = LookupAccount::whereAccountKey($accountKey)->firstOrFail();
            $isValid = ! $lookupUser || ($lookupUser->lookup_account_id == $lookupAccount->id && $lookupUser->user_id == $user->id);
        } else {
            $isValid = ! $lookupUser;
        }

        config(['database.default' => $current]);

        return $isValid;
    }
}

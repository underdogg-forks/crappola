<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 */
class LookupAccount extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'lookup_company_id',
        'account_key',
    ];

    public static function createAccount($accountKey, $companyId): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $server = DbServer::whereName($current)->firstOrFail();
        $lookupCompany = LookupCompany::whereDbServerId($server->id)
            ->whereCompanyId($companyId)->first();

        if ( ! $lookupCompany) {
            $lookupCompany = LookupCompany::create([
                'db_server_id' => $server->id,
                'company_id'   => $companyId,
            ]);
        }

        self::create([
            'lookup_company_id' => $lookupCompany->id,
            'account_key'       => $accountKey,
        ]);

        static::setDbServer($current);
    }

    public static function updateAccount($accountKey, $account): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = self::whereAccountKey($accountKey)
            ->firstOrFail();

        $lookupAccount->subdomain = $account->subdomain ?: null;
        $lookupAccount->save();

        config(['database.default' => $current]);
    }

    public static function validateField($field, $value, $account = false)
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return true;
        }

        $current = config('database.default');

        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = self::where($field, '=', $value)->first();

        if ($account) {
            $isValid = ! $lookupAccount || ($lookupAccount->account_key == $account->account_key);
        } else {
            $isValid = ! $lookupAccount;
        }

        config(['database.default' => $current]);

        return $isValid;
    }

    public function lookupCompany()
    {
        return $this->belongsTo(\App\Models\LookupCompany::class);
    }

    public function getDbServer()
    {
        return $this->lookupCompany->dbServer->name;
    }
}

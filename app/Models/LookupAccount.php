<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;

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
        'support_email_local_part',
    ];

    public static function createAccount($companyKey, $companyId): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $server = DbServer::whereName($current)->firstOrFail();
        $lookupCompanyPlan = LookupCompanyPlan::whereDbServerId($server->id)
            ->whereCompanyPlanId($companyId)->first();

        if (! $lookupCompanyPlan) {
            $lookupCompanyPlan = LookupCompanyPlan::create([
                'db_server_id' => $server->id,
                'company_id'   => $companyId,
            ]);
        }

        self::create([
            'lookup_company_id' => $lookupCompanyPlan->id,
            'account_key'       => $companyKey,
        ]);

        static::setDbServer($current);
    }

    public static function updateAccount($companyKey, $company): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = self::whereAccountKey($companyKey)
            ->firstOrFail();

        $lookupAccount->subdomain = $company->subdomain ?: null;
        $lookupAccount->save();

        config(['database.default' => $current]);
    }

    public static function updateSupportLocalPart($companyKey, $support_email_local_part): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = self::whereAccountKey($companyKey)
            ->firstOrFail();

        $lookupAccount->support_email_local_part = $support_email_local_part ?: null;
        $lookupAccount->save();

        config(['database.default' => $current]);
    }

    public static function validateField($field, $value, $company = false)
    {
        if (! config('ninja.multi_db_enabled')) {
            return true;
        }

        $current = config('database.default');

        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = self::where($field, '=', $value)->first();

        $isValid = $company ? ! $lookupAccount || ($lookupAccount->account_key == $company->account_key) : ! $lookupAccount;

        config(['database.default' => $current]);

        return $isValid;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

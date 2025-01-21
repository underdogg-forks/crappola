<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpenseCategory.
 */
class LookupModel extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public static function createNew($companyKey, $data): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($companyKey)->first();

        if ($lookupAccount) {
            $data['lookup_account_id'] = $lookupAccount->id;
        } else {
            abort(500, 'Lookup company not found for ' . $companyKey);
        }

        static::create($data);

        config(['database.default' => $current]);
    }

    public static function deleteWhere($where): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        static::where($where)->delete();

        config(['database.default' => $current]);
    }

    public static function setServerByField($field, $value): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        $className = get_called_class();
        $className = str_replace('Lookup', '', $className);
        $key = sprintf('server:%s:%s:%s', $className, $field, $value);

        // check if we've cached this lookup
        if (env('MULTI_DB_CACHE_ENABLED') && $server = Cache::get($key)) {
            static::setDbServer($server);

            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        if ($value && $lookupModel = static::where($field, '=', $value)->first()) {
            $entity = new $className();
            $server = $lookupModel->getDbServer();

            static::setDbServer($server);

            // check entity is found on the server
            if ($field === 'oauth_user_key') {
                $providerId = substr($value, 0, 1);
                $oauthId = substr($value, 2);
                $isFound = $entity::where('oauth_provider_id', '=', $providerId)
                    ->where('oauth_user_id', '=', $oauthId)
                    ->withTrashed()
                    ->first();
            } else {
                $isFound = $entity::where($field, '=', $value)
                    ->withTrashed()
                    ->first();
            }
            if (! $isFound) {
                abort(404, "Looked up {$className} not found: {$field} => {$value}");
            }

            Cache::put($key, $server, 120);
        } else {
            config(['database.default' => $current]);
        }
    }

    protected static function setDbServer($server): void
    {
        if (! config('ninja.multi_db_enabled')) {
            return;
        }

        config(['database.default' => $server]);
    }

    public function getDbServer()
    {
        return $this->lookupAccount->lookupCompanyPlan->dbServer->name;
    }

    public function lookupAccount()
    {
        return $this->belongsTo(LookupAccount::class);
    }
}

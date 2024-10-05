<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 */
class LookupModel extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public static function createNew(string $accountKey, array $data): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        $lookupAccount = LookupAccount::whereAccountKey($accountKey)->first();

        if ($lookupAccount) {
            $data['lookup_account_id'] = $lookupAccount->id;
        } else {
            abort(500, 'Lookup account not found for ' . $accountKey);
        }

        static::create($data);

        config(['database.default' => $current]);
    }

    public static function deleteWhere($where): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        $current = config('database.default');
        config(['database.default' => DB_NINJA_LOOKUP]);

        static::where($where)->delete();

        config(['database.default' => $current]);
    }

    public static function setServerByField($field, $value): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        $className = static::class;
        $className = str_replace('Lookup', '', $className);

        $key = sprintf('server:%s:%s:%s', $className, $field, $value);

        // check if we've cached this lookup
        if (env('MULTI_DB_CACHE_ENABLED') && $server = \Illuminate\Support\Facades\Cache::get($key)) {
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
                $providerId = mb_substr($value, 0, 1);
                $oauthId = mb_substr($value, 2);
                $isFound = $entity::where('oauth_provider_id', '=', $providerId)
                    ->where('oauth_user_id', '=', $oauthId)
                    ->withTrashed()
                    ->first();
            } else {
                $isFound = $entity::where($field, '=', $value)
                    ->withTrashed()
                    ->first();
            }

            if ( ! $isFound) {
                abort(404, sprintf('Looked up %s not found: %s => %s', $className, $field, $value));
            }

            \Illuminate\Support\Facades\Cache::put($key, $server, 120 * 60);
        } else {
            config(['database.default' => $current]);
        }
    }

    public function lookupAccount()
    {
        return $this->belongsTo(\App\Models\LookupAccount::class);
    }

    public function getDbServer()
    {
        return $this->lookupAccount->lookupCompany->dbServer->name;
    }

    protected static function setDbServer($server): void
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return;
        }

        config(['database.default' => $server]);
    }
}

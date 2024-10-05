<?php

namespace App\Ninja\Repositories;

use DB;

class TokenRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\AccountToken';
    }

    public function find($userId)
    {
        $query = \Illuminate\Support\Facades\DB::table('account_tokens')
            ->where('account_tokens.user_id', '=', $userId)
            ->whereNull('account_tokens.deleted_at');

        return $query->select('account_tokens.public_id', 'account_tokens.name', 'account_tokens.token', 'account_tokens.public_id', 'account_tokens.deleted_at');
    }
}

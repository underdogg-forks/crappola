<?php

namespace App\Ninja\Repositories;

use App\Models\AccountToken;
use Illuminate\Support\Facades\DB;

class TokenRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return AccountToken::class;
    }

    public function find($userId)
    {
        $query = DB::table('account_tokens')
            ->where('account_tokens.user_id', '=', $userId)
            ->whereNull('account_tokens.deleted_at');

        return $query->select('account_tokens.public_id', 'account_tokens.name', 'account_tokens.token', 'account_tokens.public_id', 'account_tokens.deleted_at');
    }
}

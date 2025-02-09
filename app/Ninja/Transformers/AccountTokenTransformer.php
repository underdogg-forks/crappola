<?php

namespace App\Ninja\Transformers;

use App\Models\AccountToken;
use League\Fractal\TransformerAbstract;

/**
 * Class AccountTokenTransformer.
 */
class AccountTokenTransformer extends TransformerAbstract
{
    
    public function transform(AccountToken $account_token): array
    {
        return [
            'name'  => $account_token->name,
            'token' => $account_token->token,
        ];
    }
}

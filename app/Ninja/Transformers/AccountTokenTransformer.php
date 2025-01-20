<?php

namespace App\Ninja\Transformers;

use App\Models\AccountToken;
use League\Fractal\TransformerAbstract;

/**
 * Class AccountTokenTransformer.
 */
class AccountTokenTransformer extends TransformerAbstract
{
    /**
     * @SWG\Property(property="name", type="string", example="Name")
     * @SWG\Property(property="token", type="string", example="Token")
     */

    /**
     * @param AccountToken $company_token
     *
     * @return array
     */
    public function transform(AccountToken $company_token)
    {
        return [
            'name'  => $company_token->name,
            'token' => $company_token->token,
        ];
    }
}

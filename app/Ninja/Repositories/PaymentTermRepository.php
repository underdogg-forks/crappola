<?php

namespace App\Ninja\Repositories;

use App\Models\PaymentTerm;
use Illuminate\Support\Facades\DB;

class PaymentTermRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return PaymentTerm::class;
    }

    public function find($accountId = 0)
    {
        return DB::table('payment_terms')
            ->where('payment_terms.account_id', '=', $accountId)
            ->where('payment_terms.deleted_at', '=', null)
            ->select('payment_terms.public_id', 'payment_terms.name', 'payment_terms.num_days', 'payment_terms.deleted_at');
    }
}

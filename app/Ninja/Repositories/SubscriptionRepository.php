<?php

namespace App\Ninja\Repositories;

use Illuminate\Support\Facades\DB;

class SubscriptionRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\Subscription';
    }

    public function find($companyId)
    {
        $query = DB::table('subscriptions')
            ->where('subscriptions.company_id', '=', $companyId)
            ->whereNull('subscriptions.deleted_at')
            ->select(
                'subscriptions.public_id',
                'subscriptions.target_url as target',
                'subscriptions.event_id as event',
                'subscriptions.deleted_at',
                'subscriptions.format'
            );

        return $query;
    }
}

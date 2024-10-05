<?php

namespace App\Ninja\Repositories;

class SubscriptionRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return \App\Models\Subscription::class;
    }

    public function find($accountId)
    {
        $query = \Illuminate\Support\Facades\DB::table('subscriptions')
            ->where('subscriptions.account_id', '=', $accountId)
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

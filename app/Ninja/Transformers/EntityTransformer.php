<?php

namespace App\Ninja\Transformers;

use App\Models\Account;
use League\Fractal\TransformerAbstract;

class EntityTransformer extends TransformerAbstract
{
    protected ?\App\Models\Account $account;

    protected $serializer;

    public function __construct(?Account $account = null, $serializer = null)
    {
        $this->account = $account;
        $this->serializer = $serializer;
    }

    public function getDefaultIncludes(): array
    {
        return $this->defaultIncludes;
    }

    protected function includeCollection($data, $transformer, $entityType): \League\Fractal\Resource\Collection
    {
        if ($this->serializer && $this->serializer != API_SERIALIZER_JSON) {
            $entityType = null;
        }

        return $this->collection($data, $transformer, $entityType);
    }

    protected function includeItem($data, $transformer, $entityType): \League\Fractal\Resource\Item
    {
        if ($this->serializer && $this->serializer != API_SERIALIZER_JSON) {
            $entityType = null;
        }

        return $this->item($data, $transformer, $entityType);
    }

    protected function getTimestamp($date)
    {
        if (method_exists($date, 'getTimestamp')) {
            return $date->getTimestamp();
        }
        if (is_string($date)) {
            return strtotime($date);
        }
    }

    protected function getDefaults($entity): array
    {
        $data = [
            'account_key' => $this->account->account_key,
            'is_owner'    => (bool) (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->owns($entity)),
        ];

        if ($entity->relationLoaded('user')) {
            $data['user_id'] = (int) $entity->user->public_id + 1;
        }

        return $data;
    }
}

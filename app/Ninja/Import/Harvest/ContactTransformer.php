<?php

namespace App\Ninja\Import\Harvest;

use App\Ninja\Import\BaseTransformer;
use League\Fractal\Resource\Item;

/**
 * Class ContactTransformer.
 */
class ContactTransformer extends BaseTransformer
{
    /**
     * @param $data
     *
     * @return bool|Item
     */
    public function transform($data): false|\League\Fractal\Resource\Item
    {
        if ( ! $this->hasClient($data->client)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'client_id'  => $this->getClientId($data->client),
            'first_name' => $this->getString($data, 'first_name'),
            'last_name'  => $this->getString($data, 'last_name'),
            'email'      => $this->getString($data, 'email'),
            'phone'      => $this->getString($data, 'office_phone') ?: $this->getString($data, 'mobile_phone'),
        ]);
    }
}

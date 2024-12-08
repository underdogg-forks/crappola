<?php

namespace App\Ninja\Import\Harvest;

use App\Ninja\Import\BaseTransformer;
use League\Fractal\Resource\Item;

/**
 * Class ClientTransformer.
 */
class ClientTransformer extends BaseTransformer
{
    /**
     * @param $data
     *
     * @return bool|Item
     */
    public function transform($data): false|Item
    {
        if ($this->hasClient($data->client_name)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name' => $this->getString($data, 'client_name'),
        ]);
    }
}

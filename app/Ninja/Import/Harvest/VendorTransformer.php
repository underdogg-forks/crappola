<?php

namespace App\Ninja\Import\Harvest;

use App\Ninja\Import\BaseTransformer;
use League\Fractal\Resource\Item;

// vendor
/**
 * Class VendorTransformer.
 */
class VendorTransformer extends BaseTransformer
{
    /**
     * @param $data
     */
    public function transform($data): false|Item
    {
        if ($this->hasVendor($data->vendor_name)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name' => $data->vendor_name,
        ]);
    }
}

<?php

namespace App\Ninja\Import\Ronin;

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
        if ($this->hasVendor($data->company)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name'       => $data->company,
            'work_phone' => $data->phone,
            'contacts'   => [
                [
                    'first_name' => $this->getFirstName($data->name),
                    'last_name'  => $this->getLastName($data->name),
                    'email'      => $data->email,
                ],
            ],
        ]);
    }
}

<?php

namespace App\Ninja\Import\Nutcache;

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
        if ($this->hasVendor($data->name)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name'          => $data->name,
            'city'          => $data->city ?? '',
            'state'         => isset($data->city) ? $data->stateprovince : '',
            'id_number'     => $data->registration_number ?? '',
            'postal_code'   => $data->postalzip_code ?? '',
            'private_notes' => $data->notes ?? '',
            'work_phone'    => $data->phone ?? '',
            'contacts'      => [
                [
                    'first_name' => isset($data->contact_name) ? $this->getFirstName($data->contact_name) : '',
                    'last_name'  => isset($data->contact_name) ? $this->getLastName($data->contact_name) : '',
                    'email'      => $data->email,
                    'phone'      => $data->mobile ?? '',
                ],
            ],
            'country_id' => isset($data->country) ? $this->getCountryId($data->country) : null,
        ]);
    }
}

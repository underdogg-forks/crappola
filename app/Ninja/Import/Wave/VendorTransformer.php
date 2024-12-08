<?php

namespace App\Ninja\Import\Wave;

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
     *
     * @return bool|Item
     */
    public function transform($data): false|Item
    {
        if ($this->hasVendor($data->customer_name)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name'          => $data->customer_name,
            'id_number'     => $data->account_number,
            'work_phone'    => $data->phone,
            'website'       => $data->website,
            'address1'      => $data->address_line_1,
            'address2'      => $data->address_line_2,
            'city'          => $data->city,
            'state'         => $data->provincestate,
            'postal_code'   => $data->postal_codezip_code,
            'private_notes' => $data->delivery_instructions,
            'contacts'      => [
                [
                    'first_name' => $data->contact_first_name,
                    'last_name'  => $data->contact_last_name,
                    'email'      => $data->email,
                    'phone'      => $data->mobile,
                ],
            ],
            'country_id' => $this->getCountryId($data->country),
        ]);
    }
}

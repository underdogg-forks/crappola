<?php

namespace App\Ninja\Import\Zoho;

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
        if ($this->hasVendor($data->customer_name)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name'          => $data->customer_name,
            'id_number'     => $data->customer_id,
            'work_phone'    => $data->phonek,
            'address1'      => $data->billing_address,
            'city'          => $data->billing_city,
            'state'         => $data->billing_state,
            'postal_code'   => $data->billing_code,
            'private_notes' => $data->notes,
            'website'       => $data->website,
            'contacts'      => [
                [
                    'first_name' => $data->first_name,
                    'last_name'  => $data->last_name,
                    'email'      => $data->emailid,
                    'phone'      => $data->mobilephone,
                ],
            ],
            'country_id' => $this->getCountryId($data->billing_country),
        ]);
    }
}

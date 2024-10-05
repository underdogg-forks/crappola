<?php

namespace App\Ninja\Import\Nutcache;

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
    public function transform($data): false|\League\Fractal\Resource\Item
    {
        if ($this->hasClient($data->name)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'name'          => $this->getString($data, 'name'),
            'city'          => $this->getString($data, 'city'),
            'state'         => $this->getString($data, 'stateprovince'),
            'id_number'     => $this->getString($data, 'registration_number'),
            'postal_code'   => $this->getString($data, 'postalzip_code'),
            'private_notes' => $this->getString($data, 'notes'),
            'work_phone'    => $this->getString($data, 'phone'),
            'contacts'      => [
                [
                    'first_name' => isset($data->contact_name) ? $this->getFirstName($data->contact_name) : '',
                    'last_name'  => isset($data->contact_name) ? $this->getLastName($data->contact_name) : '',
                    'email'      => $this->getString($data, 'email'),
                    'phone'      => $this->getString($data, 'mobile'),
                ],
            ],
            'country_id' => isset($data->country) ? $this->getCountryId($data->country) : null,
        ]);
    }
}

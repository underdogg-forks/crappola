<?php

namespace App\Ninja\Import\FreshBooks;

use App\Ninja\Import\BaseTransformer;
use League\Fractal\Resource\Item;

/**
 * Class PaymentTransformer.
 */
class PaymentTransformer extends BaseTransformer
{
    /**
     * @param $data
     */
    public function transform($data): Item
    {
        return new Item($data, fn ($data): array => [
            'amount'           => $data->paid,
            'payment_date_sql' => $data->create_date,
            'client_id'        => $data->client_id,
            'invoice_id'       => $data->invoice_id,
        ]);
    }
}

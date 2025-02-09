<?php

namespace App\Ninja\Import\Wave;

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
    public function transform($data): false|Item
    {
        if ( ! $this->getInvoiceClientId($data->invoice_num)) {
            return false;
        }

        return new Item($data, fn ($data): array => [
            'amount'            => (float) $data->amount,
            'payment_date_sql'  => $this->getDate($data, 'payment_date'),
            'client_id'         => $this->getInvoiceClientId($data->invoice_num),
            'invoice_id'        => $this->getInvoiceId($data->invoice_num),
            'invoice_public_id' => $this->getInvoicePublicId($data->invoice_num),
        ]);
    }
}

<?php

namespace App\Ninja\Presenters;

use stdClass;

class InvoiceItemPresenter extends EntityPresenter
{
    public function rBits(): stdClass
    {
        $data = new stdClass();
        $data->description = $this->entity->notes;
        $data->item_price = (float) ($this->entity->cost);
        $data->quantity = (float) ($this->entity->qty);
        $data->amount = round($data->item_price * $data->quantity, 2);

        return $data;
    }

    public function tax1(): string
    {
        $item = $this->entity;

        return $item->tax_name1 . ' ' . $item->tax_rate1 . '%';
    }

    public function tax2(): string
    {
        $item = $this->entity;

        return $item->tax_name2 . ' ' . $item->tax_rate2 . '%';
    }
}

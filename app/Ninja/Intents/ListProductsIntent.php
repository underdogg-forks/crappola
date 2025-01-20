<?php

namespace App\Ninja\Intents;

use App\Models\Product;
use Auth;

class ListProductsIntent extends ProductIntent
{
    public function process()
    {
        $company = Auth::user()->company;
        $products = Product::scope()
            ->orderBy('product_key')
            ->limit(5)
            ->get()
            ->transform(function ($item, $key) use ($company) {
                $card = $item->present()->skypeBot($company);
                if ($this->stateEntity(ENTITY_INVOICE)) {
                    $card->addButton('imBack', trans('texts.add_to_invoice', ['invoice' => '']), trans('texts.add_product_to_invoice', ['product' => $item->product_key]));
                }

                return $card;
            });

        return $this->createResponse(SKYPE_CARD_CAROUSEL, $products);
    }
}

<?php

namespace App\Ninja\Intents;

class ProductIntent extends BaseIntent
{
    /**
     * @var mixed
     */
    public $productRepo;
    public function __construct($state, $data)
    {
        $this->productRepo = app(\App\Ninja\Repositories\ProductRepository::class);

        parent::__construct($state, $data);
    }
}

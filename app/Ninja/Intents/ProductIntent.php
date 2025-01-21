<?php

namespace App\Ninja\Intents;

use App\Ninja\Repositories\ProductRepository;

class ProductIntent extends BaseIntent
{
    /**
     * @var mixed
     */
    public $productRepo;

    public function __construct($state, $data)
    {
        $this->productRepo = app(ProductRepository::class);

        parent::__construct($state, $data);
    }
}

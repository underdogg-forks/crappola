<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 */
class Product extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ProductPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'product_key',
        'notes',
        'cost',
        'qty',
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'custom_value1',
        'custom_value2',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @return array
     */
    public static function getImportColumns(): array
    {
        return [
            'product_key',
            'notes',
            'cost',
            'custom_value1',
            'custom_value2',
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap(): array
    {
        return [
            'product|item'              => 'product_key',
            'notes|description|details' => 'notes',
            'cost|amount|price'         => 'cost',
            'custom_value1'             => 'custom_value1',
            'custom_value2'             => 'custom_value2',
        ];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('product_key', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_PRODUCT;
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }
}

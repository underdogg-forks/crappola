<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 */
class Product extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ProductPresenter';

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

    /**
     * @return array
     */
    public static function getImportColumns()
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
    public static function getImportMap()
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

    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

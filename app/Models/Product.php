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

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = ProductPresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'product_key',
        'notes',
        'cost',
        'qty',
    ];

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
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('product_key', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'default_tax_rate_id');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

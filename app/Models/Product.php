<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string                          $product_key
 * @property string                          $notes
 * @property string                          $cost
 * @property string|null                     $qty
 * @property int                             $public_id
 * @property int                             $is_deleted
 * @property string|null                     $custom_value1
 * @property string|null                     $custom_value2
 * @property string|null                     $tax_name1
 * @property string                          $tax_rate1
 * @property string|null                     $tax_name2
 * @property string                          $tax_rate2
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 *
 * @mixin \Eloquent
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
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('product_key', '=', $key)->first();
    }

    public function getEntityType(): string
    {
        return ENTITY_PRODUCT;
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }
}

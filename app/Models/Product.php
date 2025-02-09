<?php

namespace App\Models;

use App\Ninja\Presenters\ProductPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int         $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string      $product_key
 * @property string      $notes
 * @property string      $cost
 * @property string|null $qty
 * @property int         $public_id
 * @property int         $is_deleted
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $tax_name1
 * @property string      $tax_rate1
 * @property string|null $tax_name2
 * @property string      $tax_rate2
 * @property User        $user
 *
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Product whereAccountId($value)
 * @method static Builder|Product whereCost($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereCustomValue1($value)
 * @method static Builder|Product whereCustomValue2($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsDeleted($value)
 * @method static Builder|Product whereNotes($value)
 * @method static Builder|Product whereProductKey($value)
 * @method static Builder|Product wherePublicId($value)
 * @method static Builder|Product whereQty($value)
 * @method static Builder|Product whereTaxName1($value)
 * @method static Builder|Product whereTaxName2($value)
 * @method static Builder|Product whereTaxRate1($value)
 * @method static Builder|Product whereTaxRate2($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product whereUserId($value)
 * @method static Builder|Product withActiveOrSelected($id = false)
 * @method static Builder|Product withArchived()
 * @method static Builder|Product withTrashed()
 * @method static Builder|Product withoutTrashed()
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
    protected $presenter = ProductPresenter::class;

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
        return $this->belongsTo(User::class)->withTrashed();
    }
}

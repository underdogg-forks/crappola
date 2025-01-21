<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class TaxRate.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int         $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string      $name
 * @property string      $rate
 * @property int         $public_id
 * @property int         $is_inclusive
 * @property User        $user
 *
 * @method static Builder|TaxRate newModelQuery()
 * @method static Builder|TaxRate newQuery()
 * @method static Builder|TaxRate onlyTrashed()
 * @method static Builder|TaxRate query()
 * @method static Builder|TaxRate scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|TaxRate whereAccountId($value)
 * @method static Builder|TaxRate whereCreatedAt($value)
 * @method static Builder|TaxRate whereDeletedAt($value)
 * @method static Builder|TaxRate whereId($value)
 * @method static Builder|TaxRate whereIsInclusive($value)
 * @method static Builder|TaxRate whereName($value)
 * @method static Builder|TaxRate wherePublicId($value)
 * @method static Builder|TaxRate whereRate($value)
 * @method static Builder|TaxRate whereUpdatedAt($value)
 * @method static Builder|TaxRate whereUserId($value)
 * @method static Builder|TaxRate withActiveOrSelected($id = false)
 * @method static Builder|TaxRate withArchived()
 * @method static Builder|TaxRate withTrashed()
 * @method static Builder|TaxRate withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TaxRate extends EntityModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'rate',
        'is_inclusive',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @return bool|string
     */
    public function __toString(): string
    {
        return sprintf('%s: %s%%', $this->name, $this->rate);
    }

    public function getEntityType(): string
    {
        return ENTITY_TAX_RATE;
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

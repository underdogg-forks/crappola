<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TaxRate.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string                          $name
 * @property string                          $rate
 * @property int                             $public_id
 * @property int                             $is_inclusive
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereIsInclusive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate withoutTrashed()
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
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }
}

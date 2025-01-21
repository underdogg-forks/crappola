<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class License.
 *
 * @property int         $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null    $affiliate_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $license_key
 * @property int|null    $is_claimed
 * @property string|null $transaction_reference
 * @property int|null    $product_id
 *
 * @method static Builder|License newModelQuery()
 * @method static Builder|License newQuery()
 * @method static Builder|License onlyTrashed()
 * @method static Builder|License query()
 * @method static Builder|License whereAffiliateId($value)
 * @method static Builder|License whereCreatedAt($value)
 * @method static Builder|License whereDeletedAt($value)
 * @method static Builder|License whereEmail($value)
 * @method static Builder|License whereFirstName($value)
 * @method static Builder|License whereId($value)
 * @method static Builder|License whereIsClaimed($value)
 * @method static Builder|License whereLastName($value)
 * @method static Builder|License whereLicenseKey($value)
 * @method static Builder|License whereProductId($value)
 * @method static Builder|License whereTransactionReference($value)
 * @method static Builder|License whereUpdatedAt($value)
 * @method static Builder|License withTrashed()
 * @method static Builder|License withoutTrashed()
 *
 * @mixin \Eloquent
 */
class License extends Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    protected $casts = ['deleted_at' => 'datetime'];
}

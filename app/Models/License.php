<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class License.
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null                        $affiliate_id
 * @property string|null                     $first_name
 * @property string|null                     $last_name
 * @property string|null                     $email
 * @property string|null                     $license_key
 * @property int|null                        $is_claimed
 * @property string|null                     $transaction_reference
 * @property int|null                        $product_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License whereAffiliateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereIsClaimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLicenseKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|License withoutTrashed()
 *
 * @mixin \Eloquent
 */
class License extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    protected $casts = ['deleted_at' => 'datetime'];
}

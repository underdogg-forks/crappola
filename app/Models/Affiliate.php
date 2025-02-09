<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Affiliate.
 *
 * @property int         $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string      $name
 * @property string      $affiliate_key
 * @property string      $payment_title
 * @property string      $payment_subtitle
 * @property string|null $price
 *
 * @method static Builder|Affiliate newModelQuery()
 * @method static Builder|Affiliate newQuery()
 * @method static Builder|Affiliate query()
 * @method static Builder|Affiliate whereAffiliateKey($value)
 * @method static Builder|Affiliate whereCreatedAt($value)
 * @method static Builder|Affiliate whereDeletedAt($value)
 * @method static Builder|Affiliate whereId($value)
 * @method static Builder|Affiliate whereName($value)
 * @method static Builder|Affiliate wherePaymentSubtitle($value)
 * @method static Builder|Affiliate wherePaymentTitle($value)
 * @method static Builder|Affiliate wherePrice($value)
 * @method static Builder|Affiliate whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Affiliate extends Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var bool
     */
    protected $softDelete = true;
}

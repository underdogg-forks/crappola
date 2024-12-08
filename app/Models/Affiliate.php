<?php

namespace App\Models;

/**
 * Class Affiliate.
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $deleted_at
 * @property string                          $name
 * @property string                          $affiliate_key
 * @property string                          $payment_title
 * @property string                          $payment_subtitle
 * @property string|null                     $price
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate whereAffiliateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate wherePaymentSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate wherePaymentTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Affiliate whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Affiliate extends \Illuminate\Database\Eloquent\Model
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

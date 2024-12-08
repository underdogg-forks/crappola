<?php

namespace App\Models;

/**
 * Class PaymentStatus.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentStatus whereName($value)
 *
 * @mixin \Eloquent
 */
class PaymentStatus extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

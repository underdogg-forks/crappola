<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentStatus.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static Builder|PaymentStatus newModelQuery()
 * @method static Builder|PaymentStatus newQuery()
 * @method static Builder|PaymentStatus query()
 * @method static Builder|PaymentStatus whereId($value)
 * @method static Builder|PaymentStatus whereName($value)
 *
 * @mixin \Eloquent
 */
class PaymentStatus extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

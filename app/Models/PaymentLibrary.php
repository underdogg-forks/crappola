<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class PaymentLibrary.
 *
 * @property int                      $id
 * @property Carbon|null              $created_at
 * @property Carbon|null              $updated_at
 * @property string                   $name
 * @property int                      $visible
 * @property Collection<int, Gateway> $gateways
 * @property int|null                 $gateways_count
 *
 * @method static Builder|PaymentLibrary newModelQuery()
 * @method static Builder|PaymentLibrary newQuery()
 * @method static Builder|PaymentLibrary query()
 * @method static Builder|PaymentLibrary whereCreatedAt($value)
 * @method static Builder|PaymentLibrary whereId($value)
 * @method static Builder|PaymentLibrary whereName($value)
 * @method static Builder|PaymentLibrary whereUpdatedAt($value)
 * @method static Builder|PaymentLibrary whereVisible($value)
 *
 * @mixin \Eloquent
 */
class PaymentLibrary extends Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $table = 'payment_libraries';

    public function gateways()
    {
        return $this->hasMany(Gateway::class, 'payment_library_id');
    }
}

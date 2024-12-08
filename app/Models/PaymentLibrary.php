<?php

namespace App\Models;

/**
 * Class PaymentLibrary.
 *
 * @property int                                                                $id
 * @property \Illuminate\Support\Carbon|null                                    $created_at
 * @property \Illuminate\Support\Carbon|null                                    $updated_at
 * @property string                                                             $name
 * @property int                                                                $visible
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gateway> $gateways
 * @property int|null                                                           $gateways_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLibrary whereVisible($value)
 *
 * @mixin \Eloquent
 */
class PaymentLibrary extends \Illuminate\Database\Eloquent\Model
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
        return $this->hasMany(\App\Models\Gateway::class, 'payment_library_id');
    }
}

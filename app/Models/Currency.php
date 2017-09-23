<?php
namespace App\Models;

use Eloquent;

/**
 * Class Currency.
 */
class Currency extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'lookup__currencies';
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'swap_currency_symbol' => 'boolean',
    ];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}

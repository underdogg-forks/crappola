<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * Class Currency.
 */
class Currency extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'swap_currency_symbol' => 'boolean',
        'exchange_rate'        => 'double',
    ];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getTranslatedName()
    {
        return trans('texts.currency_' . Str::slug($this->name, '_'));
    }
}

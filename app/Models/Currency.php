<?php

namespace App\Models;

/**
 * Class Currency.
 */
class Currency extends \Illuminate\Database\Eloquent\Model
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
        return trans('texts.currency_' . \Illuminate\Support\Str::slug($this->name, '_'));
    }
}

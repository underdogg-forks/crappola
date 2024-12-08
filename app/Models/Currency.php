<?php

namespace App\Models;

/**
 * Class Currency.
 *
 * @property int        $id
 * @property string     $name
 * @property string     $symbol
 * @property string     $precision
 * @property string     $thousand_separator
 * @property string     $decimal_separator
 * @property string     $code
 * @property bool       $swap_currency_symbol
 * @property float|null $exchange_rate
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereDecimalSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency wherePrecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereSwapCurrencySymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereThousandSeparator($value)
 *
 * @mixin \Eloquent
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

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.currency_' . \Illuminate\Support\Str::slug($this->name, '_'));
    }
}

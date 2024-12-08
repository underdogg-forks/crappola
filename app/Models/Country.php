<?php

namespace App\Models;

/**
 * Class Country.
 *
 * @property int         $id
 * @property string|null $capital
 * @property string|null $citizenship
 * @property string      $country_code
 * @property string|null $currency
 * @property string|null $currency_code
 * @property string|null $currency_sub_unit
 * @property string|null $full_name
 * @property string      $iso_3166_2
 * @property string      $iso_3166_3
 * @property string      $name
 * @property string      $region_code
 * @property string      $sub_region_code
 * @property int         $eea
 * @property bool        $swap_postal_code
 * @property bool        $swap_currency_symbol
 * @property string|null $thousand_separator
 * @property string|null $decimal_separator
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCitizenship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCurrencySubUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDecimalSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereEea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso31662($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso31663($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereSubRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereSwapCurrencySymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereSwapPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereThousandSeparator($value)
 *
 * @mixin \Eloquent
 */
class Country extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'swap_postal_code',
        'swap_currency_symbol',
        'thousand_separator',
        'decimal_separator',
        'iso_3166_2',
        'iso_3166_3',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'swap_postal_code'     => 'boolean',
        'swap_currency_symbol' => 'boolean',
    ];

    public function getName()
    {
        return trans('texts.country_' . $this->name);
    }
}

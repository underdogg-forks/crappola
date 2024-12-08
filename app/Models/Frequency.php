<?php

namespace App\Models;

/**
 * Class Frequency.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $date_interval
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Frequency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Frequency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Frequency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Frequency whereDateInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frequency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Frequency whereName($value)
 *
 * @mixin \Eloquent
 */
class Frequency extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return mixed[]
     */
    public static function selectOptions(): array
    {
        $data = [];

        foreach (\Illuminate\Support\Facades\Cache::get('frequencies') as $frequency) {
            $name = \Illuminate\Support\Str::snake(str_replace(' ', '_', $frequency->name));
            $data[$frequency->id] = trans('texts.freq_' . $name);
        }

        return $data;
    }
}

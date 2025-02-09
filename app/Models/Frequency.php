<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Class Frequency.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $date_interval
 *
 * @method static Builder|Frequency newModelQuery()
 * @method static Builder|Frequency newQuery()
 * @method static Builder|Frequency query()
 * @method static Builder|Frequency whereDateInterval($value)
 * @method static Builder|Frequency whereId($value)
 * @method static Builder|Frequency whereName($value)
 *
 * @mixin \Eloquent
 */
class Frequency extends Model
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

    public static function selectOptions(): array
    {
        $data = [];

        foreach (Cache::get('frequencies') as $frequency) {
            $name = Str::snake(str_replace(' ', '_', $frequency->name));
            $data[$frequency->id] = trans('texts.freq_' . $name);
        }

        return $data;
    }
}

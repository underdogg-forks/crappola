<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Str;

/**
 * Class Frequency.
 */
class Frequency extends Eloquent
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public static function selectOptions()
    {
        $data = [];

        foreach (Cache::get('frequencies') as $frequency) {
            $name = Str::snake(str_replace(' ', '_', $frequency->name));
            $data[$frequency->id] = trans('texts.freq_' . $name);
        }

        return $data;
    }
}

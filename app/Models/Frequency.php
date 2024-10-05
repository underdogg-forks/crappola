<?php

namespace App\Models;

/**
 * Class Frequency.
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

<?php

namespace App\Models;

/**
 * Class Timezone.
 *
 * @property int    $id
 * @property string $name
 * @property string $location
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereName($value)
 *
 * @mixin \Eloquent
 */
class Timezone extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Timezone.
 *
 * @property int    $id
 * @property string $name
 * @property string $location
 *
 * @method static Builder|Timezone newModelQuery()
 * @method static Builder|Timezone newQuery()
 * @method static Builder|Timezone query()
 * @method static Builder|Timezone whereId($value)
 * @method static Builder|Timezone whereLocation($value)
 * @method static Builder|Timezone whereName($value)
 *
 * @mixin \Eloquent
 */
class Timezone extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

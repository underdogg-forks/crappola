<?php
namespace App\Models;

use Eloquent;

/**
 * Class Timezone.
 */
class Timezone extends Eloquent
{
    public $table = 'lookup__timezones';
    /**
     * @var bool
     */
    public $timestamps = false;
}

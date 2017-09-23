<?php
namespace App\Models;

use Eloquent;

/**
 * Class Timezone.
 */
class Timezone extends Eloquent
{



    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'lookup__timezones';
    /**
     * @var bool
     */
    public $timestamps = false;
}

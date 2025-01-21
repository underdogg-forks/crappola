<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DatetimeFormat.
 */
class DatetimeFormat extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return bool|string
     */
    public function __toString(): string
    {
        $date = mktime(0, 0, 0, 12, 31, date('Y'));

        return date($this->format, $date);
    }
}

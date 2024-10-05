<?php

namespace App\Models;

/**
 * Class DatetimeFormat.
 */
class DatetimeFormat extends \Illuminate\Database\Eloquent\Model
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

<?php
namespace App\Models;

use Eloquent;

/**
 * Class DatetimeFormat.
 */
class DatetimeFormat extends Eloquent
{
    public $table = 'lookup__datetimeformats';
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return bool|string
     */
    public function __toString()
    {
        $date = mktime(0, 0, 0, 12, 31, date('Y'));
        return date($this->format, $date);
    }
}

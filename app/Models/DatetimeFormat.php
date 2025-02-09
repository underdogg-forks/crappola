<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DatetimeFormat.
 *
 * @property int    $id
 * @property string $format
 * @property string $format_moment
 * @property string $format_dart
 *
 * @method static Builder|DatetimeFormat newModelQuery()
 * @method static Builder|DatetimeFormat newQuery()
 * @method static Builder|DatetimeFormat query()
 * @method static Builder|DatetimeFormat whereFormat($value)
 * @method static Builder|DatetimeFormat whereFormatDart($value)
 * @method static Builder|DatetimeFormat whereFormatMoment($value)
 * @method static Builder|DatetimeFormat whereId($value)
 *
 * @mixin \Eloquent
 */
class DatetimeFormat extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public function __toString(): string
    {
        $date = mktime(0, 0, 0, 12, 31, Carbon::now()->format('Y'));

        return date($this->format, $date);
    }
}

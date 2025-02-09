<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DateFormat.
 *
 * @property int         $id
 * @property string      $format
 * @property string      $picker_format
 * @property string|null $format_moment
 * @property string      $format_dart
 *
 * @method static Builder|DateFormat newModelQuery()
 * @method static Builder|DateFormat newQuery()
 * @method static Builder|DateFormat query()
 * @method static Builder|DateFormat whereFormat($value)
 * @method static Builder|DateFormat whereFormatDart($value)
 * @method static Builder|DateFormat whereFormatMoment($value)
 * @method static Builder|DateFormat whereId($value)
 * @method static Builder|DateFormat wherePickerFormat($value)
 *
 * @mixin \Eloquent
 */
class DateFormat extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public function __toString(): string
    {
        $date = mktime(0, 0, 0, 12, 31, date('Y'));

        return date($this->format, $date);
    }
}

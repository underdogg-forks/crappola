<?php

namespace App\Models;

/**
 * Class DateFormat.
 *
 * @property int         $id
 * @property string      $format
 * @property string      $picker_format
 * @property string|null $format_moment
 * @property string      $format_dart
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat query()
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereFormatDart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereFormatMoment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DateFormat wherePickerFormat($value)
 *
 * @mixin \Eloquent
 */
class DateFormat extends \Illuminate\Database\Eloquent\Model
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

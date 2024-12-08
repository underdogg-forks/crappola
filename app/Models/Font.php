<?php

namespace App\Models;

/**
 * Class Font.
 *
 * @property int    $id
 * @property string $name
 * @property string $folder
 * @property string $css_stack
 * @property int    $css_weight
 * @property string $google_font
 * @property string $normal
 * @property string $bold
 * @property string $italics
 * @property string $bolditalics
 * @property int    $sort_order
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Font newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Font newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Font query()
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereBold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereBolditalics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereCssStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereCssWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereGoogleFont($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereItalics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereNormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Font whereSortOrder($value)
 *
 * @mixin \Eloquent
 */
class Font extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

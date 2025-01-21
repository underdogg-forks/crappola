<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 * @method static Builder|Font newModelQuery()
 * @method static Builder|Font newQuery()
 * @method static Builder|Font query()
 * @method static Builder|Font whereBold($value)
 * @method static Builder|Font whereBolditalics($value)
 * @method static Builder|Font whereCssStack($value)
 * @method static Builder|Font whereCssWeight($value)
 * @method static Builder|Font whereFolder($value)
 * @method static Builder|Font whereGoogleFont($value)
 * @method static Builder|Font whereId($value)
 * @method static Builder|Font whereItalics($value)
 * @method static Builder|Font whereName($value)
 * @method static Builder|Font whereNormal($value)
 * @method static Builder|Font whereSortOrder($value)
 *
 * @mixin \Eloquent
 */
class Font extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

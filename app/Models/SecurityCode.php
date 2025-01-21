<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DatetimeFormat.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int|null    $user_id
 * @property int|null    $contact_id
 * @property int         $attempts
 * @property string|null $code
 * @property string      $bot_user_id
 * @property string      $created_at
 *
 * @method static Builder|SecurityCode newModelQuery()
 * @method static Builder|SecurityCode newQuery()
 * @method static Builder|SecurityCode query()
 * @method static Builder|SecurityCode whereAccountId($value)
 * @method static Builder|SecurityCode whereAttempts($value)
 * @method static Builder|SecurityCode whereBotUserId($value)
 * @method static Builder|SecurityCode whereCode($value)
 * @method static Builder|SecurityCode whereContactId($value)
 * @method static Builder|SecurityCode whereCreatedAt($value)
 * @method static Builder|SecurityCode whereId($value)
 * @method static Builder|SecurityCode whereUserId($value)
 *
 * @mixin \Eloquent
 */
class SecurityCode extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereBotUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SecurityCode whereUserId($value)
 *
 * @mixin \Eloquent
 */
class SecurityCode extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}

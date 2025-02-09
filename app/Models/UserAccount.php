<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAccount.
 *
 * @property int      $id
 * @property int|null $user_id1
 * @property int|null $user_id2
 * @property int|null $user_id3
 * @property int|null $user_id4
 * @property int|null $user_id5
 *
 * @method static Builder|UserAccount newModelQuery()
 * @method static Builder|UserAccount newQuery()
 * @method static Builder|UserAccount query()
 * @method static Builder|UserAccount whereId($value)
 * @method static Builder|UserAccount whereUserId1($value)
 * @method static Builder|UserAccount whereUserId2($value)
 * @method static Builder|UserAccount whereUserId3($value)
 * @method static Builder|UserAccount whereUserId4($value)
 * @method static Builder|UserAccount whereUserId5($value)
 *
 * @mixin \Eloquent
 */
class UserAccount extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param $userId
     */
    public function hasUserId($userId): bool
    {
        if ( ! $userId) {
            return false;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = 'user_id' . $i;
            if ($this->{$field} && $this->{$field} == $userId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $userId
     */
    public function setUserId($userId): void
    {
        if (self::hasUserId($userId)) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = 'user_id' . $i;
            if ( ! $this->{$field}) {
                $this->{$field} = $userId;
                break;
            }
        }
    }

    /**
     * @param $userId
     */
    public function removeUserId($userId): void
    {
        if ( ! $userId || ! self::hasUserId($userId)) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = 'user_id' . $i;
            if ($this->{$field} && $this->{$field} == $userId) {
                $this->{$field} = null;
            }
        }
    }
}

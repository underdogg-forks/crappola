<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class UserAccount.
 */
class UserAccount extends Eloquent
{
    public $timestamps = false;

    /**
     * @param $userId
     *
     * @return bool
     */
    public function hasUserId($userId)
    {
        if ( ! $userId) {
            return false;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id{$i}";
            if ($this->{$field} && $this->{$field} == $userId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $userId
     */
    public function setUserId($userId)
    {
        if (self::hasUserId($userId)) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id{$i}";
            if ( ! $this->{$field}) {
                $this->{$field} = $userId;
                break;
            }
        }
    }

    /**
     * @param $userId
     */
    public function removeUserId($userId)
    {
        if ( ! $userId || ! self::hasUserId($userId)) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id{$i}";
            if ($this->{$field} && $this->{$field} == $userId) {
                $this->{$field} = null;
            }
        }
    }
}

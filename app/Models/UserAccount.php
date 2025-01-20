<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAccount.
 */
class UserAccount extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public function setUserId(array|int|float|string|bool|null $userId): void
    {
        if (self::hasUserId($userId)) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id{$i}";
            if (! $this->$field) {
                $this->$field = $userId;
                break;
            }
        }
    }

    public function hasUserId($userId): bool
    {
        if (! $userId) {
            return false;
        }

        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id{$i}";
            if (! $this->$field) {
                continue;
            }
            if ($this->$field != $userId) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function removeUserId($userId): void
    {
        if (! $userId) {
            return;
        }
        if (! self::hasUserId($userId)) {
            return;
        }
        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id{$i}";
            if (! $this->$field) {
                continue;
            }
            if ($this->$field != $userId) {
                continue;
            }
            $this->$field = null;
        }
    }
}

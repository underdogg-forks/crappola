<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Industry.
 */
class Industry extends Eloquent
{
    public $timestamps = false;

    public function getName()
    {
        return $this->name;
    }
}

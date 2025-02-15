<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Size.
 */
class Size extends Eloquent
{
    public $timestamps = false;

    public function getName()
    {
        return $this->name;
    }
}

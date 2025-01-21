<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Size.
 */
class Size extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}

<?php

namespace App\Models;

use Eloquent;

/**
 * Class Industry.
 */
class Industry extends \Illuminate\Database\Eloquent\Model
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

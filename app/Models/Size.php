<?php
namespace App\Models;

use Eloquent;

/**
 * Class Size.
 */
class Size extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'lookup__sizes';
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

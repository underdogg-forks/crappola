<?php
namespace App\Models;

use Eloquent;

/**
 * Class Industry.
 */
class Industry extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'lookup__industries';
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

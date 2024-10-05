<?php

namespace App\Models;

use Eloquent;

/**
 * Class Affiliate.
 */
class Affiliate extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var bool
     */
    protected $softDelete = true;
}

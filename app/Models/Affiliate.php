<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Affiliate.
 */
class Affiliate extends Model
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

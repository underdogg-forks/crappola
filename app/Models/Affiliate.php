<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Affiliate.
 */
class Affiliate extends Eloquent
{
    public $timestamps = true;

    protected $softDelete = true;

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Bank.
 */
class Bank extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public function getOFXBank($finance): \App\Libraries\Bank
    {
        $config = json_decode($this->config);

        return new \App\Libraries\Bank($finance, $config->fid, $config->url, $config->org);
    }
}

<?php

namespace App\Models;

use Eloquent;

/**
 * Class Bank.
 */
class Bank extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param $finance
     *
     * @return \App\Libraries\Bank
     */
    public function getOFXBank($finance)
    {
        $config = json_decode($this->config);

        return new \App\Libraries\Bank($finance, $config->fid, $config->url, $config->org);
    }
}

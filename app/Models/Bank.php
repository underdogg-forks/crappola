<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Bank.
 *
 * @property int    $id
 * @property string $name
 * @property string $remote_id
 * @property int    $bank_library_id
 * @property string $config
 *
 * @method static Builder|Bank newModelQuery()
 * @method static Builder|Bank newQuery()
 * @method static Builder|Bank query()
 * @method static Builder|Bank whereBankLibraryId($value)
 * @method static Builder|Bank whereConfig($value)
 * @method static Builder|Bank whereId($value)
 * @method static Builder|Bank whereName($value)
 * @method static Builder|Bank whereRemoteId($value)
 *
 * @mixin \Eloquent
 */
class Bank extends Model
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
    public function getOFXBank($finance): \App\Libraries\Bank
    {
        $config = json_decode($this->config);

        return new \App\Libraries\Bank($finance, $config->fid, $config->url, $config->org);
    }
}

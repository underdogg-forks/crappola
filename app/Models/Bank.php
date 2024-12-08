<?php

namespace App\Models;

/**
 * Class Bank.
 *
 * @property int    $id
 * @property string $name
 * @property string $remote_id
 * @property int    $bank_library_id
 * @property string $config
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereBankLibraryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereRemoteId($value)
 *
 * @mixin \Eloquent
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
    public function getOFXBank($finance): \App\Libraries\Bank
    {
        $config = json_decode($this->config);

        return new \App\Libraries\Bank($finance, $config->fid, $config->url, $config->org);
    }
}

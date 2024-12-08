<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 *
 * @property int                            $id
 * @property int                            $db_server_id
 * @property int                            $company_id
 * @property \App\Models\DbServer           $dbServer
 * @property \App\Models\LookupAccount|null $lookupAccount
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LookupCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupCompany whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupCompany whereDbServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupCompany whereId($value)
 *
 * @mixin \Eloquent
 */
class LookupCompany extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'db_server_id',
        'company_id',
    ];

    public function dbServer()
    {
        return $this->belongsTo(\App\Models\DbServer::class);
    }

    public function getDbServer()
    {
        return $this->dbServer->name;
    }
}

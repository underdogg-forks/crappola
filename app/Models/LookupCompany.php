<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @property int                $id
 * @property int                $db_server_id
 * @property int                $company_id
 * @property DbServer           $dbServer
 * @property LookupAccount|null $lookupAccount
 *
 * @method static Builder|LookupCompany newModelQuery()
 * @method static Builder|LookupCompany newQuery()
 * @method static Builder|LookupCompany query()
 * @method static Builder|LookupCompany whereCompanyId($value)
 * @method static Builder|LookupCompany whereDbServerId($value)
 * @method static Builder|LookupCompany whereId($value)
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
        return $this->belongsTo(DbServer::class);
    }

    public function getDbServer()
    {
        return $this->dbServer->name;
    }
}

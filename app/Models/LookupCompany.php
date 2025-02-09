<?php

namespace App\Models;

use DateTimeInterface;

/**
 * Class ExpenseCategory.
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
        return $this->belongsTo('App\Models\DbServer');
    }

    public function getDbServer()
    {
        return $this->dbServer->name;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TicketTemplate extends EntityModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TICKET_TEMPLATE;
    }

    /**
     * @return mixed
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

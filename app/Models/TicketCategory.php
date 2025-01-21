<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TICKET_CATEGORY;
    }
}

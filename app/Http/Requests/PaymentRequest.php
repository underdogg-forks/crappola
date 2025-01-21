<?php

namespace App\Http\Requests;

class PaymentRequest extends EntityRequest
{
    public $entityType = ENTITY_PAYMENT;
}

<?php

namespace App\Constants;

class Domain
{
    public const INVOICENINJA_COM = 1;

    public const INVOICE_SERVICES = 2;

    public static function getDomainFromId($id): string
    {
        return match ($id) {
            static::INVOICENINJA_COM => 'invoiceninja.com',
            static::INVOICE_SERVICES => 'invoice.services',
            default                  => 'invoiceninja.com',
        };
    }

    public static function getLinkFromId($id): string
    {
        return 'https://app.' . static::getDomainFromId($id);
    }

    public static function getEmailFromId($id): string
    {
        return 'maildelivery@' . static::getDomainFromId($id);
    }
}

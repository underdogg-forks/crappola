<?php

namespace App\Constants;

class Domain
{
    public const INVOICENINJA_COM = 1;

    public const INVOICE_SERVICES = 2;

    public static function getLinkFromId($id): string
    {
        return 'https://app.' . static::getDomainFromId($id);
    }

    public static function getDomainFromId($id): string
    {
        switch ($id) {
            case static::INVOICENINJA_COM:
                return 'invoiceninja.com';
            case static::INVOICE_SERVICES:
                return 'invoice.services';
        }

        return 'invoiceninja.com';
    }

    public static function getEmailFromId($id): string
    {
        return 'maildelivery@' . static::getDomainFromId($id);
    }

    public static function getPostmarkTokenFromId($id)
    {
        switch ($id) {
            case static::INVOICENINJA_COM:
                return config('services.postmark_token');
            case static::INVOICE_SERVICES:
                return config('services.postmark_token_2');
        }
    }

    public static function getSupportDomainFromId($id)
    {
        switch ($id) {
            case static::INVOICENINJA_COM:
                return config('ninja.tickets.ticket_support_domain');
            case static::INVOICE_SERVICES:
                return config('ninja.tickets.ticket_support_domain_2');
        }
    }
}

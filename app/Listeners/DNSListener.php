<?php

namespace App\Listeners;

use App\Events\SubdomainWasRemoved;
use App\Events\SubdomainWasUpdated;
use App\Ninja\DNS\Cloudflare;

/**
 * Class DNSListener.
 */
class DNSListener
{
    /**
     * @param DNSListener $event
     */
    public function addDNSRecord(SubdomainWasUpdated $event): void
    {
        if (env('CLOUDFLARE_DNS_ENABLED')) {
            Cloudflare::addDNSRecord($event->company);
        }
    }

    public function removeDNSRecord(SubdomainWasRemoved $event): void
    {
        if (env('CLOUDFLARE_DNS_ENABLED')) {
            Cloudflare::removeDNSRecord($event->company);
        }
    }
}

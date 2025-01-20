<?php

namespace App\Models\Traits;

use Carbon;
use App\Libraries\Utils;

/**
 * Class SendsEmails.
 */
trait Inviteable
{
    // If we're getting the link for PhantomJS to generate the PDF
    // we need to make sure it's served from our site
    /**
     * @param string $type
     * @param bool $forceOnsite
     *
     * @return string
     */
    public function getLink($type = 'view', $forceOnsite = false, $forcePlain = false)
    {
        if (!$this->company) {
            $this->load('company');
        }

        if ($this->proposal_id) {
            $type = 'proposal';
        }

        $company = $this->company;
        $iframe_url = $company->iframe_url;
        $url = trim(SITE_URL, '/');

        if (env('REQUIRE_HTTPS')) {
            $url = str_replace('http://', 'https://', $url);
        }

        if ($company->hasFeature(FEATURE_CUSTOM_URL)) {
            if (Utils::isNinjaProd() && !Utils::isReseller()) {
                $url = $company->present()->clientPortalLink();
            }

            if ($iframe_url && !$forceOnsite) {
                if ($company->is_custom_domain) {
                    $url = $iframe_url;
                } else {
                    return "{$iframe_url}?{$this->invitation_key}/{$type}";
                }
            } elseif ($this->company->subdomain && !$forcePlain) {
                $url = Utils::replaceSubdomain($url, $company->subdomain);
            }
        }

        return "{$url}/{$type}/{$this->invitation_key}";
    }

    /**
     * @return bool|string
     */
    public function getStatus()
    {
        $hasValue = false;
        $parts = [];
        $statuses = $this->message_id ? ['sent', 'opened', 'viewed'] : ['sent', 'viewed'];

        foreach ($statuses as $status) {
            $field = "{$status}_date";
            $date = '';
            if ($this->$field && $this->field != '0000-00-00 00:00:00') {
                $date = Utils::dateToString($this->$field);
                $hasValue = true;
                $parts[] = trans('texts.invitation_status_' . $status) . ': ' . $date;
            }
        }

        return $hasValue ? implode($parts, '<br/>') : false;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->invitation_key;
    }

    /**
     * @param null $messageId
     */
    public function markSent($messageId = null): void
    {
        $this->message_id = $messageId;
        $this->email_error = null;
        $this->sent_date = Carbon::now()->toDateTimeString();
        $this->save();
    }

    public function isSent()
    {
        return $this->sent_date && $this->sent_date != '0000-00-00 00:00:00';
    }

    public function markViewed(): void
    {
        $this->viewed_date = Carbon::now()->toDateTimeString();
        $this->save();

        if ($this->invoice) {
            $invoice = $this->invoice;
            $client = $invoice->client;

            $invoice->markViewed();
            $client->markLoggedIn();
        }
    }
}

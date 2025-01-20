<?php

namespace App\Listeners;

use App;
use App\Events\PaymentWasCreated;
use App\Libraries\Utils;

/**
 * Class AnalyticsListener.
 */
class AnalyticsListener
{
    /**
     * @param PaymentWasCreated $event
     */
    public function trackRevenue(PaymentWasCreated $event): void
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;
        $company = $payment->company;

        $analyticsId = false;

        if ($company->isNinjaAccount() || $company->account_key == NINJA_LICENSE_ACCOUNT_KEY) {
            $analyticsId = env('ANALYTICS_KEY');
        } else {
            if (Utils::isNinja()) {
                $analyticsId = $company->analytics_key;
            } else {
                $analyticsId = $company->analytics_key ?: env('ANALYTICS_KEY');
            }
        }

        if (!$analyticsId) {
            return;
        }

        $client = $payment->client;
        $amount = $payment->amount;
        $item = $invoice->invoice_items->last()->product_key;
        $currencyCode = $client->getCurrencyCode();

        if ($company->isNinjaAccount() && App::runningInConsole()) {
            $item .= ' [R]';
        }

        $base = "v=1&tid={$analyticsId}&cid={$client->public_id}&cu={$currencyCode}&ti={$invoice->invoice_number}";

        $url = $base . "&t=transaction&ta=ninja&tr={$amount}";
        $this->sendAnalytics($url);

        $url = $base . "&t=item&in={$item}&ip={$amount}&iq=1";
        $this->sendAnalytics($url);
    }

    /**
     * @param $data
     */
    private function sendAnalytics($data): void
    {
        $data = utf8_encode($data);
        $curl = curl_init();

        $opts = [
            CURLOPT_URL => GOOGLE_ANALYITCS_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ];

        curl_setopt_array($curl, $opts);
        curl_exec($curl);
        curl_close($curl);
    }
}

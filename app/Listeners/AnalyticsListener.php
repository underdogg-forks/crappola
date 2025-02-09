<?php

namespace App\Listeners;

use App\Events\PaymentWasCreated;
use App\Libraries\Utils;
use Illuminate\Support\Facades\App;

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
        $account = $payment->account;

        $analyticsId = false;

        if ($account->isNinjaAccount() || $account->account_key == NINJA_LICENSE_ACCOUNT_KEY) {
            $analyticsId = env('ANALYTICS_KEY');
        } elseif (Utils::isNinja()) {
            $analyticsId = $account->analytics_key;
        } else {
            $analyticsId = $account->analytics_key ?: env('ANALYTICS_KEY');
        }

        if ( ! $analyticsId) {
            return;
        }

        $client = $payment->client;
        $amount = $payment->amount;
        $item = $invoice->invoice_items->last()->product_key;
        $currencyCode = $client->getCurrencyCode();

        if ($account->isNinjaAccount() && App::runningInConsole()) {
            $item .= ' [R]';
        }

        $base = sprintf('v=1&tid=%s&cid=%s&cu=%s&ti=%s', $analyticsId, $client->public_id, $currencyCode, $invoice->invoice_number);

        $url = $base . ('&t=transaction&ta=ninja&tr=' . $amount);
        $this->sendAnalytics($url);

        $url = $base . sprintf('&t=item&in=%s&ip=%s&iq=1', $item, $amount);
        $this->sendAnalytics($url);
    }

    /**
     * @param $data
     */
    private function sendAnalytics(string $data): void
    {
        $data = utf8_encode($data);
        $curl = curl_init();

        $opts = [
            CURLOPT_URL            => GOOGLE_ANALYITCS_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => 'POST',
            CURLOPT_POSTFIELDS     => $data,
        ];

        curl_setopt_array($curl, $opts);
        curl_exec($curl);
        curl_close($curl);
    }
}

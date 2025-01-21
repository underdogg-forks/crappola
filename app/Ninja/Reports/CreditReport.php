<?php

namespace App\Ninja\Reports;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class CreditReport extends AbstractReport
{
    public function getColumns()
    {
        $columns = [
            'client'  => [],
            'amount'  => [],
            'balance' => [],
            'user'    => ['columnSelector-false'],
        ];

        return $columns;
    }

    public function run(): void
    {
        $company = Auth::user()->company;
        $subgroup = $this->options['subgroup'];

        $clients = Client::scope()
            ->orderBy('name')
            ->withArchived()
            ->with(['contacts', 'user', 'credits' => function ($query): void {
                $query->where('credit_date', '>=', $this->startDate)
                    ->where('credit_date', '<=', $this->endDate)
                    ->withArchived();
            }]);

        foreach ($clients->get() as $client) {
            $amount = 0;
            $balance = 0;

            foreach ($client->credits as $credit) {
                $amount += $credit->amount;
                $balance += $credit->balance;

                $dimension = $this->getDimension($client);
                $this->addChartData($dimension, $credit->credit_date, $credit->amount);
            }

            if (! $amount && ! $balance) {
                continue;
            }

            $row = [
                $this->isExport ? $client->getDisplayName() : $client->present()->link,
                $company->formatMoney($amount, $client),
                $company->formatMoney($balance, $client),
                $client->user->getDisplayName(),
            ];

            $this->data[] = $row;

            $this->addToTotals($client->currency_id, 'amount', $amount);
            $this->addToTotals($client->currency_id, 'balance', $balance);
        }
    }
}

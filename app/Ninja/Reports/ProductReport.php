<?php

namespace App\Ninja\Reports;

use App\Libraries\Utils;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ProductReport extends AbstractReport
{
    public function getColumns()
    {
        $columns = [
            'client'         => [],
            'invoice_number' => [],
            'invoice_date'   => [],
            'product'        => [],
            'description'    => [],
            'qty'            => [],
            'cost'           => [],
            //'tax_rate1',
            //'tax_rate2',
        ];

        $company = auth()->user()->company;

        if ($company->invoice_item_taxes) {
            $columns['tax'] = ['columnSelector-false'];
            if ($company->enable_second_tax_rate) {
                $columns['tax'] = ['columnSelector-false'];
            }
        }

        if ($company->customLabel('product1')) {
            $columns[$company->present()->customLabel('product1')] = ['columnSelector-false', 'custom'];
        }

        if ($company->customLabel('product2')) {
            $columns[$company->present()->customLabel('product2')] = ['columnSelector-false', 'custom'];
        }

        return $columns;
    }

    public function run(): void
    {
        $company = Auth::user()->company;
        $statusIds = $this->options['status_ids'];
        $subgroup = $this->options['subgroup'];

        $clients = Client::scope()
            ->orderBy('name')
            ->withArchived()
            ->with('contacts', 'user')
            ->with(['invoices' => function ($query) use ($statusIds): void {
                $query->invoices()
                    ->withArchived()
                    ->statusIds($statusIds)
                    ->where('invoice_date', '>=', $this->startDate)
                    ->where('invoice_date', '<=', $this->endDate)
                    ->with(['invoice_items']);
            }]);

        foreach ($clients->get() as $client) {
            foreach ($client->invoices as $invoice) {
                foreach ($invoice->invoice_items as $item) {
                    $row = [
                        $this->isExport ? $client->getDisplayName() : $client->present()->link,
                        $this->isExport ? $invoice->invoice_number : $invoice->present()->link,
                        $this->isExport ? $invoice->invoice_date : $invoice->present()->invoice_date,
                        $item->product_key,
                        $item->notes,
                        $item->qty + 0,
                        Utils::roundSignificant($item->cost, 2),
                    ];

                    if ($company->invoice_item_taxes) {
                        $row[] = Utils::roundSignificant($item->getTaxAmount(), 2);
                    }

                    if ($company->customLabel('product1')) {
                        $row[] = $item->custom_value1;
                    }

                    if ($company->customLabel('product2')) {
                        $row[] = $item->custom_value2;
                    }

                    $this->data[] = $row;

                    if ($subgroup == 'product') {
                        $dimension = $item->product_key;
                    } else {
                        $dimension = $this->getDimension($client);
                    }

                    $this->addChartData($dimension, $invoice->invoice_date, $invoice->amount);
                    $this->addToTotals($client->currency_id, 'total', $item->qty * $item->cost);
                }
            }
        }
    }
}

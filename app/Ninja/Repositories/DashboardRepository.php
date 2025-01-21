<?php

namespace App\Ninja\Repositories;

use App\Models\Activity;
use App\Models\Task;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardRepository
{
    /**
     * @param mixed $company
     * @param mixed $currencyId
     * @param mixed $includeExpenses
     *
     * @return array
     */
    public function chartData($company, $groupBy, $startDate, $endDate, $currencyId, $includeExpenses)
    {
        $companyId = $company->id;
        $startDate = date_create($startDate);
        $endDate = date_create($endDate);
        $groupBy = strtoupper($groupBy);
        if ($groupBy == 'DAY') {
            $groupBy = 'DAYOFYEAR';
        }

        $datasets = [];
        $labels = [];
        $totals = new stdClass();

        $entitTypes = [ENTITY_INVOICE, ENTITY_PAYMENT];
        if ($includeExpenses) {
            $entitTypes[] = ENTITY_EXPENSE;
        }

        foreach ($entitTypes as $entityType) {
            $data = [];
            $count = 0;
            $balance = 0;

            if ($currencyId == 'totals') {
                $records = $this->rawChartDataTotals($entityType, $company, $groupBy, $startDate, $endDate, $company->currency->id);
            } else {
                $records = $this->rawChartData($entityType, $company, $groupBy, $startDate, $endDate, $currencyId);
            }

            array_map(function ($item) use (&$data, &$count, &$balance, $groupBy): void {
                $data[$item->$groupBy] = $item->total;
                $count += $item->count;
                $balance += isset($item->balance) ? $item->balance : 0;
            }, $records);

            $padding = $groupBy == 'DAYOFYEAR' ? 'day' : ($groupBy == 'WEEK' ? 'week' : 'month');
            $endDate->modify('+1 ' . $padding);
            $interval = new DateInterval('P1' . substr($groupBy, 0, 1));
            $period = new DatePeriod($startDate, $interval, $endDate);
            $endDate->modify('-1 ' . $padding);
            $records = [];

            foreach ($period as $d) {
                $dateFormat = $groupBy == 'DAYOFYEAR' ? 'z' : ($groupBy == 'WEEK' ? 'W' : 'n');
                if ($groupBy == 'DAYOFYEAR') {
                    // MySQL returns 1-366 for DAYOFYEAR, whereas PHP returns 0-365
                    $date = $d->format('Y') . ($d->format($dateFormat) + 1);
                } elseif ($groupBy == 'WEEK' && ($d->format($dateFormat) < 10)) {
                    // PHP zero pads the week
                    $date = $d->format('Y') . round($d->format($dateFormat));
                } else {
                    $date = $d->format('Y' . $dateFormat);
                }
                $records[] = isset($data[$date]) ? $data[$date] : 0;

                if ($entityType == ENTITY_INVOICE) {
                    $labels[] = $d->format('m/d/Y');
                }
            }

            if ($entityType == ENTITY_INVOICE) {
                $color = '51,122,183';
            } elseif ($entityType == ENTITY_PAYMENT) {
                $color = '54,193,87';
            } elseif ($entityType == ENTITY_EXPENSE) {
                $color = '128,128,128';
            }

            $record = new stdClass();
            $record->data = $records;
            $record->label = trans("texts.{$entityType}s");
            $record->lineTension = 0;
            $record->borderWidth = 4;
            $record->borderColor = "rgba({$color}, 1)";
            $record->backgroundColor = "rgba({$color}, 0.1)";
            $datasets[] = $record;

            if ($entityType == ENTITY_INVOICE) {
                $totals->invoices = array_sum($data);
                $totals->average = $count ? round($totals->invoices / $count, 2) : 0;
                $totals->balance = $balance;
            } elseif ($entityType == ENTITY_PAYMENT) {
                $totals->revenue = array_sum($data);
            } elseif ($entityType == ENTITY_EXPENSE) {
                //$totals->profit = $totals->revenue - array_sum($data);
                $totals->expenses = array_sum($data);
            }
        }

        $data = new stdClass();
        $data->labels = $labels;
        $data->datasets = $datasets;

        $response = new stdClass();
        $response->data = $data;
        $response->totals = $totals;

        return $response;
    }

    private function rawChartDataTotals($entityType, $company, $groupBy, $startDate, $endDate, $currencyId)
    {
        if (! in_array($groupBy, ['DAYOFYEAR', 'WEEK', 'MONTH'])) {
            return [];
        }

        [$timeframe, $records] = $this->rawChartDataPrepare($entityType, $company, $groupBy, $startDate, $endDate);

        if ($entityType == ENTITY_INVOICE) {
            // as default invoice exchange rate column we use just 1 value
            $invoiceExchangeRateColumn = 1;
            if ($exchageRateCustomFieldIndex = $company->getInvoiceExchangeRateCustomFieldIndex()) {
                $invoiceExchangeRateColumn = 'invoices.custom_text_value' . $exchageRateCustomFieldIndex;
            }

            $records->select(DB::raw('sum(if(clients.currency_id = ' . $currencyId . ' OR clients.currency_id is null, invoices.amount, invoices.amount / ' . $invoiceExchangeRateColumn . ')) as total, sum(if(clients.currency_id = ' . $currencyId . ' OR clients.currency_id is null, invoices.balance, invoices.balance / ' . $invoiceExchangeRateColumn . ')) as balance, count(invoices.id) as count, ' . $timeframe . ' as ' . $groupBy))
                ->where('invoice_type_id', '=', INVOICE_TYPE_STANDARD)

                ->where('is_recurring', '=', false);
        } elseif ($entityType == ENTITY_PAYMENT) {
            $records->select(DB::raw('sum(if(clients.currency_id = ' . $currencyId . ' OR clients.currency_id is null, payments.amount - payments.refunded, (payments.amount - payments.refunded) * payments.exchange_rate)) as total, count(payments.id) as count, ' . $timeframe . ' as ' . $groupBy))
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->where('invoices.is_deleted', '=', false)
                ->whereNotIn('payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);
        } elseif ($entityType == ENTITY_EXPENSE) {
            $records->select(DB::raw('if(expenses.invoice_currency_id = ' . $currencyId . ' OR clients.currency_id is null, sum(expenses.amount + (expenses.amount * expenses.tax_rate1 / 100) + (expenses.amount * expenses.tax_rate2 / 100)), sum(expenses.amount * expenses.exchange_rate + (expenses.amount * expenses.exchange_rate * expenses.tax_rate1 / 100) + (expenses.amount * expenses.exchange_rate * expenses.tax_rate2 / 100))) as total, count(expenses.id) as count, ' . $timeframe . ' as ' . $groupBy));
        }

        return $records->get()->all();
    }

    /**
     * @return array $timeframe, $records
     */
    private function rawChartDataPrepare($entityType, $company, $groupBy, $startDate, $endDate)
    {
        $companyId = $company->id;
        $timeframe = 'concat(YEAR(' . $entityType . '_date), ' . $groupBy . '(' . $entityType . '_date))';

        $records = DB::table($entityType . 's')
            ->leftJoin('clients', 'clients.id', '=', $entityType . 's.client_id')
            ->whereRaw('(clients.id IS NULL OR clients.is_deleted = 0)')
            ->where($entityType . 's.company_id', '=', $companyId)
            ->where($entityType . 's.is_deleted', '=', false)
            ->where($entityType . 's.' . $entityType . '_date', '>=', $startDate->format('Y-m-d'))
            ->where($entityType . 's.' . $entityType . '_date', '<=', $endDate->format('Y-m-d'))
            ->groupBy($groupBy);

        return [$timeframe, $records];
    }

    private function rawChartData($entityType, $company, $groupBy, $startDate, $endDate, $currencyId)
    {
        if (! in_array($groupBy, ['DAYOFYEAR', 'WEEK', 'MONTH'])) {
            return [];
        }

        [$timeframe, $records] = $this->rawChartDataPrepare($entityType, $company, $groupBy, $startDate, $endDate);

        if ($entityType == ENTITY_EXPENSE) {
            $records->where('expenses.invoice_currency_id', '=', $currencyId);
        } elseif ($currencyId == $company->getCurrencyId()) {
            $records->whereRaw("(clients.currency_id = {$currencyId} or coalesce(clients.currency_id, 0) = 0)");
        } else {
            $records->where('clients.currency_id', '=', $currencyId);
        }

        if ($entityType == ENTITY_INVOICE) {
            $records->select(DB::raw('sum(invoices.amount) as total, sum(invoices.balance) as balance, count(invoices.id) as count, ' . $timeframe . ' as ' . $groupBy))
                ->where('invoice_type_id', '=', INVOICE_TYPE_STANDARD)

                ->where('is_recurring', '=', false);
        } elseif ($entityType == ENTITY_PAYMENT) {
            $records->select(DB::raw('sum(payments.amount - payments.refunded) as total, count(payments.id) as count, ' . $timeframe . ' as ' . $groupBy))
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->where('invoices.is_deleted', '=', false)
                ->whereNotIn('payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);
        } elseif ($entityType == ENTITY_EXPENSE) {
            $records->select(DB::raw('sum(expenses.amount + (expenses.amount * expenses.tax_rate1 / 100) + (expenses.amount * expenses.tax_rate2 / 100)) as total, count(expenses.id) as count, ' . $timeframe . ' as ' . $groupBy));
        }

        return $records->get()->all();
    }

    public function totals($companyId, $userId, $viewAll): Model|Builder|null
    {
        // total_income, billed_clients, invoice_sent and active_clients
        $select = DB::raw(
            'COUNT(DISTINCT CASE WHEN ' . DB::getQueryGrammar()->wrap('invoices.id', true) . ' IS NOT NULL THEN ' . DB::getQueryGrammar()->wrap('clients.id', true) . ' ELSE null END) billed_clients,
            SUM(CASE WHEN ' . DB::getQueryGrammar()->wrap('invoices.invoice_status_id', true) . ' >= ' . INVOICE_STATUS_SENT . ' THEN 1 ELSE 0 END) invoices_sent,
            COUNT(DISTINCT ' . DB::getQueryGrammar()->wrap('clients.id', true) . ') active_clients'
        );

        $metrics = DB::table('companies')
            ->select($select)
            ->leftJoin('clients', 'companies.id', '=', 'clients.company_id')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('companies.id', '=', $companyId)
            ->where('clients.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('invoices.is_recurring', '=', false)
            ->where('invoices.invoice_type_id', '=', INVOICE_TYPE_STANDARD);

        if (! $viewAll) {
            $metrics = $metrics->where(function ($query) use ($userId): void {
                $query->where('invoices.user_id', '=', $userId);
                $query->orwhere(function ($query) use ($userId): void {
                    $query->where('invoices.user_id', '=', null);
                    $query->where('clients.user_id', '=', $userId);
                });
            });
        }

        return $metrics->groupBy('companies.id')->first();
    }

    public function paidToDate($company, $userId, $viewAll, $startDate = false)
    {
        $select = DB::raw(
            'SUM(' . DB::getQueryGrammar()->wrap('payments.amount', true) . ' - ' . DB::getQueryGrammar()->wrap('payments.refunded', true) . ') as value,'
            . 'IFNULL(' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ', ' . $company->currency->id . ') as currency_id,'
            . DB::getQueryGrammar()->wrap('payments.exchange_rate', true) . ' as exchange_rate'
        );
        $paidToDate = DB::table('payments')
            ->select($select)
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->where('payments.company_id', '=', $company->id)
            ->where('clients.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('payments.is_deleted', '=', false)
            ->whereNotIn('payments.payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);

        if (! $viewAll) {
            $paidToDate->where('invoices.user_id', '=', $userId);
        }

        if ($startDate) {
            $paidToDate->where('payments.payment_date', '>=', $startDate);
        } elseif ($startDate = $company->financialYearStart()) {
            //$paidToDate->where('payments.payment_date', '>=', $startDate);
        }

        return $paidToDate->groupBy('payments.company_id')
            ->groupBy(DB::raw('CASE WHEN ' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' IS NULL THEN ' . ($company->currency_id ?: DEFAULT_CURRENCY) . ' ELSE ' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' END'))
            ->get();
    }

    public function averages($company, $userId, $viewAll)
    {
        // as default invoice exchange rate column we use just 1 value
        $invoiceExchangeRateColumn = 1;
        if ($exchageRateCustomFieldIndex = $company->getInvoiceExchangeRateCustomFieldIndex()) {
            $invoiceExchangeRateColumn = DB::getQueryGrammar()->wrap('invoices.custom_text_value' . $exchageRateCustomFieldIndex, true);
        }

        $select = DB::raw(
            'AVG(' . DB::getQueryGrammar()->wrap('invoices.amount', true) . ') as invoice_avg, '
            . 'IFNULL(' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ', ' . $company->currency->id . ') as currency_id,'
            . $invoiceExchangeRateColumn . ' as exchange_rate,'
            . 'COUNT(*)  as invoice_count'
        );
        $averageInvoice = DB::table('companies')
            ->select($select)
            ->leftJoin('clients', 'companies.id', '=', 'clients.company_id')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('companies.id', '=', $company->id)
            ->where('clients.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)

            ->where('invoices.invoice_type_id', '=', INVOICE_TYPE_STANDARD)
            ->where('invoices.is_recurring', '=', false);

        if (! $viewAll) {
            $averageInvoice->where('invoices.user_id', '=', $userId);
        }

        if ($startDate = $company->financialYearStart()) {
            //$averageInvoice->where('invoices.invoice_date', '>=', $startDate);
        }

        return $averageInvoice->groupBy('companies.id')
            ->groupBy(DB::raw('CASE WHEN ' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' IS NULL THEN CASE WHEN ' . DB::getQueryGrammar()->wrap('companies.currency_id', true) . ' IS NULL THEN 1 ELSE ' . DB::getQueryGrammar()->wrap('companies.currency_id', true) . ' END ELSE ' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' END'))
            ->get();
    }

    public function balances($company, $userId, $viewAll)
    {
        $select = DB::raw(
            'SUM(' . DB::getQueryGrammar()->wrap('clients.balance', true) . ') as value, '
            . 'IFNULL(' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ', ' . $company->currency->id . ') as currency_id'
        );
        $balances = DB::table('companies')
            ->select($select)
            ->leftJoin('clients', 'companies.id', '=', 'clients.company_id')
            ->where('companies.id', '=', $company->id)
            ->where('clients.is_deleted', '=', false)
            ->groupBy('companies.id')
            ->groupBy(DB::raw('CASE WHEN ' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' IS NULL THEN CASE WHEN ' . DB::getQueryGrammar()->wrap('companies.currency_id', true) . ' IS NULL THEN 1 ELSE ' . DB::getQueryGrammar()->wrap('companies.currency_id', true) . ' END ELSE ' . DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' END'));

        if (! $viewAll) {
            $balances->where('clients.user_id', '=', $userId);
        }

        return $balances->get();
    }

    public function activities($companyId, $userId, $viewAll)
    {
        $activities = Activity::where('activities.company_id', '=', $companyId)
            ->where('activities.activity_type_id', '>', 0);

        if (! $viewAll) {
            $activities = $activities->where('activities.user_id', '=', $userId);
        }

        return $activities->orderBy('activities.created_at', 'desc')->orderBy('activities.id', 'desc')
            ->with('client.contacts', 'user', 'invoice', 'payment', 'credit', 'company', 'task', 'expense', 'contact')
            ->take(100)
            ->get();
    }

    public function pastDue($companyId, $userId, $viewAll)
    {
        $pastDue = DB::table('invoices')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->where('invoices.company_id', '=', $companyId)
            ->where('clients.deleted_at', '=', null)
            ->where('contacts.deleted_at', '=', null)
            ->where('invoices.is_recurring', '=', false)
            ->where('invoices.quote_invoice_id', '=', null)
            ->where('invoices.balance', '>', 0)
            ->where('invoices.is_deleted', '=', false)
            ->where('invoices.deleted_at', '=', null)

            ->where('contacts.is_primary', '=', true)
            ->where(DB::raw('coalesce(invoices.partial_due_date, invoices.due_at)'), '<', date('Y-m-d'));

        if (! $viewAll) {
            $pastDue = $pastDue->where('invoices.user_id', '=', $userId);
        }

        return $pastDue->select([DB::raw('coalesce(invoices.partial_due_date, invoices.due_at) due_date'), 'invoices.balance', 'invoices.invoice_number', 'clients.name as client_name', 'contacts.email', 'contacts.first_name', 'contacts.last_name', 'clients.currency_id', 'clients.user_id as client_user_id', 'invoice_type_id'])
            ->orderBy('invoices.due_at', 'asc')
            ->take(100)
            ->get();
    }

    public function upcoming($companyId, $userId, $viewAll)
    {
        $upcoming = DB::table('invoices')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->where('invoices.company_id', '=', $companyId)
            ->where('clients.deleted_at', '=', null)
            ->where('contacts.deleted_at', '=', null)
            ->where('invoices.deleted_at', '=', null)
            ->where('invoices.is_recurring', '=', false)
            ->where('invoices.quote_invoice_id', '=', null)
            ->where('invoices.balance', '>', 0)
            ->where('invoices.is_deleted', '=', false)

            ->where('contacts.is_primary', '=', true)
            ->where(function ($query): void {
                $query->where(DB::raw('coalesce(invoices.partial_due_date, invoices.due_at)'), '>=', date('Y-m-d'))
                    ->orWhereNull('invoices.due_at');
            })
            ->orderBy('invoices.due_at', 'asc');

        if (! $viewAll) {
            $upcoming = $upcoming->where('invoices.user_id', '=', $userId);
        }

        return $upcoming->take(100)
            ->select([DB::raw('coalesce(invoices.partial_due_date, invoices.due_at) due_date'), 'invoices.balance', 'invoices.invoice_number', 'clients.name as client_name', 'contacts.email', 'contacts.first_name', 'contacts.last_name', 'clients.currency_id', 'clients.user_id as client_user_id', 'invoice_type_id'])
            ->get();
    }

    public function payments($companyId, $userId, $viewAll)
    {
        $payments = DB::table('payments')
            ->leftJoin('clients', 'clients.id', '=', 'payments.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->where('payments.company_id', '=', $companyId)
            ->where('payments.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('clients.is_deleted', '=', false)
            ->where('contacts.deleted_at', '=', null)
            ->where('contacts.is_primary', '=', true)
            ->whereNotIn('payments.payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);

        if (! $viewAll) {
            $payments = $payments->where('payments.user_id', '=', $userId);
        }

        return $payments->select(['payments.payment_date', DB::raw('(payments.amount - payments.refunded) as amount'), 'invoices.invoice_number', 'clients.name as client_name', 'contacts.email', 'contacts.first_name', 'contacts.last_name', 'clients.currency_id', 'clients.user_id as client_user_id'])
            ->orderBy('payments.payment_date', 'desc')
            ->take(100)
            ->get();
    }

    public function expenses($company, $userId, $viewAll)
    {
        $amountField = DB::getQueryGrammar()->wrap('expenses.amount', true);
        $taxRate1Field = DB::getQueryGrammar()->wrap('expenses.tax_rate1', true);
        $taxRate2Field = DB::getQueryGrammar()->wrap('expenses.tax_rate2', true);

        $select = DB::raw(
            "SUM({$amountField} + ({$amountField} * {$taxRate1Field} / 100) + ({$amountField} * {$taxRate2Field} / 100)) as value,"
            . DB::getQueryGrammar()->wrap('expenses.invoice_currency_id', true) . ' as currency_id,'
            . DB::getQueryGrammar()->wrap('expenses.exchange_rate', true) . ' as exchange_rate'
        );
        $expenses = DB::table('companies')
            ->select($select)
            ->leftJoin('expenses', 'companies.id', '=', 'expenses.company_id')
            ->where('companies.id', '=', $company->id)
            ->where('expenses.is_deleted', '=', false);

        if (! $viewAll) {
            $expenses = $expenses->where('expenses.user_id', '=', $userId);
        }

        if ($startDate = $company->financialYearStart()) {
            //$expenses->where('expenses.expense_date', '>=', $startDate);
        }

        return $expenses->groupBy('companies.id')
            ->groupBy('expenses.invoice_currency_id')
            ->get();
    }

    public function tasks($companyId, $userId, $viewAll)
    {
        return Task::scope()
            ->withArchived()
            ->whereIsRunning(true)
            ->get();
    }
}

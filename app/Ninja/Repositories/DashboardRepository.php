<?php

namespace App\Ninja\Repositories;

use App\Models\Activity;
use App\Models\Invoice;
use App\Models\Task;
use DateInterval;
use DatePeriod;
use DateTime;
use stdClass;

class DashboardRepository
{
    /**
     * @param       $groupBy
     * @param       $startDate
     * @param       $endDate
     * @param mixed $account
     * @param mixed $currencyId
     * @param mixed $includeExpenses
     *
     * @return array
     */
    public function chartData($account, $groupBy, $startDate, $endDate, $currencyId, $includeExpenses): stdClass
    {
        $accountId = $account->id;
        $startDate = date_create($startDate);
        $endDate = date_create($endDate);
        $groupBy = mb_strtoupper($groupBy);
        if ($groupBy === 'DAY') {
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
            $records = $this->rawChartData($entityType, $account, $groupBy, $startDate, $endDate, $currencyId);

            array_map(function ($item) use (&$data, &$count, &$balance, $groupBy): void {
                $data[$item->{$groupBy}] = $item->total;
                $count += $item->count;
                $balance += $item->balance ?? 0;
            }, $records);

            $padding = $groupBy === 'DAYOFYEAR' ? 'day' : ($groupBy === 'WEEK' ? 'week' : 'month');
            $endDate->modify('+1 ' . $padding);
            $interval = new DateInterval('P1' . mb_substr($groupBy, 0, 1));
            $period = new DatePeriod($startDate, $interval, $endDate);
            $endDate->modify('-1 ' . $padding);
            $records = [];

            foreach ($period as $d) {
                $dateFormat = $groupBy === 'DAYOFYEAR' ? 'z' : ($groupBy === 'WEEK' ? 'W' : 'n');
                if ($groupBy === 'DAYOFYEAR') {
                    // MySQL returns 1-366 for DAYOFYEAR, whereas PHP returns 0-365
                    $date = $d->format('Y') . ($d->format($dateFormat) + 1);
                } elseif ($groupBy === 'WEEK' && ($d->format($dateFormat) < 10)) {
                    // PHP zero pads the week
                    $date = $d->format('Y') . round($d->format($dateFormat));
                } else {
                    $date = $d->format('Y' . $dateFormat);
                }

                $records[] = $data[$date] ?? 0;

                if ($entityType === ENTITY_INVOICE) {
                    $labels[] = $d->format('m/d/Y');
                }
            }

            if ($entityType === ENTITY_INVOICE) {
                $color = '51,122,183';
            } elseif ($entityType === ENTITY_PAYMENT) {
                $color = '54,193,87';
            } elseif ($entityType === ENTITY_EXPENSE) {
                $color = '128,128,128';
            }

            $record = new stdClass();
            $record->data = $records;
            $record->label = trans(sprintf('texts.%ss', $entityType));
            $record->lineTension = 0;
            $record->borderWidth = 4;
            $record->borderColor = sprintf('rgba(%s, 1)', $color);
            $record->backgroundColor = sprintf('rgba(%s, 0.1)', $color);
            $datasets[] = $record;

            if ($entityType === ENTITY_INVOICE) {
                $totals->invoices = array_sum($data);
                $totals->average = $count ? round($totals->invoices / $count, 2) : 0;
                $totals->balance = $balance;
            } elseif ($entityType === ENTITY_PAYMENT) {
                $totals->revenue = array_sum($data);
            } elseif ($entityType === ENTITY_EXPENSE) {
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

    public function totals($accountId, $userId, $viewAll)
    {
        // total_income, billed_clients, invoice_sent and active_clients
        $select = \Illuminate\Support\Facades\DB::raw(
            'COUNT(DISTINCT CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('invoices.id', true) . ' IS NOT NULL THEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.id', true) . ' ELSE null END) billed_clients,
            SUM(CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('invoices.invoice_status_id', true) . ' >= ' . INVOICE_STATUS_SENT . ' THEN 1 ELSE 0 END) invoices_sent,
            COUNT(DISTINCT ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.id', true) . ') active_clients'
        );

        $metrics = \Illuminate\Support\Facades\DB::table('accounts')
            ->select($select)
            ->leftJoin('clients', 'accounts.id', '=', 'clients.account_id')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('accounts.id', '=', $accountId)
            ->where('clients.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('invoices.is_recurring', '=', false)
            ->where('invoices.is_public', '=', true)
            ->where('invoices.invoice_type_id', '=', INVOICE_TYPE_STANDARD);

        if ( ! $viewAll) {
            $metrics = $metrics->where(function ($query) use ($userId): void {
                $query->where('invoices.user_id', '=', $userId);
                $query->orwhere(function ($query) use ($userId): void {
                    $query->where('invoices.user_id', '=', null);
                    $query->where('clients.user_id', '=', $userId);
                });
            });
        }

        return $metrics->groupBy('accounts.id')->first();
    }

    public function paidToDate($account, $userId, $viewAll, $startDate = false)
    {
        $accountId = $account->id;
        $select = \Illuminate\Support\Facades\DB::raw(
            'SUM(' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('payments.amount', true) . ' - ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('payments.refunded', true) . ') as value,'
                  . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' as currency_id'
        );
        $paidToDate = \Illuminate\Support\Facades\DB::table('payments')
            ->select($select)
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->where('payments.account_id', '=', $accountId)
            ->where('clients.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('payments.is_deleted', '=', false)
            ->whereNotIn('payments.payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);

        if ( ! $viewAll) {
            $paidToDate->where('invoices.user_id', '=', $userId);
        }

        if ($startDate) {
            $paidToDate->where('payments.payment_date', '>=', $startDate);
        } elseif ($startDate = $account->financialYearStart()) {
            //$paidToDate->where('payments.payment_date', '>=', $startDate);
        }

        return $paidToDate->groupBy('payments.account_id')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' IS NULL THEN ' . ($account->currency_id ?: DEFAULT_CURRENCY) . ' ELSE ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' END'))
            ->get();
    }

    public function averages($account, $userId, $viewAll)
    {
        $accountId = $account->id;
        $select = \Illuminate\Support\Facades\DB::raw(
            'AVG(' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('invoices.amount', true) . ') as invoice_avg, '
                  . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' as currency_id'
        );
        $averageInvoice = \Illuminate\Support\Facades\DB::table('accounts')
            ->select($select)
            ->leftJoin('clients', 'accounts.id', '=', 'clients.account_id')
            ->leftJoin('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('accounts.id', '=', $accountId)
            ->where('clients.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('invoices.is_public', '=', true)
            ->where('invoices.invoice_type_id', '=', INVOICE_TYPE_STANDARD)
            ->where('invoices.is_recurring', '=', false);

        if ( ! $viewAll) {
            $averageInvoice->where('invoices.user_id', '=', $userId);
        }

        if ($startDate = $account->financialYearStart()) {
            //$averageInvoice->where('invoices.invoice_date', '>=', $startDate);
        }

        return $averageInvoice->groupBy('accounts.id')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' IS NULL THEN CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('accounts.currency_id', true) . ' IS NULL THEN 1 ELSE ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('accounts.currency_id', true) . ' END ELSE ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' END'))
            ->get();
    }

    public function balances($accountId, $userId, $viewAll)
    {
        $select = \Illuminate\Support\Facades\DB::raw(
            'SUM(' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.balance', true) . ') as value, '
                  . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' as currency_id'
        );
        $balances = \Illuminate\Support\Facades\DB::table('accounts')
            ->select($select)
            ->leftJoin('clients', 'accounts.id', '=', 'clients.account_id')
            ->where('accounts.id', '=', $accountId)
            ->where('clients.is_deleted', '=', false)
            ->groupBy('accounts.id')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' IS NULL THEN CASE WHEN ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('accounts.currency_id', true) . ' IS NULL THEN 1 ELSE ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('accounts.currency_id', true) . ' END ELSE ' . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('clients.currency_id', true) . ' END'));

        if ( ! $viewAll) {
            $balances->where('clients.user_id', '=', $userId);
        }

        return $balances->get();
    }

    public function activities($accountId, $userId, $viewAll)
    {
        $activities = Activity::where('activities.account_id', '=', $accountId)
            ->where('activities.activity_type_id', '>', 0);

        if ( ! $viewAll) {
            $activities = $activities->where('activities.user_id', '=', $userId);
        }

        return $activities->orderBy('activities.created_at', 'desc')
            ->with('client.contacts', 'user', 'invoice', 'payment', 'credit', 'account', 'task', 'expense', 'contact')
            ->take(100)
            ->get();
    }

    public function pastDue($accountId, $userId, $viewAll)
    {
        $pastDue = \Illuminate\Support\Facades\DB::table('invoices')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->where('invoices.account_id', '=', $accountId)
            ->where('clients.deleted_at', '=', null)
            ->where('contacts.deleted_at', '=', null)
            ->where('invoices.is_recurring', '=', false)
            ->where('invoices.quote_invoice_id', '=', null)
            ->where('invoices.balance', '>', 0)
            ->where('invoices.is_deleted', '=', false)
            ->where('invoices.deleted_at', '=', null)
            ->where('invoices.is_public', '=', true)
            ->where('contacts.is_primary', '=', true)
            ->where(\Illuminate\Support\Facades\DB::raw('coalesce(invoices.partial_due_date, invoices.due_date)'), '<', date('Y-m-d'));

        if ( ! $viewAll) {
            $pastDue = $pastDue->where('invoices.user_id', '=', $userId);
        }

        return $pastDue->select([\Illuminate\Support\Facades\DB::raw('coalesce(invoices.partial_due_date, invoices.due_date) due_date'), 'invoices.balance', 'invoices.public_id', 'invoices.invoice_number', 'clients.name as client_name', 'contacts.email', 'contacts.first_name', 'contacts.last_name', 'clients.currency_id', 'clients.public_id as client_public_id', 'clients.user_id as client_user_id', 'invoice_type_id'])
            ->orderBy('invoices.due_date', 'asc')
            ->take(100)
            ->get();
    }

    public function upcoming($accountId, $userId, $viewAll)
    {
        $upcoming = \Illuminate\Support\Facades\DB::table('invoices')
            ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->where('invoices.account_id', '=', $accountId)
            ->where('clients.deleted_at', '=', null)
            ->where('contacts.deleted_at', '=', null)
            ->where('invoices.deleted_at', '=', null)
            ->where('invoices.is_recurring', '=', false)
            ->where('invoices.quote_invoice_id', '=', null)
            ->where('invoices.balance', '>', 0)
            ->where('invoices.is_deleted', '=', false)
            ->where('invoices.is_public', '=', true)
            ->where('contacts.is_primary', '=', true)
            ->where(function ($query): void {
                $query->where(\Illuminate\Support\Facades\DB::raw('coalesce(invoices.partial_due_date, invoices.due_date)'), '>=', date('Y-m-d'))
                    ->orWhereNull('invoices.due_date');
            })
            ->orderBy('invoices.due_date', 'asc');

        if ( ! $viewAll) {
            $upcoming = $upcoming->where('invoices.user_id', '=', $userId);
        }

        return $upcoming->take(100)
            ->select([\Illuminate\Support\Facades\DB::raw('coalesce(invoices.partial_due_date, invoices.due_date) due_date'), 'invoices.balance', 'invoices.public_id', 'invoices.invoice_number', 'clients.name as client_name', 'contacts.email', 'contacts.first_name', 'contacts.last_name', 'clients.currency_id', 'clients.public_id as client_public_id', 'clients.user_id as client_user_id', 'invoice_type_id'])
            ->get();
    }

    public function payments($accountId, $userId, $viewAll)
    {
        $payments = \Illuminate\Support\Facades\DB::table('payments')
            ->leftJoin('clients', 'clients.id', '=', 'payments.client_id')
            ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->where('payments.account_id', '=', $accountId)
            ->where('payments.is_deleted', '=', false)
            ->where('invoices.is_deleted', '=', false)
            ->where('clients.is_deleted', '=', false)
            ->where('contacts.deleted_at', '=', null)
            ->where('contacts.is_primary', '=', true)
            ->whereNotIn('payments.payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);

        if ( ! $viewAll) {
            $payments = $payments->where('payments.user_id', '=', $userId);
        }

        return $payments->select(['payments.payment_date', \Illuminate\Support\Facades\DB::raw('(payments.amount - payments.refunded) as amount'), 'invoices.public_id', 'invoices.invoice_number', 'clients.name as client_name', 'contacts.email', 'contacts.first_name', 'contacts.last_name', 'clients.currency_id', 'clients.public_id as client_public_id', 'clients.user_id as client_user_id'])
            ->orderBy('payments.payment_date', 'desc')
            ->take(100)
            ->get();
    }

    public function expenses($account, $userId, $viewAll)
    {
        $amountField = \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('expenses.amount', true);
        $taxRate1Field = \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('expenses.tax_rate1', true);
        $taxRate2Field = \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('expenses.tax_rate2', true);

        $select = \Illuminate\Support\Facades\DB::raw(
            sprintf('SUM(%s + (%s * %s / 100) + (%s * %s / 100)) as value,', $amountField, $amountField, $taxRate1Field, $amountField, $taxRate2Field)
                  . \Illuminate\Support\Facades\DB::getQueryGrammar()->wrap('expenses.expense_currency_id', true) . ' as currency_id'
        );
        $expenses = \Illuminate\Support\Facades\DB::table('accounts')
            ->select($select)
            ->leftJoin('expenses', 'accounts.id', '=', 'expenses.account_id')
            ->where('accounts.id', '=', $account->id)
            ->where('expenses.is_deleted', '=', false);

        if ( ! $viewAll) {
            $expenses = $expenses->where('expenses.user_id', '=', $userId);
        }

        if ($startDate = $account->financialYearStart()) {
            //$expenses->where('expenses.expense_date', '>=', $startDate);
        }

        return $expenses->groupBy('accounts.id')
            ->groupBy('expenses.expense_currency_id')
            ->get();
    }

    public function tasks($accountId, $userId, $viewAll)
    {
        return Task::scope()
            ->withArchived()
            ->whereIsRunning(true)
            ->get();
    }

    private function rawChartData(string $entityType, $account, string $groupBy, DateTime|bool $startDate, DateTime|bool $endDate, $currencyId)
    {
        if ( ! in_array($groupBy, ['DAYOFYEAR', 'WEEK', 'MONTH'])) {
            return [];
        }

        $accountId = $account->id;
        $currencyId = (int) $currencyId;
        $timeframe = 'concat(YEAR(' . $entityType . '_date), ' . $groupBy . '(' . $entityType . '_date))';

        $records = \Illuminate\Support\Facades\DB::table($entityType . 's')
            ->leftJoin('clients', 'clients.id', '=', $entityType . 's.client_id')
            ->whereRaw('(clients.id IS NULL OR clients.is_deleted = 0)')
            ->where($entityType . 's.account_id', '=', $accountId)
            ->where($entityType . 's.is_deleted', '=', false)
            ->where($entityType . 's.' . $entityType . '_date', '>=', $startDate->format('Y-m-d'))
            ->where($entityType . 's.' . $entityType . '_date', '<=', $endDate->format('Y-m-d'))
            ->groupBy($groupBy);

        if ($entityType === ENTITY_EXPENSE) {
            $records->where('expenses.expense_currency_id', '=', $currencyId);
        } elseif ($currencyId == $account->getCurrencyId()) {
            $records->whereRaw(sprintf('(clients.currency_id = %d or coalesce(clients.currency_id, 0) = 0)', $currencyId));
        } else {
            $records->where('clients.currency_id', '=', $currencyId);
        }

        if ($entityType === ENTITY_INVOICE) {
            $records->select(\Illuminate\Support\Facades\DB::raw('sum(invoices.amount) as total, sum(invoices.balance) as balance, count(invoices.id) as count, ' . $timeframe . ' as ' . $groupBy))
                ->where('invoice_type_id', '=', INVOICE_TYPE_STANDARD)
                ->where('invoices.is_public', '=', true)
                ->where('is_recurring', '=', false);
        } elseif ($entityType === ENTITY_PAYMENT) {
            $records->select(\Illuminate\Support\Facades\DB::raw('sum(payments.amount - payments.refunded) as total, count(payments.id) as count, ' . $timeframe . ' as ' . $groupBy))
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->where('invoices.is_deleted', '=', false)
                ->whereNotIn('payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);
        } elseif ($entityType === ENTITY_EXPENSE) {
            $records->select(\Illuminate\Support\Facades\DB::raw('sum(expenses.amount + (expenses.amount * expenses.tax_rate1 / 100) + (expenses.amount * expenses.tax_rate2 / 100)) as total, count(expenses.id) as count, ' . $timeframe . ' as ' . $groupBy));
        }

        return $records->get()->all();
    }
}

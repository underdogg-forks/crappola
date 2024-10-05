<?php

namespace App\Ninja\Datatables;

use App\Models\Expense;
use Utils;

class ExpenseDatatable extends EntityDatatable
{
    public $entityType = ENTITY_EXPENSE;

    public $sortCol = 3;

    public function columns(): array
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    if ($model->vendor_public_id) {
                        if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_VENDOR, $model])) {
                            return link_to('vendors/' . $model->vendor_public_id, $model->vendor_name)->toHtml();
                        }

                        return $model->vendor_name;
                    }

                    return '';
                },
                ! $this->hideClient,
            ],
            [
                'client_name',
                function ($model) {
                    if ($model->client_public_id) {
                        if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                            return link_to('clients/' . $model->client_public_id, Utils::getClientDisplayName($model))->toHtml();
                        }

                        return Utils::getClientDisplayName($model);
                    }

                    return '';
                },
                ! $this->hideClient,
            ],
            [
                'expense_date',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_EXPENSE, $model])) {
                        return $this->addNote(link_to(sprintf('expenses/%s/edit', $model->public_id), Utils::fromSqlDate($model->expense_date_sql))->toHtml(), $model->private_notes);
                    }

                    return Utils::fromSqlDate($model->expense_date_sql);
                },
            ],
            [
                'amount',
                function ($model) {
                    $amount = $model->amount + Utils::calculateTaxes($model->amount, $model->tax_rate1, $model->tax_rate2);
                    $str = Utils::formatMoney($amount, $model->expense_currency_id);

                    // show both the amount and the converted amount
                    if ($model->exchange_rate != 1) {
                        $converted = round($amount * $model->exchange_rate, 2);
                        $str .= ' | ' . Utils::formatMoney($converted, $model->invoice_currency_id);
                    }

                    return $str;
                },
            ],
            [
                'category',
                function ($model) {
                    $category = $model->category != null ? mb_substr($model->category, 0, 100) : '';
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_EXPENSE_CATEGORY, $model])) {
                        return $model->category_public_id ? link_to(sprintf('expense_categories/%s/edit', $model->category_public_id), $category)->toHtml() : '';
                    }

                    return $category;
                },
            ],
            [
                'public_notes',
                fn ($model) => $this->showWithTooltip($model->public_notes),
            ],
            [
                'status',
                fn ($model) => self::getStatusLabel($model->invoice_id, $model->should_be_invoiced, $model->balance, $model->payment_date),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_expense'),
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('expenses/%s/edit', $model->public_id)),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_EXPENSE, $model]),
            ],
            [
                trans('texts.clone_expense'),
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('expenses/%s/clone', $model->public_id)),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_EXPENSE),
            ],
            [
                trans('texts.view_invoice'),
                fn ($model)       => \Illuminate\Support\Facades\URL::to(sprintf('/invoices/%s/edit', $model->invoice_public_id)),
                fn ($model): bool => $model->invoice_public_id && \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_INVOICE, $model]),
            ],
            [
                trans('texts.invoice_expense'),
                fn ($model): string => sprintf("javascript:submitForm_expense('invoice', %s)", $model->public_id),
                fn ($model): bool   => ! $model->invoice_id && ( ! $model->deleted_at || $model->deleted_at == '0000-00-00') && \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE),
            ],
        ];
    }

    private function getStatusLabel($invoiceId, $shouldBeInvoiced, $balance, $paymentDate): string
    {
        $label = Expense::calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance, $paymentDate);
        $class = Expense::calcStatusClass($shouldBeInvoiced, $invoiceId, $balance);

        return sprintf('<h4><div class="label label-%s">%s</div></h4>', $class, $label);
    }
}

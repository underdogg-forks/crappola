<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\Invoice;
use Bootstrapper\Facades\DropdownButton;
use Illuminate\Support\Facades\Auth;

class InvoiceDatatable extends EntityDatatable
{
    public $entityType = ENTITY_INVOICE;

    public $sortCol = 3;

    public $fieldToSum = 'amount';

    public function columns()
    {
        $entityType = $this->entityType;

        return [
            [
                $entityType == ENTITY_INVOICE ? 'invoice_number' : 'quote_number',
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE ? $model->invoice_number : $model->quote_number;
                },
            ],
            [
                'client_name',
                function ($model) {
                    $model->entityType = ENTITY_CLIENT;

                    if (Auth::user()->can('viewModel', $model)) {
                        return link_to("clients/{$model->client_id}", Utils::getClientDisplayName($model))->toHtml();
                    }

                    return Utils::getClientDisplayName($model);
                },
                ! $this->hideClient,
            ],
            [
                'date',
                function ($model) {
                    return Utils::fromSqlDate($model->invoice_date);
                },
            ],
            [
                'amount',
                function ($model) {
                    return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
                },
            ],
            [
                'balance',
                function ($model) {
                    return $model->partial > 0 ?
                        trans(
                            'texts.partial_remaining',
                            [
                                'partial' => Utils::formatMoney($model->partial, $model->currency_id, $model->country_id),
                                'balance' => Utils::formatMoney($model->balance, $model->currency_id, $model->country_id), ]
                        ) :
                        Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
                $entityType == ENTITY_INVOICE,
            ],
            [
                $entityType == ENTITY_INVOICE ? 'due_at' : 'valid_until',
                function ($model) {
                    $str = '';
                    if ($model->partial_due_date) {
                        $str = Utils::fromSqlDate($model->partial_due_date);
                        if ($model->due_date_sql && $model->due_date_sql != '0000-00-00') {
                            $str .= ', ';
                        }
                    }

                    return $str . Utils::fromSqlDate($model->due_date_sql);
                },
            ],
            [
                'status',
                function ($model) {
                    return $model->quote_invoice_id ? link_to("invoices/{$model->quote_invoice_id}/edit", trans('texts.converted'))->toHtml() : self::getStatusLabel($model);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = Invoice::calcStatusClass($model->invoice_status_id, $model->balance, $model->partial_due_date ?: $model->due_date_sql, $model->is_recurring);
        $label = Invoice::calcStatusLabel($model->invoice_status_name, $class, $this->entityType, $model->quote_invoice_id);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function actions()
    {
        $entityType = $this->entityType;

        return [
            [
                trans('texts.clone_invoice'),
                function ($model) {
                    return "invoices/{$model->id}/clone";
                },
                function ($model) {
                    return Auth::user()->can('createEntity', ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.clone_quote'),
                function ($model) {
                    return "quotes/{$model->id}/clone";
                },
            ],
            [
                trans("texts.{$entityType}_history"),
                function ($model) use ($entityType) {
                    return "{$entityType}s/{$entityType}_history/{$model->id}";
                },
            ],
            [
                trans('texts.delivery_note'),
                function ($model) {
                    return url("invoices/delivery_note/{$model->id}");
                },
                /*function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE;
                },*/
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return Auth::user()->canCreateOrEdit(ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.mark_sent'),
                function ($model) use ($entityType) {
                    return "javascript:submitForm_{$entityType}('markSent', {$model->id})";
                },
                function ($model) {
                    return ! $model->is_public && Auth::user()->can('edit', [ENTITY_INVOICE, $model]);
                },
            ],
            [
                trans('texts.mark_paid'),
                function ($model) use ($entityType) {
                    return "javascript:submitForm_{$entityType}('markPaid', {$model->id})";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE && $model->invoice_status_id != INVOICE_STATUS_PAID && Auth::user()->can('edit', [ENTITY_INVOICE, $model]);
                },
            ],
            [
                trans('texts.enter_payment'),
                function ($model) {
                    return "payments/create/{$model->client_id}/{$model->id}";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_INVOICE && $model->invoice_status_id != INVOICE_STATUS_PAID && Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.view_invoice'),
                function ($model) {
                    return "invoices/{$model->quote_invoice_id}/edit";
                },
                function ($model) use ($entityType) {
                    $model->entityType = ENTITY_INVOICE;

                    return $entityType == ENTITY_QUOTE && $model->quote_invoice_id && Auth::user()->can('viewModel', $model);
                },
            ],
            [
                trans('texts.new_proposal'),
                function ($model) {
                    return "proposals/create/{$model->id}";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && ! $model->quote_invoice_id && $model->invoice_status_id < INVOICE_STATUS_APPROVED && Auth::user()->can('create', ENTITY_PROPOSAL);
                },
            ],
            [
                trans('texts.convert_to_invoice'),
                function ($model) {
                    return "javascript:submitForm_quote('convert', {$model->id})";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && ! $model->quote_invoice_id && Auth::user()->can('edit', [ENTITY_INVOICE, $model]);
                },
            ],
        ];
    }

    public function bulkActions()
    {
        $actions = [];

        if ($this->entityType == ENTITY_INVOICE || $this->entityType == ENTITY_QUOTE) {
            $actions[] = [
                'label' => mtrans($this->entityType, 'download_' . $this->entityType),
                'url'   => 'javascript:submitForm_' . $this->entityType . '("download")',
            ];
            if (auth()->user()->isTrusted()) {
                $actions[] = [
                    'label' => mtrans($this->entityType, 'email_' . $this->entityType),
                    'url'   => 'javascript:submitForm_' . $this->entityType . '("emailInvoice")',
                ];
            }
            $actions[] = DropdownButton::DIVIDER;
            $actions[] = [
                'label' => mtrans($this->entityType, 'mark_sent'),
                'url'   => 'javascript:submitForm_' . $this->entityType . '("markSent")',
            ];
        }

        if ($this->entityType == ENTITY_INVOICE) {
            $actions[] = [
                'label' => mtrans($this->entityType, 'mark_paid'),
                'url'   => 'javascript:submitForm_' . $this->entityType . '("markPaid")',
            ];
        }

        $actions[] = DropdownButton::DIVIDER;
        return array_merge($actions, parent::bulkActions());
    }
}

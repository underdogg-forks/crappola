<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\Invoice;
use Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class RecurringInvoiceDatatable extends EntityDatatable
{
    public $entityType = ENTITY_RECURRING_INVOICE;

    public function columns(): array
    {
        return [
            [
                'frequency',
                function ($model) {
                    if ($model->frequency) {
                        $frequency = mb_strtolower($model->frequency);
                        $frequency = preg_replace('/\s/', '_', $frequency);
                        $label = trans('texts.freq_' . $frequency);
                    } else {
                        $label = trans('texts.freq_inactive');
                    }

                    return link_to(sprintf('recurring_invoices/%s/edit', $model->public_id), $label)->toHtml();
                },
            ],
            [
                'client_name',
                fn ($model) => link_to('clients/' . $model->client_public_id, Utils::getClientDisplayName($model))->toHtml(),
                ! $this->hideClient,
            ],
            [
                'start_date',
                fn ($model) => Utils::fromSqlDate($model->start_date_sql),
            ],
            [
                'last_sent',
                fn ($model) => Utils::fromSqlDate($model->last_sent_date_sql),
            ],
            /*
            [
                'end_date',
                function ($model) {
                    return Utils::fromSqlDate($model->end_date_sql);
                },
            ],
            */
            [
                'amount',
                fn ($model) => Utils::formatMoney($model->amount, $model->currency_id, $model->country_id),
            ],
            [
                'private_notes',
                fn ($model) => $this->showWithTooltip($model->private_notes),
            ],
            [
                'status',
                fn ($model) => self::getStatusLabel($model),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_invoice'),
                fn ($model) => URL::to(sprintf('invoices/%s/edit', $model->public_id)),
                fn ($model) => Auth::user()->can('view', [ENTITY_INVOICE, $model]),
            ],
            [
                trans('texts.clone_invoice'),
                fn ($model) => URL::to(sprintf('invoices/%s/clone', $model->public_id)),
                fn ($model) => Auth::user()->can('create', ENTITY_INVOICE),
            ],
            [
                trans('texts.clone_quote'),
                fn ($model) => URL::to(sprintf('quotes/%s/clone', $model->public_id)),
                fn ($model) => Auth::user()->can('create', ENTITY_QUOTE),
            ],
        ];
    }

    private function getStatusLabel($model): string
    {
        $class = Invoice::calcStatusClass($model->invoice_status_id, $model->balance, $model->due_date_sql, $model->is_recurring);
        $label = Invoice::calcStatusLabel($model->invoice_status_name, $class, $this->entityType, $model->quote_invoice_id);

        if ($model->invoice_status_id == INVOICE_STATUS_SENT) {
            if ( ! $model->last_sent_date_sql || $model->last_sent_date_sql == '0000-00-00') {
                $label = trans('texts.pending');
            } elseif ($model->end_date_sql && Carbon::parse($model->end_date_sql)->isPast()) {
                $label = trans('texts.status_completed');
            } else {
                $label = trans('texts.active');
            }
        }

        return sprintf('<h4><div class="label label-%s">%s</div></h4>', $class, $label);
    }
}

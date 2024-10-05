<?php

namespace App\Ninja\Datatables;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Utils;

class PaymentDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PAYMENT;

    public $sortCol = 7;

    protected static $refundableGateways = [
        GATEWAY_STRIPE,
        GATEWAY_BRAINTREE,
        GATEWAY_WEPAY,
    ];

    public function columns(): array
    {
        return [
            [
                'invoice_name',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_INVOICE, $model->invoice_user_id])) {
                        return link_to(sprintf('invoices/%s/edit', $model->invoice_public_id), $model->invoice_number, ['class' => Utils::getEntityRowClass($model)])->toHtml();
                    }

                    return $model->invoice_number;
                },
            ],
            [
                'client_name',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, ENTITY_CLIENT])) {
                        return $model->client_public_id ? link_to('clients/' . $model->client_public_id, Utils::getClientDisplayName($model))->toHtml() : '';
                    }

                    return Utils::getClientDisplayName($model);
                },
                ! $this->hideClient,
            ],
            [
                'transaction_reference',
                function ($model) {
                    $str = $model->transaction_reference ? e($model->transaction_reference) : '<i>' . trans('texts.manual_entry') . '</i>';

                    return $this->addNote($str, $model->private_notes);
                },
            ],
            [
                'method',
                fn ($model) => $model->account_gateway_id ? $model->gateway_name : ($model->payment_type ? trans('texts.payment_type_' . $model->payment_type) : ''),
            ],
            [
                'source',
                function ($model) {
                    $code = str_replace(' ', '', mb_strtolower($model->payment_type));
                    $card_type = trans('texts.card_' . $code);
                    if ($model->payment_type_id != PAYMENT_TYPE_ACH) {
                        if ($model->last4) {
                            $expiration = Utils::fromSqlDate($model->expiration, false)->format('m/y');

                            return '<img height="22" src="' . \Illuminate\Support\Facades\URL::to('/images/credit_cards/' . $code . '.png') . '" alt="' . htmlentities($card_type) . '">&nbsp; &bull;&bull;&bull;' . $model->last4 . ' ' . $expiration;
                        }

                        if ($model->email) {
                            return $model->email;
                        }

                        if ($model->payment_type) {
                            return trans('texts.payment_type_' . $model->payment_type);
                        }
                    } elseif ($model->last4) {
                        if ($model->bank_name) {
                            $bankName = $model->bank_name;
                        } else {
                            $bankData = PaymentMethod::lookupBankData($model->routing_number);
                            if ($bankData) {
                                $bankName = $bankData->name;
                            }
                        }

                        if ( ! empty($bankName)) {
                            return $bankName . '&nbsp; &bull;&bull;&bull;' . $model->last4;
                        }

                        if ($model->last4) {
                            return '<img height="22" src="' . \Illuminate\Support\Facades\URL::to('/images/credit_cards/ach.png') . '" alt="' . htmlentities($card_type) . '">&nbsp; &bull;&bull;&bull;' . $model->last4;
                        }
                    }
                },
            ],
            [
                'amount',
                function ($model) {
                    $amount = Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);

                    if ($model->exchange_currency_id && $model->exchange_rate != 1) {
                        $amount .= ' | ' . Utils::formatMoney($model->amount * $model->exchange_rate, $model->exchange_currency_id, $model->country_id);
                    }

                    return $amount;
                },
            ],
            [
                'date',
                function ($model) {
                    if ($model->is_deleted) {
                        return Utils::dateToString($model->payment_date);
                    }

                    return link_to(sprintf('payments/%s/edit', $model->public_id), Utils::dateToString($model->payment_date))->toHtml();
                },
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
                trans('texts.edit_payment'),
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('payments/%s/edit', $model->public_id)),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PAYMENT, $model]),
            ],
            [
                trans('texts.email_payment'),
                fn ($model): string => sprintf("javascript:submitForm_payment('email', %s)", $model->public_id),
                fn ($model)         => \Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_PAYMENT, $model]),
            ],
            [
                trans('texts.refund_payment'),
                function ($model): string {
                    $max_refund = $model->amount - $model->refunded;
                    $formatted = Utils::formatMoney($max_refund, $model->currency_id, $model->country_id);
                    $symbol = Utils::getFromCache($model->currency_id ?: 1, 'currencies')->symbol;
                    $local = in_array($model->gateway_id, [GATEWAY_BRAINTREE, GATEWAY_STRIPE, GATEWAY_WEPAY]) || ! $model->gateway_id ? 0 : 1;

                    return sprintf("javascript:showRefundModal(%s, '%s', '%s', '%s', %s)", $model->public_id, $max_refund, $formatted, $symbol, $local);
                },
                fn ($model): bool => \Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_PAYMENT, $model])
                    && $model->payment_status_id >= PAYMENT_STATUS_COMPLETED
                    && $model->refunded < $model->amount,
            ],
        ];
    }

    private function getStatusLabel($model): string
    {
        $amount = Utils::formatMoney($model->refunded, $model->currency_id, $model->country_id);
        $label = Payment::calcStatusLabel($model->payment_status_id, $model->status, $amount);
        $class = Payment::calcStatusClass($model->payment_status_id);

        return sprintf('<h4><div class="label label-%s">%s</div></h4>', $class, $label);
    }
}

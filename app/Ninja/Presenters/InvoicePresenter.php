<?php

namespace App\Ninja\Presenters;

use App\Libraries\Skype\InvoiceCard;
use Carbon;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Utils;

class InvoicePresenter extends EntityPresenter
{
    public function client()
    {
        return $this->entity->client ? $this->entity->client->getDisplayName() : '';
    }

    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function amount()
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        return $account->formatMoney($invoice->amount, $invoice->client);
    }

    public function balance()
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        return $account->formatMoney($invoice->balance, $invoice->client);
    }

    public function paid()
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        return $account->formatMoney($invoice->amount - $invoice->balance, $invoice->client);
    }

    public function partial()
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        return $account->formatMoney($invoice->partial, $invoice->client);
    }

    public function requestedAmount()
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        return $account->formatMoney($invoice->getRequestedAmount(), $invoice->client);
    }

    public function balanceDueLabel(): string
    {
        if ($this->entity->partial > 0) {
            return 'partial_due';
        }

        if ($this->entity->isType(INVOICE_TYPE_QUOTE)) {
            return 'total';
        }

        return 'balance_due';
    }

    public function age()
    {
        $invoice = $this->entity;
        $dueDate = $invoice->partial_due_date ?: $invoice->due_date;

        if ( ! $dueDate || $dueDate == '0000-00-00') {
            return 0;
        }

        $date = Carbon::parse($dueDate);

        if ($date->isFuture()) {
            return 0;
        }

        return $date->diffInDays();
    }

    public function ageGroup(): string
    {
        $age = $this->age();

        if ($age > 120) {
            return 'age_group_120';
        }

        if ($age > 90) {
            return 'age_group_90';
        }

        if ($age > 60) {
            return 'age_group_60';
        }

        if ($age > 30) {
            return 'age_group_30';
        }

        return 'age_group_0';
    }

    public function dueDateLabel()
    {
        if ($this->entity->isType(INVOICE_TYPE_STANDARD)) {
            return trans('texts.due_date');
        }

        return trans('texts.valid_until');
    }

    public function discount()
    {
        $invoice = $this->entity;

        if ($invoice->is_amount_discount) {
            return $invoice->account->formatMoney($invoice->discount);
        }

        return $invoice->discount . '%';
    }

    // https://schema.org/PaymentStatusType
    public function paymentStatus(): string
    {
        if ( ! $this->entity->balance) {
            return 'PaymentComplete';
        }

        if ($this->entity->isOverdue()) {
            return 'PaymentPastDue';
        }

        return 'PaymentDue';
    }

    public function status()
    {
        if ($this->entity->is_deleted) {
            return trans('texts.deleted');
        }

        if ($this->entity->trashed()) {
            return trans('texts.archived');
        }

        if ($this->entity->is_recurring) {
            return trans('texts.active');
        }

        $status = $this->entity->invoice_status ? $this->entity->invoice_status->name : 'draft';
        $status = mb_strtolower($status);

        return trans('texts.status_' . $status);
    }

    public function invoice_date()
    {
        return Utils::fromSqlDate($this->entity->invoice_date);
    }

    public function due_date()
    {
        return Utils::fromSqlDate($this->entity->due_date);
    }

    public function partial_due_date()
    {
        return Utils::fromSqlDate($this->entity->partial_due_date);
    }

    public function frequency()
    {
        $frequency = $this->entity->frequency ? $this->entity->frequency->name : '';
        $frequency = mb_strtolower($frequency);

        return trans('texts.freq_' . $frequency);
    }

    public function email()
    {
        $client = $this->entity->client;

        return $client->contacts->count() ? $client->contacts[0]->email : '';
    }

    public function autoBillEmailMessage()
    {
        $client = $this->entity->client;
        $paymentMethod = $client->defaultPaymentMethod();

        if ( ! $paymentMethod) {
            return false;
        }

        if ($paymentMethod->payment_type_id == PAYMENT_TYPE_ACH) {
            $paymentMethodString = trans('texts.auto_bill_payment_method_bank_transfer');
        } elseif ($paymentMethod->payment_type_id == PAYMENT_TYPE_PAYPAL) {
            $paymentMethodString = trans('texts.auto_bill_payment_method_paypal');
        } else {
            $paymentMethodString = trans('texts.auto_bill_payment_method_credit_card');
        }

        $data = [
            'payment_method' => $paymentMethodString,
            'due_date'       => $this->due_date(),
        ];

        return trans('texts.auto_bill_notification', $data);
    }

    public function skypeBot(): InvoiceCard
    {
        return new InvoiceCard($this->entity);
    }

    public function rBits(): array
    {
        $properties = new stdClass();
        $properties->terms_text = $this->entity->terms;
        $properties->note = $this->entity->public_notes;
        $properties->itemized_receipt = [];

        foreach ($this->entity->invoice_items as $item) {
            $properties->itemized_receipt[] = $item->present()->rBits;
        }

        $data = new stdClass();
        $data->receive_time = time();
        $data->type = 'transaction_details';
        $data->source = 'user';
        $data->properties = $properties;

        return [$data];
    }

    public function moreActions(): array
    {
        $invoice = $this->entity;
        $entityType = $invoice->getEntityType();

        $actions = [
            ['url' => 'javascript:onCloneInvoiceClick()', 'label' => trans('texts.clone_invoice')],
        ];

        if (Auth::user()->can('create', ENTITY_QUOTE)) {
            $actions[] = ['url' => 'javascript:onCloneQuoteClick()', 'label' => trans('texts.clone_quote')];
        }

        $actions[] = ['url' => url(sprintf('%ss/%s_history/%s', $entityType, $entityType, $invoice->public_id)), 'label' => trans('texts.view_history')];

        if ($entityType == ENTITY_INVOICE) {
            $actions[] = ['url' => url('invoices/delivery_note/' . $invoice->public_id), 'label' => trans('texts.delivery_note')];
        }

        $actions[] = DropdownButton::DIVIDER;

        if ($entityType == ENTITY_QUOTE) {
            if ($invoice->quote_invoice_id) {
                $actions[] = ['url' => url(sprintf('invoices/%s/edit', $invoice->quote_invoice_id)), 'label' => trans('texts.view_invoice')];
            } else {
                if ( ! $invoice->isApproved()) {
                    $actions[] = ['url' => url('proposals/create/' . $invoice->public_id), 'label' => trans('texts.new_proposal')];
                }

                $actions[] = ['url' => 'javascript:onConvertClick()', 'label' => trans('texts.convert_to_invoice')];
            }
        } elseif ($entityType == ENTITY_INVOICE) {
            if ($invoice->quote_id && $invoice->quote) {
                $actions[] = ['url' => url(sprintf('quotes/%s/edit', $invoice->quote->public_id)), 'label' => trans('texts.view_quote')];
            }

            if ($invoice->onlyHasTasks()) {
                $actions[] = ['url' => 'javascript:onAddItemClick()', 'label' => trans('texts.add_product')];
            }

            if ($invoice->canBePaid()) {
                $actions[] = ['url' => 'javascript:submitBulkAction("markPaid")', 'label' => trans('texts.mark_paid')];
                $actions[] = ['url' => 'javascript:onPaymentClick()', 'label' => trans('texts.enter_payment')];
            }

            foreach ($invoice->payments as $payment) {
                $label = trans('texts.view_payment');
                if ($invoice->payments->count() > 1) {
                    $label .= ' - ' . $invoice->account->formatMoney($payment->amount, $invoice->client);
                }

                $actions[] = ['url' => $payment->present()->url, 'label' => $label];
            }
        }

        if (count($actions) > 3) {
            $actions[] = DropdownButton::DIVIDER;
        }

        if ( ! $invoice->trashed()) {
            $actions[] = ['url' => 'javascript:onArchiveClick()', 'label' => trans('texts.archive_' . $entityType)];
        }

        if ( ! $invoice->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans('texts.delete_' . $entityType)];
        }

        return $actions;
    }

    public function gatewayFee($gatewayTypeId = false): string
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        if ( ! $account->gateway_fee_enabled) {
            return '';
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);

        if ( ! $settings || ! $settings->areFeesEnabled()) {
            return '';
        }

        if ($invoice->getGatewayFeeItem()) {
            $label = ' + ' . trans('texts.fee');
        } else {
            $fee = $invoice->calcGatewayFee($gatewayTypeId, true);
            $fee = $account->formatMoney($fee, $invoice->client);

            if ((float) ($settings->fee_amount) < 0 || (float) ($settings->fee_percent) < 0) {
                $label = trans('texts.discount');
            } else {
                $label = trans('texts.fee');
            }

            $label = ' - ' . $fee . ' ' . $label;
        }

        $label .= '&nbsp;&nbsp; <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="' . trans('texts.fee_help') . '"></i>';

        return $label;
    }

    public function multiAccountLink()
    {
        $invoice = $this->entity;
        $account = $invoice->account;

        if ($account->hasMultipleAccounts()) {
            return url(sprintf('/account/%s?redirect_to=%s', $account->account_key, $invoice->present()->path));
        }

        return $invoice->present()->url;
    }

    public function calendarEvent($subColors = false): stdClass
    {
        $data = parent::calendarEvent();
        $invoice = $this->entity;
        $entityType = $invoice->getEntityType();

        $data->title = trans('texts.' . $entityType) . ' ' . $invoice->invoice_number . ' | ' . $this->amount() . ' | ' . $this->client();
        $data->start = $invoice->due_date ?: $invoice->invoice_date;

        if ($subColors) {
            $data->borderColor = $invoice->present()->statusColor();
            $data->backgroundColor = $data->borderColor;
        } else {
            $data->borderColor = $invoice->isQuote() ? '#716cb1' : '#377eb8';
            $data->backgroundColor = $invoice->isQuote() ? '#716cb1' : '#377eb8';
        }

        return $data;
    }
}

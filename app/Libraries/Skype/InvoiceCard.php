<?php

namespace App\Libraries\Skype;

use HTML;
use stdClass;

class InvoiceCard
{
    /**
     * @var string
     */
    public $contentType = 'application/vnd.microsoft.card.receipt';

    /**
     * @var stdClass
     */
    public $content;

    public function __construct($invoice)
    {
        $this->content = new stdClass();
        $this->content->facts = [];
        $this->content->items = [];
        $this->content->buttons = [];

        $this->setTitle('test');

        $this->setTitle(trans('texts.invoice_for_client', [
            'invoice' => link_to($invoice->getRoute(), $invoice->invoice_number),
            'client'  => link_to($invoice->client->getRoute(), $invoice->client->getDisplayName()),
        ]));

        $this->addFact(trans('texts.email'), HTML::mailto($invoice->client->contacts[0]->email)->toHtml());

        if ($invoice->due_date) {
            $this->addFact($invoice->present()->dueDateLabel, $invoice->present()->due_date);
        }

        if ($invoice->po_number) {
            $this->addFact(trans('texts.po_number'), $invoice->po_number);
        }

        if ($invoice->discount) {
            $this->addFact(trans('texts.discount'), $invoice->present()->discount);
        }

        foreach ($invoice->invoice_items as $item) {
            $this->addItem($item, $invoice->account);
        }

        $this->setTotal($invoice->present()->requestedAmount);

        if ((float) ($invoice->amount) !== 0.0) {
            $this->addButton(SKYPE_BUTTON_OPEN_URL, trans('texts.download_pdf'), $invoice->getInvitationLink('download', true));
            $this->addButton(SKYPE_BUTTON_IM_BACK, trans('texts.email_invoice'), trans('texts.email_invoice'));
        } else {
            $this->addButton(SKYPE_BUTTON_IM_BACK, trans('texts.list_products'), trans('texts.list_products'));
        }
    }

    public function setTitle($title): void
    {
        $this->content->title = $title;
    }

    public function setTotal($value): void
    {
        $this->content->total = $value;
    }

    public function addFact($key, $value): void
    {
        $fact = new stdClass();
        $fact->key = $key;
        $fact->value = $value;

        $this->content->facts[] = $fact;
    }

    public function addItem($item, $account): void
    {
        $this->content->items[] = new InvoiceItemCard($item, $account);
    }

    public function addButton($type, $title, $value, $url = false): void
    {
        $this->content->buttons[] = new ButtonCard($type, $title, $value, $url);
    }
}

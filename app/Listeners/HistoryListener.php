<?php

namespace App\Listeners;

use App\Events\ClientWasDeleted;
use App\Events\ExpenseWasDeleted;
use App\Events\InvoiceWasDeleted;
use App\Events\ProjectWasDeleted;
use App\Events\ProposalWasDeleted;
use App\Events\QuoteWasDeleted;
use App\Events\TaskWasDeleted;
use App\Libraries\HistoryUtils;

/**
 * Class InvoiceListener.
 */
class HistoryListener
{
    public function deletedClient(ClientWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->client);
    }

    public function deletedInvoice(InvoiceWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->invoice);
    }

    public function deletedQuote(QuoteWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->quote);
    }

    public function deletedTask(TaskWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->task);
    }

    public function deletedExpense(ExpenseWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->expense);
    }

    public function deletedProject(ProjectWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->project);
    }

    public function deletedProposal(ProposalWasDeleted $event): void
    {
        HistoryUtils::deleteHistory($event->proposal);
    }
}

<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Document;
use DateTime;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class RemoveOrphanedDocuments.
 */
class RemoveOrphanedDocuments extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:remove-orphaned-documents';

    /**
     * @var string
     */
    protected $description = 'Removes old documents not associated with an expense or invoice';

    public function handle(): void
    {
        $this->info(Carbon::now()->format('r') . ' Running RemoveOrphanedDocuments...');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        $documents = Document::whereRaw('invoice_id IS NULL AND expense_id IS NULL AND updated_at <= ?', [Carbon::now()->subHours(1)])
            ->get();

        $this->info($documents->count() . ' orphaned document(s) found');

        foreach ($documents as $document) {
            $document->delete();
        }

        $this->info('Done');
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
        ];
    }
}

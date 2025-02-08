<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class PruneData.
 */
class PruneData extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:prune-data';

    /**
     * @var string
     */
    protected $description = 'Delete inactive accounts';

    public function handle(): void
    {
        $this->info(date('r') . ' Running PruneData...');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        // delete accounts who never registered, didn't create any invoices,
        // hansn't logged in within the past 6 months and isn't linked to another company
        $sql = 'select c.id
                from companies c
                left join accounts a on a.company_id = c.id
                left join clients cl on cl.company_id = a.id
                left join tasks t on t.company_id = a.id
                left join expenses e on e.company_id = a.id
                left join users u on u.company_id = a.id and u.registered = 1
                where c.created_at < DATE_SUB(now(), INTERVAL 6 MONTH)
                and c.trial_started is null
                and c.plan is null
                group by c.id
                having count(cl.id) = 0
                and count(t.id) = 0
                and count(e.id) = 0
                and count(u.id) = 0';

        $results = DB::select($sql);

        foreach ($results as $result) {
            $this->info("Deleting companyPlan: {$result->id}");
            try {
                DB::table('companies')
                    ->where('id', '=', $result->id)
                    ->delete();
            } catch (QueryException $e) {
                // most likely because a user_account record exists which doesn't cascade delete
                $this->info("Unable to delete companyId: {$result->id}");
            }
        }

        $this->info('Done');
        return 0;
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
        ];
    }
}

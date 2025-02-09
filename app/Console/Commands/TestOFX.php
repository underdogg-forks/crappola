<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Services\BankAccountService;
use Illuminate\Console\Command;

/**
 * Class TestOFX.
 */
class TestOFX extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:test-ofx';

    /**
     * @var string
     */
    protected $description = 'Test OFX';

    protected BankAccountService $bankAccountService;

    /**
     * TestOFX constructor.
     */
    public function __construct(BankAccountService $bankAccountService)
    {
        parent::__construct();

        $this->bankAccountService = $bankAccountService;
    }

    public function handle(): void
    {
        $this->info(Carbon::now()->format('r') . ' Running TestOFX...');
    }
}

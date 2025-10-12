<?php

namespace Tests\Unit;

use App\Libraries\Finance;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FinanceTest extends TestCase
{
    #[Test]
    public function itConstructsFinanceObject(): void
    {
        $this->markTestIncomplete();
        
        $finance = new Finance();

        $this->assertInstanceOf(Finance::class, $finance);
    }

    #[Test]
    public function itHasBanksProperty(): void
    {
        $this->markTestIncomplete();
        
        $finance = new Finance();

        $this->assertObjectHasProperty('banks', $finance);
    }

    #[Test]
    public function itInitializesWithNullBanks(): void
    {
        $this->markTestIncomplete();
        
        $finance = new Finance();

        $this->assertNull($finance->banks);
    }

    #[Test]
    public function itAllowsSettingBanksArray(): void
    {
        $this->markTestIncomplete();
        
        $finance = new Finance();
        $finance->banks = [];

        $this->assertIsArray($finance->banks);
    }
}
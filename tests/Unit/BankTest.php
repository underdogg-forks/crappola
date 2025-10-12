<?php

namespace Tests\Unit;

use App\Libraries\Bank;
use App\Libraries\Finance;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    private Finance $finance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->finance = new Finance();
    }

    #[Test]
    public function itConstructsBankWithAllParameters(): void
    {
        $this->markTestIncomplete();
        
        $fid = 'TEST_FID_123';
        $url = 'https://ofx.bank.example.com';
        $org = 'TEST_BANK_ORG';

        $bank = new Bank($this->finance, $fid, $url, $org);

        $this->assertInstanceOf(Bank::class, $bank);
        $this->assertSame($this->finance, $bank->finance);
        $this->assertEquals($fid, $bank->fid);
        $this->assertEquals($url, $bank->url);
        $this->assertEquals($org, $bank->org);
    }

    #[Test]
    public function itStoresFinanceReference(): void
    {
        $this->markTestIncomplete();
        
        $bank = new Bank($this->finance, 'FID', 'URL', 'ORG');

        $this->assertSame($this->finance, $bank->finance);
    }

    #[Test]
    public function itStoresFinancialInstitutionId(): void
    {
        $this->markTestIncomplete();
        
        $fid = 'UNIQUE_FID_12345';
        $bank = new Bank($this->finance, $fid, 'https://example.com', 'ORG');

        $this->assertEquals($fid, $bank->fid);
    }

    #[Test]
    public function itStoresOfxEndpointUrl(): void
    {
        $this->markTestIncomplete();
        
        $url = 'https://secure.bank.example.com/ofxserver';
        $bank = new Bank($this->finance, 'FID', $url, 'ORG');

        $this->assertEquals($url, $bank->url);
    }

    #[Test]
    public function itStoresOrganizationIdentifier(): void
    {
        $this->markTestIncomplete();
        
        $org = 'EXAMPLE_BANK_ORG';
        $bank = new Bank($this->finance, 'FID', 'URL', $org);

        $this->assertEquals($org, $bank->org);
    }

    #[Test]
    public function itHasLoginsProperty(): void
    {
        $this->markTestIncomplete();
        
        $bank = new Bank($this->finance, 'FID', 'URL', 'ORG');

        $this->assertObjectHasProperty('logins', $bank);
    }

    #[Test]
    public function itAcceptsEmptyStringForFid(): void
    {
        $this->markTestIncomplete();
        
        $bank = new Bank($this->finance, '', 'URL', 'ORG');

        $this->assertEquals('', $bank->fid);
    }

    #[Test]
    public function itAcceptsUrlsWithDifferentProtocols(): void
    {
        $this->markTestIncomplete();
        
        $httpUrl = 'http://example.com/ofx';
        $httpsUrl = 'https://example.com/ofx';

        $bank1 = new Bank($this->finance, 'FID', $httpUrl, 'ORG');
        $bank2 = new Bank($this->finance, 'FID', $httpsUrl, 'ORG');

        $this->assertEquals($httpUrl, $bank1->url);
        $this->assertEquals($httpsUrl, $bank2->url);
    }
}
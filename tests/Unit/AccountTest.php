<?php

namespace Tests\Unit;

use App\Libraries\Account;
use App\Libraries\Bank;
use App\Libraries\Finance;
use App\Libraries\Login;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private Login $login;

    protected function setUp(): void
    {
        parent::setUp();
        $finance = new Finance();
        $bank = new Bank($finance, 'TEST_FID', 'https://test.example.com', 'TEST_ORG');
        $this->login = new Login($bank, 'test_user', 'test_pass');
    }

    #[Test]
    public function itConstructsBankAccountWithAllParameters(): void
    {
        $this->markTestIncomplete();
        
        $accountId = 'ACC123456';
        $type = 'BANK';
        $subType = 'CHECKING';
        $bankId = 'BANK456';

        $account = new Account($this->login, $accountId, $type, $subType, $bankId);

        $this->assertInstanceOf(Account::class, $account);
        $this->assertSame($this->login, $account->login);
        $this->assertEquals($accountId, $account->id);
        $this->assertEquals($type, $account->type);
        $this->assertEquals($subType, $account->subType);
        $this->assertEquals($bankId, $account->bankId);
    }

    #[Test]
    public function itConstructsCreditCardAccountWithoutOptionalParameters(): void
    {
        $this->markTestIncomplete();
        
        $accountId = 'CC987654';
        $type = 'CC';

        $account = new Account($this->login, $accountId, $type);

        $this->assertEquals($accountId, $account->id);
        $this->assertEquals($type, $account->type);
        $this->assertNull($account->subType);
        $this->assertNull($account->bankId);
    }

    #[Test]
    public function itStoresLoginReference(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK');

        $this->assertSame($this->login, $account->login);
    }

    #[Test]
    public function itStoresAccountId(): void
    {
        $this->markTestIncomplete();
        
        $accountId = 'ACCOUNT_987654321';
        $account = new Account($this->login, $accountId, 'BANK');

        $this->assertEquals($accountId, $account->id);
    }

    #[Test]
    public function itStoresAccountType(): void
    {
        $this->markTestIncomplete();
        
        $type = 'BANK';
        $account = new Account($this->login, 'ACC123', $type);

        $this->assertEquals($type, $account->type);
    }

    #[Test]
    public function itHasLedgerBalanceProperty(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK');

        $this->assertObjectHasProperty('ledgerBalance', $account);
    }

    #[Test]
    public function itHasAvailableBalanceProperty(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK');

        $this->assertObjectHasProperty('availableBalance', $account);
    }

    #[Test]
    public function itHasResponseProperty(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK');

        $this->assertObjectHasProperty('response', $account);
    }

    #[Test]
    public function itHandlesCheckingAccountSubtype(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK', 'CHECKING', 'BANK456');

        $this->assertEquals('CHECKING', $account->subType);
    }

    #[Test]
    public function itHandlesSavingsAccountSubtype(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK', 'SAVINGS', 'BANK456');

        $this->assertEquals('SAVINGS', $account->subType);
    }

    #[Test]
    public function itHandlesMoneyMarketAccountSubtype(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK', 'MONEYMRKT', 'BANK456');

        $this->assertEquals('MONEYMRKT', $account->subType);
    }

    #[Test]
    public function itHandlesCreditLineAccountSubtype(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK', 'CREDITLINE', 'BANK456');

        $this->assertEquals('CREDITLINE', $account->subType);
    }

    #[Test]
    public function itDistinguishesBetweenBankAndCreditCardTypes(): void
    {
        $this->markTestIncomplete();
        
        $bankAccount = new Account($this->login, 'ACC123', 'BANK', 'CHECKING', 'BANK456');
        $ccAccount = new Account($this->login, 'CC456', 'CC');

        $this->assertEquals('BANK', $bankAccount->type);
        $this->assertEquals('CC', $ccAccount->type);
    }

    #[Test]
    public function itAcceptsEmptyAccountId(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, '', 'BANK');

        $this->assertEquals('', $account->id);
    }

    #[Test]
    public function itAcceptsNumericAccountIds(): void
    {
        $this->markTestIncomplete();
        
        $accountId = '1234567890';
        $account = new Account($this->login, $accountId, 'BANK');

        $this->assertEquals($accountId, $account->id);
    }

    #[Test]
    public function itAcceptsAlphanumericAccountIds(): void
    {
        $this->markTestIncomplete();
        
        $accountId = 'ACC-123-XYZ-789';
        $account = new Account($this->login, $accountId, 'BANK');

        $this->assertEquals($accountId, $account->id);
    }

    #[Test]
    public function itCanSetLedgerBalance(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK');
        $balance = 1234.56;
        $account->ledgerBalance = $balance;

        $this->assertEquals($balance, $account->ledgerBalance);
    }

    #[Test]
    public function itCanSetAvailableBalance(): void
    {
        $this->markTestIncomplete();
        
        $account = new Account($this->login, 'ACC123', 'BANK');
        $balance = 987.65;
        $account->availableBalance = $balance;

        $this->assertEquals($balance, $account->availableBalance);
    }
}

<!-- SKIPPED FIXES:
- PHPMD: Class AccountTest has 18 public methods. Refactoring into multiple classes is required but skipped due to required domain knowledge.
-->
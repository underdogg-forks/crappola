<?php

namespace Tests\Unit;

use App\Libraries\Bank;
use App\Libraries\Finance;
use App\Libraries\Login;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings("TooManyPublicMethods")
 */
class LoginTest extends TestCase
{
    private Bank $bank;

    protected function setUp(): void
    {
        parent::setUp();
        $finance = new Finance();
        $this->bank = new Bank($finance, 'TEST_FID', 'https://test.example.com', 'TEST_ORG');
    }

    #[Test]
    public function itConstructsLoginWithCredentials(): void
    {
        $this->markTestIncomplete();
        
        $userId = 'test_user';
        $password = 'test_password';

        $login = new Login($this->bank, $userId, $password);

        $this->assertInstanceOf(Login::class, $login);
        $this->assertSame($this->bank, $login->bank);
        $this->assertEquals($userId, $login->id);
        $this->assertEquals($password, $login->pass);
    }

    #[Test]
    public function itStoresBankReference(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, 'user', 'pass');

        $this->assertSame($this->bank, $login->bank);
    }

    #[Test]
    public function itStoresUserId(): void
    {
        $this->markTestIncomplete();
        
        $userId = 'user12345';
        $login = new Login($this->bank, $userId, 'password');

        $this->assertEquals($userId, $login->id);
    }

    #[Test]
    public function itStoresPassword(): void
    {
        $this->markTestIncomplete();
        
        $password = 'securePassword123';
        $login = new Login($this->bank, 'user', $password);

        $this->assertEquals($password, $login->pass);
    }

    #[Test]
    public function itHasAccountsProperty(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, 'user', 'pass');

        $this->assertObjectHasProperty('accounts', $login);
    }

    #[Test]
    public function itHasOfxVersionProperty(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, 'user', 'pass');

        $this->assertObjectHasProperty('ofxVersion', $login);
    }

    #[Test]
    public function itHasAppVersionProperty(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, 'user', 'pass');

        $this->assertObjectHasProperty('appVersion', $login);
    }

    #[Test]
    public function itAcceptsEmptyCredentials(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, '', '');

        $this->assertEquals('', $login->id);
        $this->assertEquals('', $login->pass);
    }

    #[Test]
    public function itHandlesSpecialCharactersInCredentials(): void
    {
        $this->markTestIncomplete();
        
        $userId = 'user@example.com';
        $password = 'p@$$w0rd!#$';

        $login = new Login($this->bank, $userId, $password);

        $this->assertEquals($userId, $login->id);
        $this->assertEquals($password, $login->pass);
    }

    #[Test]
    public function itCanSetOfxVersion(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, 'user', 'pass');
        $version = '103';
        $login->ofxVersion = $version;

        $this->assertEquals($version, $login->ofxVersion);
    }

    #[Test]
    public function itCanSetAppVersion(): void
    {
        $this->markTestIncomplete();
        
        $login = new Login($this->bank, 'user', 'pass');
        $appVersion = '1900';
        $login->appVersion = $appVersion;

        $this->assertEquals($appVersion, $login->appVersion);
    }
}
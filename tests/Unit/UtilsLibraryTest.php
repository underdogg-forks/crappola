<?php

namespace Tests\Unit;

use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Exception;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UtilsLibraryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itFormatsNumberWithCurrencySeparators(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $value = 1234.56;
        $currencyId = CURRENCY_DOLLAR;

        // Act
        $result = Utils::formatNumber($value, $currencyId);

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString('1,234', $result);
    }

    #[Test]
    public function itFormatsNumberWithCustomPrecision(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $value = 123.456789;
        $currencyId = CURRENCY_DOLLAR;
        $precision = 3;

        // Act
        $result = Utils::formatNumber($value, $currencyId, $precision);

        // Assert
        $this->assertEquals('123.457', $result);
    }

    #[Test]
    public function itFormatsMoneyWithCurrencyCode(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $value = 100.00;
        $currencyId = CURRENCY_DOLLAR;

        // Act
        $result = Utils::formatMoney($value, $currencyId);

        // Assert
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/\$?\d+\.\d{2}/', $result);
    }

    #[Test]
    public function itGetsPlanPriceForFreePlan(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $plan = [
            'plan' => PLAN_FREE,
            'term' => PLAN_TERM_MONTHLY,
            'num_users' => 1,
        ];

        // Act
        $price = Utils::getPlanPrice($plan);

        // Assert
        $this->assertEquals(0, $price);
    }

    #[Test]
    public function itGetsPlanPriceForProMonthly(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $plan = [
            'plan' => PLAN_PRO,
            'term' => PLAN_TERM_MONTHLY,
            'num_users' => 1,
        ];

        // Act
        $price = Utils::getPlanPrice($plan);

        // Assert
        $this->assertGreaterThan(0, $price);
    }

    #[Test]
    public function itGetsPlanPriceForEnterpriseWithMultipleUsers(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $plan = [
            'plan' => PLAN_ENTERPRISE,
            'term' => PLAN_TERM_MONTHLY,
            'num_users' => 5,
        ];

        // Act
        $price = Utils::getPlanPrice($plan);

        // Assert
        $this->assertGreaterThan(0, $price);
    }

    #[Test]
    public function itGetsErrorStringFromException(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $exception = new Exception('Test error message', 500);

        // Act
        $errorString = Utils::getErrorString($exception);

        // Assert
        $this->assertStringContainsString('Exception', $errorString);
        $this->assertStringContainsString('500', $errorString);
        $this->assertStringContainsString('Test error message', $errorString);
    }

    #[Test]
    public function itChecksIfRegistered(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $user = User::factory()->create(['registered' => true]);
        Auth::login($user);

        // Act
        $result = Utils::isRegistered();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function itChecksIfNotRegistered(): void
    {
        $this->markTestIncomplete();

        // Arrange
        // No user logged in

        // Act
        $result = Utils::isRegistered();

        // Assert
        $this->assertFalse($result);
    }

    #[Test]
    public function itChecksIfConfirmed(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $user = User::factory()->create(['confirmed' => true]);
        Auth::login($user);

        // Act
        $result = Utils::isConfirmed();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function itChecksIfNinjaEnvironment(): void
    {
        $this->markTestIncomplete();

        // Arrange
        // Environment variable should be set in phpunit.xml or .env.testing

        // Act
        $result = Utils::isNinja();

        // Assert
        $this->assertIsBool($result);
    }

    #[Test]
    public function itChecksIfSelfHostEnvironment(): void
    {
        $this->markTestIncomplete();

        // Arrange
        // Environment variable should be set in phpunit.xml or .env.testing

        // Act
        $result = Utils::isSelfHost();

        // Assert
        $this->assertIsBool($result);
    }

    #[Test]
    public function itGetsUserType(): void
    {
        $this->markTestIncomplete();

        // Arrange
        // Depends on environment configuration

        // Act
        $userType = Utils::getUserType();

        // Assert
        $this->assertContains($userType, [USER_TYPE_CLOUD_HOST, USER_TYPE_SELF_HOST]);
    }

    #[Test]
    public function itGetsNewsFeedResponse(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $userType = USER_TYPE_CLOUD_HOST;

        // Act
        $response = Utils::getNewsFeedResponse($userType);

        // Assert
        $this->assertIsObject($response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('id', $response);
        $this->assertObjectHasProperty('version', $response);
    }

    #[Test]
    public function itChecksIfWhiteLabel(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $account = Account::factory()->create();
        $user = User::factory()->create(['account_id' => $account->id]);
        Auth::login($user);

        // Act
        $result = Utils::isWhiteLabel();

        // Assert
        $this->assertIsBool($result);
    }

    #[Test]
    public function itChecksIfIsAdmin(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $user = User::factory()->create(['is_admin' => true]);
        Auth::login($user);

        // Act
        $result = Utils::isAdmin();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function itChecksIfNotAdmin(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $user = User::factory()->create(['is_admin' => false]);
        Auth::login($user);

        // Act
        $result = Utils::isAdmin();

        // Assert
        $this->assertFalse($result);
    }

    #[Test]
    public function itChecksIfPro(): void
    {
        $this->markTestIncomplete();

        // Arrange
        $account = Account::factory()->create(['pro_plan_paid' => now()]);
        $user = User::factory()->create(['account_id' => $account->id]);
        Auth::login($user);

        // Act
        $result = Utils::isPro();

        // Assert
        $this->assertIsBool($result);
    }

    #[Test]
    public function itGetsLocaleRegion(): void
    {
        $this->markTestIncomplete();

        // Arrange
        app()->setLocale('en_US');

        // Act
        $region = Utils::getLocaleRegion();

        // Assert
        $this->assertEquals('en', $region);
    }

    #[Test]
    public function itGetsLocaleRegionForSimpleLocale(): void
    {
        $this->markTestIncomplete();

        // Arrange
        app()->setLocale('fr');

        // Act
        $region = Utils::getLocaleRegion();

        // Assert
        $this->assertEquals('fr', $region);
    }

    #[Test]
    public function itChecksIfEnglishLocale(): void
    {
        $this->markTestIncomplete();

        // Arrange
        app()->setLocale('en');

        // Act
        $result = Utils::isEnglish();

        // Assert
        $this->assertTrue($result);
    }

    #[Test]
    public function itChecksIfNotEnglishLocale(): void
    {
        $this->markTestIncomplete();

        // Arrange
        app()->setLocale('fr');

        // Act
        $result = Utils::isEnglish();

        // Assert
        $this->assertFalse($result);
    }
}
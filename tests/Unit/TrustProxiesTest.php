<?php

namespace Tests\Unit;

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TrustProxiesTest extends TestCase
{
    private TrustProxies $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new TrustProxies();
    }

    #[Test]
    public function itConstructsTrustProxiesMiddleware(): void
    {
        $this->markTestIncomplete();

        $middleware = new TrustProxies();

        $this->assertInstanceOf(TrustProxies::class, $middleware);
    }

    #[Test]
    public function itHasHeadersProperty(): void
    {
        $this->markTestIncomplete();

        $reflection = new ReflectionClass($this->middleware);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);

        $this->assertIsInt($property->getValue($this->middleware));
    }

    #[Test]
    public function itHasProxiesPropertyDefined(): void
    {
        $this->markTestIncomplete();

        $reflection = new ReflectionClass($this->middleware);

        $this->assertTrue($reflection->hasProperty('proxies'));
    }

    #[Test]
    public function itInitializesProxiesAsNullByDefault(): void
    {
        $this->markTestIncomplete();

        $reflection = new ReflectionClass($this->middleware);
        $property = $reflection->getProperty('proxies');
        $property->setAccessible(true);

        $this->assertNull($property->getValue($this->middleware));
    }

    #[Test]
    public function itCombinesAllProxyHeadersWithBitwiseOr(): void
    {
        $this->markTestIncomplete();

        $reflection = new ReflectionClass($this->middleware);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($this->middleware);

        $expected = Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB;

        $this->assertEquals($expected, $headers);
    }

    #[Test]
    public function itExtendsLaravelTrustProxiesMiddleware(): void
    {
        $this->markTestIncomplete();

        $parent = get_parent_class($this->middleware);

        $this->assertEquals('Illuminate\Http\Middleware\TrustProxies', $parent);
    }
}
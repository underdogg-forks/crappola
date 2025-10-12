<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class RectorLaravelConfigurationTest extends TestCase
{
    private string $configPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configPath = __DIR__ . '/../../rector-laravel.php';
    }

    #[Test]
    public function itExistsAndIsReadable(): void
    {
        $this->assertFileExists($this->configPath);
        $this->assertFileIsReadable($this->configPath);
    }

    #[Test]
    public function itHasValidPhpSyntax(): void
    {
        $output = [];
        $returnCode = 0;
        
        exec("php -l {$this->configPath}", $output, $returnCode);
        
        $this->assertEquals(0, $returnCode, 'rector-laravel.php has syntax errors');
    }

    #[Test]
    public function itDeclaresStrictTypes(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('declare(strict_types=1);', $contents);
    }

    #[Test]
    public function itReturnsClosure(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('return static function (RectorConfig $rectorConfig): void {', $contents);
    }

    #[Test]
    public function itConfiguresPathsToProcess(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('$rectorConfig->paths([', $contents);
        $this->assertStringContainsString("__DIR__ . '/app'", $contents);
        $this->assertStringContainsString("__DIR__ . '/config'", $contents);
        $this->assertStringContainsString("__DIR__ . '/routes'", $contents);
    }

    #[Test]
    public function itEnablesImportNames(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('$rectorConfig->importNames(true)', $contents);
    }

    #[Test]
    public function itEnablesShortClassImports(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('$rectorConfig->importShortClasses()', $contents);
    }

    #[Test]
    public function itConfiguresCodeQualityRules(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('SimplifyConditionsRector::class', $contents);
        $this->assertStringContainsString('SimplifyForeachToArrayFilterRector::class', $contents);
        $this->assertStringContainsString('SimplifyIfElseToTernaryRector::class', $contents);
        $this->assertStringContainsString('SimplifyUselessVariableRector::class', $contents);
    }

    #[Test]
    public function itConfiguresRuleSets(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('$rectorConfig->sets([', $contents);
        $this->assertStringContainsString('SetList::TYPE_DECLARATION', $contents);
        $this->assertStringContainsString('SetList::EARLY_RETURN', $contents);
    }

    #[Test]
    public function itIncludesUsageComment(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('// rector process app --config=rector-laravel.php', $contents);
    }

    #[Test]
    public function itImportsRequiredRectorClasses(): void
    {
        $contents = file_get_contents($this->configPath);
        $this->assertStringContainsString('use Rector\Config\RectorConfig;', $contents);
        $this->assertStringContainsString('use Rector\\CodeQuality\\Rector\\', $contents);
        $this->assertStringContainsString('use RectorLaravel\Set\LaravelSetList;', $contents);
    }

    #[Test]
    public function itHasConsistentCommentFormatting(): void
    {
        $contents = file_get_contents($this->configPath);
        
        // Check that all commented-out rules use consistent formatting
        $lines = explode("\n", $contents);
        $commentedRuleLines = array_filter($lines, function ($line) {
            return preg_match('/^\s+\/\/[A-Z]/', trim($line));
        });
        
        foreach ($commentedRuleLines as $line) {
            // Verify proper spacing after //
            $this->assertMatchesRegularExpression('/^\s+\/\/[A-Z]/', trim($line), 
                "Comment line should have consistent spacing: {$line}");
        }
    }
}
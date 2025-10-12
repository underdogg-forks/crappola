<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Parser;

class GitHubWorkflowPintTest extends TestCase
{
    private string $workflowPath;
    private array $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflowPath = __DIR__ . '/../../.github/workflows/pint.yml';

        if (!file_exists($this->workflowPath)) {
            $this->markTestSkipped('pint.yml workflow file not found');
        }

        $contents = file_get_contents($this->workflowPath);
        $parser = new Parser();
        $this->workflow = $parser->parse($contents);
    }

    #[Test]
    public function itHasValidYamlStructure(): void
    {
        $this->assertIsArray($this->workflow);
        $this->assertNotEmpty($this->workflow);
    }

    #[Test]
    public function itHasDescriptiveName(): void
    {
        $this->assertArrayHasKey('name', $this->workflow);
        $this->assertEquals('Laravel Pint', $this->workflow['name']);
    }

    #[Test]
    public function itUsesManualWorkflowDispatch(): void
    {
        $this->assertArrayHasKey('on', $this->workflow);
        $this->assertArrayHasKey('workflow_dispatch', $this->workflow['on']);
    }

    #[Test]
    public function itDefinesPintJob(): void
    {
        $this->assertArrayHasKey('jobs', $this->workflow);
        $this->assertArrayHasKey('pint', $this->workflow['jobs']);
    }

    #[Test]
    public function itRunsOnUbuntuLatest(): void
    {
        $job = $this->workflow['jobs']['pint'];
        $this->assertArrayHasKey('runs-on', $job);
        $this->assertEquals('ubuntu-latest', $job['runs-on']);
    }

    #[Test]
    public function itChecksOutRepository(): void
    {
        $steps = $this->workflow['jobs']['pint']['steps'];
        $checkoutStep = null;

        foreach ($steps as $step) {
            if (isset($step['name']) && str_contains($step['name'], 'Checkout')) {
                $checkoutStep = $step;
                break;
            }
        }

        $this->assertNotNull($checkoutStep, 'Checkout step not found');
        $this->assertEquals('actions/checkout@v4', $checkoutStep['uses']);
    }

    #[Test]
    public function itUsesPhp82(): void
    {
        $steps = $this->workflow['jobs']['pint']['steps'];
        $phpSetupStep = null;

        foreach ($steps as $step) {
            if (isset($step['name']) && str_contains($step['name'], 'Set up PHP')) {
                $phpSetupStep = $step;
                break;
            }
        }

        $this->assertNotNull($phpSetupStep, 'PHP setup step not found');
        $this->assertEquals('shivammathur/setup-php@v2', $phpSetupStep['uses']);
        $this->assertEquals('8.2', $phpSetupStep['with']['php-version']);
    }

    #[Test]
    public function itInstallsComposerDependencies(): void
    {
        $steps = $this->workflow['jobs']['pint']['steps'];
        $installStep = null;

        foreach ($steps as $step) {
            if (isset($step['name']) && str_contains($step['name'], 'Install dependencies')) {
                $installStep = $step;
                break;
            }
        }

        $this->assertNotNull($installStep, 'Install dependencies step not found');
        $this->assertStringContainsString('composer install', $installStep['run']);
        $this->assertStringContainsString('--prefer-dist', $installStep['run']);
        $this->assertStringContainsString('--no-interaction', $installStep['run']);
    }

    #[Test]
    public function itRunsPintCommand(): void
    {
        $steps = $this->workflow['jobs']['pint']['steps'];
        $pintStep = null;

        foreach ($steps as $step) {
            if (isset($step['name']) && str_contains($step['name'], 'Run Laravel Pint')) {
                $pintStep = $step;
                break;
            }
        }

        $this->assertNotNull($pintStep, 'Pint execution step not found');
        $this->assertStringContainsString('vendor/bin/pint', $pintStep['run']);
    }

    #[Test]
    public function itCommitsAndPushesChanges(): void
    {
        $steps = $this->workflow['jobs']['pint']['steps'];
        $commitStep = null;

        foreach ($steps as $step) {
            if (isset($step['name']) && str_contains($step['name'], 'Commit')) {
                $commitStep = $step;
                break;
            }
        }

        $this->assertNotNull($commitStep, 'Commit step not found');
        $this->assertStringContainsString('git config', $commitStep['run']);
        $this->assertStringContainsString('git commit', $commitStep['run']);
        $this->assertStringContainsString('style: Apply Laravel Pint fixes', $commitStep['run']);
        $this->assertStringContainsString('github-actions[bot]', $commitStep['run']);
    }
}
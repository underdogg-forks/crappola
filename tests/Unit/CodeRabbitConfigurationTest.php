<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Parser;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class CodeRabbitConfigurationTest extends TestCase
{
    private string $configPath;
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configPath = __DIR__ . '/../../.coderabbit.yaml';

        if (!file_exists($this->configPath)) {
            $this->markTestSkipped('.coderabbit.yaml file not found');
        }

        $parser = new Parser();
        $this->config = $parser->parse(file_get_contents($this->configPath));
    }

    #[Test]
    public function itHasValidYamlStructure(): void
    {
        $this->assertIsArray($this->config, '.coderabbit.yaml must be valid YAML');
        $this->assertNotEmpty($this->config, '.coderabbit.yaml cannot be empty');
    }

    #[Test]
    public function itUsesEnglishLanguage(): void
    {
        $this->assertArrayHasKey('language', $this->config);
        $this->assertEquals('en-US', $this->config['language']);
    }

    #[Test]
    public function itConfiguresReviewSettings(): void
    {
        $this->assertArrayHasKey('reviews', $this->config);
        $this->assertIsArray($this->config['reviews']);
    }

    #[Test]
    public function itSetsAssertiveReviewProfile(): void
    {
        $this->assertArrayHasKey('profile', $this->config['reviews']);
        $this->assertEquals('assertive', $this->config['reviews']['profile']);
    }

    #[Test]
    public function itEnablesUnitTestSuggestions(): void
    {
        $this->assertArrayHasKey('finishing_touches', $this->config['reviews']);
        $this->assertArrayHasKey('unit_tests', $this->config['reviews']['finishing_touches']);
        $this->assertTrue(
            $this->config['reviews']['finishing_touches']['unit_tests']['enabled'],
            'Unit test suggestions should be enabled'
        );
    }

    #[Test]
    public function itEnablesDocstringSuggestions(): void
    {
        $this->assertArrayHasKey('finishing_touches', $this->config['reviews']);
        $this->assertArrayHasKey('docstrings', $this->config['reviews']['finishing_touches']);
        $this->assertTrue(
            $this->config['reviews']['finishing_touches']['docstrings']['enabled'],
            'Docstring suggestions should be enabled'
        );
    }

    #[Test]
    public function itConfiguresCorrectBaseBranches(): void
    {
        $this->assertArrayHasKey('auto_review', $this->config['reviews']);
        $this->assertArrayHasKey('base_branches', $this->config['reviews']['auto_review']);

        $baseBranches = $this->config['reviews']['auto_review']['base_branches'];
        $this->assertIsArray($baseBranches);
        $this->assertContains('master', $baseBranches);
        $this->assertContains('develop', $baseBranches);
    }

    #[Test]
    public function itConfiguresPhpstanTool(): void
    {
        $this->assertArrayHasKey('tools', $this->config);
        $this->assertArrayHasKey('phpstan', $this->config['tools']);
        $this->assertTrue($this->config['tools']['phpstan']['enabled']);
    }

    #[Test]
    public function itSetsPhpstanLevel(): void
    {
        $level = $this->config['tools']['phpstan']['level'];
        $this->assertEquals('0', $level, 'PHPStan level should be 0 for gradual adoption');
    }

    #[Test]
    public function itEnablesPhpcsTool(): void
    {
        $this->assertArrayHasKey('phpcs', $this->config['tools']);
        $this->assertTrue($this->config['tools']['phpcs']['enabled']);
    }

    #[Test]
    public function itEnablesEslintTool(): void
    {
        $this->assertArrayHasKey('eslint', $this->config['tools']);
        $this->assertTrue($this->config['tools']['eslint']['enabled']);
    }

    #[Test]
    public function itEnablesActionlintForGithubWorkflows(): void
    {
        $this->assertArrayHasKey('actionlint', $this->config['tools']);
        $this->assertTrue($this->config['tools']['actionlint']['enabled']);
    }

    #[Test]
    public function itDefinesUnitTestPathInstructions(): void
    {
        $this->assertArrayHasKey('code_generation', $this->config);
        $this->assertArrayHasKey('unit_tests', $this->config['code_generation']);
        $this->assertArrayHasKey('path_instructions', $this->config['code_generation']['unit_tests']);

        $instructions = $this->config['code_generation']['unit_tests']['path_instructions'];
        $this->assertIsArray($instructions);
        $this->assertNotEmpty($instructions);
    }

    #[Test]
    public function itConfiguresUnitTestConventions(): void
    {
        $instructions = $this->config['code_generation']['unit_tests']['path_instructions'];

        $unitTestPath = null;
        foreach ($instructions as $instruction) {
            if ($instruction['path'] === 'tests/Unit/**') {
                $unitTestPath = $instruction;
                break;
            }
        }

        $this->assertNotNull($unitTestPath, 'Unit test path instructions not found');
        $this->assertArrayHasKey('instructions', $unitTestPath);
        $this->assertStringContainsString('it_', $unitTestPath['instructions']);
        $this->assertStringContainsString('#[Test]', $unitTestPath['instructions']);
    }

    #[Test]
    public function itDisablesJiraIntegration(): void
    {
        $this->assertArrayHasKey('chat', $this->config);
        $this->assertArrayHasKey('integrations', $this->config['chat']);
        $this->assertArrayHasKey('jira', $this->config['chat']['integrations']);
        $this->assertEquals('disabled', $this->config['chat']['integrations']['jira']['usage']);
    }

    #[Test]
    public function itDisablesLinearIntegration(): void
    {
        $this->assertArrayHasKey('linear', $this->config['chat']['integrations']);
        $this->assertEquals('disabled', $this->config['chat']['integrations']['linear']['usage']);
    }
}
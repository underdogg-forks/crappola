<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PintConfigurationTest extends TestCase
{
    private string $configPath;
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configPath = __DIR__ . '/../../pint.json';
        $this->config = json_decode(file_get_contents($this->configPath), true);
    }

    #[Test]
    public function itHasValidJsonStructure(): void
    {
        $this->assertIsArray($this->config, 'pint.json must be valid JSON');
        $this->assertNotEmpty($this->config, 'pint.json cannot be empty');
    }

    #[Test]
    public function itDefinesPresetConfiguration(): void
    {
        $this->assertArrayHasKey('preset', $this->config);
        $this->assertEquals('per', $this->config['preset'], 'Preset should be PER (PHP Evolving Recommendation)');
    }

    #[Test]
    public function itExcludesNecessaryDirectories(): void
    {
        $this->assertArrayHasKey('exclude', $this->config);
        $this->assertIsArray($this->config['exclude']);

        $expectedExclusions = [
            'application/views/**/pdf/',
            'resources',
            'storage',
        ];

        foreach ($expectedExclusions as $exclusion) {
            $this->assertContains(
                $exclusion,
                $this->config['exclude'],
                "Expected exclusion '{$exclusion}' not found in configuration"
            );
        }
    }

    #[Test]
    public function itDefinesRulesConfiguration(): void
    {
        $this->assertArrayHasKey('rules', $this->config);
        $this->assertIsArray($this->config['rules']);
        $this->assertNotEmpty($this->config['rules'], 'Rules configuration cannot be empty');
    }

    #[Test]
    public function itEnforcesPsr12Standards(): void
    {
        $this->assertArrayHasKey('@PSR12', $this->config['rules']);
        $this->assertTrue($this->config['rules']['@PSR12'], 'PSR-12 standards must be enabled');
    }

    #[Test]
    public function itConfiguresBinaryOperatorSpacesForAlignment(): void
    {
        $this->assertArrayHasKey('binary_operator_spaces', $this->config['rules']);
        $operators = $this->config['rules']['binary_operator_spaces'];

        $this->assertArrayHasKey('operators', $operators);
        $this->assertEquals('align_single_space_minimal', $operators['operators']['=']);
        $this->assertEquals('align_single_space_minimal', $operators['operators']['=>']);
    }

    #[Test]
    public function itUsesShortArraySyntax(): void
    {
        $this->assertArrayHasKey('array_syntax', $this->config['rules']);
        $this->assertEquals('short', $this->config['rules']['array_syntax']['syntax']);
    }

    #[Test]
    public function itUsesSingleQuotesForStrings(): void
    {
        $this->assertArrayHasKey('single_quote', $this->config['rules']);
        $this->assertTrue($this->config['rules']['single_quote'], 'Single quotes should be enforced');
    }

    #[Test]
    public function itDisablesStrictComparison(): void
    {
        $this->assertArrayHasKey('strict_comparison', $this->config['rules']);
        $this->assertFalse($this->config['rules']['strict_comparison'], 'Strict comparison is intentionally disabled');
    }

    #[Test]
    public function itConfiguresOrderedImports(): void
    {
        $this->assertArrayHasKey('ordered_imports', $this->config['rules']);
        $this->assertArrayHasKey('sort_algorithm', $this->config['rules']['ordered_imports']);
        $this->assertEquals('alpha', $this->config['rules']['ordered_imports']['sort_algorithm']);
    }

    #[Test]
    public function itConfiguresClassElementOrdering(): void
    {
        $this->assertArrayHasKey('ordered_class_elements', $this->config['rules']);
        $ordering = $this->config['rules']['ordered_class_elements'];

        $this->assertArrayHasKey('order', $ordering);
        $this->assertIsArray($ordering['order']);

        // Verify critical ordering elements
        $expectedOrder = ['use_trait', 'constant', 'property_public', 'construct', 'method_public'];
        foreach ($expectedOrder as $element) {
            $this->assertContains($element, $ordering['order'], "Expected element '{$element}' in ordering");
        }
    }

    #[Test]
    public function itEnablesBlankLinesBeforeReturnStatements(): void
    {
        $this->assertArrayHasKey('blank_line_before_statement', $this->config['rules']);
        $this->assertArrayHasKey('statements', $this->config['rules']['blank_line_before_statement']);
        $this->assertContains('return', $this->config['rules']['blank_line_before_statement']['statements']);
    }

    #[Test]
    public function itRemovesUnusedImports(): void
    {
        $this->assertArrayHasKey('no_unused_imports', $this->config['rules']);
        $this->assertTrue($this->config['rules']['no_unused_imports']);
    }

    #[Test]
    public function itDisablesYodaStyleConditions(): void
    {
        $this->assertArrayHasKey('yoda_style', $this->config['rules']);
        $this->assertFalse($this->config['rules']['yoda_style'], 'Yoda style should be disabled for readability');
    }

    #[Test]
    public function itConfiguresConcatSpacing(): void
    {
        $this->assertArrayHasKey('concat_space', $this->config['rules']);
        $this->assertArrayHasKey('spacing', $this->config['rules']['concat_space']);
        $this->assertEquals('one', $this->config['rules']['concat_space']['spacing']);
    }
}
<?php

namespace Tests\Unit;

use App\Libraries\HTMLUtils;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HTMLUtilsTest extends TestCase
{
    #[Test]
    public function testSanitizeHTMLScenarios()
    {
        $utils = new HTMLUtils();
        $cases = [
            '<p>Hello World</p>' => [
                'contains'    => ['Hello World', '<p>'],
                'notContains' => [],
            ],
            '<p>Hello</p><script>alert("xss")</script>' => [
                'contains'    => ['Hello'],
                'notContains' => ['<script>', 'alert'],
            ],
            '<a href="#" onclick="alert()">Link</a>' => [
                'contains'    => ['Link'],
                'notContains' => ['onclick'],
            ],
            '&lt;p&gt;Hello&lt;/p&gt;' => [
                'contains'    => ['Hello'],
                'notContains' => [],
            ],
            '' => [
                'equals' => '',
            ],
            '<a href="https://example.com">Link</a>' => [
                'contains'    => ['href', 'example.com'],
                'notContains' => [],
            ],
        ];

        foreach ($cases as $input => $expected) {
            $result = $utils->sanitizeHTML($input);

            if (isset($expected['equals'])) {
                $this->assertEquals($expected['equals'], $result);
                continue;
            }
            foreach ($expected['contains'] as $str) {
                $this->assertStringContainsString($str, $result);
            }
            foreach ($expected['notContains'] as $str) {
                $this->assertStringNotContainsString($str, $result);
            }
        }
    }

    #[Test]
    public function testSanitizeCSSScenarios()
    {
        $utils = new HTMLUtils();
        $cases = [
            '.class { color: red; }' => [
                'contains'    => ['color', 'red'],
                'notContains' => [],
            ],
            '.class { behavior: url(xss.htc); }' => [
                'contains'    => [],
                'notContains' => ['behavior'],
            ],
            'body { background: white; }' => [
                'contains'    => ['.body'],
                'notContains' => ['body {'],
                'lower'       => true,
            ],
            '' => [
                'equals' => '',
            ],
            '.class { color: red !important; }' => [
                'contains'    => ['!important'],
                'notContains' => [],
            ],
            '@media (max-width: 600px) { .class { color: blue; } }' => [
                'notEmpty' => true,
            ],
        ];

        foreach ($cases as $input => $expected) {
            $result = $utils->sanitizeCSS($input);

            if (isset($expected['equals'])) {
                $this->assertEquals($expected['equals'], $result);
                continue;
            }
            if (isset($expected['notEmpty'])) {
                $this->assertNotEmpty($result);
                continue;
            }
            foreach ($expected['contains'] as $str) {
                $this->assertStringContainsString($str, $result);
            }
            foreach ($expected['notContains'] as $str) {
                if (!empty($expected['lower'])) {
                    $this->assertStringNotContainsString($str, strtolower($result));
                    continue;
                }
                $this->assertStringNotContainsString($str, $result);
            }
        }
    }
}
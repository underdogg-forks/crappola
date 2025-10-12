<?php

namespace Tests\Unit;

use App\Libraries\Utils;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UtilsTest extends TestCase
{
    #[Test]
    public function it_splits_name_with_first_and_last_name()
    {
        $result = Utils::splitName('John Doe');
        
        $this->assertEquals(['John', 'Doe'], $result);
    }

    #[Test]
    public function it_splits_name_with_single_name()
    {
        $result = Utils::splitName('John');
        
        $this->assertEquals(['John', ''], $result);
    }

    #[Test]
    public function it_splits_name_with_multiple_names()
    {
        $result = Utils::splitName('John Michael Doe');
        
        $this->assertEquals(['John Michael', 'Doe'], $result);
    }

    #[Test]
    public function it_splits_name_with_hyphenated_last_name()
    {
        $result = Utils::splitName('John Smith-Johnson');
        
        $this->assertEquals(['John', 'Smith-Johnson'], $result);
    }

    #[Test]
    public function it_splits_name_with_extra_whitespace()
    {
        $result = Utils::splitName('  John   Doe  ');
        
        $this->assertEquals(['John', 'Doe'], $result);
    }

    #[Test]
    public function it_splits_empty_name()
    {
        $result = Utils::splitName('');
        
        $this->assertEquals(['', ''], $result);
    }

    #[Test]
    public function it_calculates_taxes_with_single_rate()
    {
        $result = Utils::calculateTaxes(100, 10, 0);
        
        $this->assertEquals(10.00, $result);
    }

    #[Test]
    public function it_calculates_taxes_with_two_rates()
    {
        $result = Utils::calculateTaxes(100, 10, 5);
        
        $this->assertEquals(15.00, $result);
    }

    #[Test]
    public function it_calculates_taxes_with_fractional_amounts()
    {
        $result = Utils::calculateTaxes(99.99, 8.5, 2.5);
        
        $this->assertEquals(11.00, $result);
    }

    #[Test]
    public function it_calculates_taxes_with_zero_amount()
    {
        $result = Utils::calculateTaxes(0, 10, 5);
        
        $this->assertEquals(0.00, $result);
    }

    #[Test]
    public function it_calculates_taxes_with_large_amounts()
    {
        $result = Utils::calculateTaxes(10000, 10, 5);
        
        $this->assertEquals(1500.00, $result);
    }

    #[Test]
    public function it_calculates_taxes_rounding_properly()
    {
        $result = Utils::calculateTaxes(123.45, 7.5, 3.25);
        
        $this->assertEquals(13.29, $result);
    }

    #[Test]
    public function it_rounds_significant_digits_for_whole_numbers()
    {
        $result = Utils::roundSignificant(10.0);
        
        $this->assertEquals('10.00', $result);
    }

    #[Test]
    public function it_rounds_significant_digits_for_one_decimal()
    {
        $result = Utils::roundSignificant(10.5);
        
        $this->assertEquals('10.50', $result);
    }

    #[Test]
    public function it_rounds_significant_digits_for_two_decimals()
    {
        $result = Utils::roundSignificant(10.25);
        
        $this->assertEquals('10.25', $result);
    }

    #[Test]
    public function it_rounds_significant_digits_for_three_decimals()
    {
        $result = Utils::roundSignificant(10.125);
        
        $this->assertEquals('10.125', $result);
    }

    #[Test]
    public function it_rounds_significant_digits_for_four_decimals()
    {
        $result = Utils::roundSignificant(10.1234);
        
        $this->assertEquals('10.1234', $result);
    }

    #[Test]
    public function it_rounds_significant_digits_for_many_decimals()
    {
        $result = Utils::roundSignificant(10.123456789);
        
        $this->assertEquals('10.1235', $result);
    }

    #[Test]
    public function it_rounds_significant_zero()
    {
        $result = Utils::roundSignificant(0);
        
        $this->assertEquals('0.00', $result);
    }

    #[Test]
    public function it_checks_is_empty_with_null()
    {
        $this->assertTrue(Utils::isEmpty(null));
    }

    #[Test]
    public function it_checks_is_empty_with_empty_string()
    {
        $this->assertTrue(Utils::isEmpty(''));
    }

    #[Test]
    public function it_checks_is_empty_with_whitespace()
    {
        $this->assertTrue(Utils::isEmpty('   '));
    }

    #[Test]
    public function it_checks_is_empty_with_zero_string()
    {
        $this->assertFalse(Utils::isEmpty('0'));
    }

    #[Test]
    public function it_checks_is_empty_with_zero_integer()
    {
        $this->assertFalse(Utils::isEmpty(0));
    }

    #[Test]
    public function it_checks_is_empty_with_non_empty_string()
    {
        $this->assertFalse(Utils::isEmpty('test'));
    }

    #[Test]
    public function it_checks_starts_with_matching_prefix()
    {
        $this->assertTrue(Utils::startsWith('hello world', 'hello'));
    }

    #[Test]
    public function it_checks_starts_with_non_matching_prefix()
    {
        $this->assertFalse(Utils::startsWith('hello world', 'world'));
    }

    #[Test]
    public function it_checks_starts_with_empty_needle()
    {
        $this->assertTrue(Utils::startsWith('hello', ''));
    }

    #[Test]
    public function it_checks_starts_with_case_sensitive()
    {
        $this->assertFalse(Utils::startsWith('hello', 'Hello'));
    }

    #[Test]
    public function it_checks_ends_with_matching_suffix()
    {
        $this->assertTrue(Utils::endsWith('hello world', 'world'));
    }

    #[Test]
    public function it_checks_ends_with_non_matching_suffix()
    {
        $this->assertFalse(Utils::endsWith('hello world', 'hello'));
    }

    #[Test]
    public function it_checks_ends_with_empty_needle()
    {
        $this->assertTrue(Utils::endsWith('hello', ''));
    }

    #[Test]
    public function it_checks_ends_with_case_sensitive()
    {
        $this->assertFalse(Utils::endsWith('world', 'World'));
    }

    #[Test]
    public function it_formats_website_with_http()
    {
        $result = Utils::formatWebsite('http://example.com');
        
        $this->assertEquals('http://example.com', $result);
    }

    #[Test]
    public function it_formats_website_without_protocol()
    {
        $result = Utils::formatWebsite('example.com');
        
        $this->assertEquals('http://example.com', $result);
    }

    #[Test]
    public function it_formats_website_with_https()
    {
        $result = Utils::formatWebsite('https://example.com');
        
        $this->assertEquals('https://example.com', $result);
    }

    #[Test]
    public function it_formats_website_with_trailing_slash()
    {
        $result = Utils::formatWebsite('example.com/');
        
        $this->assertEquals('http://example.com/', $result);
    }

    #[Test]
    public function it_formats_empty_website()
    {
        $result = Utils::formatWebsite('');
        
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_adds_http_to_url_without_protocol()
    {
        $result = Utils::addHttp('example.com');
        
        $this->assertEquals('http://example.com', $result);
    }

    #[Test]
    public function it_does_not_add_http_to_url_with_protocol()
    {
        $result = Utils::addHttp('https://example.com');
        
        $this->assertEquals('https://example.com', $result);
    }

    #[Test]
    public function it_handles_null_url_when_adding_http()
    {
        $result = Utils::addHttp(null);
        
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_truncates_string_shorter_than_limit()
    {
        $result = Utils::truncateString('Hello', 10);
        
        $this->assertEquals('Hello', $result);
    }

    #[Test]
    public function it_truncates_string_longer_than_limit()
    {
        $result = Utils::truncateString('Hello World', 5);
        
        $this->assertEquals('Hello...', $result);
    }

    #[Test]
    public function it_truncates_string_at_exact_limit()
    {
        $result = Utils::truncateString('Hello', 5);
        
        $this->assertEquals('Hello', $result);
    }

    #[Test]
    public function it_truncates_empty_string()
    {
        $result = Utils::truncateString('', 5);
        
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_converts_number_to_alpha_for_single_digit()
    {
        $this->assertEquals('a', Utils::num2alpha(0));
        $this->assertEquals('b', Utils::num2alpha(1));
        $this->assertEquals('z', Utils::num2alpha(25));
    }

    #[Test]
    public function it_converts_number_to_alpha_for_double_letters()
    {
        $this->assertEquals('aa', Utils::num2alpha(26));
        $this->assertEquals('ab', Utils::num2alpha(27));
    }

    #[Test]
    public function it_converts_number_to_alpha_for_triple_letters()
    {
        $this->assertEquals('aaa', Utils::num2alpha(702));
    }

    #[Test]
    public function it_gets_first_value_from_multiple_values()
    {
        $result = Utils::getFirst(['one', 'two', 'three']);
        
        $this->assertEquals('one', $result);
    }

    #[Test]
    public function it_gets_first_value_from_single_value()
    {
        $result = Utils::getFirst('single');
        
        $this->assertEquals('single', $result);
    }

    #[Test]
    public function it_gets_first_value_from_empty_array()
    {
        $result = Utils::getFirst([]);
        
        $this->assertNull($result);
    }

    #[Test]
    public function it_gets_first_value_ignoring_empty_strings()
    {
        $result = Utils::getFirst(['', 'second', 'third']);
        
        $this->assertEquals('second', $result);
    }

    #[Test]
    public function it_gets_subdomain_from_url_with_subdomain()
    {
        $result = Utils::getSubdomain('https://app.example.com');
        
        $this->assertEquals('app', $result);
    }

    #[Test]
    public function it_gets_subdomain_from_url_without_subdomain()
    {
        $result = Utils::getSubdomain('https://example.com');
        
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_gets_subdomain_from_url_with_www()
    {
        $result = Utils::getSubdomain('https://www.example.com');
        
        $this->assertEquals('www', $result);
    }

    #[Test]
    public function it_replaces_subdomain_in_url()
    {
        $result = Utils::replaceSubdomain('app.example.com', 'api');
        
        $this->assertEquals('api.example.com', $result);
    }

    #[Test]
    public function it_replaces_subdomain_when_none_exists()
    {
        $result = Utils::replaceSubdomain('example.com', 'app');
        
        $this->assertEquals('app.example.com', $result);
    }

    #[Test]
    public function it_formats_city_state_zip_without_swap()
    {
        $result = Utils::cityStateZip('New York', 'NY', '10001', false);
        
        $this->assertEquals('New York, NY 10001', $result);
    }

    #[Test]
    public function it_formats_city_state_zip_with_swap()
    {
        $result = Utils::cityStateZip('New York', 'NY', '10001', true);
        
        $this->assertEquals('10001 New York, NY', $result);
    }

    #[Test]
    public function it_formats_city_state_zip_with_missing_components()
    {
        $result = Utils::cityStateZip('New York', '', '', false);
        
        $this->assertEquals('New York', $result);
    }

    #[Test]
    public function it_formats_city_state_zip_only_postal_code()
    {
        $result = Utils::cityStateZip('', '', '10001', false);
        
        $this->assertEquals('10001', $result);
    }

    #[Test]
    public function it_normalizes_chars_with_accented_characters()
    {
        $result = Utils::normalizeChars('café');
        
        $this->assertEquals('cafe', $result);
    }

    #[Test]
    public function it_normalizes_chars_with_special_characters()
    {
        $result = Utils::normalizeChars('hello™world®');
        
        $this->assertStringNotContainsString('™', $result);
        $this->assertStringNotContainsString('®', $result);
    }

    #[Test]
    public function it_normalizes_chars_with_plain_ascii()
    {
        $result = Utils::normalizeChars('hello');
        
        $this->assertEquals('hello', $result);
    }
}
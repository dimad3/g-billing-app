<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
// use Tests\TestCase;
use function format_decimal_with_precision;

class FormatDecimalWithPrecisionTest extends TestCase
{
    #[Test]
    public function formats_integer_input_with_default_decimal_places()
    {
        $result = format_decimal_with_precision(123);
        $this->assertEquals('123.00', $result);
    }

    #[Test]
    public function formats_string_integer_input_with_default_decimal_places()
    {
        $result = format_decimal_with_precision('123');
        $this->assertEquals('123.00', $result);
    }

    #[Test]
    public function formats_decimal_with_less_than_two_digits_with_default_precision()
    {
        $result = format_decimal_with_precision('123.4');
        $this->assertEquals('123.40', $result);
        $result = format_decimal_with_precision('123.');
        $this->assertEquals('123.00', $result);
        $result = format_decimal_with_precision('123');
        $this->assertEquals('123.00', $result);
    }

    #[Test]
    public function formats_decimal_with_exactly_two_digits_with_default_precision()
    {
        $result = format_decimal_with_precision('123.45');
        $this->assertEquals('123.45', $result);
    }

    #[Test]
    public function preserves_precision_for_more_than_two_decimal_digits()
    {
        $result = format_decimal_with_precision('123.4567');
        $this->assertEquals('123.4567', $result);
    }

    #[Test]
    public function formats_numeric_string_with_non_numeric_characters_using_default_precision()
    {
        $result = format_decimal_with_precision('123.45abc');
        $this->assertEquals('123.45', $result); // PHP (float) casting will stop at non-numeric chars
    }

    #[Test]
    public function formats_float_input_with_precision_based_on_decimals()
    {
        $result = format_decimal_with_precision(123.4567);
        $this->assertEquals('123.4567', $result);
    }

    #[Test]
    public function formats_large_number_with_many_decimals_correctly()
    {
        $result = format_decimal_with_precision('1000000.123456789');
        $this->assertEquals('1,000,000.123456789', $result);
    }

    #[Test]
    public function formats_input_without_decimal_separator_with_default_precision()
    {
        $result = format_decimal_with_precision('1000');
        $this->assertEquals('1,000.00', $result);
    }

    #[Test]
    public function accepts_custom_default_decimal_places()
    {
        $result = format_decimal_with_precision('123.4', 3);
        $this->assertEquals('123.400', $result);
    }

    #[Test]
    public function custom_precision_is_ignored_when_decimal_digits_exceed_two()
    {
        $result = format_decimal_with_precision('123.456', 1);
        $this->assertEquals('123.456', $result);
    }
}

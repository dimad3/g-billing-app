<?php

if (!function_exists('format_decimal_with_precision')) {
    /**
     * Format a numeric string based on the number of decimal digits.
     *
     * If the decimal separator is not '.', the number will be formatted to default decimal places.
     * If there are 0, 1, or 2 decimal digits, it will be formatted to default decimal places.
     * Otherwise, the full decimal precision will be preserved.
     *
     * Usage:
     *   echo formatNumber("123.456");       // Outputs: 123.456
     *   echo formatNumber("123.4");         // Outputs: 123.40
     *   echo formatNumber("123,45");        // Outputs: 123.00 (invalid separator, defaults to 2 decimals)
     *   echo formatNumber("100", 3);        // Outputs: 100.000
     *
     * @param string|float $input The input number to format.
     * @param int $defaultDecimalPlaces Number of decimal places to format to (default: 2).
     * @return string Formatted number as a string.
     */
    function format_decimal_with_precision($input, int $defaultDecimalPlaces = 2): string
    {
        // Convert to string to ensure consistent parsing
        $inputStr = (string) $input;

        // If the decimal separator is not '.', return number_format with default precision
        if (strpos($inputStr, '.') === false || preg_match('/[^0-9.]/', $inputStr)) {
            return number_format((float)$inputStr, $defaultDecimalPlaces);
        }

        // Split number into whole and decimal parts
        [$whole, $decimal] = explode('.', $inputStr) + [null, null];

        // Determine decimal length
        $decimalLength = strlen($decimal ?? '');

        // Format based on decimal length
        if ($decimalLength <= 2) {
            return number_format((float)$inputStr, $defaultDecimalPlaces);
        }

        // If more than 2 decimal digits, preserve original precision
        return number_format((float)$inputStr, $decimalLength);
    }
}

<?php

namespace Pkg6\Laravel\Plus\Support;

class Str extends \Illuminate\Support\Str
{
    /**
     * Explodes string into array, optionally trims values and skips empty ones.
     *
     * @param string $string String to be exploded.
     * @param string $delimiter Delimiter. Default is ','.
     * @param mixed $trim Whether to trim each element. Can be:
     *   - boolean - to trim normally;
     *   - string - custom characters to trim. Will be passed as a second argument to `trim()` function.
     *   - callable - will be called for each value instead of trim. Takes the only argument - value.
     * @param bool $skipEmpty Whether to skip empty strings between delimiters. Default is false.
     * @return array
     */
    public static function explode($string, $delimiter = ',', $trim = true, $skipEmpty = false)
    {
        $result = explode($delimiter, $string);
        if ($trim !== false) {
            if ($trim === true) {
                $trim = 'trim';
            } elseif (!is_callable($trim)) {
                $trim = function ($v) use ($trim) {
                    return trim($v, $trim);
                };
            }
            $result = array_map($trim, $result);
        }
        if ($skipEmpty) {
            // Wrapped with array_values to make array keys sequential after empty values removing
            $result = array_values(
                array_filter(
                    $result,
                    function ($value) {
                        return $value !== '';
                    }
                )
            );
        }

        return $result;
    }

    /**
     * @param $length
     * @return string
     */
    public static function randomNumber($length = 6)
    {
        $str = '';
        $chars = str_repeat(str_repeat('0123456789', 3), $length);
        for ($i = 0; $i < $length; $i++) {
            $str .= mb_substr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
        return $str;
    }

    /**
     * @param $number
     * @return array|string|string[]
     */
    public static function floatToString($number)
    {
        return str_replace(',', '.', (string)$number);
    }

    /**
     * This method provides a unicode-safe implementation of built-in PHP function `ucfirst()`.
     *
     * @param string $string the string to be proceeded
     * @param string $encoding Optional, defaults to "UTF-8"
     * @return string
     * @see https://www.php.net/manual/en/function.ucfirst.php
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public static function mb_ucfirst($string, $encoding = 'UTF-8')
    {
        $firstChar = mb_substr((string)$string, 0, 1, $encoding);
        $rest = mb_substr((string)$string, 1, null, $encoding);

        return mb_strtoupper($firstChar, $encoding) . $rest;
    }

    /**
     * This method provides a unicode-safe implementation of built-in PHP function `ucwords()`.
     *
     * @param string $string the string to be proceeded
     * @param string $encoding Optional, defaults to "UTF-8"
     * @return string
     * @see https://www.php.net/manual/en/function.ucwords
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public static function mb_ucwords($string, $encoding = 'UTF-8')
    {
        $string = (string)$string;
        if (empty($string)) {
            return $string;
        }

        $parts = preg_split('/(\s+\W+\s+|^\W+\s+|\s+)/u', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $ucfirstEven = trim(mb_substr($parts[0], -1, 1, $encoding)) === '';
        foreach ($parts as $key => $value) {
            $isEven = (bool)($key % 2);
            if ($ucfirstEven === $isEven) {
                $parts[$key] = static::mb_ucfirst($value, $encoding);
            }
        }
        return implode('', $parts);
    }

    /**
     * Returns the portion of the string that lies between the first occurrence of the start string
     * and the last occurrence of the end string after that.
     *
     * @param string $string The input string.
     * @param string $start The string marking the start of the portion to extract.
     * @param string $end The string marking the end of the portion to extract.
     * @return string|null The portion of the string between the first occurrence of
     * start and the last occurrence of end, or null if either start or end cannot be found.
     */
    public static function findBetween($string, $start, $end)
    {
        $startPos = mb_strpos($string, $start);
        if ($startPos === false) {
            return null;
        }
        $startPos += mb_strlen($start);
        $endPos = mb_strrpos($string, $end, $startPos);
        if ($endPos === false) {
            return null;
        }
        return mb_substr($string, $startPos, $endPos - $startPos);
    }

}
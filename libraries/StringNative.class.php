<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Implements PMA_StringByte interface using native PHP functions.
 *
 * @package    PhpMyAdmin-String
 * @subpackage Native
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

require_once 'libraries/StringByte.int.php';

/**
 * Implements PMA_StringByte interface using native PHP functions.
 *
 * @package    PhpMyAdmin-String
 * @subpackage Native
 */
class PMA_StringNative implements PMA_StringByte
{
    /**
     * Returns length of string depending on current charset.
     *
     * @param string $string string to count
     *
     * @return int string length
     */
    public function strlen($string)
    {
        return strlen($string);
    }

    /**
     * Returns substring from string, works depending on current charset.
     *
     * @param string $string string to count
     * @param int    $start  start of substring
     * @param int    $length length of substring
     *
     * @return string the sub string
     */
    public function substr($string, $start, $length = 2147483647)
    {
        return substr($string, $start, $length);
    }

    /**
     * Returns postion of $needle in $haystack or false if not found
     *
     * @param string $haystack the string being checked
     * @param string $needle   the string to find in haystack
     * @param int    $offset   the search offset
     *
     * @return integer position of $needle in $haystack or false
     */
    public function strpos($haystack, $needle, $offset = 0)
    {
        return strpos($haystack, $needle, $offset);
    }

    /**
     * Returns position of $needle in $haystack - case insensitive - or false if
     * not found
     *
     * @param string $haystack the string being checked
     * @param string $needle   the string to find in haystack
     * @param int    $offset   the search offset
     *
     * @return integer position of $needle in $haystack or false
     */
    public function stripos($haystack, $needle, $offset = 0)
    {
        if (('' === $haystack || false === $haystack)
            && $offset >= $this->strlen($haystack)
        ) {
            return false;
        }
        return stripos($haystack, $needle, $offset);
    }

    /**
     * Returns position of last $needle in $haystack or false if not found
     *
     * @param string $haystack the string being checked
     * @param string $needle   the string to find in haystack
     * @param int    $offset   the search offset
     *
     * @return integer position of last $needle in $haystack or false
     */
    public function strrpos($haystack, $needle, $offset = 0)
    {
        return strrpos($haystack, $needle, $offset);
    }

    /**
     * Returns position of last $needle in $haystack - case insensitive - or false
     * if not found
     *
     * @param string $haystack the string being checked
     * @param string $needle   the string to find in haystack
     * @param int    $offset   the search offset
     *
     * @return integer position of last $needle in $haystack or false
     */
    public function strripos($haystack, $needle, $offset = 0)
    {
        if (('' === $haystack || false === $haystack)
            && $offset >= $this->strlen($haystack)
        ) {
            return false;
        }
        return strripos($haystack, $needle, $offset);
    }

    /**
     * Returns part of $haystack string starting from and including the first
     * occurrence of $needle to the end of $haystack or false if not found
     *
     * @param string $haystack      the string being checked
     * @param string $needle        the string to find in haystack
     * @param bool   $before_needle the part before the needle
     *
     * @return string part of $haystack or false
     */
    public function strstr($haystack, $needle, $before_needle = false)
    {
        return strstr($haystack, $needle, $before_needle);
    }

    /**
     * Returns part of $haystack string starting from and including the first
     * occurrence of $needle to the end of $haystack - case insensitive - or false
     * if not found
     *
     * @param string $haystack      the string being checked
     * @param string $needle        the string to find in haystack
     * @param bool   $before_needle the part before the needle
     *
     * @return string part of $haystack or false
     */
    public function stristr($haystack, $needle, $before_needle = false)
    {
        return stristr($haystack, $needle, $before_needle);
    }

    /**
     * Returns the portion of haystack which starts at the last occurrence or false
     * if not found
     *
     * @param string $haystack the string being checked
     * @param string $needle   the string to find in haystack
     *
     * @return string portion of haystack which starts at the last occurrence or
     * false
     */
    public function strrchr($haystack, $needle)
    {
        return strrchr($haystack, $needle);
    }

    /**
     * Make a string lowercase
     *
     * @param string $string the string being lowercased
     *
     * @return string the lower case string
     */
    public function strtolower($string)
    {
        return strtolower($string);
    }

    /**
     * Make a string uppercase
     *
     * @param string $string the string being uppercased
     *
     * @return string the upper case string
     */
    public function strtoupper($string)
    {
        return strtoupper($string);
    }

    /**
     * Get the ordinal value of a string
     *
     * @param string $string the string for which ord is required
     *
     * @return int the ord value
     */
    public function ord($string)
    {
        return ord($string);
    }

    /**
     * Get the character of an ASCII
     *
     * @param int $ascii the ASCII code for which character is required
     *
     * @return string the character
     */
    public function chr($ascii)
    {
        return chr($ascii);
    }
};
?>

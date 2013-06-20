<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Tests for Specialized String Functions for phpMyAdmin
 *
 * @package PhpMyAdmin-test
 */

/*
 * Include to test.
 */
require_once 'libraries/string.lib.php';
require_once 'libraries/StringNativeType.class.php';
/**
 * Tests for Specialized String Functions for phpMyAdmin
 *
 * @package PhpMyAdmin-test
 */
class PMA_String_Test extends PHPUnit_Framework_TestCase
{

    /**
     * Setup function for test cases
     * 
     * @access protected
     * @return void
     */
    protected function setUp() 
    {
        $GLOBALS['PMA_StringType'] = new PMA_StringNativeType();
    }

    /**
     * Test for Str_charIsEscaped
     * 
     * @param boolean $expected Expected value from test
     * @param string  $str      String to check for
     * @param integer $pos      Character to check for
     * @param integer $start    Starting position of string
     * 
     * @return void
     * @test
     * @dataProvider charIsEscapedData
     */
    public function testCharIsEscaped($expected, $str, $pos, $start)
    {   
        $this->assertEquals(
            $expected,
            PMA_STR_charIsEscaped($str, $pos, $start)
        );
    }

    /**
     * Data provider for testCharIsEscaped
     * 
     * @return array Test data
     */
    public function charIsEscapedData() 
    {
        return array(
            array(false, 'test', -1, 0),
            array(false, 'test', 5, 3),
            array(false, 'test', 3, 5),
            array(true, '\\test', 1, -1),
            array(false, '\\\\test', 2, -1),
            array(true, '\\\\tes\\t', 6, 0)
        );
    }

    /**
     * Test for PMA_STR_numberInRangeInclusive
     * 
     * @param bool    $expected Expected value from test
     * @param integer $num      Number to check for
     * @param integer $lower    Lower bound
     * @param integer $upper    Upper bound
     * 
     * @return void
     * @test
     * @dataProvider numberInRangeData
     */
    public function testNumberInRangeInclusive(
        $expected, $num, $lower, $upper
    ) {
        $this->assertEquals(
            $expected,
            PMA_STR_numberInRangeInclusive($num, $lower, $upper)
        );
    }

    /**
     * Data provider for testNumberInRangeInclusive
     * 
     * @return void
     */
    public function numberInRangeData() 
    {
        return array(
            array(true, 2, 2, 3),
            array(true, 5, 4, 5),
            array(true, 50, 0, 100),
            array(false, -1, 0, 20),
            array(false, 31, 0, 30)
        );
    }

    /**
     * Test for PMA_STR_isSqlIdentifier
     * 
     * @param boolean $expected     Expected value from test
     * @param string  $c            Character to check for
     * @param boolean $dot_is_valid whether the dot character is valid or not
     * 
     * @return void
     * @test
     * @dataProvider isSqlIdentifierData
     */
    public function testIsSqlIdentifier($expected, $c, $dot_is_valid = false) 
    {
        $this->assertEquals(
            $expected,
            PMA_STR_isSqlIdentifier($c, $dot_is_valid)
        );
    }

    /**
     * Data provider for testIsSqlIdentifier
     * 
     * @return array Test data
     */
    public function isSqlIdentifierData() 
    {
        return array(
            array(true, '2'),
            array(true, 'a'),
            array(true, '.', true),
            array(false, '.'),
            array(true, chr(192)),
            array(false, chr(215)),
            array(false, chr(249)),
            array(true, '_'),
            array(true, '$')
        );
    }


}
?>

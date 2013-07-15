<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Tests for Table.class.php
 *
 * @package PhpMyAdmin-test
 */

/*
 * Include to test.
 */
require_once 'libraries/Table.class.php';
require_once 'libraries/Util.class.php';
require_once 'libraries/database_interface.inc.php';
require_once 'libraries/php-gettext/gettext.inc';
require_once 'libraries/url_generating.lib.php';
require_once 'libraries/Theme.class.php';
require_once 'libraries/Tracker.class.php';
require_once 'libraries/relation.lib.php';

/**
 * Tests behaviour of PMA_Table class
 *
 * @package PhpMyAdmin-test
 */
class PMA_Table_Test extends PHPUnit_Framework_TestCase
{
    /**
     * Configures environment
     *
     * @return void
     */
    protected function setUp()
    {
        /**
         * SET these to avoid undefined index error
         */
        $GLOBALS['server'] = 0;
        $GLOBALS['cfg']['Server']['DisableIS'] = false;
        $GLOBALS['cfg']['ServerDefault'] = 1;
        $GLOBALS['cfg']['MySQLManualType'] = 'viewable';
        $GLOBALS['cfg']['MySQLManualBase'] = 'http://dev.mysql.com/doc/refman';
        $GLOBALS['cfg']['ActionLinksMode'] = 'both';
        $_SESSION['PMA_Theme'] = new PMA_Theme();
        $GLOBALS['pmaThemeImage'] = 'themes/dot.gif';
        $GLOBALS['is_ajax_request'] = false;
        $GLOBALS['cfgRelation'] = PMA_getRelationsParam();
        
        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        $sql_isView_true =  "SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'PMA'
                AND TABLE_NAME = 'PMA_BookMark'";
        
        $sql_isView_false =  "SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'PMA'
                AND TABLE_NAME = 'PMA_BookMark_2'";
                
        $sql_isUpdatableView_true = "SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'PMA'
                AND TABLE_NAME = 'PMA_BookMark'
                AND IS_UPDATABLE = 'YES'";
                
        $sql_isUpdatableView_false = "SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'PMA'
                AND TABLE_NAME = 'PMA_BookMark_2'
                AND IS_UPDATABLE = 'YES'";
                
        $sql_analyzeStructure_true = "SELECT COLUMN_NAME, DATA_TYPE
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = 'PMA'
                AND TABLE_NAME = 'PMA_BookMark'";
   
        $fetchResult = array(
            array(
                $sql_isView_true,
                null,
                null,
                null,
                0,
                true
            ),
            array(
                $sql_isView_false,
                null,
                null,
                null,
                0,
                false
            ),
            array(
                $sql_isUpdatableView_true,
                null,
                null,
                null,
                0,
                true
            ),
            array(
                $sql_isUpdatableView_false,
                null,
                null,
                null,
                0,
                false
            ),
            array(
                $sql_analyzeStructure_true,
                null,
                null,
                null,
                0,
                array(
                    array('COLUMN_NAME'=>'COLUMN_NAME', 'DATA_TYPE'=>'DATA_TYPE')
                )
            ),
        );
        
        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        $dbi->expects($this->any())->method('fetchResult')
            ->will($this->returnValueMap($fetchResult));
        
        $GLOBALS['dbi'] = $dbi;
    }
    
    /**
     * Test object creating
     *
     * @return void
     */
    public function testCreate()
    {
        $table = new PMA_Table('table1', 'pma_test');
        $this->assertInstanceOf('PMA_Table', $table);
    }

    /**
     * Test for constructor
     *
     * @return void
     */
    public function testConstruct()
    {
        $table = new PMA_Table("PMA_BookMark", "PMA");
        $this->assertEquals(
            'PMA_BookMark',
            $table->__toString()
        );
        $this->assertEquals(
            'PMA_BookMark',
            $table->getName()
        );
        $this->assertEquals(
            'PMA',
            $table->getDbName()
        );
        $this->assertEquals(
            'PMA.PMA_BookMark',
            $table->getFullName()
        );
    }

    /**
     * Test object creating
     *
     * @return void
     */

    /**
     * Test Set & Get
     *
     * @return void
     */
    public function testSetAndGet()
    {
        $table = new PMA_Table('table1', 'pma_test');
        $table->set('production', 'Phpmyadmin');
        $table->set('db', 'mysql');
        $this->assertEquals(
            "Phpmyadmin",
            $table->get("production")
        );
        $this->assertEquals(
            "mysql",
            $table->get("db")
        );
    }

    /**
     * Test name validation
     *
     * @param string  $name   name to test
     * @param boolena $result expected result
     *
     * @return void
     *
     * @dataProvider dataValidateName
     */
    public function testValidateName($name, $result)
    {
        $this->assertEquals(
            $result,
            PMA_Table::isValidName($name)
        );
    }

    /**
     * Data provider for name validation
     *
     * @return array with test data
     */
    public function dataValidateName()
    {
        return array(
            array('test', true),
            array('te/st', false),
            array('te.st', false),
            array('te\\st', false),
        );
    }
    
    /**
     * Test for isView
     *
     * @return void
     */
    public function testIsView()
    {
        $this->assertEquals(
            false,
            PMA_Table::isView()
        );

        //validate that it is the same as DBI fetchResult
        $this->assertEquals(
            true,
            PMA_Table::isView('PMA', 'PMA_BookMark')
        );
        $this->assertEquals(
            false,
            PMA_Table::isView('PMA', 'PMA_BookMark_2')
        );
    }

    /**
     * Test for isUpdatableView
     *
     * @return void
     */
    public function testIsUpdatableView()
    {
        $this->assertEquals(
            false,
            PMA_Table::isUpdatableView()
        );
        
        //validate that it is the same as DBI fetchResult
        $this->assertEquals(
            true,
            PMA_Table::isUpdatableView('PMA', 'PMA_BookMark')
        );
        $this->assertEquals(
            false,
            PMA_Table::isUpdatableView('PMA', 'PMA_BookMark_2')
        );
    }

    /**
     * Test for analyzeStructure
     *
     * @return void
     */
    public function testAnalyzeStructure()
    {
        $this->assertEquals(
            false,
            PMA_Table::analyzeStructure()
        );
        
        //validate that it is the same as DBI fetchResult
        $show_create_table = PMA_Table::analyzeStructure('PMA', 'PMA_BookMark');
        $this->assertEquals(
            array('type'=>'DATA_TYPE'),
            $show_create_table[0]['create_table_fields']['COLUMN_NAME']
        );
    }

}
?>

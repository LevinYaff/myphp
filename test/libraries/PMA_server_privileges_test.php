<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * tests for server_privileges.lib.php
 *
 * @package PhpMyAdmin-test
 */

/*
 * Include to test.
 */
require_once 'libraries/Util.class.php';
require_once 'libraries/php-gettext/gettext.inc';
require_once 'libraries/url_generating.lib.php';
require_once 'libraries/Theme.class.php';
require_once 'libraries/database_interface.inc.php';
require_once 'libraries/Message.class.php';
require_once 'libraries/sanitizing.lib.php';
require_once 'libraries/sqlparser.lib.php';
require_once 'libraries/js_escape.lib.php';
require_once 'libraries/Message.class.php';
require_once 'libraries/Response.class.php';
require_once 'libraries/server_privileges.lib.php';

/**
 * PMA_ServerPrivileges_Test class
 *
 * this class is for testing server_privileges.lib.php functions
 *
 * @package PhpMyAdmin-test
 */
class PMA_ServerPrivileges_Test extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares environment for the test.
     *
     * @return void
     */
    public function setUp()
    {
        //$_REQUEST
        $_REQUEST['log'] = "index1";
        $_REQUEST['pos'] = 3;

        //$GLOBALS
        $GLOBALS['cfg']['MaxRows'] = 10;
        $GLOBALS['cfg']['ServerDefault'] = "server";
        $GLOBALS['cfg']['RememberSorting'] = true;
        $GLOBALS['cfg']['SQP'] = array();
        $GLOBALS['cfg']['MaxCharactersInDisplayedSQL'] = 1000;
        $GLOBALS['cfg']['ShowSQL'] = true;
        $GLOBALS['cfg']['TableNavigationLinksMode'] = 'icons';
        $GLOBALS['cfg']['LimitChars'] = 100;
        $GLOBALS['cfg']['DBG']['sql'] = false;
        $GLOBALS['cfg']['AllowThirdPartyFraming'] = false;

        $GLOBALS['table'] = "table";
        $GLOBALS['PMA_PHP_SELF'] = PMA_getenv('PHP_SELF');
        $GLOBALS['pmaThemeImage'] = 'image';
        $GLOBALS['server'] = 1;
        
        //$_POST
        $_POST['pred_password'] = 'none';
        //$_SESSION
        $_SESSION['PMA_Theme'] = PMA_Theme::load('./themes/pmahomme');
        $_SESSION['PMA_Theme'] = new PMA_Theme();
        
        //Mock DBI
        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        $dbi->expects($this->any())
            ->method('fetchResult')
            ->will(
                $this->returnValue(
                    array(
                        'grant user1 select',
                        'grant user2 delete'
                    )
                )
            );

        $fetchSingleRow = array('password' => 'pma_password');
        $dbi->expects($this->any())->method('fetchSingleRow')
            ->will($this->returnValue($fetchSingleRow));

        $fetchValue = array('key1' => 'value1');
        $dbi->expects($this->any())->method('fetchValue')
            ->will($this->returnValue($fetchValue));
        
        $dbi->expects($this->any())->method('tryQuery')
            ->will($this->returnValue(true));
        
        $GLOBALS['dbi'] = $dbi;
    }

    /**
     * Test for PMA_getDataForDBInfo
     *
     * @return void
     */
    public function testPMAGetDataForDBInfo()
    {
        $_REQUEST['tablename'] = "PMA_tablename";
        $_REQUEST['dbname'] = "PMA_dbname";
        list($dbname, $tablename, $db_and_table, $dbname_is_wildcard) 
            = PMA_getDataForDBInfo();
        $this->assertEquals(
            "PMA_dbname",
            $dbname
        );
        $this->assertEquals(
            "PMA_tablename",
            $tablename
        );
        $this->assertEquals(
            "`PMA_dbname`.`PMA_tablename`",
            $db_and_table
        );
        $this->assertEquals(
            true,
            $dbname_is_wildcard
        );
        
        //pre variable have been defined
        $_REQUEST['pred_tablename'] = "PMA_pred__tablename";
        $_REQUEST['pred_dbname'] = "PMA_pred_dbname";
        list($dbname, $tablename, $db_and_table, $dbname_is_wildcard) 
            = PMA_getDataForDBInfo();
        $this->assertEquals(
            "PMA_pred_dbname",
            $dbname
        );
        $this->assertEquals(
            "PMA_pred__tablename",
            $tablename
        );
        $this->assertEquals(
            "`PMA_pred_dbname`.`PMA_pred__tablename`",
            $db_and_table
        );
        $this->assertEquals(
            true,
            $dbname_is_wildcard
        );
        
    }


    /**
     * Test for PMA_getDataForChangeOrCopyUser
     *
     * @return void
     */
    public function testPMAGetDataForChangeOrCopyUser()
    {
        //$_REQUEST['change_copy'] not set
        list($queries, $password) = PMA_getDataForChangeOrCopyUser();
        $this->assertEquals(
            null,
            $queries
        );
        $this->assertEquals(
            null,
            $queries
        );

        //$_REQUEST['change_copy'] is set
        $_REQUEST['change_copy'] = true;
        $_REQUEST['old_username'] = 'PMA_old_username';
        $_REQUEST['old_hostname'] = 'PMA_old_hostname';
        list($queries, $password) = PMA_getDataForChangeOrCopyUser();
        $this->assertEquals(
            'pma_password',
            $password
        );
        $this->assertEquals(
            array(),
            $queries
        );
        unset($_REQUEST['change_copy']);
    }
    
    
    /**
     * Test for PMA_getListForExportUserDefinition
     *
     * @return void
     */
    public function testPMAGetHtmlForExportUserDefinition()
    {
        $username = "PMA_username";
        $hostname = "PMA_hostname";
        $GLOBALS['cfg']['Server']['pmadb'] = 'pmadb';
        $GLOBALS['cfg']['Server']['usergroups'] = 'usergroups';
        $GLOBALS['cfg']['Server']['users'] = 'users';
        $GLOBALS['cfg']['TextareaCols'] = 'TextareaCols';
        $GLOBALS['cfg']['TextareaRows'] = 'TextareaCols';
        
        list($title, $export) 
            = PMA_getListForExportUserDefinition($username, $hostname);
        
        //validate 1: $export
        $result = '<textarea class="export" cols="' . $GLOBALS['cfg']['TextareaCols']
        . '" rows="' . $GLOBALS['cfg']['TextareaRows'];
        $this->assertContains(
            'grant user2 delete',
            $export
        );
        $this->assertContains(
            'grant user1 select',
            $export
        );
        $this->assertContains(
            $result,
            $export
        );
 
        //validate 2: $title
        $title_user = __('User') . ' `' . htmlspecialchars($username)
            . '`@`' . htmlspecialchars($hostname) . '`';
        $this->assertContains(
            $title_user,
            $title
        );
    }

    /**
     * Test for PMA_getSqlQueriesForDisplayAndAddUser
     *
     * @return void
     */
    public function testPMAGetSqlQueriesForDisplayAndAddNewUser()
    {
        $dbname = 'pma_dbname';
        $username = 'pma_username';
        $hostname = 'pma_hostname';
        $dbname = 'pma_dbname';
        $password = 'pma_password';
        $_REQUEST['adduser_submit'] = true;
        $_POST['pred_username'] = 'any';
        $_POST['pred_hostname'] = 'localhost';
        $_REQUEST['createdb-3'] = true;
        list($create_user_real, $create_user_show, $real_sql_query, $sql_query)
            = PMA_getSqlQueriesForDisplayAndAddUser(
                $username, $hostname, 
                (isset ($password) ? $password : '')
            );
        $this->assertEquals(
            "CREATE USER 'pma_username'@'pma_hostname';",
            $create_user_real
        );
        $this->assertEquals(
            "CREATE USER 'pma_username'@'pma_hostname';",
            $create_user_show
        );
        $this->assertEquals(
            "GRANT USAGE ON *.* TO 'pma_username'@'pma_hostname';",
            $real_sql_query
        );
        $this->assertEquals(
            "GRANT USAGE ON *.* TO 'pma_username'@'pma_hostname';",
            $sql_query
        );
    }

    /**
     * Test for PMA_addUser
     *
     * @return void
     */
    public function testPMAAddUser()
    {
        $dbname = 'pma_dbname';
        $username = 'pma_username';
        $hostname = 'pma_hostname';
        $tablename = 'pma_tablename';
        $password = 'pma_password';
        $_REQUEST['adduser_submit'] = true;
        $_POST['pred_username'] = 'any';
        $_POST['pred_hostname'] = 'localhost';
        $_REQUEST['createdb-3'] = true;
        list(
            $ret_message, $ret_queries, 
            $queries_for_display, $sql_query, 
            $_add_user_error
        ) = PMA_addUser(
            $dbname,
            $username,
            $hostname,
            $dbname,
            true
        );
        $this->assertEquals(
            'You have added a new user.',
            $ret_message->getMessage()
        );
        $this->assertEquals(
            "CREATE USER ''@'localhost';GRANT USAGE ON *.* TO ''@'localhost';" 
            . "GRANT ALL PRIVILEGES ON `pma_dbname`.* TO ''@'localhost';",
            $sql_query
        );
        $this->assertEquals(
            false,
            $_add_user_error
        );
    }

    /**
     * Test for PMA_updatePassword
     *
     * @return void
     */
    public function testPMAUpdatePassword()
    {
        $dbname = 'pma_dbname';
        $db_and_table = 'pma_dbname.pma_tablename';
        $username = 'pma_username';
        $hostname = 'pma_hostname';
        $tablename = 'pma_tablename';
        $password = 'pma_password';
        $err_url = "error.php";
        $_POST['pma_pw'] = 'pma_pw';
        
        $message = PMA_updatePassword(
            $err_url, $username, $hostname
        );
    
        $this->assertEquals(
            "The password for 'pma_username'@'pma_hostname' " 
            . "was changed successfully.",
            $message->getMessage()
        );
    }

    /**
     * Test for PMA_getMessageAndSqlQueryForPrivilegesRevoke
     *
     * @return void
     */
    public function testPMAGetMessageAndSqlQueryForPrivilegesRevoke()
    {
        $dbname = 'pma_dbname';
        $db_and_table = 'pma_dbname.pma_tablename';
        $username = 'pma_username';
        $hostname = 'pma_hostname';
        $tablename = 'pma_tablename';
        $password = 'pma_password';
        $_REQUEST['adduser_submit'] = true;
        $_POST['pred_username'] = 'any';
        $_POST['pred_hostname'] = 'localhost';
        $_REQUEST['createdb-3'] = true;
        $_POST['Grant_priv'] = 'Y';
        $_POST['max_questions'] = 1000;
        list ($message, $sql_query) 
            = PMA_getMessageAndSqlQueryForPrivilegesRevoke(
                $db_and_table, $dbname, $tablename, $username, $hostname
            );
    
        $this->assertEquals(
            "You have revoked the privileges for 'pma_username'@'pma_hostname'",
            $message->getMessage()
        );
        $this->assertEquals(
            "REVOKE ALL PRIVILEGES ON `pma_dbname`.`pma_tablename` " 
            . "FROM 'pma_username'@'pma_hostname'; " 
            . "REVOKE GRANT OPTION ON `pma_dbname`.`pma_tablename` " 
            . "FROM 'pma_username'@'pma_hostname';",
            $sql_query
        );
    }

    /**
     * Test for PMA_updatePrivileges
     *
     * @return void
     */
    public function testPMAUpdatePrivileges()
    {
        $dbname = 'pma_dbname';
        $username = 'pma_username';
        $hostname = 'pma_hostname';
        $tablename = 'pma_tablename';
        $password = 'pma_password';
        $_REQUEST['adduser_submit'] = true;
        $_POST['pred_username'] = 'any';
        $_POST['pred_hostname'] = 'localhost';
        $_REQUEST['createdb-3'] = true;
        $_POST['Grant_priv'] = 'Y';
        $_POST['max_questions'] = 1000;
        list($sql_query, $message) = PMA_updatePrivileges(
            $username, $hostname, $tablename, $dbname
        );
    
        $this->assertEquals(
            "You have updated the privileges for 'pma_username'@'pma_hostname'.",
            $message->getMessage()
        );
        $this->assertEquals(
            "REVOKE ALL PRIVILEGES ON `pma_dbname`.`pma_tablename` " 
            . "FROM 'pma_username'@'pma_hostname';  ",
            $sql_query
        );
    }

    /**
     * Test for PMA_getHtmlForSubMenusOnUsersPage
     *
     * @return void
     */
    public function testPMAGetHtmlForSubMenusOnUsersPage()
    {
        $html = PMA_getHtmlForSubMenusOnUsersPage('server_privileges.php');

        //validate 1: topmenu2
        $this->assertContains(
            '<ul id="topmenu2">',
            $html
        );
        
        //validate 2: tabactive for server_privileges.php
        $this->assertContains(
            '<a class="tabactive" href="server_privileges.php',
            $html
        );
        $this->assertContains(
            __('Users overview'),
            $html
        );
        
        //validate 3: not-active for server_user_groups.php
        $this->assertContains(
            '<a href="server_user_groups.php',
            $html
        );
        $this->assertContains(
            __('User groups'),
            $html
        );
    }

    /**
     * Test for PMA_getSqlQueriesForDisplayAndAddUser
     *
     * @return void
     */
    public function testPMAGetSqlQueriesForDisplayAndAddUser()
    {
        $username = "PMA_username";
        $hostname = "PMA_hostname";
        $password = "PMA_password";
        $dbname = "PMA_db";

        list($create_user_real, $create_user_show, $real_sql_query, $sql_query)
            = PMA_getSqlQueriesForDisplayAndAddUser($username, $hostname, $password);

        //validate 1: $create_user_real
        $this->assertEquals(
            "CREATE USER 'PMA_username'@'PMA_hostname';",
            $create_user_real
        );
        
        //validate 2: $create_user_show
        $this->assertEquals(
            "CREATE USER 'PMA_username'@'PMA_hostname';",
            $create_user_show
        );
        
        //validate 3:$real_sql_query
        $this->assertEquals(
            "GRANT USAGE ON *.* TO 'PMA_username'@'PMA_hostname';",
            $real_sql_query
        );
        
        //validate 4:$sql_query
        $this->assertEquals(
            "GRANT USAGE ON *.* TO 'PMA_username'@'PMA_hostname';",
            $sql_query
        );
        
        
        //test for PMA_addUserAndCreateDatabase
        list($sql_query, $message) = PMA_addUserAndCreateDatabase(
            false, $real_sql_query, $sql_query, $username, $hostname, $dbname
        );
        
        //validate 5: $sql_query
        $this->assertEquals(
            "GRANT USAGE ON *.* TO 'PMA_username'@'PMA_hostname';",
            $sql_query
        );
        
        //validate 6: $message
        $this->assertEquals(
            "You have added a new user.",
            $message->getMessage()
        );
    }
}

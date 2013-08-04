<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Tests for server_user_groups.lib.php
 *
 * @package PhpMyAdmin-test
 */

require_once 'libraries/php-gettext/gettext.inc';
require_once 'libraries/Util.class.php';
require_once 'libraries/relation.lib.php';
/*
 * Include to test.
 */
require_once 'libraries/server_privileges.lib.php';

/**
 * Tests for server_user_groups.lib.php
 *
 * @package PhpMyAdmin-test
 */
class PMA_ServerUserGroupsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares environment for the test.
     *
     * @return void
     */
    public function setUp()
    {
        $GLOBALS['cfg']['Server']['pmadb'] = 'pmadb';
        $GLOBALS['cfg']['Server']['users'] = 'users';
        $GLOBALS['cfg']['Server']['usergroups'] = 'usergroups';
    }

    /**
     * Tests PMA_getHtmlForUserGroupsTable() function when there are no user groups
     *
     * @return void
     */
    public function testGetHtmlForUserGroupsTableWithNoUserGroups()
    {
        $expectedQuery = "SELECT * FROM `pmadb`.`usergroups`"
            . " ORDER BY `usergroup` ASC";

        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->once())
            ->method('tryQuery')
            ->with($expectedQuery)
            ->will($this->returnValue(true));
        $dbi->expects($this->once())
            ->method('numRows')
            ->withAnyParameters()
            ->will($this->returnValue(0));
        $dbi->expects($this->once())
            ->method('freeResult');
        $GLOBALS['dbi'] = $dbi;

        $html = PMA_getHtmlForUserGroupsTable();
        $this->assertNotContains(
            '<table id="userGroupsTable">',
            $html
        );
        $this->assertContains(
            '<a href="server_user_groups.php?'
            . PMA_generate_common_url() . '&addUserGroup=1">',
            $html
        );
    }

    /**
     * Tests PMA_getHtmlForUserGroupsTable() function when there are user groups
     *
     * @return void
     */
    public function testGetHtmlForUserGroupsTableWithUserGroups()
    {
        $expectedQuery = "SELECT * FROM `pmadb`.`usergroups`"
            . " ORDER BY `usergroup` ASC";

        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->once())
            ->method('tryQuery')
            ->with($expectedQuery)
            ->will($this->returnValue(true));
        $dbi->expects($this->once())
            ->method('numRows')
            ->withAnyParameters()
            ->will($this->returnValue(1));
        $dbi->expects($this->at(2))
            ->method('fetchAssoc')
            ->withAnyParameters()
            ->will(
                $this->returnValue(
                    array(
                        'usergroup' => 'usergroup<',
                        'server_sql' => 'Y',
                        'server_databases' => 'N',
                        'db_sql' => 'Y',
                        'db_structure' => 'N',
                        'table_sql' => 'Y',
                        'table_browse' => 'N'
                    )
                )
            );
        $dbi->expects($this->at(3))
            ->method('fetchAssoc')
            ->withAnyParameters()
            ->will($this->returnValue(false));
        $dbi->expects($this->once())
            ->method('freeResult');
        $GLOBALS['dbi'] = $dbi;

        $html = PMA_getHtmlForUserGroupsTable();
        $this->assertContains(
            '<td>usergroup&lt;</td>',
            $html
        );
        $this->assertContains(
            '<a class="" href="server_user_groups.php?'
            . PMA_generate_common_url() . '&viewUsers=1&userGroup='
            . urlencode('usergroup<') . '">',
            $html
        );
        $this->assertContains(
            '<a class="" href="server_user_groups.php?'
            . PMA_generate_common_url() . '&editUserGroup=1&userGroup='
            . urlencode('usergroup<') . '">',
            $html
        );
        $this->assertContains(
            '<a class="deleteUserGroup ajax" href="server_user_groups.php?'
            . PMA_generate_common_url() . '&deleteUserGroup=1&userGroup='
            . urlencode('usergroup<') . '">',
            $html
        );
    }

    /**
     * Tests PMA_deleteUserGroup() function
     *
     * @return void
     */
    public function testDeleteUserGroup()
    {
        $userDelQuery = "DELETE FROM `pmadb`.`users`"
            . " WHERE `usergroup`='ug'";
        $userGrpDelQuery = "DELETE FROM `pmadb`.`usergroups`"
            . " WHERE `usergroup`='ug'";

        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->at(0))
            ->method('query')
            ->with($userDelQuery);
        $dbi->expects($this->at(1))
            ->method('query')
            ->with($userGrpDelQuery);
        $GLOBALS['dbi'] = $dbi;

        PMA_deleteUserGroup('ug');
    }

    /**
     * Tests PMA_getHtmlToEditUserGroup() function
     *
     * @return void
     */
    public function testGetHtmlToEditUserGroup()
    {
        // adding a user group
        $html = PMA_getHtmlToEditUserGroup();
        $this->assertContains(
            '<input type="hidden" name="addUserGroupSubmit" value="1"',
            $html
        );
        $this->assertContains(
            '<input type="text" name="userGroup"',
            $html
        );

        $expectedQuery = "SELECT * FROM `pmadb`.`usergroups`"
            . " WHERE `usergroup`='ug'";
        $dbi = $this->getMockBuilder('PMA_DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->once())
            ->method('tryQuery')
            ->with($expectedQuery)
            ->will($this->returnValue(true));
        $dbi->expects($this->once())
            ->method('fetchAssoc')
            ->withAnyParameters()
            ->will(
                $this->returnValue(
                    array(
                        'usergroup' => 'ug',
                        'server_sql' => 'Y',
                        'server_databases' => 'N',
                        'db_sql' => 'Y',
                        'db_structure' => 'N',
                        'table_sql' => 'Y',
                        'table_browse' => 'N'
                    )
                )
            );
        $dbi->expects($this->once())
            ->method('freeResult');
        $GLOBALS['dbi'] = $dbi;

        // editing a user group
        $html = PMA_getHtmlToEditUserGroup('ug');
        $this->assertContains(
            '<input type="hidden" name="userGroup" value="ug"',
            $html
        );
        $this->assertContains(
            '<input type="hidden" name="editUserGroupSubmit" value="1"',
            $html
        );
        $this->assertContains(
            '<input type="hidden" name="editUserGroupSubmit" value="1"',
            $html
        );
        $this->assertContains(
            '<input type="checkbox" class="checkall" checked="checked"'
            . ' name="server_sql" value="Y" />',
            $html
        );
        $this->assertContains(
            '<input type="checkbox" class="checkall"'
            . ' name="server_databases" value="Y" />',
            $html
        );
    }
}
?>
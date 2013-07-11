<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * tests for methods under setup/lib/index.lib.php
 *
 * @package PhpMyAdmin-test
 */

/*
 * Include to test
 */
require_once 'setup/lib/index.lib.php';
require_once 'libraries/config/ConfigFile.class.php';
require_once 'libraries/core.lib.php';
require_once 'libraries/Util.class.php';

/**
 * tests for methods under setup/lib/index.lib.php
 *
 * @package PhpMyAdmin-test
 */
class PMA_SetupIndex_Test extends PHPUnit_Framework_TestCase
{
    /**
     * Test for messages_begin()
     * 
     * @return void
     */
    public function testMessagesBegin()
    {
        $_SESSION['messages'] = array(
            array(
                array('foo'),
                array('bar')
            )
        );

        messages_begin();

        $this->assertEquals(
            array(
                array(
                    array(
                        0 => 'foo',
                        'fresh' => false,
                        'active' => false
                    ),
                    array(
                        0 => 'bar',
                        'fresh' => false,
                        'active' => false
                    )
                )
            ),
            $_SESSION['messages']
        );

        // case 2

        unset($_SESSION['messages']);
        messages_begin();
        $this->assertEquals(
            array(
                'error' => array(),
                'notice' => array()
            ),
            $_SESSION['messages']
        );
    }

    /**
     * Test for messages_set
     * 
     * @return void
     */
    public function testMessagesSet()
    {
        messages_set('type', '123', 'testTitle', 'msg');

        $this->assertEquals(
            array(
                'fresh' => true,
                'active' => true,
                'title' => 'testTitle',
                'message' => 'msg'
            ),
            $_SESSION['messages']['type']['123']
        );
    }

    /**
     * Test for messages_end
     * 
     * @return void
     */
    public function testMessagesEnd()
    {
        $_SESSION['messages'] = array(
            array(
                array('msg' => 'foo', 'active' => false),
                array('msg' => 'bar', 'active' => true),
            )
        );

        messages_end();

        $this->assertEquals(
            array(
                array(
                    '1' => array(
                        'msg' => 'bar',
                        'active' => 1
                    )
                )
            ),
            $_SESSION['messages']
        );
    }

    /**
     * Test for messages_show_html
     * 
     * @return void
     */
    public function testMessagesShowHTML()
    {
        $_SESSION['messages'] = array(
            'type' => array(
                array('title' => 'foo', 'message' => '123', 'fresh' => false),
                array('title' => 'bar', 'message' => '321', 'fresh' => true),
            )
        );

        ob_start();
        messages_show_html();
        $result = ob_get_clean();

        $this->assertContains(
            '<div class="type" id="0"><h4>foo</h4>123</div>',
            $result
        );

        $this->assertContains(
            '<div class="type" id="1"><h4>bar</h4>321</div>',
            $result
        );

        $this->assertContains(
            '<script type="text/javascript">',
            $result
        );
        
        $this->assertContains(
            "hiddenMessages.push('0');",
            $result
        );

        $this->assertContains(
            "</script>",
            $result
        );
    }

    /**
     * Test for PMA_version_check
     * 
     * @return void
     */
    public function testPMAVersionCheckCase1()
    {
        $pmaconfig = $this->getMockBuilder('PMA_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $pmaconfig->expects($this->once())
            ->method('get')
            ->with('PMA_VERSION')
            ->will($this->returnValue('1.0.2'));

        $GLOBALS['PMA_Config'] = $pmaconfig;

        PMA_version_check();

        $this->assertArrayHasKey(
            'notice',
            $_SESSION['messages']
        );
        $var = array_values($_SESSION['messages']['notice']);
        $notice = array_shift($var);

        $this->assertEquals(
            1,
            $notice['fresh']
        );

        $this->assertEquals(
            1,
            $notice['active']
        );

        $this->assertEquals(
            'Version check',
            $notice['title']
        );

        $this->assertContains(
            "A newer version of phpMyAdmin is available",
            $notice['message']
        );
    }

    /**
     * Test for PMA_version_check
     * 
     * @return void
     */
    public function testPMAVersionCheckCase2()
    {   
        $pmaconfig = $this->getMockBuilder('PMA_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $pmaconfig->expects($this->once())
            ->method('get')
            ->with('PMA_VERSION')
            ->will($this->returnValue('100.0.0-dev0'));

        $GLOBALS['PMA_Config'] = $pmaconfig;

        PMA_version_check();
        
        $this->assertArrayHasKey(
            'notice',
            $_SESSION['messages']
        );
        $var = array_values($_SESSION['messages']['notice']);
        $notice = array_shift($var);

        $this->assertEquals(
            1,
            $notice['fresh']
        );

        $this->assertEquals(
            1,
            $notice['active']
        );

        $this->assertEquals(
            'Version check',
            $notice['title']
        );

        $this->assertContains(
            "You are using Git version",
            $notice['message']
        );
    }

    /**
     * Test for PMA_version_check
     * 
     * @return void
     */
    public function testPMAVersionCheckCase3()
    {   
        $pmaconfig = $this->getMockBuilder('PMA_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $pmaconfig->expects($this->once())
            ->method('get')
            ->with('PMA_VERSION')
            ->will($this->returnValue('100.0.0-dev2'));

        $GLOBALS['PMA_Config'] = $pmaconfig;

        PMA_version_check();
        
        $this->assertArrayHasKey(
            'notice',
            $_SESSION['messages']
        );
        $var = array_values($_SESSION['messages']['notice']);
        $notice = array_shift($var);

        $this->assertEquals(
            1,
            $notice['fresh']
        );

        $this->assertEquals(
            1,
            $notice['active']
        );

        $this->assertEquals(
            'Version check',
            $notice['title']
        );

        $this->assertContains(
            "No newer stable version is available",
            $notice['message']
        );
    }

    /**
     * Test for version_to_int
     * 
     * @param string $version  Version String
     * @param int    $expected Expected int
     * 
     * @return void
     * @dataProvider versionToIntProvider
     */
    public function testVersionToInt($version, $expected)
    {   
        $this->assertEquals(
            version_to_int($version),
            $expected
        );
    }

    /**
     * Data Provider for testVersionToInt
     * 
     * @return array Test data
     */
    public function versionToIntProvider()
    {   
        return array(
            array('1.0.0', 1000050),
            array('2.0.0.2-dev', 2000052),
            array('3.4.2.1', 3040251),
            array('3.4.2-dev3', 3040203),
            array('3.4.2-dev', 3040200),
            array('3.4.2-pl', 3040260),
            array('3.4.2-pl3', 3040263),
            array('4.4.2-rc22', 4040252),
            array('4.4.2-rc', 4040230),
            array('4.4.22-beta22', 4042242),
            array('4.4.22-beta', 4042220),
            array('4.4.21-alpha22', 4042132),
            array('4.4.20-alpha', 4042010),
            array('4.40.20-alpha-dev', 4402010),
            array('4.4a', false),
            array('4.4.4-test', false)
        );
    }

    /**
     * Test for check_config_rw
     * 
     * @return void
     */
    public function testCheckConfigRW()
    {   
        if (!function_exists('runkit_constant_redefine')) {
            $this->markTestSkipped('Cannot redefine constant');
        }

        $redefine = null;
        $GLOBALS['cfg']['AvailableCharsets'] = array();
        if (!defined('SETUP_CONFIG_FILE')) {
            define('SETUP_CONFIG_FILE', 'test/test_data/configfile');
        } else {
            $redefine = 'SETUP_CONFIG_FILE';
            runkit_constant_redefine(
                'SETUP_CONFIG_FILE',
                'test/test_data/configfile'
            );
        }
        $is_readable = false;
        $is_writable = false;
        $file_exists = false;
        
        check_config_rw($is_readable, $is_writable, $file_exists);
        
        $this->assertTrue(
            $is_readable
        );

        $this->assertTrue(
            $is_writable
        );

        $this->assertFalse(
            $file_exists
        );

        runkit_constant_redefine(
            'SETUP_CONFIG_FILE',
            'test/test_data/test.file'
        );

        check_config_rw($is_readable, $is_writable, $file_exists);
        
        $this->assertTrue(
            $is_readable
        );

        $this->assertTrue(
            $is_writable
        );

        $this->assertTrue(
            $file_exists
        );

        if ($redefine !== null) {
            runkit_constant_redefine('SETUP_CONFIG_FILE', $redefine);
        } else {
            runkit_constant_remove('SETUP_CONFIG_FILE');
        }
    }

    /**
     * Test for perform_config_checks
     * 
     * @return void
     */
    public function testPerformConfigChecks()
    {   

        $GLOBALS['cfg']['AvailableCharsets'] = array();
        $GLOBALS['cfg']['ServerDefault'] = 0;

        $cf = ConfigFile::getInstance();
        $reflection = new \ReflectionProperty('ConfigFile', '_id');
        $reflection->setAccessible(true);
        $sessionID = $reflection->getValue($cf);

        $_SESSION[$sessionID]['Servers'] = array(
            '1' => array(
                'host' => 'localhost',
                'ssl' => false,
                'extension' => 'mysql',
                'auth_type' => 'config',
                'user' => 'username',
                'password' => 'password',
                'AllowRoot' => true,
                'AllowNoPassword' => true,
            )
        );

        $_SESSION[$sessionID]['ForceSSL'] = false;
        $_SESSION[$sessionID]['AllowArbitraryServer'] = true;
        $_SESSION[$sessionID]['LoginCookieValidity'] = 5000;
        $_SESSION[$sessionID]['LoginCookieStore'] = 4000;
        $_SESSION[$sessionID]['SaveDir'] = true;
        $_SESSION[$sessionID]['TempDir'] = true;
        $_SESSION[$sessionID]['GZipDump'] = true;
        $_SESSION[$sessionID]['BZipDump'] = true;
        $_SESSION[$sessionID]['ZipDump'] = true;
        
        $noticeArrayKeys = array(
            'TempDir',
            'SaveDir',
            'LoginCookieValidity',
            'AllowArbitraryServer',
            'ForceSSL',
            'Servers/1/AllowNoPassword',
            'Servers/1/auth_type',
            'Servers/1/extension',
            'Servers/1/ssl'
        );

        $errorArrayKeys = array(
            'LoginCookieValidity'
        );

        if (@!function_exists('gzopen') || @!function_exists('gzencode')) {
            $errorArrayKeys[] = 'GZipDump';
        }

        if (@!function_exists('bzopen') || @!function_exists('bzcompress')) {
            $errorArrayKeys[] = 'BZipDump';
        }

        if (!@function_exists('zip_open')) {
            $errorArrayKeys[] = 'ZipDump_import';
        }

        if (!@function_exists('gzcompress')) {
            $errorArrayKeys[] = 'ZipDump_export';
        }

        perform_config_checks();

        foreach ($noticeArrayKeys as $noticeKey) {
            $this->assertArrayHasKey(
                $noticeKey,
                $_SESSION['messages']['notice']
            );
        }

        foreach ($errorArrayKeys as $errorKey) {
            $this->assertArrayHasKey(
                $errorKey,
                $_SESSION['messages']['error']
            );
        }
        
        // Case 2

        unset($_SESSION['messages']);
        unset($_SESSION[$sessionID]);

        
        $_SESSION[$sessionID]['Servers'] = array(
            '1' => array(
                'host' => 'localhost',
                'ssl' => true,
                'extension' => 'mysqli',
                'auth_type' => 'cookie',
                'AllowRoot' => false
            )
        );

        $_SESSION[$sessionID]['ForceSSL'] = true;
        $_SESSION[$sessionID]['AllowArbitraryServer'] = false;
        $_SESSION[$sessionID]['LoginCookieValidity'] = -1;
        $_SESSION[$sessionID]['LoginCookieStore'] = 0;
        $_SESSION[$sessionID]['SaveDir'] = '';
        $_SESSION[$sessionID]['TempDir'] = '';
        $_SESSION[$sessionID]['GZipDump'] = false;
        $_SESSION[$sessionID]['BZipDump'] = false;
        $_SESSION[$sessionID]['ZipDump'] = false;

        perform_config_checks();
        $this->assertArrayHasKey(
            'blowfish_secret_created',
            $_SESSION['messages']['notice']
        );

        foreach ($noticeArrayKeys as $noticeKey) {
            $this->assertArrayNotHasKey(
                $noticeKey,
                $_SESSION['messages']['notice']
            );
        }

        $this->assertArrayNotHasKey(
            'error',
            $_SESSION['messages']
        );

        // Case 3

        $_SESSION[$sessionID]['blowfish_secret'] = 'sec';
        
        $_SESSION[$sessionID]['Servers'] = array(
            '1' => array(
                'host' => 'localhost',
                'auth_type' => 'cookie'
            )
        );
        perform_config_checks();
        $this->assertArrayHasKey(
            'blowfish_warnings2',
            $_SESSION['messages']['error']
        );

    }
}
?>

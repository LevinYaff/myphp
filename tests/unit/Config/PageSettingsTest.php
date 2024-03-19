<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Config;

use PhpMyAdmin\Config;
use PhpMyAdmin\Config\PageSettings;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Current;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\UserPreferences;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

#[CoversClass(PageSettings::class)]
class PageSettingsTest extends AbstractTestCase
{
    /**
     * Setup tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setLanguage();

        $this->setGlobalConfig();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        Current::$database = 'db';
        Current::$table = '';
        $_SERVER['SCRIPT_NAME'] = 'index.php';
        Config::getInstance()->selectedServer['DisableIS'] = false;
    }

    /**
     * Test showGroup when group passed does not exist
     */
    public function testShowGroupNonExistent(): void
    {
        $dbi = DatabaseInterface::getInstance();
        $object = new PageSettings(new UserPreferences($dbi, new Relation($dbi), new Template()));
        $object->init('NonExistent');

        self::assertSame('', $object->getHTML());
    }

    /**
     * Test showGroup with a known group name
     */
    #[BackupStaticProperties(true)]
    public function testShowGroupBrowse(): void
    {
        (new ReflectionProperty(ResponseRenderer::class, 'instance'))->setValue(null, null);

        $dbi = DatabaseInterface::getInstance();
        $object = new PageSettings(
            new UserPreferences($dbi, new Relation($dbi), new Template()),
        );
        $object->init('Browse');

        $html = $object->getHTML();

        // Test some sample parts
        self::assertStringContainsString(
            '<div id="page_settings_modal">'
            . '<div class="page_settings">'
            . '<form method="post" '
            . 'action="index.php&#x3F;route&#x3D;&#x25;2F&amp;db&#x3D;db&amp;server&#x3D;1&amp;lang&#x3D;en" '
            . 'class="config-form disableAjax">',
            $html,
        );

        self::assertStringContainsString('<input type="hidden" name="submit_save" value="Browse">', $html);

        self::assertStringContainsString(
            "window.Config.registerFieldValidator('MaxRows', 'validatePositiveNumber', true);\n"
            . "window.Config.registerFieldValidator('RepeatCells', 'validateNonNegativeNumber', true);\n"
            . "window.Config.registerFieldValidator('LimitChars', 'validatePositiveNumber', true);\n",
            $html,
        );
    }

    /**
     * Test getNaviSettings
     */
    public function testGetNaviSettings(): void
    {
        $dbi = DatabaseInterface::getInstance();
        $pageSettings = new PageSettings(
            new UserPreferences($dbi, new Relation($dbi), new Template()),
        );
        $pageSettings->init('Navi', 'pma_navigation_settings');

        $html = $pageSettings->getHTML();

        // Test some sample parts
        self::assertStringContainsString('<div id="pma_navigation_settings">', $html);

        self::assertStringContainsString('<input type="hidden" name="submit_save" value="Navi">', $html);
    }
}

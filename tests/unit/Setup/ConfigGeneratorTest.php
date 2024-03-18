<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Setup;

use PhpMyAdmin\Config\ConfigFile;
use PhpMyAdmin\Current;
use PhpMyAdmin\Setup\ConfigGenerator;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Version;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;

use function explode;
use function hex2bin;
use function mb_strlen;
use function str_repeat;

use const SODIUM_CRYPTO_SECRETBOX_KEYBYTES;

#[CoversClass(ConfigGenerator::class)]
class ConfigGeneratorTest extends AbstractTestCase
{
    /**
     * Test for ConfigGenerator::getConfigFile
     */
    #[Group('medium')]
    public function testGetConfigFile(): void
    {
        unset($_SESSION['eol']);

        $this->setGlobalConfig();

        Current::$server = 2;
        $cf = new ConfigFile();
        $_SESSION['ConfigFile2'] = ['a', 'b', 'c'];
        $_SESSION['ConfigFile2']['Servers'] = [[1, 2, 3]];

        $cf->setPersistKeys(['1/', 2]);

        $result = ConfigGenerator::getConfigFile($cf);

        self::assertStringContainsString(
            "<?php\n" .
            "/**\n" .
            " * Generated configuration file\n" .
            ' * Generated by: phpMyAdmin ' . Version::VERSION . " setup script\n",
            $result,
        );

        self::assertStringContainsString(
            "/* Servers configuration */\n" .
            '$i = 0;' . "\n\n" .
            "/* Server: localhost [0] */\n" .
            '$i++;' . "\n" .
            '$cfg[\'Servers\'][$i][\'0\'] = 1;' . "\n" .
            '$cfg[\'Servers\'][$i][\'1\'] = 2;' . "\n" .
            '$cfg[\'Servers\'][$i][\'2\'] = 3;' . "\n\n" .
            "/* End of servers configuration */\n\n",
            $result,
        );
    }

    /**
     * Test for ConfigGenerator::getVarExport
     */
    public function testGetVarExport(): void
    {
        $reflection = new ReflectionClass(ConfigGenerator::class);
        $method = $reflection->getMethod('getVarExport');

        self::assertSame(
            '$cfg[\'var_name\'] = 1;' . "\n",
            $method->invoke(null, 'var_name', 1, "\n"),
        );

        self::assertSame(
            '$cfg[\'var_name\'] = array (' .
            "\n);\n",
            $method->invoke(null, 'var_name', [], "\n"),
        );

        self::assertSame(
            '$cfg[\'var_name\'] = [1, 2, 3];' . "\n",
            $method->invoke(
                null,
                'var_name',
                [1, 2, 3],
                "\n",
            ),
        );

        self::assertSame(
            '$cfg[\'var_name\'][\'1a\'] = \'foo\';' . "\n" .
            '$cfg[\'var_name\'][\'b\'] = \'bar\';' . "\n",
            $method->invoke(
                null,
                'var_name',
                ['1a' => 'foo', 'b' => 'bar'],
                "\n",
            ),
        );
    }

    public function testGetVarExportForBlowfishSecret(): void
    {
        $reflection = new ReflectionClass(ConfigGenerator::class);
        $method = $reflection->getMethod('getVarExport');

        self::assertSame(
            '$cfg[\'blowfish_secret\'] = \sodium_hex2bin(\''
            . '6161616161616161616161616161616161616161616161616161616161616161\');' . "\n",
            $method->invoke(null, 'blowfish_secret', str_repeat('a', SODIUM_CRYPTO_SECRETBOX_KEYBYTES), "\n"),
        );

        /** @var string $actual */
        $actual = $method->invoke(null, 'blowfish_secret', 'invalid secret', "\n");
        self::assertStringStartsWith('$cfg[\'blowfish_secret\'] = \sodium_hex2bin(\'', $actual);
        self::assertStringEndsWith('\');' . "\n", $actual);
        $pieces = explode('\'', $actual);
        self::assertCount(5, $pieces);
        $binaryString = hex2bin($pieces[3]);
        self::assertIsString($binaryString);
        self::assertSame(SODIUM_CRYPTO_SECRETBOX_KEYBYTES, mb_strlen($binaryString, '8bit'));
    }

    /**
     * Test for ConfigGenerator::exportZeroBasedArray
     */
    public function testExportZeroBasedArray(): void
    {
        $reflection = new ReflectionClass(ConfigGenerator::class);
        $method = $reflection->getMethod('exportZeroBasedArray');

        $arr = [1, 2, 3, 4];

        $result = $method->invoke(null, $arr, "\n");

        self::assertSame('[1, 2, 3, 4]', $result);

        $arr = [1, 2, 3, 4, 7, 'foo'];

        $result = $method->invoke(null, $arr, "\n");

        self::assertSame(
            '[' . "\n" .
            '    1,' . "\n" .
            '    2,' . "\n" .
            '    3,' . "\n" .
            '    4,' . "\n" .
            '    7,' . "\n" .
            '    \'foo\']',
            $result,
        );
    }
}

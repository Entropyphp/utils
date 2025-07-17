<?php

declare(strict_types=1);

namespace PgTests\Utils\File;

use Pg\Utils\File\FileUtils;
use PHPUnit\Framework\TestCase;

class FileUtilsTest extends TestCase
{
    public function testGetFilesReturnsPhpFiles()
    {
        $dir = __DIR__ . '/fixtures';
        mkdir($dir);
        file_put_contents($dir . '/test1.php', '<?php');
        file_put_contents($dir . '/test2.txt', 'text');
        file_put_contents($dir . '/.hidden.php', '<?php');

        $files = FileUtils::getFiles($dir, 'php');
        $fileNames = array_map(fn($f) => basename($f->getPathname()), $files);

        $this->assertContains('test1.php', $fileNames);
        $this->assertNotContains('test2.txt', $fileNames);
        $this->assertNotContains('.hidden.php', $fileNames);

        unlink($dir . '/test1.php');
        unlink($dir . '/test2.txt');
        unlink($dir . '/.hidden.php');
        rmdir($dir);
    }

    public function testGetFilesWithExclude()
    {
        $dir = __DIR__ . '/fixtures2';
        mkdir($dir);
        file_put_contents($dir . '/exclude.php', '<?php');
        file_put_contents($dir . '/keep.php', '<?php');

        $files = FileUtils::getFiles($dir, 'php', 'exclude');
        $fileNames = array_map(fn($f) => basename($f->getPathname()), $files);

        $this->assertContains('keep.php', $fileNames);
        $this->assertNotContains('exclude.php', $fileNames);

        unlink($dir . '/exclude.php');
        unlink($dir . '/keep.php');
        rmdir($dir);
    }

    public function testGetProjectDirFindsComposerJson()
    {
        $dir = sys_get_temp_dir() . '/projectdirtest';
        mkdir($dir);
        file_put_contents($dir . '/composer.json', '{}');
        $srcDir = $dir . '/src';
        mkdir($srcDir);

        $result = FileUtils::getProjectDir($srcDir);
        $this->assertEquals($dir, $result);

        unlink($dir . '/composer.json');
        rmdir($srcDir);
        rmdir($dir);
    }

    public function testGetRootPathReturnsString()
    {
        $path = FileUtils::getRootPath();
        $this->assertIsString($path);
        $this->assertDirectoryExists($path);
    }
}
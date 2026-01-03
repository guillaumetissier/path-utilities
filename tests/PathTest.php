<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities\Tests;

use Guillaumetissier\PathUtilities\Path;
use Guillaumetissier\PathUtilities\PathPermissions;
use Guillaumetissier\PathUtilities\PathTimes;
use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir().'/path_test';
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir);
        }
        file_put_contents($this->tmpDir.'/file.txt', 'hello');
        mkdir($this->tmpDir.'/subdir');
    }

    protected function tearDown(): void
    {
        @unlink($this->tmpDir.'/file.txt');
        @rmdir($this->tmpDir.'/subdir');
        @rmdir($this->tmpDir);
    }

    public function testBasics(): void
    {
        $path = new Path($this->tmpDir.'/file.txt');
        $this->assertSame('file.txt', $path->basename());
        $this->assertSame('file', $path->filename());
        $this->assertSame('txt', $path->extension());
        $this->assertSame($this->tmpDir, $path->dirname());
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        $this->assertFalse($path->isDir());
        $this->assertFalse($path->isLink());
        $this->assertSame(5, $path->size());
    }

    public function testDir(): void
    {
        $path = new Path($this->tmpDir.'/subdir');
        $this->assertTrue($path->exists());
        $this->assertFalse($path->isFile());
        $this->assertTrue($path->isDir());
        $this->assertSame('subdir', $path->basename());
        $this->assertSame('subdir', $path->filename());
        $this->assertSame('', $path->extension());
    }

    public function testAbsolutePath(): void
    {
        $path = new Path($this->tmpDir.'/../path_test/file.txt');

        $abs = $path->absolutePath();
        $this->assertIsString($abs);
        $this->assertSame($this->tmpDir.'/file.txt', $abs);
        $this->assertFileExists($abs);
    }

    public function testParent(): void
    {
        $path = new Path($this->tmpDir.'/file.txt');
        $this->assertSame($this->tmpDir, (string) $path->parent());
    }

    public function testPermissionsObject(): void
    {
        $path = new Path($this->tmpDir.'/file.txt');
        $this->assertInstanceOf(PathPermissions::class, $path->permissions());
    }

    public function testTimesObject(): void
    {
        $path = new Path($this->tmpDir.'/file.txt');
        $this->assertInstanceOf(PathTimes::class, $path->times());
    }

    public function testWithExceptionMode(): void
    {
        $this->assertInstanceOf(Path::class, (new Path($this->tmpDir.'/file.txt'))->withExceptionOnError());
    }
}

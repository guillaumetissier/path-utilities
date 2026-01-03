<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities\Tests;

use Guillaumetissier\PathUtilities\PathTimes;
use PHPUnit\Framework\TestCase;

final class PathTimesTest extends TestCase
{
    private string $tmpDir;

    private string $tmpFile;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir().'/path_times_test';
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir);
        }

        $this->tmpFile = $this->tmpDir.'/file.txt';
        file_put_contents($this->tmpFile, 'hello');
    }

    protected function tearDown(): void
    {
        @unlink($this->tmpFile);
        @rmdir($this->tmpDir);
    }

    public function testAccessModificationInode(): void
    {
        $times = new PathTimes($this->tmpFile);

        $access = $times->access();
        $modification = $times->modification();
        $inode = $times->inodeChange();

        $this->assertInstanceOf(\DateTimeImmutable::class, $access);
        $this->assertInstanceOf(\DateTimeImmutable::class, $modification);
        $this->assertInstanceOf(\DateTimeImmutable::class, $inode);

        $now = new \DateTimeImmutable();
        $this->assertLessThanOrEqual($now, $access);
        $this->assertLessThanOrEqual($now, $modification);
        $this->assertLessThanOrEqual($now, $inode);
    }

    public function testNonExistentFileReturnsNull(): void
    {
        $times = new PathTimes($this->tmpDir.'/nonexistent.txt');
        $this->assertNull($times->access());
        $this->assertNull($times->modification());
        $this->assertNull($times->inodeChange());
    }

    public function testExceptionMode(): void
    {
        $times = new PathTimes($this->tmpDir.'/nonexistent.txt', true);

        $this->expectException(\RuntimeException::class);
        $times->access();
    }
}

<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities\Tests;

use Guillaumetissier\PathUtilities\PathPermissions;
use PHPUnit\Framework\TestCase;

final class PathPermissionsTest extends TestCase
{
    private string $tmpDir;

    private string $tmpFile;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir().'/path_permissions_test';
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir);
        }

        $this->tmpFile = $this->tmpDir.'/file.txt';
        file_put_contents($this->tmpFile, 'hello');

        chmod($this->tmpFile, 0644);
    }

    protected function tearDown(): void
    {
        @unlink($this->tmpFile);
        @rmdir($this->tmpDir);
    }

    public function testReadableWritableExecutable(): void
    {
        $perm = new PathPermissions($this->tmpFile);

        $this->assertTrue($perm->isReadable());
        $this->assertTrue($perm->isWritable());
        $this->assertFalse($perm->isExecutable());
    }

    public function testRawPermissions(): void
    {
        $perm = new PathPermissions($this->tmpFile);

        $this->assertEquals(0100644, $perm->raw());
    }

    public function testOctal(): void
    {
        $perm = new PathPermissions($this->tmpFile);

        $this->assertSame('0644', $perm->octal());
    }

    public function testSymbolic(): void
    {
        $perm = new PathPermissions($this->tmpFile);

        $this->assertSame('rw-r--r--', $perm->symbolic());
    }

    public function testOwnerAndGroup(): void
    {
        $perm = new PathPermissions($this->tmpFile);

        $owner = $perm->ownerId();
        $group = $perm->groupId();

        $this->assertIsInt($owner);
        $this->assertIsInt($group);
        $this->assertGreaterThan(0, $owner);
        $this->assertGreaterThan(0, $group);
    }

    public function testExceptionMode(): void
    {
        $this->expectException(\RuntimeException::class);

        $perm = new PathPermissions('/non/existent/file', true);
        $perm->raw();
    }
}

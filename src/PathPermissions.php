<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities;

use Guillaumetissier\PathUtilities\Internal\FilesystemErrorHandlingTrait;

final class PathPermissions
{
    use FilesystemErrorHandlingTrait;

    public function __construct(private readonly string $path, bool $exceptionOnError = false)
    {
        $this->exceptionOnError = $exceptionOnError;
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    public function isExecutable(): bool
    {
        return is_executable($this->path);
    }

    /**
     * Returns file permissions.
     *
     * Returns null if the path does not exist or permissions cannot be read.
     *
     * @return int|null File permissions as an integer or null on failure
     */
    public function raw(): ?int
    {
        return $this->callWithException(fn () => fileperms($this->path));
    }

    /**
     * Returns permissions in octal form (e.g. 0755).
     */
    public function octal(): ?string
    {
        if (null === ($perms = $this->raw())) {
            return null;
        }

        return substr(sprintf('%o', $perms), -4);
    }

    /**
     * Returns symbolic permissions (e.g. rwxr-xr--).
     */
    public function symbolic(): ?string
    {
        if (null === ($perms = $this->raw())) {
            return null;
        }

        $map = [
            0x0100 => 'r', 0x0080 => 'w', 0x0040 => 'x',
            0x0020 => 'r', 0x0010 => 'w', 0x0008 => 'x',
            0x0004 => 'r', 0x0002 => 'w', 0x0001 => 'x',
        ];

        $result = '';
        foreach ($map as $bit => $char) {
            $result .= ($perms & $bit) ? $char : '-';
        }

        return $result;
    }

    /**
     * Returns the group ID of the file.
     *
     * Returns null if the path does not exist or the group cannot be determined.
     *
     * @return int|null Group ID or null on failure
     */
    public function groupId(): ?int
    {
        return $this->callWithException(fn () => filegroup($this->path));
    }

    /**
     * Returns the owner ID of the file.
     *
     * Returns null if the path does not exist or the owner cannot be determined.
     *
     * @return int|null Owner ID or null on failure
     */
    public function ownerId(): ?int
    {
        return $this->callWithException(fn () => fileowner($this->path));
    }
}

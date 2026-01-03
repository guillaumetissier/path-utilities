<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities;

use Guillaumetissier\PathUtilities\Internal\FilesystemErrorHandlingTrait;

final class PathTimes
{
    use FilesystemErrorHandlingTrait;

    public function __construct(private readonly string $path, bool $exceptionOnError = false)
    {
        $this->exceptionOnError = $exceptionOnError;
    }

    /**
     * Last access time (atime).
     */
    public function access(): ?\DateTimeImmutable
    {
        if (null === ($ts = $this->callWithException(fn () => fileatime($this->path)))) {
            return null;
        }

        return new \DateTimeImmutable("@$ts");
    }

    /**
     * Last modification time (mtime).
     */
    public function modification(): ?\DateTimeImmutable
    {
        if (null === ($ts = $this->callWithException(fn () => filemtime($this->path)))) {
            return null;
        }

        return new \DateTimeImmutable("@$ts");
    }

    /**
     * Last inode change time (ctime).
     */
    public function inodeChange(): ?\DateTimeImmutable
    {
        if (null === ($ts = $this->callWithException(fn () => filectime($this->path)))) {
            return null;
        }

        return new \DateTimeImmutable("@$ts");
    }
}

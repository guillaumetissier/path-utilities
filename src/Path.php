<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities;

use Guillaumetissier\PathUtilities\Internal\FilesystemErrorHandlingTrait;

/**
 * Value object representing a filesystem path.
 *
 * This class provides read-only helpers around filesystem paths
 * (existence, permissions, metadata).
 *
 * By default, filesystem errors are silenced and methods return `null`
 * on failure. When exception mode is enabled, filesystem warnings are
 * converted to RuntimeException.
 */
final class Path
{
    use FilesystemErrorHandlingTrait;

    public function __construct(private readonly string $path, bool $exceptionOnError = false)
    {
        $this->exceptionOnError = $exceptionOnError;
    }

    public function withExceptionOnError(bool $exceptionOnError = true): self
    {
        return new self($this->path, $exceptionOnError);
    }

    public function parent(): self
    {
        return new self(dirname($this->path), $this->exceptionOnError);
    }

    public function dirname(): string
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    public function basename(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    public function filename(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    /**
     * Returns the absolute canonical path.
     *
     * Returns null if the path does not exist or cannot be resolved.
     *
     * @return string|null Absolute path or null on failure
     */
    public function absolutePath(): ?string
    {
        return $this->callWithException(fn () => realpath($this->path));
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function isFile(): bool
    {
        return is_file($this->path);
    }

    public function isLink(): bool
    {
        return is_link($this->path);
    }

    public function isDir(): bool
    {
        return is_dir($this->path);
    }

    /**
     * Returns the file size in bytes.
     *
     * Returns null if the path does not exist, is not a file,
     * or the size cannot be determined.
     *
     * @return int|null File size in bytes or null on failure
     */
    public function size(): ?int
    {
        return $this->callWithException(fn () => filesize($this->path));
    }

    public function __toString(): string
    {
        return $this->path;
    }

    public function permissions(): PathPermissions
    {
        return new PathPermissions($this->path, $this->exceptionOnError);
    }

    public function times(): PathTimes
    {
        return new PathTimes($this->path, $this->exceptionOnError);
    }
}

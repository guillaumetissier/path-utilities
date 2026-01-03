<?php

declare(strict_types=1);

namespace Guillaumetissier\PathUtilities\Internal;

/**
 * @internal
 */
trait FilesystemErrorHandlingTrait
{
    protected bool $exceptionOnError = false;

    /**
     * Executes a filesystem call and optionally converts PHP warnings into exceptions.
     *
     * When exception mode is disabled, returns null on failure.
     * When enabled, PHP warnings are converted into RuntimeException.
     *
     * @throws \RuntimeException When exception mode is enabled and a warning occurs
     */
    private function callWithException(callable $callback): mixed
    {
        if (!$this->exceptionOnError) {
            return false === ($ret = @$callback()) ? null : $ret;
        }

        set_error_handler(
            function ($severity, $message) {
                throw new \RuntimeException($message);
            },
            E_WARNING
        );

        try {
            return $callback();
        } finally {
            restore_error_handler();
        }
    }
}

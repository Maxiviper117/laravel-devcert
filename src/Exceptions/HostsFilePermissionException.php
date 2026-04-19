<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Exceptions;

use Maxiviper117\LaravelDevcert\Support\OperatingSystem;
use RuntimeException;

class HostsFilePermissionException extends RuntimeException
{
    public static function forPath(string $path): self
    {
        $hint = OperatingSystem::isWindows()
            ? 'Re-run this command from an Administrator terminal (Run as Administrator).'
            : 'Re-run this command with sudo.';

        return new self(sprintf('Cannot write to hosts file at [%s]. %s', $path, $hint));
    }
}

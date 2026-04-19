<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Support;

class OperatingSystem
{
    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    public static function isUnixLike(): bool
    {
        return ! self::isWindows();
    }

    public static function isWsl(): bool
    {
        if (getenv('WSL_DISTRO_NAME') !== false && getenv('WSL_DISTRO_NAME') !== '') {
            return true;
        }

        if (getenv('WSL_INTEROP') !== false && getenv('WSL_INTEROP') !== '') {
            return true;
        }

        $release = strtolower(php_uname('r'));

        return str_contains($release, 'microsoft') || str_contains($release, 'wsl');
    }
}

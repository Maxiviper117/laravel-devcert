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
}

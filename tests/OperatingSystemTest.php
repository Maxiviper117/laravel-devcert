<?php

declare(strict_types=1);

use Maxiviper117\LaravelDevcert\Support\OperatingSystem;

it('detects windows and unix-like operating systems', function () {
    $isWindows = PHP_OS_FAMILY === 'Windows';

    expect(OperatingSystem::isWindows())->toBe($isWindows)
        ->and(OperatingSystem::isUnixLike())->toBe(! $isWindows);
});

<?php

declare(strict_types=1);

use Maxiviper117\LaravelDevcert\Support\OperatingSystem;

it('detects windows and unix-like operating systems', function () {
    $isWindows = PHP_OS_FAMILY === 'Windows';

    expect(OperatingSystem::isWindows())->toBe($isWindows)
        ->and(OperatingSystem::isUnixLike())->toBe(! $isWindows);
});

it('detects wsl from environment markers', function () {
    $originalDistro = getenv('WSL_DISTRO_NAME');
    $originalInterop = getenv('WSL_INTEROP');

    try {
        putenv('WSL_DISTRO_NAME=Ubuntu-24');
        putenv('WSL_INTEROP=/run/WSL/interop');

        expect(OperatingSystem::isWsl())->toBeTrue();
    } finally {
        if ($originalDistro === false) {
            putenv('WSL_DISTRO_NAME');
        } else {
            putenv('WSL_DISTRO_NAME='.$originalDistro);
        }

        if ($originalInterop === false) {
            putenv('WSL_INTEROP');
        } else {
            putenv('WSL_INTEROP='.$originalInterop);
        }
    }
});

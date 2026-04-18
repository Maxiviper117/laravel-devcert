<?php

use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

it('scans hosts entries and avoids duplicates', function () {
    $path = tempnam(sys_get_temp_dir(), 'hosts');
    file_put_contents($path, <<<'HOSTS'
# comment
127.0.0.1 alpha.test beta.test localhost
::1 gamma.test
HOSTS);

    config()->set('laravel-devcert.hosts_path', $path);

    $hosts = app(HostsFileManager::class);

    expect($hosts->scan())->toBe(['alpha.test', 'beta.test', 'gamma.test'])
        ->and($hosts->contains('alpha.test'))->toBeTrue()
        ->and($hosts->contains('alpha'))->toBeFalse();

    $hosts->add('alpha.test');
    $contents = file_get_contents($path);

    expect(collect(file($path, FILE_IGNORE_NEW_LINES) ?: [])->filter(fn (string $line) => trim($line) === '127.0.0.1 alpha.test' || trim($line) === '::1 alpha.test')->count())->toBe(2);

    unlink($path);
});

it('removes only exact hosts entries', function () {
    $path = tempnam(sys_get_temp_dir(), 'hosts');
    file_put_contents($path, "127.0.0.1 alpha.test alphabeta.test\n::1 alphabeta.test\n");

    config()->set('laravel-devcert.hosts_path', $path);

    $hosts = app(HostsFileManager::class);
    $hosts->remove('alpha.test');

    expect(file_get_contents($path))->not->toContain('alpha.test')
        ->and(file_get_contents($path))->toContain('alphabeta.test');

    unlink($path);
});

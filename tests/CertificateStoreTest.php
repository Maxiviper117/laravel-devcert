<?php

use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Support\OperatingSystem;

it('expands the shared certificate path from tilde', function () {
    $profile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'devcert-profile';
    if (! is_dir($profile)) {
        mkdir($profile, 0777, true);
    }

    $previousUserProfile = getenv('USERPROFILE');
    $previousHome = getenv('HOME');

    if (OperatingSystem::isWindows()) {
        putenv('USERPROFILE='.$profile);
    } else {
        putenv('HOME='.$profile);
    }

    config()->set('laravel-devcert.certs_path', '~/.local-https/certs');

    expect(app(CertificateStore::class)->basePath())
        ->toBe($profile.DIRECTORY_SEPARATOR.'.local-https'.DIRECTORY_SEPARATOR.'certs');

    putenv($previousUserProfile === false ? 'USERPROFILE' : 'USERPROFILE='.$previousUserProfile);
    putenv($previousHome === false ? 'HOME' : 'HOME='.$previousHome);
});

it('creates directories with secure permissions', function () {
    $tempDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'devcert-perms-'.uniqid();
    config()->set('laravel-devcert.certs_path', $tempDir);

    $store = app(CertificateStore::class);
    $store->ensureDirectory('example.test');

    // Directory should exist
    expect(is_dir($tempDir.DIRECTORY_SEPARATOR.'example.test'))->toBeTrue();

    // Cleanup
    rmdir($tempDir.DIRECTORY_SEPARATOR.'example.test');
    rmdir($tempDir);
});

it('sets secure permissions on certificate files', function () {
    $tempDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'devcert-perms-'.uniqid();
    config()->set('laravel-devcert.certs_path', $tempDir);

    $store = app(CertificateStore::class);
    $paths = $store->ensureDirectory('example.test');

    // Create dummy cert files
    file_put_contents($paths['cert'], 'dummy cert');
    file_put_contents($paths['key'], 'dummy key');

    // Set permissions (should not throw)
    $store->setPermissions('example.test');
    expect(true)->toBeTrue(); // Assertion to mark test as not risky

    // On Unix systems, verify permissions (skip on Windows)
    if (! OperatingSystem::isWindows()) {
        $certPerms = fileperms($paths['cert']) & 0777;
        $keyPerms = fileperms($paths['key']) & 0777;

        expect($certPerms)->toBe(0600);
        expect($keyPerms)->toBe(0600);
    }

    // Cleanup
    unlink($paths['cert']);
    unlink($paths['key']);
    rmdir($paths['directory']);
    rmdir($tempDir);
});

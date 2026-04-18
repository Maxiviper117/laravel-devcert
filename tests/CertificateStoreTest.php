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

<?php

use Maxiviper117\LaravelDevcert\Actions\RemoveLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\EnvironmentFileManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

it('removes hosts entry, certificates, and env variables', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('remove')
        ->with('example.test')
        ->once();

    $certificates = Mockery::mock(CertificateStore::class);
    $certificates->shouldReceive('delete')
        ->with('example.test')
        ->once();

    $environment = Mockery::mock(EnvironmentFileManager::class);
    $environment->shouldReceive('remove')
        ->with(['LOCAL_HTTPS_DOMAIN', 'LOCAL_HTTPS_CERT', 'LOCAL_HTTPS_KEY'])
        ->once();

    app()->instance(HostsFileManager::class, $hosts);
    app()->instance(CertificateStore::class, $certificates);
    app()->instance(EnvironmentFileManager::class, $environment);

    $action = app(RemoveLocalHttpsAction::class);
    $action->execute('example.test');

    // Mockery verifies expectations automatically
    expect(true)->toBeTrue();
});

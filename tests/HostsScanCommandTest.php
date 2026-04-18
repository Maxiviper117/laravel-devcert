<?php

use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

it('scans and outputs domains from hosts file', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn(['example.test', 'another.test']);

    app()->instance(HostsFileManager::class, $hosts);

    $this->artisan('local-https:hosts:scan')
        ->expectsOutput('example.test')
        ->expectsOutput('another.test')
        ->assertSuccessful();
});

it('outputs nothing when hosts file is empty', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn([]);

    app()->instance(HostsFileManager::class, $hosts);

    $this->artisan('local-https:hosts:scan')
        ->assertSuccessful();
});

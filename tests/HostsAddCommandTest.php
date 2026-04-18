<?php

use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

it('adds a domain to the hosts file', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('add')
        ->with('example.test')
        ->once();

    app()->instance(HostsFileManager::class, $hosts);

    $this->artisan('local-https:hosts:add example.test')
        ->expectsOutputToContain('Hosts entry added')
        ->assertSuccessful();
});

it('adds a domain with different TLD', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('add')
        ->with('myapp.local')
        ->once();

    app()->instance(HostsFileManager::class, $hosts);

    $this->artisan('local-https:hosts:add myapp.local')
        ->expectsOutputToContain('Hosts entry added')
        ->assertSuccessful();
});

it('displays permission error when hosts file is not writable', function () {
    $path = tempnam(sys_get_temp_dir(), 'hosts');
    file_put_contents($path, "127.0.0.1 alpha.test\n");
    chmod($path, 0444);

    config()->set('laravel-devcert.hosts_path', $path);

    $this->artisan('local-https:hosts:add beta.test')
        ->assertFailed();

    chmod($path, 0666);
    unlink($path);
});

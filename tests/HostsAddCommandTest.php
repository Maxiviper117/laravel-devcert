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

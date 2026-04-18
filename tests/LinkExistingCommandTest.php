<?php

use Maxiviper117\LaravelDevcert\Actions\LinkExistingAction;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

it('links to an existing domain from hosts file', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn(['example.test', 'another.test']);

    $linkExisting = Mockery::mock(LinkExistingAction::class);
    $linkExisting->shouldReceive('execute')
        ->with('example.test')
        ->once()
        ->andReturn([
            'domain' => 'example.test',
            'paths' => [
                'cert' => '/certs/example.test.crt',
                'key' => '/certs/example.test.key',
            ],
            'messages' => ['Linked to existing domain'],
        ]);

    app()->instance(HostsFileManager::class, $hosts);
    app()->instance(LinkExistingAction::class, $linkExisting);

    $this->artisan('local-https:link-existing example.test')
        ->expectsOutputToContain('Linked to existing domain')
        ->assertSuccessful();
});

it('shows interactive choice when no domain argument provided', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn(['example.test', 'another.test']);

    $linkExisting = Mockery::mock(LinkExistingAction::class);
    $linkExisting->shouldReceive('execute')
        ->with('example.test')
        ->once()
        ->andReturn([
            'domain' => 'example.test',
            'paths' => [
                'cert' => '/certs/example.test.crt',
                'key' => '/certs/example.test.key',
            ],
            'messages' => ['Linked to existing domain'],
        ]);

    app()->instance(HostsFileManager::class, $hosts);
    app()->instance(LinkExistingAction::class, $linkExisting);

    $this->artisan('local-https:link-existing')
        ->expectsQuestion('Select a domain', 'example.test')
        ->expectsOutputToContain('Linked to existing domain')
        ->assertSuccessful();
});

it('fails when no domains exist in hosts file', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn([]);

    app()->instance(HostsFileManager::class, $hosts);

    $this->artisan('local-https:link-existing')
        ->expectsOutputToContain('No existing domain found')
        ->assertFailed();
});

it('displays permission error when hosts file is not writable', function () {
    $path = tempnam(sys_get_temp_dir(), 'hosts');
    file_put_contents($path, "127.0.0.1 alpha.test\n");
    chmod($path, 0444);

    config()->set('laravel-devcert.hosts_path', $path);

    $this->artisan('local-https:link-existing alpha.test')
        ->assertFailed();

    chmod($path, 0666);
    unlink($path);
});

<?php

use Maxiviper117\LaravelDevcert\Actions\LinkExistingAction;
use Maxiviper117\LaravelDevcert\Actions\SetupLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Services\DomainManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

it('links to provided domain', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldNotReceive('scan');

    $domains = Mockery::mock(DomainManager::class);
    $domains->shouldNotReceive('resolve');

    $setup = Mockery::mock(SetupLocalHttpsAction::class);
    $setup->shouldReceive('execute')
        ->with('example.test')
        ->once()
        ->andReturn([
            'domain' => 'example.test',
            'paths' => [
                'cert' => '/certs/example.test.crt',
                'key' => '/certs/example.test.key',
            ],
            'messages' => ['Linked successfully'],
        ]);

    app()->instance(HostsFileManager::class, $hosts);
    app()->instance(DomainManager::class, $domains);
    app()->instance(SetupLocalHttpsAction::class, $setup);

    $action = app(LinkExistingAction::class);
    $result = $action->execute('example.test');

    expect($result['domain'])->toBe('example.test');
});

it('uses first domain from hosts scan when no domain provided', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn(['first.test', 'second.test']);

    $domains = Mockery::mock(DomainManager::class);
    $domains->shouldNotReceive('resolve');

    $setup = Mockery::mock(SetupLocalHttpsAction::class);
    $setup->shouldReceive('execute')
        ->with('first.test')
        ->once()
        ->andReturn([
            'domain' => 'first.test',
            'paths' => [
                'cert' => '/certs/first.test.crt',
                'key' => '/certs/first.test.key',
            ],
            'messages' => ['Linked successfully'],
        ]);

    app()->instance(HostsFileManager::class, $hosts);
    app()->instance(DomainManager::class, $domains);
    app()->instance(SetupLocalHttpsAction::class, $setup);

    $action = app(LinkExistingAction::class);
    $result = $action->execute();

    expect($result['domain'])->toBe('first.test');
});

it('falls back to domain manager when hosts scan is empty', function () {
    $hosts = Mockery::mock(HostsFileManager::class);
    $hosts->shouldReceive('scan')
        ->once()
        ->andReturn([]);

    $domains = Mockery::mock(DomainManager::class);
    $domains->shouldReceive('resolve')
        ->once()
        ->andReturn('fallback.test');

    $setup = Mockery::mock(SetupLocalHttpsAction::class);
    $setup->shouldReceive('execute')
        ->with('fallback.test')
        ->once()
        ->andReturn([
            'domain' => 'fallback.test',
            'paths' => [
                'cert' => '/certs/fallback.test.crt',
                'key' => '/certs/fallback.test.key',
            ],
            'messages' => ['Linked successfully'],
        ]);

    app()->instance(HostsFileManager::class, $hosts);
    app()->instance(DomainManager::class, $domains);
    app()->instance(SetupLocalHttpsAction::class, $setup);

    $action = app(LinkExistingAction::class);
    $result = $action->execute();

    expect($result['domain'])->toBe('fallback.test');
});

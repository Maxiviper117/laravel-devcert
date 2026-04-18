<?php

use Maxiviper117\LaravelDevcert\Actions\GenerateCertificateAction;
use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\MkcertService;

it('generates certificate when it does not exist', function () {
    $certificates = Mockery::mock(CertificateStore::class);
    $certificates->shouldReceive('ensureDirectory')
        ->with('example.test')
        ->once()
        ->andReturn([
            'directory' => '/certs/example.test',
            'cert' => '/certs/example.test/example.test.crt',
            'key' => '/certs/example.test/example.test.key',
        ]);
    $certificates->shouldReceive('exists')
        ->with('example.test')
        ->once()
        ->andReturn(false);

    $mkcert = Mockery::mock(MkcertService::class);
    $mkcert->shouldReceive('generate')
        ->with('example.test', '/certs/example.test/example.test.crt', '/certs/example.test/example.test.key')
        ->once();

    app()->instance(CertificateStore::class, $certificates);
    app()->instance(MkcertService::class, $mkcert);

    $action = app(GenerateCertificateAction::class);
    $result = $action->execute('example.test');

    expect($result)->toHaveKey('directory');
    expect($result)->toHaveKey('cert');
    expect($result)->toHaveKey('key');
});

it('skips generation when certificate already exists', function () {
    $certificates = Mockery::mock(CertificateStore::class);
    $certificates->shouldReceive('ensureDirectory')
        ->with('example.test')
        ->once()
        ->andReturn([
            'directory' => '/certs/example.test',
            'cert' => '/certs/example.test/example.test.crt',
            'key' => '/certs/example.test/example.test.key',
        ]);
    $certificates->shouldReceive('exists')
        ->with('example.test')
        ->once()
        ->andReturn(true);

    $mkcert = Mockery::mock(MkcertService::class);
    $mkcert->shouldNotReceive('generate');

    app()->instance(CertificateStore::class, $certificates);
    app()->instance(MkcertService::class, $mkcert);

    $action = app(GenerateCertificateAction::class);
    $result = $action->execute('example.test');

    expect($result['cert'])->toBe('/certs/example.test/example.test.crt');
});

it('forces regeneration when force flag is true', function () {
    $certificates = Mockery::mock(CertificateStore::class);
    $certificates->shouldReceive('ensureDirectory')
        ->with('example.test')
        ->once()
        ->andReturn([
            'directory' => '/certs/example.test',
            'cert' => '/certs/example.test/example.test.crt',
            'key' => '/certs/example.test/example.test.key',
        ]);
    // exists() is not called when force=true
    $certificates->shouldNotReceive('exists');

    $mkcert = Mockery::mock(MkcertService::class);
    $mkcert->shouldReceive('generate')
        ->once();

    app()->instance(CertificateStore::class, $certificates);
    app()->instance(MkcertService::class, $mkcert);

    $action = app(GenerateCertificateAction::class);
    $action->execute('example.test', true);

    expect(true)->toBeTrue();
});

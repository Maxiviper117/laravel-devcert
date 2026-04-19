<?php

use Maxiviper117\LaravelDevcert\Actions\GenerateCertificateAction;

it('generates a certificate for a domain', function () {
    $generate = Mockery::mock(GenerateCertificateAction::class);
    $generate->shouldReceive('execute')
        ->with('example.test', false)
        ->once()
        ->andReturn([
            'directory' => '/certs/example.test',
            'cert' => '/certs/example.test/example.test.crt',
            'key' => '/certs/example.test/example.test.key',
        ]);

    app()->instance(GenerateCertificateAction::class, $generate);

    $this->artisan('local-https:cert:generate example.test')
        ->assertSuccessful();
});

it('forces certificate regeneration with --force flag', function () {
    $generate = Mockery::mock(GenerateCertificateAction::class);
    $generate->shouldReceive('execute')
        ->with('example.test', true)
        ->once()
        ->andReturn([
            'directory' => '/certs/example.test',
            'cert' => '/certs/example.test/example.test.crt',
            'key' => '/certs/example.test/example.test.key',
        ]);

    app()->instance(GenerateCertificateAction::class, $generate);

    $this->artisan('local-https:cert:generate example.test --force')
        ->assertSuccessful();
});

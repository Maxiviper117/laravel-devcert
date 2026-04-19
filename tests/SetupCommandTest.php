<?php

use Maxiviper117\LaravelDevcert\Actions\SetupLocalHttpsAction;

it('passes skip-vite through the setup command', function () {
    $setup = Mockery::mock(SetupLocalHttpsAction::class);
    $setup->shouldReceive('execute')
        ->with('example.test', true, true)
        ->andReturn([
            'domain' => 'example.test',
            'paths' => [
                'cert' => 'C:/certs/example.test.crt',
                'key' => 'C:/certs/example.test.key',
            ],
            'messages' => ['Vite config update skipped'],
        ]);

    app()->instance(SetupLocalHttpsAction::class, $setup);

    $this->artisan('local-https:setup example.test --force --skip-vite')
        ->expectsOutputToContain('domain')
        ->expectsOutputToContain('example.test')
        ->assertExitCode(0);
});

it('blocks the setup command on wsl', function () {
    $originalDistro = getenv('WSL_DISTRO_NAME');
    $originalInterop = getenv('WSL_INTEROP');

    try {
        putenv('WSL_DISTRO_NAME=Ubuntu-24');
        putenv('WSL_INTEROP=/run/WSL/interop');

        $setup = Mockery::mock(SetupLocalHttpsAction::class);
        $setup->shouldNotReceive('execute');
        app()->instance(SetupLocalHttpsAction::class, $setup);

        $this->artisan('local-https:setup example.test')
            ->expectsOutputToContain('WSL is not supported')
            ->assertExitCode(1);
    } finally {
        if ($originalDistro === false) {
            putenv('WSL_DISTRO_NAME');
        } else {
            putenv('WSL_DISTRO_NAME='.$originalDistro);
        }

        if ($originalInterop === false) {
            putenv('WSL_INTEROP');
        } else {
            putenv('WSL_INTEROP='.$originalInterop);
        }
    }
});

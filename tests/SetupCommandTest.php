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

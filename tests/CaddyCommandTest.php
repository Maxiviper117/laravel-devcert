<?php

use Maxiviper117\LaravelDevcert\Contracts\ManagedProcess;
use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use Maxiviper117\LaravelDevcert\Services\CaddyService;
use Maxiviper117\LaravelDevcert\Support\ProcessResult;

it('starts the caddy command and exits cleanly when the process ends', function () {
    $process = new class implements ManagedProcess
    {
        public bool $started = false;

        public function start(): void
        {
            $this->started = true;
        }

        public function isRunning(): bool
        {
            return false;
        }

        public function getIncrementalOutput(): string
        {
            return '';
        }

        public function getIncrementalErrorOutput(): string
        {
            return '';
        }

        public function stop(int $timeout = 3, ?int $signal = null): int
        {
            return 0;
        }

        public function wait(): int
        {
            return 0;
        }

        public function getExitCode(): int
        {
            return 0;
        }
    };

    $service = Mockery::mock(CaddyService::class);
    $service->shouldReceive('installed')->andReturnTrue();
    $service->shouldReceive('buildCaddyfile')
        ->with('example.test', 'http://127.0.0.1:8000', Mockery::type('string'), Mockery::type('string'))
        ->andReturn("example.test {\n    tls C:/cert.crt C:/cert.key\n    reverse_proxy http://127.0.0.1:8000\n}\n");
    $service->shouldReceive('startReverseProxy')
        ->with(Mockery::type('string'))
        ->andReturn($process);

    app()->instance(CaddyService::class, $service);
    app()->instance(ProcessRunner::class, new class implements ProcessRunner
    {
        public function run(string $command): ProcessResult
        {
            return new ProcessResult(0, ['Caddy version 2.8.4']);
        }
    });

    $this->artisan('local-https:caddy example.test')
        ->expectsOutputToContain('Caddy reverse proxy started for https://example.test -> http://127.0.0.1:8000')
        ->assertExitCode(0);
});

it('throws a helpful message when caddy is missing', function () {
    $service = Mockery::mock(CaddyService::class);
    $service->shouldReceive('installed')->andReturnFalse();

    app()->instance(CaddyService::class, $service);

    expect(fn () => $this->artisan('local-https:caddy example.test')->run())
        ->toThrow(RuntimeException::class, 'See https://github.com/Maxiviper117/laravel-devcert for setup instructions.');
});

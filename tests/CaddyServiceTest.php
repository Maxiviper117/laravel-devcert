<?php

use Maxiviper117\LaravelDevcert\Contracts\ManagedProcess;
use Maxiviper117\LaravelDevcert\Contracts\ManagedProcessFactory;
use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use Maxiviper117\LaravelDevcert\Services\CaddyService;
use Maxiviper117\LaravelDevcert\Support\ProcessResult;

class MapProcessRunner implements ProcessRunner
{
    public function __construct(private array $responses) {}

    public function run(string $command): ProcessResult
    {
        return $this->responses[$command] ?? new ProcessResult(1, []);
    }
}

class SpyManagedProcess implements ManagedProcess
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

    public function getExitCode(): ?int
    {
        return 0;
    }
}

class SpyManagedProcessFactory implements ManagedProcessFactory
{
    public array $commands = [];

    public function __construct(private ?ManagedProcess $process = null) {}

    public function create(array $command): ManagedProcess
    {
        $this->commands[] = $command;

        return $this->process ?? new SpyManagedProcess;
    }
}

it('detects caddy and returns its version', function () {
    $runner = new MapProcessRunner([
        'caddy version' => new ProcessResult(0, ['Caddy version 2.8.4']),
    ]);
    $factory = new SpyManagedProcessFactory;
    $service = new CaddyService($runner, $factory);

    expect($service->installed())->toBeTrue()
        ->and($service->version())->toBe('Caddy version 2.8.4');
});

it('builds the caddy reverse proxy command', function () {
    $runner = new MapProcessRunner([
        'caddy version' => new ProcessResult(0, ['Caddy version 2.8.4']),
    ]);
    $factory = new SpyManagedProcessFactory;
    $service = new CaddyService($runner, $factory);

    $caddyfile = $service->buildCaddyfile(
        'example.test',
        'http://127.0.0.1:8000',
        'C:/certs/example.test.crt',
        'C:/certs/example.test.key',
    );

    expect($caddyfile)->toContain('example.test {')
        ->and($caddyfile)->toContain('tls C:/certs/example.test.crt C:/certs/example.test.key')
        ->and($caddyfile)->toContain('reverse_proxy http://127.0.0.1:8000');
});

it('creates a managed process for the reverse proxy command', function () {
    $runner = new MapProcessRunner([
        'caddy version' => new ProcessResult(0, ['Caddy version 2.8.4']),
    ]);
    $factory = new SpyManagedProcessFactory;
    $service = new CaddyService($runner, $factory);

    $process = $service->startReverseProxy('C:/temp/example.caddyfile');

    expect($factory->commands)->toHaveCount(1)
        ->and($factory->commands[0])->toBe([
            'caddy',
            'run',
            '--config',
            'C:/temp/example.caddyfile',
            '--adapter',
            'caddyfile',
        ])
        ->and($process)->toBeInstanceOf(ManagedProcess::class);
});

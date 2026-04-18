<?php

use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use Maxiviper117\LaravelDevcert\Services\MkcertService;
use Maxiviper117\LaravelDevcert\Support\ProcessResult;

class FakeProcessRunner implements ProcessRunner
{
    public array $commands = [];

    public function __construct(private int $exitCode = 0) {}

    public function run(string $command): ProcessResult
    {
        $this->commands[] = $command;

        return new ProcessResult($this->exitCode, []);
    }
}

it('builds the mkcert install command through the process runner', function () {
    $runner = new FakeProcessRunner;
    app()->instance(ProcessRunner::class, $runner);

    app(MkcertService::class)->installIfNeeded();

    expect($runner->commands)->toHaveCount(2)
        ->and($runner->commands[0])->toBe('mkcert -help')
        ->and($runner->commands[1])->toBe('mkcert -install');
});

it('throws a helpful message when mkcert is missing', function () {
    $runner = new FakeProcessRunner(1);
    app()->instance(ProcessRunner::class, $runner);

    expect(fn () => app(MkcertService::class)->installIfNeeded())
        ->toThrow(RuntimeException::class, 'See https://github.com/Maxiviper117/laravel-devcert for setup instructions.');
});

it('builds the mkcert generate command through the process runner', function () {
    $runner = new FakeProcessRunner;
    app()->instance(ProcessRunner::class, $runner);

    config()->set('laravel-devcert.include_wildcard', true);

    app(MkcertService::class)->generate('demo.test', sys_get_temp_dir().DIRECTORY_SEPARATOR.'demo.crt', sys_get_temp_dir().DIRECTORY_SEPARATOR.'demo.key');

    expect($runner->commands)->toHaveCount(1)
        ->and($runner->commands[0])->toContain('mkcert -cert-file')
        ->and($runner->commands[0])->toContain('demo.test')
        ->and($runner->commands[0])->toContain('*.demo.test');
});

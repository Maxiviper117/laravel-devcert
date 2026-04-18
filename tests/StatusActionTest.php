<?php

use Maxiviper117\LaravelDevcert\Actions\StatusAction;
use Maxiviper117\LaravelDevcert\Contracts\ManagedProcess;
use Maxiviper117\LaravelDevcert\Contracts\ManagedProcessFactory;
use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use Maxiviper117\LaravelDevcert\Services\CaddyService;
use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\DomainManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;
use Maxiviper117\LaravelDevcert\Services\MkcertService;
use Maxiviper117\LaravelDevcert\Support\ProcessResult;

it('includes caddy status in the status action', function () {
    $profile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'devcert-status-profile';
    if (! is_dir($profile)) {
        mkdir($profile, 0777, true);
    }

    $hostsPath = tempnam(sys_get_temp_dir(), 'hosts');
    file_put_contents($hostsPath, "127.0.0.1 example.test\n");

    config()->set('app.name', 'Laravel Devcert');
    config()->set('laravel-devcert.local_https_domain', 'example.test');
    config()->set('app.url', 'https://example.test');
    config()->set('laravel-devcert.certs_path', $profile.DIRECTORY_SEPARATOR.'.local-https'.DIRECTORY_SEPARATOR.'certs');
    config()->set('laravel-devcert.hosts_path', $hostsPath);

    $runner = new class implements ProcessRunner
    {
        public function run(string $command): ProcessResult
        {
            return match ($command) {
                'mkcert -help' => new ProcessResult(0, ['mkcert help']),
                'caddy version' => new ProcessResult(0, ['Caddy version 2.8.4']),
                default => new ProcessResult(1, []),
            };
        }
    };

    $factory = new class implements ManagedProcessFactory
    {
        public function create(array $command): ManagedProcess
        {
            return new class implements ManagedProcess
            {
                public function start(): void {}

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
        }
    };

    $status = (new StatusAction(
        app(DomainManager::class),
        app(CertificateStore::class),
        app(HostsFileManager::class),
        new MkcertService($runner),
        new CaddyService($runner, $factory),
    ))->execute();

    expect($status['domain'])->toBe('example.test')
        ->and($status['hosts_entry'])->toBeTrue()
        ->and($status['mkcert_installed'])->toBeTrue()
        ->and($status['caddy_installed'])->toBeTrue()
        ->and($status['caddy_version'])->toBe('Caddy version 2.8.4');

    unlink($hostsPath);
});

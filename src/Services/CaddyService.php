<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Services;

use Maxiviper117\LaravelDevcert\Contracts\ManagedProcess;
use Maxiviper117\LaravelDevcert\Contracts\ManagedProcessFactory;
use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;

class CaddyService
{
    public function __construct(
        private ProcessRunner $process,
        private ManagedProcessFactory $managedProcesses,
    ) {}

    public function installed(): bool
    {
        return $this->run('caddy version') === 0;
    }

    public function version(): ?string
    {
        $result = $this->process->run('caddy version');

        if ($result->exitCode !== 0) {
            return null;
        }

        $version = trim(implode("\n", $result->output));

        return $version !== '' ? $version : null;
    }

    public function buildCaddyfile(string $domain, string $upstream, string $certPath, string $keyPath): string
    {
        return implode(PHP_EOL, [
            $domain.' {',
            sprintf('    tls %s %s', $this->escapeCaddyfilePath($certPath), $this->escapeCaddyfilePath($keyPath)),
            '    reverse_proxy '.$upstream,
            '}',
            '',
        ]);
    }

    public function buildRunCommand(string $caddyfilePath): array
    {
        return [
            'caddy',
            'run',
            '--config',
            $caddyfilePath,
            '--adapter',
            'caddyfile',
        ];
    }

    public function startReverseProxy(string $caddyfilePath): ManagedProcess
    {
        return $this->managedProcesses->create($this->buildRunCommand($caddyfilePath));
    }

    private function run(string $command): int
    {
        return $this->process->run($command)->exitCode;
    }

    private function escapeCaddyfilePath(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);

        return str_replace(' ', '\\ ', $normalized);
    }
}

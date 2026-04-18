<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Services;

use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use RuntimeException;

class MkcertService
{
    private const HELP_URL = 'https://github.com/Maxiviper117/laravel-devcert';

    public function __construct(
        private ProcessRunner $process,
    ) {}

    public function installed(): bool
    {
        return $this->run('mkcert -help') === 0;
    }

    public function installIfNeeded(): void
    {
        if (! $this->installed()) {
            throw new RuntimeException('mkcert is not installed or not available on PATH. See '.self::HELP_URL.' for setup instructions.');
        }

        $code = $this->run('mkcert -install');

        if ($code !== 0) {
            throw new RuntimeException('mkcert CA installation failed.');
        }
    }

    public function generate(string $domain, string $certPath, string $keyPath): void
    {
        $directory = dirname($certPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $hosts = [$domain];

        if (config('laravel-devcert.include_wildcard', false)) {
            $hosts[] = '*.'.$domain;
        }

        $command = sprintf(
            'mkcert -cert-file %s -key-file %s %s',
            escapeshellarg($certPath),
            escapeshellarg($keyPath),
            implode(' ', array_map('escapeshellarg', $hosts))
        );

        if ($this->run($command) !== 0) {
            throw new RuntimeException(sprintf('mkcert certificate generation failed for %s.', $domain));
        }
    }

    private function run(string $command): int
    {
        return $this->process->run($command)->exitCode;
    }
}

<?php

namespace Maxiviper117\LaravelDevcert\Services;

use RuntimeException;
use Maxiviper117\LaravelDevcert\Support\OperatingSystem;

class HostsFileManager
{
    public function path(): string
    {
        $configured = config('laravel-devcert.hosts_path');

        if ($configured !== null && $configured !== '') {
            return $configured;
        }

        if (OperatingSystem::isWindows()) {
            return rtrim((string) (getenv('SystemRoot') ?: 'C:\\Windows'), '\\/').DIRECTORY_SEPARATOR.'System32'.DIRECTORY_SEPARATOR.'drivers'.DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'hosts';
        }

        return '/etc/hosts';
    }

    public function scan(): array
    {
        $domains = [];

        foreach ($this->lines() as $line) {
            if ($line === '' || str_starts_with(ltrim($line), '#')) {
                continue;
            }

            $parts = preg_split('/\s+/', trim($line)) ?: [];
            array_shift($parts);

            foreach ($parts as $part) {
                if ($part === '' || str_starts_with($part, '#') || $part === 'localhost') {
                    continue;
                }

                if (! in_array($part, $domains, true)) {
                    $domains[] = $part;
                }
            }
        }

        return $domains;
    }

    public function contains(string $domain): bool
    {
        foreach ($this->lines() as $line) {
            if ($this->lineContainsDomain($line, $domain)) {
                return true;
            }
        }

        return false;
    }

    public function add(string $domain): void
    {
        $lines = $this->lines();
        $entries = ['127.0.0.1 '.$domain];

        if (config('laravel-devcert.include_ipv6', true)) {
            $entries[] = '::1 '.$domain;
        }

        foreach ($entries as $entry) {
            if (! $this->lineExists($lines, $entry)) {
                $lines[] = $entry;
            }
        }

        $this->write($lines);
    }

    public function remove(string $domain): void
    {
        $lines = array_values(array_filter(
            $this->lines(),
            fn (string $line) => ! $this->lineContainsDomain($line, $domain)
        ));

        $this->write($lines);
    }

    private function lineContainsDomain(string $line, string $domain): bool
    {
        if ($line === '' || str_starts_with(ltrim($line), '#')) {
            return false;
        }

        $parts = preg_split('/\s+/', trim($line)) ?: [];
        array_shift($parts);

        return in_array($domain, $parts, true);
    }

    private function lineExists(array $lines, string $candidate): bool
    {
        foreach ($lines as $line) {
            if (trim($line) === $candidate) {
                return true;
            }
        }

        return false;
    }

    private function lines(): array
    {
        $path = $this->path();

        if (! file_exists($path)) {
            return [];
        }

        return file($path, FILE_IGNORE_NEW_LINES) ?: [];
    }

    private function write(array $lines): void
    {
        $path = $this->path();
        $result = file_put_contents($path, implode(PHP_EOL, $lines).PHP_EOL);

        if ($result === false) {
            throw new RuntimeException('Unable to write hosts file at '.$path);
        }
    }
}

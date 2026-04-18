<?php

namespace Maxiviper117\LaravelDevcert\Services;

use Maxiviper117\LaravelDevcert\Support\OperatingSystem;

class CertificateStore
{
    public function basePath(): string
    {
        $configured = config('laravel-devcert.certs_path');

        if ($configured !== null && $configured !== '') {
            return $this->expandTilde($configured);
        }

        $root = OperatingSystem::isWindows()
            ? (getenv('USERPROFILE') ?: (getenv('HOMEDRIVE').getenv('HOMEPATH')))
            : (getenv('HOME') ?: sys_get_temp_dir());

        return rtrim((string) $root, '\\/').DIRECTORY_SEPARATOR.'.local-https'.DIRECTORY_SEPARATOR.'certs';
    }

    public function paths(string $domain): array
    {
        $directory = rtrim($this->basePath(), '\\/').DIRECTORY_SEPARATOR.$domain;

        return [
            'directory' => $directory,
            'cert' => $directory.DIRECTORY_SEPARATOR.$domain.'.crt',
            'key' => $directory.DIRECTORY_SEPARATOR.$domain.'.key',
        ];
    }

    public function ensureDirectory(string $domain): array
    {
        $paths = $this->paths($domain);

        if (! is_dir($paths['directory'])) {
            mkdir($paths['directory'], 0777, true);
        }

        return $paths;
    }

    public function exists(string $domain): bool
    {
        $paths = $this->paths($domain);

        return file_exists($paths['cert']) && file_exists($paths['key']);
    }

    public function delete(string $domain): void
    {
        $paths = $this->paths($domain);

        if (file_exists($paths['cert'])) {
            unlink($paths['cert']);
        }

        if (file_exists($paths['key'])) {
            unlink($paths['key']);
        }
    }

    private function expandTilde(string $path): string
    {
        if (! str_starts_with($path, '~')) {
            return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        }

        $home = OperatingSystem::isWindows()
            ? (getenv('USERPROFILE') ?: (getenv('HOMEDRIVE').getenv('HOMEPATH')))
            : (getenv('HOME') ?: sys_get_temp_dir());

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $home.substr($path, 1));
    }
}

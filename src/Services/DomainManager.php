<?php

namespace Maxiviper117\LaravelDevcert\Services;

use Illuminate\Support\Str;

class DomainManager
{
    public function resolve(?string $domain = null): string
    {
        if ($domain !== null && trim($domain) !== '') {
            return trim($domain);
        }

        $configuredDomain = $this->configuredDomain();
        if ($configuredDomain !== null) {
            return $configuredDomain;
        }

        $appName = config('app.name', 'laravel');
        $slug = Str::slug((string) $appName) ?: 'laravel';

        return $slug.config('laravel-devcert.default_tld', '.test');
    }

    private function configuredDomain(): ?string
    {
        $domain = trim((string) config('laravel-devcert.local_https_domain', ''));
        if ($domain !== '') {
            return $domain;
        }

        $appUrl = trim((string) config('app.url', ''));
        if ($appUrl === '') {
            return null;
        }

        $host = trim((string) parse_url($appUrl, PHP_URL_HOST));
        if ($host === '' || in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return null;
        }

        return $host;
    }
}

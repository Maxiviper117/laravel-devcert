<?php

namespace Maxiviper117\LaravelDevcert\Services;

use Illuminate\Support\Str;
use InvalidArgumentException;

class DomainManager
{
    public function resolve(?string $domain = null): string
    {
        if ($domain !== null && trim($domain) !== '') {
            $domain = trim($domain);
            $this->validate($domain);

            return $domain;
        }

        $configuredDomain = $this->configuredDomain();
        if ($configuredDomain !== null) {
            return $configuredDomain;
        }

        $appName = config('app.name', 'laravel');
        $slug = Str::slug((string) $appName) ?: 'laravel';

        return $slug.config('laravel-devcert.default_tld', '.test');
    }

    private function validate(string $domain): void
    {
        // Check for spaces
        if (str_contains($domain, ' ')) {
            throw new InvalidArgumentException(sprintf("Domain name cannot contain spaces: '%s'", $domain));
        }

        // Check for consecutive dots
        if (str_contains($domain, '..')) {
            throw new InvalidArgumentException(sprintf("Domain name cannot contain consecutive dots: '%s'", $domain));
        }

        // Check total length (max 253 characters)
        if (strlen($domain) > 253) {
            throw new InvalidArgumentException(sprintf("Domain name too long (max 253 characters): '%s'", $domain));
        }

        // Check each label for specific rules
        $labels = explode('.', $domain);
        foreach ($labels as $label) {
            // Check label length (max 63 characters)
            if (strlen($label) > 63) {
                throw new InvalidArgumentException(sprintf("Domain label too long (max 63 characters): '%s'", $label));
            }

            // Check for leading/trailing hyphens in labels
            if (str_starts_with($label, '-') || str_ends_with($label, '-')) {
                throw new InvalidArgumentException(sprintf("Domain labels cannot start or end with hyphens: '%s'", $label));
            }

            // Check for empty labels (from leading/trailing dots or consecutive dots)
            if ($label === '') {
                throw new InvalidArgumentException(sprintf("Domain name cannot contain empty labels: '%s'", $domain));
            }
        }

        // Check for valid characters (alphanumeric, hyphens, dots) - RFC 1123 compliant
        if (! preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $domain)) {
            throw new InvalidArgumentException(sprintf("Invalid domain name format: '%s'", $domain));
        }
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

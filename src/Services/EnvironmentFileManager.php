<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Services;

class EnvironmentFileManager
{
    public function update(array $values): void
    {
        $path = base_path('.env');

        if (! file_exists($path) && file_exists(base_path('.env.example'))) {
            copy(base_path('.env.example'), $path);
        }

        $contents = file_exists($path) ? (string) file_get_contents($path) : '';

        foreach ($values as $key => $value) {
            $line = $key.'='.$this->formatValue((string) $value);

            if (preg_match('/^'.preg_quote($key, '/').'=.*/m', (string) $contents)) {
                $contents = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, (string) $contents) ?: (string) $contents;

                continue;
            }

            $contents = trim((string) $contents) === ''
                ? $line.PHP_EOL
                : rtrim((string) $contents).PHP_EOL.$line.PHP_EOL;
        }

        file_put_contents($path, $contents);
    }

    public function remove(array $keys): void
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];

        $lines = array_values(array_filter($lines, function (string $line) use ($keys) {
            foreach ($keys as $key) {
                if (str_starts_with($line, $key.'=')) {
                    return false;
                }
            }

            return true;
        }));

        file_put_contents($path, implode(PHP_EOL, $lines).PHP_EOL);
    }

    private function formatValue(string $value): string
    {
        if ($value === '') {
            return '""';
        }

        if (preg_match('/\s|#|"/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }
}

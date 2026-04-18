<?php

namespace Maxiviper117\LaravelDevcert\Services;

class ViteConfigManager
{
    public function ensureLocalHttpsConfiguration(string $domain, string $certPath, string $keyPath): bool
    {
        $path = $this->path();

        if (! file_exists($path)) {
            return false;
        }

        $contents = str_replace(["\r\n", "\r"], "\n", (string) file_get_contents($path));

        if ($this->alreadyConfigured($contents)) {
            return false;
        }

        $output = null;

        if (str_contains($contents, "import tailwindcss from '@tailwindcss/vite';")) {
            $output = $this->patchWithTailwind();
        } elseif (str_contains($contents, 'export default defineConfig({')) {
            $output = $this->patchWithoutTailwind();
        }

        if (! is_string($output) || $output === $contents) {
            return false;
        }

        file_put_contents($path, $output);

        return true;
    }

    private function path(): string
    {
        $configured = config('laravel-devcert.vite_config_path');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return base_path('vite.config.js');
    }

    private function alreadyConfigured(string $contents): bool
    {
        return str_contains($contents, 'LOCAL_HTTPS_DOMAIN')
            && str_contains($contents, 'LOCAL_HTTPS_CERT')
            && str_contains($contents, 'LOCAL_HTTPS_KEY');
    }

    private function patchWithTailwind(): string
    {
        return <<<'JS'
import fs from 'fs';
import path from 'path';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const host = env.LOCAL_HTTPS_DOMAIN || null;
    const certPath = env.LOCAL_HTTPS_CERT || null;
    const keyPath = env.LOCAL_HTTPS_KEY || null;

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                ...(host ? { detectTls: host } : {}),
            }),
            tailwindcss(),
        ],
        server: {
            ...(host ? { host, hmr: { host } } : {}),
            ...(host && certPath && keyPath
                ? {
                    https: {
                        cert: fs.readFileSync(path.resolve(certPath)),
                        key: fs.readFileSync(path.resolve(keyPath)),
                    },
                }
                : {}),
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
JS;
    }

    private function patchWithoutTailwind(): string
    {
        return <<<'JS'
import fs from 'fs';
import path from 'path';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const host = env.LOCAL_HTTPS_DOMAIN || null;
    const certPath = env.LOCAL_HTTPS_CERT || null;
    const keyPath = env.LOCAL_HTTPS_KEY || null;

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                ...(host ? { detectTls: host } : {}),
            }),
        ],
        server: {
            ...(host ? { host, hmr: { host } } : {}),
            ...(host && certPath && keyPath
                ? {
                    https: {
                        cert: fs.readFileSync(path.resolve(certPath)),
                        key: fs.readFileSync(path.resolve(keyPath)),
                    },
                }
                : {}),
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
JS;
    }
}

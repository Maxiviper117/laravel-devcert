<?php

use Maxiviper117\LaravelDevcert\Services\ViteConfigManager;

it('patches a basic vite config for local https', function () {
    $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'vite-config-'.uniqid().'.js';

    file_put_contents($path, <<<'JS'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
JS);

    config()->set('laravel-devcert.vite_config_path', $path);

    $updated = app(ViteConfigManager::class)->ensureLocalHttpsConfiguration(
        'example.test',
        'C:/Users/david/.local-https/certs/example.test/example.test.crt',
        'C:/Users/david/.local-https/certs/example.test/example.test.key',
    );

    $contents = file_get_contents($path);

    expect($updated)->toBeTrue()
        ->and($contents)->toContain("import fs from 'fs';")
        ->and($contents)->toContain("import path from 'path';")
        ->and($contents)->toContain("import { defineConfig, loadEnv } from 'vite';")
        ->and($contents)->toContain("const env = loadEnv(mode, process.cwd(), '');")
        ->and($contents)->toContain('LOCAL_HTTPS_DOMAIN')
        ->and($contents)->toContain('server: {')
        ->and($contents)->toContain('hmr: { host }')
        ->and($contents)->toContain('https: {');

    unlink($path);
});

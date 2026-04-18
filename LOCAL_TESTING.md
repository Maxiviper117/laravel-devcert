# Local Testing Guide

This file is for development only. It describes how to run this package locally inside a real Laravel app so you can test the artisan commands and HTTPS flow by hand.

## Prerequisites

- PHP 8.2 or newer
- Composer
- `mkcert` installed and trusted on your machine
- A separate Laravel app to act as the consumer

## Run The Package From A Local Path

1. Clone or keep this package on disk.
2. If you are using the bundled `workbench/` app, its Composer file is already wired to this package path and branch.
3. If you are using a separate Laravel app, add a local path repository entry to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "D:/Users/david/Development/Github/ALL/laravel-devcert",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

4. Require the package from that local path:

```powershell
composer require maxiviper117/laravel-devcert:dev-feature/laravel-devcert-refactor
```

5. Publish the package config if you need to change paths or defaults:

```powershell
php artisan vendor:publish --tag=laravel-devcert-config
```

## Manual Commands To Try

- `php artisan local-https:status`
- `php artisan local-https:setup example.test`
- `php artisan local-https:hosts:scan`
- `php artisan local-https:hosts:add example.test`
- `php artisan local-https:cert:generate example.test --force`
- `php artisan local-https:remove example.test`
- `php artisan local-https:caddy example.test --to=http://127.0.0.1:8000`
- `php artisan local-https:setup example.test --skip-vite`

## Caddy Workflow

- Run `php artisan serve --host=127.0.0.1 --port=8000` in one terminal.
- Run `php artisan local-https:caddy example.test --to=http://127.0.0.1:8000` in another terminal.
- Open `https://example.test` in the browser.
- Stop the Caddy command with `Ctrl+C`; it should shut down cleanly.

## Vite With The Generated Certificate

If you run `npm run dev` alongside the Caddy workflow, Vite should use the same secure host and the certificate files written by the package.

The package writes these values into `.env`:

- `LOCAL_HTTPS_DOMAIN`
- `LOCAL_HTTPS_CERT`
- `LOCAL_HTTPS_KEY`

A matching `vite.config.js` can read those values directly:

```js
import fs from 'fs';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  const host = env.LOCAL_HTTPS_DOMAIN || 'example.test';
  const cert = env.LOCAL_HTTPS_CERT;
  const key = env.LOCAL_HTTPS_KEY;

  return {
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
        detectTls: host,
      }),
    ],
    server: {
      host,
      hmr: { host },
      https: {
        cert: fs.readFileSync(cert),
        key: fs.readFileSync(key),
      },
    },
  };
});
```

This keeps the browser origin, Laravel URL generation, and Vite HMR aligned on the same HTTPS domain.

If the workbench has a standard `vite.config.js`, `local-https:setup` will patch it automatically. If the file is heavily customized, the command may leave it alone and you can keep the manual Vite config path.

## What To Verify

- The package resolves a sensible local domain.
- The certificate path is created where you expect it.
- The hosts file entry is added and removed correctly.
- `mkcert` is detected and used to generate certificates.
- Windows path handling and Unix-like path handling both behave correctly in the relevant environment.

## Useful Test Commands In This Repo

- `composer test`
- `composer analyse`
- `composer format`

## Notes

- Do not use this guide as end-user documentation.
- If public commands or config change, update this guide together with `README.md` and `AGENTS.md`.

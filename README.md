# Laravel Devcert

Laravel Devcert automates trusted local HTTPS for Laravel projects with `mkcert`, shared certificate storage, and hosts file management.

## Table of Contents

- [Before You Run It](#before-you-run-it)
- [Install Caddy And mkcert](#install-caddy-and-mkcert)
- [Commands](#commands)
- [Run Your App Over HTTPS](#run-your-app-over-https)
- [Installation](#installation)
- [Configuration](#configuration)
- [Testing](#testing)

## Before You Run It

`local-https:setup` updates your system hosts file and may need elevated privileges.

- Windows PowerShell: open a new terminal as Administrator before running the command.
  - From an existing PowerShell window: `Start-Process powershell -Verb RunAs`
  - If you use Windows Terminal: `Start-Process wt -Verb RunAs`
- Linux/macOS bash: run the command with `sudo` if your hosts file is protected.
  - Example: `sudo php artisan local-https:setup example.test`

## Install Caddy And mkcert

You need both `caddy` and `mkcert` on your PATH before the HTTPS workflow will work.
This is a one-time setup per machine.

### Quick Install

Windows:

```powershell
choco install caddy
choco install mkcert
```

Ubuntu / Debian:

```bash
sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https curl
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
sudo chmod o+r /usr/share/keyrings/caddy-stable-archive-keyring.gpg
sudo chmod o+r /etc/apt/sources.list.d/caddy-stable.list
sudo apt update
sudo apt install caddy

sudo apt install libnss3-tools
curl -JLO "https://dl.filippo.io/mkcert/latest?for=linux/amd64"
chmod +x mkcert-v*-linux-amd64
sudo cp mkcert-v*-linux-amd64 /usr/local/bin/mkcert
mkcert -install
```

If you are on another Linux distribution or want the official references, use:

- Caddy install docs: https://caddyserver.com/docs/install
- mkcert README: https://github.com/FiloSottile/mkcert

## Commands

| Command | What it does |
| --- | --- |
| `php artisan local-https:setup [domain]` | Sets up trusted local HTTPS for the current project. |
| `php artisan local-https:status` | Shows the current local HTTPS state. |
| `php artisan local-https:hosts:scan` | Lists domains currently found in the hosts file. |
| `php artisan local-https:hosts:add {domain}` | Adds a domain to the hosts file. |
| `php artisan local-https:remove {domain}` | Removes the package-managed HTTPS setup for a domain. |
| `php artisan local-https:link-existing [domain]` | Connects the project to an already existing local domain. |
| `php artisan local-https:domain [domain]` | Prints the domain the package would use. |
| `php artisan local-https:cert:generate {domain} [--force]` | Generates a certificate and key for a domain. |
| `php artisan local-https:caddy [domain] [--to=...]` | Runs Caddy as a local HTTPS reverse proxy. |

### `php artisan local-https:setup [domain]`

Set up trusted local HTTPS for the current project.

- Resolves the domain to use, or accepts the domain you pass in.
- Checks that `mkcert` is installed and trusted.
- Ensures the hosts file contains the domain.
- Generates the certificate and key files.
- Updates the app environment with the HTTPS paths.
- Patches `vite.config.js` automatically when the file matches the standard Laravel Vite layout.
- Use `--skip-vite` to keep the Vite file untouched.

```bash
php artisan local-https:setup
php artisan local-https:setup example.test
php artisan local-https:setup example.test --skip-vite
```

### `php artisan local-https:status`

Show the current local HTTPS state for the project.

- Reports whether `mkcert` is available.
- Reports whether `caddy` is available.
- Shows the Caddy version when installed.
- Shows the resolved domain.
- Shows whether the hosts file contains that domain.
- Shows the certificate and key paths.
- Shows whether the certificate files already exist.

```bash
php artisan local-https:status
```

### `php artisan local-https:hosts:scan`

List the domains currently found in your hosts file.

- Reads the configured hosts file.
- Extracts hostnames from active entries.
- Prints each discovered domain on its own line.

```bash
php artisan local-https:hosts:scan
```

### `php artisan local-https:hosts:add {domain}`

Add a domain to the hosts file.

- Writes `127.0.0.1 <domain>`.
- Also writes `::1 <domain>` when IPv6 entries are enabled in config.

```bash
php artisan local-https:hosts:add example.test
```

### `php artisan local-https:remove {domain}`

Remove a domain from the package-managed local HTTPS setup.

- Removes the domain from the hosts file.
- Deletes the certificate and key files for that domain.
- Clears the local HTTPS environment values.

```bash
php artisan local-https:remove example.test
```

### `php artisan local-https:link-existing [domain]`

Connect the project to an already existing local domain.

- Uses the domain you pass in, if provided.
- Otherwise tries the first domain found in the hosts file.
- Falls back to the package's default resolved local domain.
- Then runs the normal setup flow for that domain.

```bash
php artisan local-https:link-existing
php artisan local-https:link-existing example.test
```

### `php artisan local-https:domain [domain]`

Print the domain the package would use for local HTTPS.

- Returns the explicitly provided domain when you pass one.
- Otherwise resolves the default domain for the current project.

```bash
php artisan local-https:domain
php artisan local-https:domain example.test
```

### `php artisan local-https:cert:generate {domain} [--force]`

Generate a certificate and key for a domain.

- Creates the certificate directory if needed.
- Skips regeneration when the files already exist.
- Use `--force` to regenerate even if files are already present.

```bash
php artisan local-https:cert:generate example.test
php artisan local-https:cert:generate example.test --force
```

### `php artisan local-https:caddy [domain] [--to=...]`

Run Caddy as a local HTTPS reverse proxy for the app.

- Resolves the domain to use, or accepts the domain you pass in.
- Uses Caddy’s reverse-proxy mode with automatic local HTTPS.
- Proxies the browser traffic to the upstream HTTP server you pass with `--to`.
- Stops Caddy cleanly when you press Ctrl+C or the process shuts down.

```bash
php artisan local-https:caddy
php artisan local-https:caddy example.test --to=http://127.0.0.1:8000
```

## Run Your App Over HTTPS

After you run `local-https:setup`, the package updates your app to use the HTTPS domain in `.env`:

- `LOCAL_HTTPS_DOMAIN`
- `LOCAL_HTTPS_CERT`
- `LOCAL_HTTPS_KEY`
- `APP_URL=https://<your-domain>`

The package also adds the domain to your hosts file, so the domain resolves to `127.0.0.1` on your machine.

```bash
php artisan local-https:setup example.test
```

### Using `php artisan serve`

`php artisan serve` is fine for local development, but it serves HTTP only. It does not terminate TLS, so it cannot serve `https://example.test` by itself.

If you want to keep using `php artisan serve` while testing this package:

- Run `php artisan local-https:setup example.test`
- Start the app with `php artisan serve` or your existing Composer dev script
- Browse the serve URL directly, for example `http://127.0.0.1:8000`
- Use the generated HTTPS domain in `.env`, but do not expect `serve` itself to answer HTTPS requests

If `php artisan serve` fails to start or does not pick up the expected environment values, check your PHP configuration first. In some setups, `variables_order=EGPCS` can cause the serve command to behave differently than expected. You can override it for the current terminal session with:

```bash
php -d variables_order=GPCS artisan serve
```

If you want to browse the created host domain over HTTPS, you need an HTTPS-capable local server or reverse proxy, such as:

- Laravel Herd
- Laravel Valet
- Caddy, Nginx, Apache, or another local server configured with the generated certificate and key

### Using Caddy with `php artisan serve`

The Caddy workflow matches the gist-style setup:

1. Run `php artisan local-https:setup example.test`
2. Start Laravel’s HTTP server:

```bash
php artisan serve --host 127.0.0.1 --port 8000
```

3. In another terminal, start the Caddy proxy:

```bash
php artisan local-https:caddy example.test --to=http://127.0.0.1:8000
```

4. Open the created HTTPS domain in your browser:

```text
https://example.test
```

Caddy stops cleanly when the command exits, so `Ctrl+C` is enough to shut it down.

If you also run `npm run dev`, follow Laravel's Vite secure-development guidance for the same HTTPS origin so HMR and asset URLs line up with the browser domain. Depending on your setup, that usually means setting the secure host and Vite HTTPS/HMR options in `vite.config.js`.

When the package finds a standard `vite.config.js`, `local-https:setup` will try to add that HTTPS block automatically. If your Vite file is heavily customized, you may still prefer to add the block manually.

### Using Vite with the generated certificate

If you run Vite alongside Caddy, configure Vite to use the same HTTPS domain and the certificate files written by the package.

The package already writes these values into `.env`:

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

This keeps the browser origin, Laravel URL generation, and Vite HMR on the same secure host. If you do not use `npm run dev`, you do not need this Vite configuration.

### Using another local server with the generated certificate

If you configure Caddy, Nginx, Apache, or another local server to load the generated certificate and key, open the created domain in your browser:

- The package writes the domain to your hosts file so it resolves to `127.0.0.1`
- The package writes `APP_URL=https://<your-domain>` in `.env`
- The certificate and key paths are available from `php artisan local-https:status`

Example browser URL:

```text
https://example.test
```

## Installation

You can install the package via composer:

```bash
composer require maxiviper117/laravel-devcert
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-devcert-config"
```

## Configuration

The published `config/laravel-devcert.php` file controls where the package looks for certificates, where it writes hosts entries, and how it resolves the local domain.

```php
return [
    'default_tld' => '.test',
    'local_https_domain' => env('LOCAL_HTTPS_DOMAIN'),
    'certs_path' => null,
    'hosts_path' => null,
    'vite_config_path' => null,
    'include_ipv6' => true,
    'include_wildcard' => false,
];
```

What each option does:

- `default_tld`
  - The fallback suffix used when the package derives a domain from `APP_NAME`.
  - Default: `.test`
- `local_https_domain`
  - The last domain written by `local-https:setup`.
  - The package reads this from `.env` so `status` and follow-up commands report the same domain you set up.
- `certs_path`
  - Overrides where certificate folders are stored.
  - Leave it `null` to use the default user-local `.local-https/certs` directory.
- `hosts_path`
  - Overrides the hosts file path.
  - Leave it `null` to use the system hosts file for the current OS.
- `vite_config_path`
  - Overrides the Vite config file path that `local-https:setup` can patch automatically.
  - Leave it `null` to use the project root `vite.config.js`.
- `include_ipv6`
  - Adds the IPv6 loopback entry `::1 <domain>` when enabled.
  - Default: `true`
- `include_wildcard`
  - Tells `mkcert` to also generate a wildcard certificate for `*.example.test`.
  - Default: `false`

If you change any of these values, run `php artisan local-https:status` again to confirm the paths and domain match what you expect.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Maxiviper117](https://github.com/Maxiviper117)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

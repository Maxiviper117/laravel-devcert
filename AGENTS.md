# AGENTS.md

## Project Overview

- This repository is a Laravel package for local HTTPS setup with `mkcert`, certificate storage, and hosts file management.
- The package now also includes a Caddy reverse-proxy workflow for HTTPS local development.
- Keep changes aligned with the existing package namespace: `Maxiviper117\LaravelDevcert`.
- This codebase uses PHP 8.2+, Pest, Orchestra Testbench, Laravel Pint, and PHPStan.

## Working Rules

- Prefer small, focused changes that preserve the public artisan and facade API unless a change is explicitly requested.
- If you change command names, signatures, config keys, or facade behavior, update the README and tests in the same change.
- Keep implementations dependency-injected. For process execution, use the existing `ProcessRunner` abstraction instead of calling shell functions directly.
- Detect the runtime OS with `PHP_OS_FAMILY` before choosing paths or shell behavior, and keep Windows-specific handling separate from Linux/macOS handling.
- Use conventional commit messages for git commits, for example `feat: add local https command` or `chore: update docs`.
- Keep `AGENTS.md` up to date whenever significant repository changes land, especially new workflows, commands, conventions, or architecture shifts.
- Use ASCII by default.

## Common Commands

- `composer test` for the main test suite.
- `composer analyse` for PHPStan.
- `composer format` for formatting with Pint.
- `composer rector` for a Rector dry-run against `src` and `tests`.

## Local Development

- This package is tested from the repository root, not from a full Laravel app by default.
- If a `workbench/` app exists, use it as the preferred manual test target for package behavior before falling back to a separate consumer app.
- Prefer the bundled Caddy workflow when you need to test the browser-facing HTTPS flow locally.
- Before changing code, run the focused tests for the area you are editing when possible.
- After behavioral changes, run `composer test`; for higher confidence, also run `composer analyse` and `composer format`.
- For manual package testing, install `mkcert`, trust its CA, then run the artisan commands in a Laravel app that requires this package from a local path repository.
- For Caddy-based manual testing, start `php artisan serve` on `127.0.0.1`, then run `php artisan local-https:caddy <domain> --to=http://127.0.0.1:8000` in a second terminal.
- When validating Windows-specific behavior, test on Windows semantics explicitly; when validating path and shell behavior on Unix-like systems, keep the assumptions separate.
- Do not treat `AGENTS.md` as user-facing documentation; it is for development workflow and repository rules only.
- Keep the workbench Composer setup aligned with the root package when package name, branch, or local path changes.

## Package Commands

- `php artisan local-https:setup [domain] [--force] [--skip-vite]`
- `php artisan local-https:status`
- `php artisan local-https:hosts:scan`
- `php artisan local-https:hosts:add {domain}`
- `php artisan local-https:remove {domain}`
- `php artisan local-https:link-existing [domain]`
- `php artisan local-https:domain [domain]`
- `php artisan local-https:cert:generate {domain} [--force]`
- `php artisan local-https:caddy [domain] [--to=...]`

## Testing Expectations

- Add or update Pest tests for behavior changes in `src/Actions`, `src/Services`, and command classes.
- Prefer focused unit tests for pure logic and Testbench-backed tests for package integration points.
- When changing hosts, certificate, or environment file behavior, verify the affected edge cases explicitly.

## Documentation Expectations

- Keep `README.md` in sync with any public-facing change.
- Document new config keys in `config/laravel-devcert.php` and the README example together.
- Keep `LOCAL_TESTING.md` in sync with any manual testing workflow changes or package-run instructions.

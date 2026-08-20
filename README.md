# package-boost-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sandermuller/package-boost-php.svg?style=flat-square)](https://packagist.org/packages/sandermuller/package-boost-php)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sandermuller/package-boost-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sandermuller/package-boost-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/sandermuller/package-boost-php.svg?style=flat-square)](https://packagist.org/packages/sandermuller/package-boost-php)
[![License](https://img.shields.io/packagist/l/sandermuller/package-boost-php.svg?style=flat-square)](LICENSE)
[![Laravel Boost](https://badge.laravel.cloud/boost-badge.svg?style=flat-square)](https://github.com/laravel/boost)

AI agent skills, guidelines, and `.gitattributes` commands for framework-agnostic Composer package authors. Sibling of [`sandermuller/package-boost-laravel`](https://github.com/sandermuller/package-boost-laravel) (Laravel-package flavor); both ride the [`sandermuller/boost-core`](https://github.com/sandermuller/boost-core) sync engine.

**Documentation: <https://sandermuller.github.io/boost-core/packages/package-boost-php/>**

![overview image](overview.png)

> Where [`laravel/boost`](https://github.com/laravel/boost) ships Laravel
> application guidelines, this package ships package-author CLI infrastructure
> and skill-authoring tooling. Framework-agnostic, no Laravel dependency. Not
> sure which family member fits? The
> [picker](https://sandermuller.github.io/boost-core/guide/which-package) decides
> it in two questions.

## What you get

**Two CLI commands.** Both target `.gitattributes`, the file that controls what
ends up in the Composer archive. Neither overlaps with `laravel/boost`.

| Command | Purpose |
|---|---|
| `vendor/bin/package-boost-php lean` | Check that `.gitattributes` excludes non-shipping paths (tests, fixtures, CI configs, `.ai/`). Wraps `stolt/lean-package-validator` |
| `vendor/bin/package-boost-php gitattributes` | Maintain the `# >>> package-boost (managed) >>>` block. Foreign lines added by other tools are preserved |

**Two guidelines.**

| Guideline | Scope | Tag |
|---|---|---|
| `foundation` | Package-is-not-an-app rules: no `app/` or `.env`, the public API is semver-governed, tests are the spec | — |
| `release-automation` | CHANGELOG-via-CI and release-notes-in-`internal/` conventions | `release-automation` |

**Three skills.**

| Skill | When it loads | Tag |
|---|---|---|
| `lean-dist` | Keeping the Composer archive lean with `.gitattributes` export-ignore | — |
| `skill-authoring` | Authoring or editing AI skills for the boost family | `boost-extension` |
| `writing-file-emitter` | Implementing a custom `FileEmitter` for boost-core | `boost-extension` |

The `readme`, `release-notes`, and `upgrading` skills ship from [`sandermuller/boost-skills`](https://github.com/sandermuller/boost-skills) under the `release-automation` tag. See [UPGRADING](UPGRADING.md) for that migration.

## Install

```bash
composer require --dev sandermuller/package-boost-php
vendor/bin/boost install                     # pick agents and allowlist vendors
vendor/bin/boost sync                        # fan skills + guidelines out
vendor/bin/package-boost-php gitattributes   # write the managed block
vendor/bin/package-boost-php lean            # confirm the archive is lean
```

PHP 8.3+. `sandermuller/boost-core` and `stolt/lean-package-validator` come in transitively — do not require them separately. The auto-sync callback lives under this package's own namespace, so your `composer.json` never names the engine.

The minimum `boost.php` is one agent plus this package in the allowlist:

```php
return BoostConfig::configure()
    ->withAgents([Agent::CLAUDE_CODE])
    ->withAllowedVendors(['sandermuller/package-boost-php']);
```

Two opt-in tags: `release-automation` and `boost-extension`. Neither ships until you declare it.

## Documentation

| Topic | Page |
|---|---|
| What this package ships | [Overview](https://sandermuller.github.io/boost-core/packages/package-boost-php/) |
| Install and first run | [Install](https://sandermuller.github.io/boost-core/packages/package-boost-php/install) |
| `boost.php`, the opt-in tags, auto-sync, coexistence | [Configuration](https://sandermuller.github.io/boost-core/packages/package-boost-php/configuration) |
| Publishing your own skill package | [Publishing a skill package](https://sandermuller.github.io/boost-core/packages/boost-core/publishing-skills) |
| Tags, skill dependencies, remote skills, conventions | [Guide](https://sandermuller.github.io/boost-core/guide/what-is-boost) |
| Every command and exit code | [CLI reference](https://sandermuller.github.io/boost-core/reference/cli) |

The semver-protected surface — the `AutoSync` Composer-hook façade, the `bin/package-boost-php` CLI contract, and the managed-block marker format — is in [PUBLIC_API.md](PUBLIC_API.md). Everything else is `@internal`.

## Testing

```bash
composer test       # Pest suite
composer qa         # Rector + Pint + PHPStan + .gitattributes validator
```

## License

MIT. See [LICENSE](LICENSE).

# package-boost-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sandermuller/package-boost-php.svg?style=flat-square)](https://packagist.org/packages/sandermuller/package-boost-php)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sandermuller/package-boost-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sandermuller/package-boost-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/sandermuller/package-boost-php.svg?style=flat-square)](https://packagist.org/packages/sandermuller/package-boost-php)
[![License](https://img.shields.io/packagist/l/sandermuller/package-boost-php.svg?style=flat-square)](LICENSE)

> AI agent skills for framework-agnostic Composer package authors. Ships two guidelines (`foundation` — package-not-an-app rules; `release-automation` — CHANGELOG + release-notes conventions, opt-in) and six package-author skills (`readme`, `release-notes`, `upgrading`, `lean-dist`, `skill-authoring`, `writing-file-emitter`), plus two commands: `package-boost-php:lean` (validates `.gitattributes` excludes non-shipping paths) and `package-boost-php:gitattributes` (maintains the `# >>> package-boost (managed) >>>` block, preserving foreign lines added by other tools).

## Install

```bash
composer require --dev sandermuller/package-boost-php
```

## Usage

```bash
vendor/bin/boost install   # interactive picker: agents + vendor allowlist (auto-generates boost.php on first run)
vendor/bin/boost sync      # fan out skills + guidelines to selected agents

composer package-boost-php:lean            # validate .gitattributes
composer package-boost-php:gitattributes   # sync the managed block
```

Opt-in via `boost.php` `withTags()`: the `skill-authoring` + `writing-file-emitter` skills require `'boost-extension'`; the `release-automation` guideline requires `'release-automation'`. The other four skills and the `foundation` guideline ship to every consumer.

Generated agent dirs are added to `.gitignore` automatically — edit `.ai/` only, then run `vendor/bin/boost sync`. To re-sync on every `composer install`, wire `SanderMuller\BoostCore\Scripts\BoostAutoSync::run` into your project's `post-install-cmd` / `post-update-cmd`; `BOOST_SKIP_AUTOSYNC=1` disables it.

## License

MIT. See [LICENSE](LICENSE).

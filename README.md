# package-boost-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sandermuller/package-boost-php.svg?style=flat-square)](https://packagist.org/packages/sandermuller/package-boost-php)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sandermuller/package-boost-php/ci.yml?branch=main&label=tests&style=flat-square)](https://github.com/sandermuller/package-boost-php/actions/workflows/ci.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/sandermuller/package-boost-php.svg?style=flat-square)](https://packagist.org/packages/sandermuller/package-boost-php)
[![License](https://img.shields.io/packagist/l/sandermuller/package-boost-php.svg?style=flat-square)](LICENSE)

> AI agent skills for framework-agnostic Composer package authors. Ships five package-author skills (`readme`, `release-notes`, `upgrading`, `lean-dist`, `skill-authoring`) plus two commands: `package-boost-php:lean` (validates `.gitattributes` excludes non-shipping paths) and `package-boost-php:gitattributes` (maintains the `# >>> package-boost (managed) >>>` block, preserving foreign lines added by other tools).

## Install

Not yet on Packagist. While you wait, install via a vcs repository:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/sandermuller/package-boost-php" }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

```bash
composer require --dev sandermuller/package-boost-php
```

## Usage

```bash
composer boost:init      # generate boost.php starter (from boost-core)
composer boost:install   # interactive picker: agents + vendor allowlist
vendor/bin/boost sync      # fan out skills + guidelines to selected agents

composer package-boost-php:lean            # validate .gitattributes
composer package-boost-php:gitattributes   # sync the managed block
```

Generated agent dirs are added to `.gitignore` automatically and regenerated on every `composer install` — edit `.ai/` only. Set `BOOST_SKIP_AUTOSYNC=1` to disable.

## License

MIT. See [LICENSE](LICENSE).

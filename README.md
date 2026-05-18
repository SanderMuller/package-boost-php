# package-boost-php

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
composer boost:sync      # fan out skills + guidelines to selected agents

composer package-boost-php:lean            # validate .gitattributes
composer package-boost-php:gitattributes   # sync the managed block
```

## License

MIT. See [LICENSE](LICENSE).

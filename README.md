# package-boost-php

> AI agent skills for framework-agnostic Composer package authors.

Depends on [`sandermuller/boost-core`](https://github.com/sandermuller/boost-core) for the actual sync mechanism. This package ships:

- 5 skills targeting Composer package authoring: `readme`, `release-notes`, `upgrading`, `lean-dist`, `skill-authoring`
- `package-boost-php:lean` — validates `.gitattributes` excludes non-shipping paths from the published archive
- `package-boost-php:gitattributes` — maintains the `# >>> package-boost (managed) >>>` block in `.gitattributes` (preserves foreign lines added by other tools like [`repo-init`](https://github.com/sandermuller/repo-init))

## Status

**Under construction.** `boost-core` is not yet on Packagist — this package currently resolves it via a path repository pointing at `../boost-core/`. Will switch to Packagist constraint once `boost-core` ships.

## Installation

Coming soon:

```bash
composer require --dev sandermuller/package-boost-php
composer boost:init
composer boost:install   # interactive picker for agents + allowlist
composer boost:sync      # fan out to selected agents
```

## License

MIT. See [LICENSE](LICENSE).

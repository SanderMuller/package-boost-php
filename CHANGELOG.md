# Changelog

All notable changes to `sandermuller/package-boost-php` will be documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Bumped `sandermuller/boost-core` constraint to `^0.3.0`. 0.3.0 removes `composer boost:init` — auto-rolled into `composer boost:install` first-run path. Plugin code paths untouched.
- README: replaced `composer boost:init` reference with the unified `composer boost:install` flow.

## [0.2.0] - 2026-05-18

### Fixed

- Composer plugin capability error in consumers: `PackageBoostPhpCommandProvider` now wraps `LeanCommand` and `GitattributesCommand` in `SanderMuller\BoostCore\Commands\BaseCommandAdapter` so they satisfy Composer's `BaseCommand` contract. Before this fix, every `composer` invocation in any project that required `package-boost-php` printed an "invalid value" capability error.

### Changed

- Bumped `sandermuller/boost-core` constraint to `^0.2.0` (was `^0.1.0` on 0.1.x; a stray `^1.0@dev` on `main` is reverted here). 0.2.0 ships the `BaseCommandAdapter` required by the fix above.
- Renamed `phpunit.xml.dist` → `phpunit.xml` to match the repo-init 0.2.4+ canonical baseline.

## [0.1.x]

### Added

- Initial scaffolding via the canonical sandermuller setup (PHP 8.2+, Pint, PHPStan strict, Pest).
- 5 framework-agnostic package-author skills: `readme`, `release-notes`, `upgrading`, `lean-dist`, `skill-authoring`.
- `package-boost-php:lean` Composer command — validates `.gitattributes` via `stolt/lean-package-validator`.
- `package-boost-php:gitattributes` Composer command — maintains the `# >>> package-boost (managed) >>>` block, preserving foreign lines per the repo-init contract.
- Depends on `sandermuller/boost-core` (currently resolved via path repository — switches to Packagist constraint once boost-core is published).

[Unreleased]: https://github.com/sandermuller/package-boost-php/compare/0.2.0...HEAD
[0.2.0]: https://github.com/sandermuller/package-boost-php/compare/0.1.1...0.2.0

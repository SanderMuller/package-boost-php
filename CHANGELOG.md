# Changelog

All notable changes to `sandermuller/package-boost-php` will be documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/sandermuller/package-boost-php/compare/0.3.0...HEAD)

## [0.3.0]

### Added

- `boost.php` tracked at project root: configures boost-core's agent fan-out + vendor allowlist for `vendor/bin/boost sync`.
- Composer `post-install-cmd` / `post-update-cmd` hooks wired to `SanderMuller\BoostCore\Scripts\BoostAutoSync::run` (boost-core 0.3.1+). Cross-platform replacement for the prior bash one-liner — auto-syncs `.ai/` to per-agent dirs on every `composer install` / `composer update` in dev mode.
- `validate-gitattributes` Composer script + `.lpv` config so `lean-package-validator` runs as part of `composer ci`.
- Canonical config files: `.editorconfig`, `pint.json`, `.github/dependabot.yml`.
- Shared GitHub workflows: `phpstan.yml`, `pint-check.yml`, `run-tests.yml` (PHP 8.3 prefer-lowest + 8.3/8.4 prefer-stable matrix), `update-changelog.yml`.

### Changed

- Bumped `sandermuller/boost-core` constraint to `^0.3` (resolves to 0.3.1, picks up future 0.3.x patches).
- Bumped `extra.branch-alias.dev-main` to `0.3.x-dev` to align with the boost-core ecosystem's 0.3.x stream (per family-wide version-stream policy: stay on 0.x until 0.4 absorbs manifest cleanup; 1.0 reserved for FileEmitter-stable).
- README: replaced `composer boost:init` reference with the unified `composer boost:install` flow (0.3.0 auto-rolls init into install's first-run path).
- Replaced monolithic `ci.yml` workflow with the canonical per-tool split (phpstan / pint / tests / changelog).
- Dropped redundant `extra.boost.skills` / `extra.boost.guidelines` entries — they restated boost-core's `VendorScanner` defaults.
- `.ai/skills/ai-guidelines` and `.ai/skills/pre-release` rewritten to reflect boost-core (Composer plugin) reality — swapped stale `vendor/bin/testbench` references for `vendor/bin/boost sync`, noted gitignored per-agent dirs, mentioned the auto-sync hook.

### Important

- **0.1.0 and 0.1.1 are broken — use 0.2.0+.** Those tags shipped `PackageBoostPhpCommandProvider::getCommands()` returning plain Symfony commands without `BaseCommandAdapter` wrapping. Any consumer pinning 0.1.0 / 0.1.1 hits a `"Plugin capability returned an invalid value"` Composer error on every invocation. 0.2.0 wired the `BoostCore\BaseCommandAdapter` wrap and is the lowest correctly-functioning tag.

## [0.3.0](https://github.com/sandermuller/package-boost-php/compare/0.2.0...0.3.0) - 2026-05-18

### Fixed

- Cross-platform Composer `post-install-cmd` / `post-update-cmd` hook. Replaced the bash `if [ … ] then vendor/bin/boost sync … fi` one-liner with `SanderMuller\BoostCore\Scripts\BoostAutoSync::run` (boost-core 0.3.1+). The bash form broke on Windows `cmd.exe`; the PHP callback works everywhere Composer does.

### Changed

- Bumped `sandermuller/boost-core` constraint to `^0.3` — resolves to 0.3.1 today, picks up future 0.3.x patches.
- Bumped `extra.branch-alias.dev-main` to `0.3.x-dev` (was `0.x-dev`) to align with the boost-core 0.3.x version stream.
- README: `composer boost:init` reference dropped (auto-rolled into `composer boost:install` in boost-core 0.3.0). Tests badge URL fixed (was pointing at the deleted `ci.yml`).
- Split monolithic `ci.yml` into canonical per-tool workflows (`phpstan`, `pint-check`, `run-tests`, `update-changelog`). Adds a `prefer-lowest` matrix lane to catch accidental floor-tightening.
- Dropped redundant `extra.boost.skills` / `extra.boost.guidelines` entries — they restated boost-core's `VendorScanner` defaults.

### Added

- `boost.php` tracked at project root: configures agent fan-out + vendor allowlist for `vendor/bin/boost sync`. Fresh clones + CI now reproduce the same boost-core config.
- `.lpv` config + `validate-gitattributes` Composer script, wired into `composer ci`.
- Canonical config files: `.editorconfig`, `pint.json`, `.github/dependabot.yml`.

### Important

**0.1.0 and 0.1.1 are broken — use 0.2.0+.** Those tags shipped `PackageBoostPhpCommandProvider::getCommands()` returning plain Symfony commands without `BaseCommandAdapter` wrapping, causing a `"Plugin capability returned an invalid value"` Composer error in every consumer. 0.2.0 wired the adapter and is the lowest correctly-functioning tag.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.2.0...0.3.0

## [0.2.0](https://github.com/sandermuller/package-boost-php/compare/0.1.1...0.2.0) - 2026-05-18

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

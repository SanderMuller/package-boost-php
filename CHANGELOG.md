# Changelog

All notable changes to `sandermuller/package-boost-php` will be documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/sandermuller/package-boost-php/compare/0.4.0...HEAD)

## [0.4.0](https://github.com/sandermuller/package-boost-php/compare/0.3.1...0.4.0) - 2026-05-20

### boost-core 0.4 family alignment

Tracks the boost-core 0.4.x version stream. No breaking changes for package-boost-php consumers — boost-core 0.4.0's breaking change (vendor-namespaced user-scope skill dirs) is scoped to user-scope sync; package-boost-php uses project-scope sync via the `BoostAutoSync` post-install hook.

#### Changed

- Bumped `sandermuller/boost-core` constraint to `^0.4`. boost-core 0.4.0 vendor-namespaces user-scope skill directories (`~/.{agent}/skills/<vendor>__<package>/`); the migration is automatic on first sync. package-boost-php has no direct `SyncEngine` callers and no hard-coded user-scope skill paths, so the bump is transparent.
- Bumped `extra.branch-alias.dev-main` to `0.4.x-dev`.
- Composer `post-install-cmd` / `post-update-cmd` hooks switched to `BoostAutoSync::runWithSummary` — emits boost-core's one-line sync summary through Composer IO instead of running silently.
- `phpstan` CI workflow now also triggers on `composer.json` / `composer.lock` changes, so a dependency bump that shifts phpstan's verdict re-runs static analysis on the bump commit.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.3.1...0.4.0

## [0.3.1](https://github.com/sandermuller/package-boost-php/compare/0.3.0...0.3.1) - 2026-05-18

### Changed

- Bumped `sandermuller/boost-core` constraint floor to `^0.3.2`. 0.3.2 adds `BOOST_SKIP_AUTOSYNC` env-var support to `BoostAutoSync::run` — the script callback our `post-install-cmd` / `post-update-cmd` hooks invoke.
- README: restored the `Set BOOST_SKIP_AUTOSYNC=1 to disable` line. It was dropped in 0.3.0 because our hook didn't honor the env var; 0.3.2 fixes that upstream, so the documented disable knob is accurate again.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.3.0...0.3.1

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

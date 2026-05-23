# Changelog

All notable changes to `sandermuller/package-boost-php` will be documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/sandermuller/package-boost-php/compare/0.8.0...HEAD)

## [0.8.0](https://github.com/sandermuller/package-boost-php/compare/0.7.0...0.8.0) - 2026-05-23

### Added

- **`resources/boost/guidelines/release-automation.md`** — pins the release-flow conventions consumers want always-loaded when they follow this workflow: `CHANGELOG.md` is prepended by CI (`update-changelog.yml`), release notes are drafted in `internal/release-notes-<version>.md` (gitignored), the first line of that file pins a verified-green commit SHA, tags are bare (`0.8.0`) while release titles take the `v` prefix (`v0.8.0`), and agents stop at the ready-to-tag handoff. The procedural detail lives in the `release-notes` and `pre-release` skills — this guideline only pins the conventions those skills lean on.
  
- **`resources/boost/guidelines/.boost-tags.yaml`** — sidecar manifest gating `release-automation.md` behind the `release-automation` tag (boost-core 0.6's mechanism for tagging frontmatter-free guidelines). Consumers who follow this release workflow declare it in `boost.php`:
  
  ```php
  ->withTags('release-automation')
  
  ```
  Consumers who don't get nothing — the guideline never enters their CLAUDE.md / AGENTS.md. The `foundation` guideline stays untagged and always ships.
  

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.7.0...0.8.0

## [0.7.0](https://github.com/sandermuller/package-boost-php/compare/0.6.0...0.7.0) - 2026-05-22

Adopts `sandermuller/boost-core ^0.6`. boost-core 0.6.0 retired its Composer plugin, so the `composer boost:*` subcommands move to the standalone `vendor/bin/boost` binary and auto-sync on `composer install` becomes an opt-in callback you wire into your own project. package-boost-php itself remains a Composer plugin — its `package-boost-php:lean` and `package-boost-php:gitattributes` commands are unaffected.

Also widens `symfony/console` and `symfony/process` to permit symfony 8 (Laravel 13).

See [UPGRADING.md](UPGRADING.md) for the full 0.6 → 0.7 migration.

### Breaking changes

- **Requires `sandermuller/boost-core ^0.6`.** boost-core 0.6.0 retired its Composer plugin:
  - The `composer boost:*` subcommands are gone — run them through the standalone binary instead (the `boost:` prefix is dropped):
    
    | Was | Now |
    |---|---|
    | `composer boost:install` | `vendor/bin/boost install` |
    | `composer boost:sync` | `vendor/bin/boost sync` |
    | `composer boost:doctor` | `vendor/bin/boost doctor` |
    | `composer boost:tags` | `vendor/bin/boost tags` |
    
  - **Auto-sync on `composer install` is no longer automatic.** To keep `composer install` re-syncing, wire the callback into *your own project's* `composer.json`:
    
    ```json
    "scripts": {
        "post-install-cmd": ["SanderMuller\\BoostCore\\Scripts\\BoostAutoSync::run"],
        "post-update-cmd": ["SanderMuller\\BoostCore\\Scripts\\BoostAutoSync::run"]
    }
    
    
    ```
    A dependency's `post-install-cmd` does not fire in a consuming project — only the root package's scripts run — so this must live in your `composer.json`. Otherwise, run `vendor/bin/boost sync` yourself (e.g. in CI). `BOOST_SKIP_AUTOSYNC=1` disables the callback.
    
  - Optionally drop the now-dead `sandermuller/boost-core` entry from your `config.allow-plugins` — boost-core is no longer a plugin. Composer ignores the stale entry, so leaving it is harmless.
    
  

### Changed

- Widened `symfony/console` and `symfony/process` to `^7.0||^8.0`. `^7.0` alone excluded symfony 8 (Laravel 13); the widen unblocks installation on those projects. package-boost-laravel inherits the cap transitively, so the widen lifts it for the whole package-boost line.

### Fixed

- **0.6.0's release notes / README had `->withTags(['boost-extension'])`** — that array form passes an array into a variadic `Tag|string ...$tags` parameter and fatals with a TypeError on `boost.php` load. The correct call is variadic: `->withTags('boost-extension')`. Both files are corrected in this release; if you copied the array form from 0.6.0's notes into your `boost.php`, switch it.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.6.0...0.7.0

## [0.6.0](https://github.com/sandermuller/package-boost-php/compare/0.5.0...0.6.0) - 2026-05-22

### Breaking changes

- **`skill-authoring` and `writing-file-emitter` are now tagged `boost-extension`.** boost-core's tag filter ships a tagged skill only when the skill's tags are a subset of the consumer's `boost.php` `withTags()` declaration. These two skills therefore no longer sync to a project that has not declared the tag. A project that wants them must add it to `boost.php`:
  
  ```php
  ->withTags('boost-extension')
  
  
  ```
  The other four skills — `readme`, `release-notes`, `upgrading`, `lean-dist` — stay untagged and continue shipping to every consumer, unchanged. Rationale: the two extension skills are only actionable for packages that themselves ship boost-core skills or `FileEmitter`s; tagging lets every other package author opt out of guidance they can't use. Run `composer boost:tags` to see the tags declared by installed skills.
  

### Added

- **`resources/boost/guidelines/foundation.md`** — the Package Boost foundation guideline. Framework-agnostic package-authoring guidance: the package-is-not-an-application framing, source layout, tests-as-specification, public API discipline, and conventions. Restored from the retired `package-boost` package, whose guidance reached no boost consumer after the migration to the split family. Auto-discovered by boost-core's guideline scanner — no consumer action required.

### Changed

- The `skill-authoring` skill now documents the `metadata.boost-tags` field — frontmatter shape, the subset-match rule, and the caveat that tagging an already-shipped skill is a breaking change for the package.
- The PHPUnit cache directory is now `.cache/phpunit`, alongside the existing `.cache/phpstan` and `.cache/pint`. Dev-only; no consumer impact.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.5.0...0.6.0

## [0.5.0](https://github.com/sandermuller/package-boost-php/compare/0.4.0...0.5.0) - 2026-05-21

### Added

- New shipped skill `writing-file-emitter` — guides Composer package authors through implementing a boost-core `FileEmitter` to emit a custom file (e.g. `.mcp.json`, `.editorconfig`) into the host project during `boost:sync`. Sixth package-author skill, alongside `readme`, `release-notes`, `upgrading`, `lean-dist`, and `skill-authoring`. `package-boost-laravel` consumers receive it transitively.

### Changed

- Bumped `sandermuller/boost-core` constraint to `^0.5`. boost-core 0.5.0 adds tag-based conditional skill filtering — `metadata.boost-tags` in `SKILL.md`, `withTags()` / `withExcludedSkills()` in `boost.php`, and a `boost:doctor` tag report. A caret on `0.x` stops at the minor, so `^0.4` could not resolve `0.5.x` and consumers were held below it; `^0.5` covers the whole 0.5.x line — 0.5.0 today and the upcoming additive 0.5.1.

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

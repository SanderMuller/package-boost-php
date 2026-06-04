# Changelog

All notable changes to `sandermuller/package-boost-php` will be documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/sandermuller/package-boost-php/compare/0.19.0...HEAD)

## [0.19.0](https://github.com/sandermuller/package-boost-php/compare/0.18.1...0.19.0) - 2026-06-04

<!-- verified-sha: cfd1896ce6e836ed9eaa1081500b656ae33c5e8a -->
### Added

- **`PUBLIC_API.md`** — declares the package's semver-protected surface
  ahead of the eventual 1.0 freeze. The stable surface is the `AutoSync`
  composer-hook façade (`run` / `runWithSummary`), the
  `bin/package-boost-php` CLI contract (the `gitattributes` / `lean`
  command names, their `--check` / `--working-dir` options, and the
  `0` ok / `1` failure exit codes), and the `# >>> package-boost (managed) >>>`
  managed-block marker format. Everything else is `@internal`.

### Changed

- **Surface markers added in source:** `@api` on `AutoSync`; `@internal`
  on `ManagedBlockWriter` and the two CLI command classes. These document
  intent — the CLI invocation and the marker format are the contract, not
  the PHP classes. No behavior change.

Non-breaking — documentation and PHPDoc annotations only. Prepares the
package to tag 1.0.0 in lockstep with boost-core's 1.0 freeze (at which
point the surface above locks for the 1.x line).

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.18.1...0.19.0

## [0.18.1](https://github.com/sandermuller/package-boost-php/compare/0.18.0...0.18.1) - 2026-06-03

<!-- verified-sha: 8c81c32ceed014bc38f6fe9738ad7c616349c730 -->
### Changed

- **Widened the `sandermuller/boost-core` constraint** from `^0.20` to
  `^0.20 || ^0.21 || ^0.22`. boost-core 0.21 and 0.22 are additive over
  0.20 for this package's surface — the `BoostAutoSync` composer hooks, the
  two `BoostBaseCommand`-derived CLI commands, and the `boost.php`
  authoring API are unchanged (and frozen `@api` as of boost-core 0.22).
  Verified on 0.22.0: config loads, `boost doctor` clean, both CLI commands
  succeed, suite green; `--prefer-lowest` still resolves boost-core 0.20.0.

Non-breaking — a constraint widen only. Consumers already on boost-core
0.20 need no change; those on 0.21/0.22 can now install this package.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.18.0...0.18.1

## [0.18.0](https://github.com/sandermuller/package-boost-php/compare/0.17.0...0.18.0) - 2026-06-03

<!-- verified-sha: 7b15cd02bbb30285c773efed5af9222cb42c2efc -->
### Breaking — `sandermuller/boost-core` floor raised to `^0.20`

The `sandermuller/boost-core` constraint narrows from `^0.18 || ^0.19`
to `^0.20`, dropping support for boost-core 0.18 and 0.19.

boost-core 0.20 makes the `BoostConfig` builder methods array-typed. The
`withTags()` variadic form fatals on 0.20, and the new array form fatals
on 0.18/0.19 — the two styles cannot be expressed compatibly in a single
`boost.php`, so the floor moves to 0.20 in lockstep.

If your `composer.json` requires boost-core below `^0.20`, bump it:

```bash
composer require sandermuller/boost-core:^0.20



```
#### `withTags()` is now array-typed

Update your `boost.php` (or `.config/boost.php`) to pass an array:

```diff
-    ->withTags(
-        Tag::Php,
-        'release-automation',
-    );
+    ->withTags([
+        Tag::Php,
+        'release-automation',
+    ]);



```
Only `withTags()` callers using the variadic form need the change —
`withAgents()`, `withAllowedVendors()`, `withExcludedSkills()`,
`withRemoteSkills()`, and `withConventions()` already took arrays. See
[UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md)
for the full note.

### Changed

- **`minimum-stability` set to `stable`** (was `dev`), with
  `prefer-stable: true` unchanged. No dependency requires dev stability.

No source/API changes — the package's public surface is unchanged. This
release is constraint and configuration only.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.17.0...0.18.0

## [0.17.0](https://github.com/sandermuller/package-boost-php/compare/0.16.3...0.17.0) - 2026-06-02

<!-- verified-sha: ecfa2812bc3d9b81f97bae2640f6e4820f182f3e -->
### Breaking — `sandermuller/boost-core` floor raised to `^0.18`

The `sandermuller/boost-core` constraint narrows from
`^0.16 || ^0.17 || ^0.18` to `^0.18 || ^0.19`, dropping support for
boost-core 0.16 and 0.17.

This package now uses boost-core's `.config/` layout — config at
`.config/boost.php`, sync manifest under `.config/boost/`. Config-path
resolution arrived in boost-core 0.17, but the manifest relocation from
root `.boost/` to `.config/boost/` only landed in 0.18, so 0.18 is the
floor where the `.config/` layout is fully consistent.

If your `composer.json` requires boost-core below `^0.18`, bump it:

```bash
composer require sandermuller/boost-core:^0.18




```
Already on `boost-core ^0.18` or later? No action needed. See
[UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md)
for the full migration note.

### Changed

- **Dogfood config moved to `.config/boost.php`.** This repo's own boost
  config now lives under `.config/` instead of the repo root, demonstrating
  the layout boost-core 0.18+ supports. boost-core resolves either location
  (root `boost.php` or `.config/boost.php`) — but not both at once, or it
  fails loud. Consumers may relocate their own config the same way; a root
  `boost.php` keeps working unchanged.
- **`stolt/lean-package-validator` widened to `^5.7 || ^6.0`**, allowing
  consumers to resolve the 6.x line.

No source/API changes — the package's public surface is unchanged. This
release is constraint and configuration-layout only.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.16.3...0.17.0

## [0.16.3](https://github.com/sandermuller/package-boost-php/compare/0.16.2...0.16.3) - 2026-06-02

<!-- verified-sha: 61a0575942e3f05a23bda89a0169ff96747c561d -->
### 0.16.3

A dependency-constraint widen. No code, API, or behavior change in this package.

#### Changed

- **`sandermuller/boost-core` constraint widened to `^0.16 || ^0.17 || ^0.18`.** Both new boost-core minors are additive and backward-safe — 0.17 adds the `.config/` config layout, 0.18 adds sync/doctor/validate observability and groups runtime state under `.config/boost/`. Neither touches an API this package depends on, so the widen is non-breaking and ships as a patch. Consumers can now resolve boost-core 0.17 or 0.18 alongside package-boost-php without a floor move.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.16.2...0.16.3

## [0.16.2](https://github.com/sandermuller/package-boost-php/compare/0.16.1...0.16.2) - 2026-05-31

<!-- verified-sha: 637f6a494c1f76bb7f5e8dbc178aa045492ba1c9 -->
### Fixed — `.gitattributes` managed-block writer

Edge-case correctness fixes to the block the `gitattributes` command maintains:

- **Malformed-block recovery is now self-healing.** A block with an opening marker but no closing marker previously caused the next sync to *append a second block*, leaving successive syncs unstable. The writer now collapses the region into a single clean block, and `sync()` is idempotent for any input (`sync(sync(x)) === sync(x)`).
- **The file's dominant line ending is preserved.** A CRLF-authored `.gitattributes` stays CRLF instead of being rewritten to LF; a mostly-LF file with a stray CRLF line settles to LF rather than churning every line.
- **Whitespace-variant canonical rules are recognised, not duplicated.** A managed export-ignore entry written with different padding is treated as managed instead of being kept (and re-emitted) as a "foreign" line.
- **Repeated foreign lines inside the block are de-duplicated.**

### Added

- **Test coverage for the `lean` and `gitattributes` CLI commands** — exit codes, `--check` no-write behaviour, foreign-line preservation end-to-end, and validator-binary resolution are now pinned. The test suite grows from 8 to 21.

### Changed

- **`lean-dist` skill** now points at the live managed block and `.lpv` as the source of truth for export-ignore entries, replacing a hard-coded list that had drifted from what the tool actually writes.
- **README**: removed stale version pins (version-agnostic restatements).
- **Internal**: removed an unreachable validator-binary fallback in the `lean` command.

### Breaking — `sandermuller/boost-core` narrowed to `^0.16`

> **Correction (post-release):** this entry originally read "patch release — non-breaking". That was inaccurate. 0.16.2 also narrowed the `sandermuller/boost-core` constraint from `^0.13 || ^0.14 || ^0.15 || ^0.16` to `^0.16`, dropping support for boost-core 0.13–0.15 — a **breaking** change that warranted a `0.17.0` bump, not a patch. The family had already converged on boost-core `^0.16`, so no current consumer is affected; a consumer pinned to `boost-core < 0.16` must upgrade it to `^0.16` for this version. See [UPGRADING](UPGRADING.md) (0.16.1 → 0.16.2).

The managed-block writer correctness fixes and CLI command-test coverage above are themselves non-breaking.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.16.1...0.16.2

## [0.16.1](https://github.com/sandermuller/package-boost-php/compare/0.16.0...0.16.1) - 2026-05-31

<!-- verified-sha: f0d24452eda7357969704de151c163d958444370 -->
### `sandermuller/boost-core` widened to accept `^0.16`

0.16.1 widens the boost-core constraint from `^0.13 || ^0.14 || ^0.15` to `^0.13 || ^0.14 || ^0.15 || ^0.16` — a non-breaking "track latest" bump that absorbs boost-core 0.16.0 without dropping support for 0.13–0.15.

boost-core 0.16.0 adds conventions-token leak detection (`boost sync` / `boost doctor` / `boost validate --strict` surface an unresolved `<!--boost:conv-->` token that would otherwise land verbatim in an emitted agent file). It is **non-load-bearing for `package-boost-php`** — this package ships zero convention tokens and declares no `withConventions([...])`, so the detector has nothing to scan here. Dogfood output is byte-identical to boost-core 0.15.

No code, public-API, or skill changes. If you stay on `boost-core ^0.13`–`^0.15`, no action is required.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.16.0...0.16.1

## [0.16.0](https://github.com/sandermuller/package-boost-php/compare/0.15.2...0.16.0) - 2026-05-31

<!-- verified-sha: bf4cb0f359a96f2907cb1230d12383d2117583ea -->
### One-package install: the `AutoSync` façade

A consumer requiring only `sandermuller/package-boost-php` already gets boost-core transitively — but the recommended auto-sync wiring referenced boost-core's `SanderMuller\BoostCore\Scripts\BoostAutoSync::run`, a transitive-dependency class. "Declare what you use" hygiene then nudged consumers to `require sandermuller/boost-core` explicitly, making a one-package install feel like two.

0.16.0 closes that seam.

#### Added

- **`SanderMuller\PackageBoostPhp\Scripts\AutoSync`** — a thin façade over boost-core's `BoostAutoSync`, exposing both Composer-callback delegates under this package's own namespace:
  
  - `AutoSync::run` — `post-install-cmd` / `post-update-cmd` hook; silent on a no-op install.
  - `AutoSync::runWithSummary` — for user-invoked scripts (e.g. `composer sync-ai`) that should always print the one-line summary.
  
  Delegation is total: every guard (`--no-dev` short-circuit, `BOOST_SKIP_AUTOSYNC`, the binary-not-executable skip, the failure warning) lives in boost-core and fires unchanged through the façade. A consumer now wires auto-sync while referencing only `package-boost-php`, and never needs to name `sandermuller/boost-core` in their `composer.json`.
  

#### Changed

- README Install + Auto-sync sections document the one-package story and the façade wiring (`run` / `runWithSummary`).

#### Upgrade

Non-breaking. The previous `BoostAutoSync::run` wiring still resolves transitively. The optional swap to `AutoSync::run` (for a clean one-package install) is in [UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md) 0.15 → 0.16.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.15.2...0.16.0

## [0.15.2](https://github.com/sandermuller/package-boost-php/compare/0.15.1...0.15.2) - 2026-05-31

<!-- verified-sha: 6c7d5a797a99e43c8eb222cfd9829fded7c94b37 -->
### `sandermuller/boost-core` widened to accept `^0.15`

0.15.2 widens the boost-core constraint from `^0.13 || ^0.14` to `^0.13 || ^0.14 || ^0.15` — a non-breaking "track latest" bump that absorbs boost-core 0.15.0 without dropping support for 0.13 or 0.14.

boost-core 0.15.0 is the conventions-inlining engine (Phase 1): render-time `<!--boost:conv-->` tokens that resolve project-convention values into generated skills/guidelines, so the always-loaded `## Project Conventions` block can drop once a consumer's skills are fully token-based. It is **non-load-bearing for `package-boost-php`** — this package carries no convention-slot references, declares no `withConventions([...])`, and ships no wrapper, so the inliner finds nothing to resolve and the drop gate never fires. Dogfood output is byte-identical to boost-core 0.14.

No code, public-API, or skill changes. If you stay on `boost-core ^0.13` or `^0.14`, no action is required.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.15.1...0.15.2

## [0.15.1](https://github.com/sandermuller/package-boost-php/compare/0.15.0...0.15.1) - 2026-05-31

<!-- verified-sha: ebce4730873bc5d704ba5a141aa9a03f944ef2ea -->
### Emitter reconcile guidance is now shipped behavior

boost-core 0.14.0 shipped the project-scope reconcile-on-sync, so the `writing-file-emitter` skill's emitter-dormancy reaping guidance moves from "forthcoming" to documented, shipped behavior.

- All forward-framed reconcile language flips to **"as of boost-core 0.14.0"** — version-anchored, since the constraint still allows boost-core 0.13 (where no reaping happens and a dropped emitter's file is orphaned until upgrade).
- Quotes the shipped manifest category constant `SyncManifest::CATEGORY_FILE`.
- Folds in the new author-facing hardening facts:
  - **Reaping is sha-gated** — a dormant emitter's output is reaped only if its on-disk content still matches what boost wrote; operator hand-edits are preserved, not deleted.
  - **Take-over is never claimed** — if an emitter overwrites a file boost never owned, boost warns and never reaps it; pre-existing operator content can't be silently deleted.
  - **Reserved paths are canonicalized + case-folded** before the denylist — `claude.md` resolves to the reserved `CLAUDE.md`; don't rely on `./` prefixes or case variants.
  

The durable author-facing contract (return `null` not throw; emit only through the managed write path to a path you alone own; disabled/errored preserve) is unchanged.

### `sandermuller/boost-core` widened to `^0.13 || ^0.14`

Non-breaking — absorbs boost-core 0.14.0 so consumers can run the reconcile the skill now documents, without dropping 0.13. No code or public-API change.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.15.0...0.15.1

## [0.15.0](https://github.com/sandermuller/package-boost-php/compare/0.14.0...0.15.0) - 2026-05-31

<!-- verified-sha: b9e2ff9c2b417a94a2d0c8992f0017ccc4928bae -->
0.15.0 narrows the `sandermuller/boost-core` constraint from `^0.12` to `^0.13`, dropping boost-core 0.12 from the supported matrix.

This is a **retention-policy bump** (matrix shrink), like 0.14.0 — not the absorption of a load-bearing boost-core 0.13 feature. `package-boost-php`'s runtime behaves identically against boost-core 0.12 / 0.13.

boost-core 0.13.0 ships guideline-shadow annotation — `boost where` marks a host guideline that shadows a same-named vendor guideline with `(shadows <vendor>)` and `boost doctor` reports it; that surface is diagnostic only. On the first `boost sync` against 0.13, the boost-managed `.gitignore` block also gains a `.boost/` entry.

#### Upgrade

Consumers on `boost-core ^0.12` must bump:

```bash
composer require sandermuller/boost-core:^0.13











```
Full note: [UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md) 0.14 → 0.15.

### Skill + guideline content

- **`writing-file-emitter`** — new emitter teardown/reaping lifecycle guidance. Returning `null` skips this sync but does **not** reap a previously-emitted file; emit through the managed write path so a forthcoming boost-core reconcile-on-sync can claim and prune the orphan; go dormant by returning `null` (throwing or disabling **preserves**, never reaps); reserved-path and first-adoption notes added.
- **`lean-dist`** — clarifies that the `lean` validator is opt-in CI enforcement while the `gitattributes` managed block is the baseline that keeps the archive lean. Resolves a recurring "is this load-bearing or optional?" question.
- **`foundation`** — adds an "Extending boost-core" note so a package authoring a custom `FileEmitter` knows to declare the `boost-extension` tag to pull `writing-file-emitter` (the skill is tag-gated off by default).

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.14.0...0.15.0

## [0.14.0](https://github.com/sandermuller/package-boost-php/compare/0.13.0...0.14.0) - 2026-05-30

<!-- verified-sha: 63bfb36a8fad5d575ad2733b638668685dc2ce2b -->
### ⚠️ Breaking: `sandermuller/boost-core` narrowed to `^0.12`

0.14.0 narrows the `sandermuller/boost-core` constraint from `^0.10 || ^0.11` to `^0.12`, dropping boost-core 0.10 and 0.11 from the supported matrix.

This is a **retention-policy bump** — a deliberate maintenance choice to shrink the test surface — not the absorption of a load-bearing boost-core 0.12 feature. Nothing in boost-core 0.12.0 requires a package-side code change here; `package-boost-php`'s runtime behaves identically against boost-core 0.10 / 0.11 / 0.12.

#### Upgrade

Consumers on `boost-core ^0.10` or `^0.11` must bump:

```bash
composer require sandermuller/boost-core:^0.12












```
boost-core 0.12.0 ships markerless agent-guidance files — `CLAUDE.md` / `AGENTS.md` are now wholesale boost-owned and regenerated in full each sync (no `<!-- boost-core:guidelines -->` markers), with an empty-assembly guard that never blanks a non-empty guidance file. On your first `boost sync` against 0.12 the marker comments are stripped from tracked guidance files; the guideline content is preserved. See boost-core's own UPGRADING.md for details.

### Dogfood adopts the markerless model

This repo's own `CLAUDE.md` / `AGENTS.md` regenerated under 0.12.0 — markers stripped, authored guideline body intact (PHPStan-fix, verification, package-boost, release-automation guidelines all retained). The empty `## Project Conventions` stub drops out, since `boost.php` declares no conventions.

### No public-API or skill changes

The package's commands, guidelines, and skills are unchanged. Full migration note in [UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md).

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.13.0...0.14.0

## [0.13.0](https://github.com/sandermuller/package-boost-php/compare/0.12.0...0.13.0) - 2026-05-30

<!-- verified-sha: 093e9dba731e7011b76f9d6e0398421ef91468fe -->
### `sandermuller/boost-core` constraint widened to `^0.10 || ^0.11`

0.13.0 widens the `sandermuller/boost-core` constraint from `^0.10` to `^0.10 || ^0.11` — gentle absorption, no floor-bump.

boost-core 0.11.0 adds drift-comparison wrapper-injection awareness (`BoostWrapperContract` + `WrapperEmitDiscovery`), closing a bare-CLI false-positive-deletion bug class for vendors that inject wrapper files. That capability is additive and framework-agnostic-package-author-irrelevant — `package-boost-php` ships no wrapper, so the change is not load-bearing here. Widened-OR keeps `^0.10` a valid floor for consumers not on the wrapper path while letting consumers move to boost-core `^0.11` (typically alongside `project-boost-laravel ^0.4`) without pinning `package-boost-php` back.

No code, API, or skill changes.

### Upgrade

- `package-boost-php: ^0.12` → `^0.13`
- If you stay on `boost-core ^0.10`, no action is required. To move to `boost-core ^0.11`, `composer update sandermuller/boost-core` resolves it.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.12.0...0.13.0

## [0.12.0](https://github.com/sandermuller/package-boost-php/compare/0.11.0...0.12.0) - 2026-05-29

<!-- verified-sha: a11882b5d00a95d5c216ac189134ad1e74fa9dcb -->
### Breaking changes

#### `sandermuller/boost-core: ^0.10` required

Floor-bumps the boost-core constraint from `^0.9` to `^0.10`.

boost-core 0.10.0 is the wrong-entry-point ergonomics cycle — `boost doctor` entry-point mismatch banner + three-case diagnostic split, closing a silent-capability-loss bug class. Per the family's load-bearing-only floor-pin discipline, that load-bearing condition supports floor-bumping over absorb-via-widened-OR here.

This release skips the widened-OR lifecycle's open + absorb beats: 0.11.0's previous-close (`^0.9`) goes directly to 0.12.0's next-close (`^0.10`). Different shape than the 0.10.1 → 0.11.0 cycle, which exercised the full open → absorb → close progression for the 0.8 → 0.9 transition. The floor-bump path is the empirical counterpoint to the lifecycle pattern, supported when the bump is load-bearing and the wrapper's adoption cohort is tight enough to absorb the breaking-resolve.

No code or API changes in this package — `BoostBaseCommand` remains the only boost-core symbol used, with stable signature across all relevant versions.

**Migration**: bump your boost-core constraint to `^0.10`:

```bash
composer require sandermuller/boost-core:^0.10














```
If you were already on `boost-core ^0.10`, no action is required.

See [UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md) for the `0.11 → 0.12` migration entry. boost-core 0.10.0's own release notes cover the wrong-entry-point cycle's user-facing improvements (entry-point mismatch banner, three-case diagnostic split).

### Upgrade

| From | To | Action |
|---|---|---|
| `package-boost-php: ^0.11` + `boost-core: ^0.10` | `^0.12` | None — `composer update sandermuller/package-boost-php` resolves cleanly |
| `package-boost-php: ^0.11` + `boost-core: ^0.9` | `^0.12` | Bump boost-core: `composer require sandermuller/boost-core:^0.10` |

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.11.0...0.12.0

## [0.11.0](https://github.com/sandermuller/package-boost-php/compare/0.10.3...0.11.0) - 2026-05-29

### Breaking changes

#### `sandermuller/boost-core: ^0.9` required

Drops support for `boost-core ^0.8`. The constraint narrows from `^0.8 || ^0.9` to `^0.9`.

The widened-OR shipped in 0.10.1 was the gentle-absorption window for consumers crossing the boost-core 0.8 → 0.9 line; that window closes in 0.11.0. boost-core's 0.9 line stabilised across 0.9.1 → 0.9.6 (validate fix, data-loss safety on guideline writes, path-ownership-registry reframe), and the family has settled on 0.9 as the canonical foundation.

No code or API changes in this package — `BoostBaseCommand` is the only boost-core symbol used and has stable signature across both versions. The narrowing is purely a constraint-range decision.

**Migration**: bump your boost-core constraint to `^0.9`:

```bash
composer require sandermuller/boost-core:^0.9















```
If you were already on `boost-core ^0.9`, no action is required.

See [UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md) for the full migration note and a pointer to boost-core's own UPGRADING.md (the conventions-schema layer, Project Conventions edit-surface move, Copilot/AGENTS.md merge).

### Internal

- `sandermuller/boost-skills` dev floor bumped from `^1.6` to `^1.7` (no consumer impact; dev dependency only).

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.10.3...0.11.0

## [0.10.3](https://github.com/sandermuller/package-boost-php/compare/0.10.2...0.10.3) - 2026-05-29

### Documentation + cleanup absorb

Two polish-tier commits since 0.10.2:

- **README line-66 clarification**: tightened the "Generated agent dirs ... `.gitignore` automatically" phrasing to disambiguate dirs (`.claude/`, `.cursor/`, `.codex/`, etc.) from root-level agent files (`AGENTS.md`, `CLAUDE.md`). The root-level files are tracked per boost-core 0.8.3's tracking-model and survive sync round-trips; the previous phrasing risked misreading them as also auto-gitignored.
- **Stale `.github/copilot-instructions.md` removed**: the file was tracked into git in 0.10.1's widen commit when boost-core 0.8.3's tracking-model flip exposed previously-gitignored agent files. boost-core 0.9.6 ships the path-ownership-registry reframe — retired emitter paths are cleaned unconditionally on next sync. Absorbing 0.9.6 cleaned the file on disk; this release commits the deletion.

No constraint change. boost-core `^0.8 || ^0.9` still resolves; current absorb is against 0.9.6.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.10.2...0.10.3

## [0.10.2](https://github.com/sandermuller/package-boost-php/compare/0.10.1...0.10.2) - 2026-05-28

### boost-core 0.9.1+ sync absorb

Picks up the boost-core 0.9.1 → 0.9.4 ladder via the existing `^0.8 || ^0.9` constraint. No constraint change.

What landed on disk:

- `.github/skills/` removed from the boost-managed `.gitignore` block. 0.9.1 routes Copilot to `.agents/skills/` + the root `AGENTS.md` surface (per GitHub Changelog 2025-12-18 for agent skills), so `.github/skills/` is no longer an emitted target. Leftover content at that path would not be refreshed by future syncs; removing the gitignore entry surfaces drift if any consumer still has files there.
- `CLAUDE.md` gains the rendered `## Project Conventions` audit-trail block between `<!-- boost-core:conventions:start --> ... end -->` markers. Source of truth lives in `boost.php` (`->withConventions([...])`, empty here); the marker-bounded YAML body is the rendered surface for operators reading CLAUDE.md.

What 0.9.1 → 0.9.4 fixed upstream (relevant to consumers):

- 0.9.1: active sync-time cleanup of `.github/copilot-instructions.md` + `.github/skills/` for the Copilot consolidation
- 0.9.2: `boost validate` no longer errors on default-empty `withConventions` (`[]` → `(object) []` cast at the validator boundary; required-key surfacing preserved)
- 0.9.3: data-loss safety patch on guideline write path
- 0.9.4: diagnostic-visibility short-circuit fix surfaced during 0.9.3 verification

### Upgrade

No consumer action required. `package-boost-php: ^0.10` continues to resolve; `composer update sandermuller/boost-core` picks up the latest patch in the 0.9.x branch.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.10.1...0.10.2

## [0.10.1](https://github.com/sandermuller/package-boost-php/compare/0.10.0...0.10.1) - 2026-05-28

### `sandermuller/boost-core: ^0.8 || ^0.9` accepted

Widened the boost-core constraint. Consumers can now resolve `package-boost-php: ^0.10` against either boost-core 0.8.x or 0.9.x — additive widen, no consumer of `^0.8` breaks.

boost-core 0.9.0 ships the Project Conventions shape change (operator-edit surface moves from CLAUDE.md's YAML body to `boost.php`'s `->withConventions([...])` chain), the Copilot/AGENTS.md merge per the GitHub Changelog 2025-08-28 update, and marker-bounded guideline writes that survive operator edits across sync. Public surface unchanged — no source edits in this package.

Verified across the existing CI matrix: PHP 8.3 + 8.4, `prefer-lowest` (resolves 0.8.0) and `prefer-stable` (resolves 0.9.0). All green.

### Tracking-model flip absorbed

`AGENTS.md`, `CLAUDE.md`, and `.github/copilot-instructions.md` are now tracked in git per boost-core 0.8.3, which dropped them from the boost-managed `.gitignore` block. Operator-owned content lives outside the `<!-- boost-core:guidelines:start --> ... end -->` markers and survives sync round-trips.

A duplicate-content drift surfaced during dogfood: pre-0.9.0 sync wrote the full guideline content to those files when the paths were still gitignored, leaving the old copy as "operator content" above the new markers after the 0.8.3 flip. Trimmed in this release. Other consumers upgrading from 0.8.x with previously-gitignored agent files will see the same drift — `vendor/bin/boost sync` is idempotent after the trim.

### Upgrade

- `package-boost-php: ^0.10` → no constraint change required from consumers
- Pair with `sandermuller/boost-core: ^0.9` to pick up the 0.9.0 Project Conventions surface

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.10.0...0.10.1

## [0.10.0](https://github.com/sandermuller/package-boost-php/compare/0.9.0...0.10.0) - 2026-05-27

### Skill migration to `sandermuller/boost-skills`

The `readme`, `release-notes`, and `upgrading` skills move to [`sandermuller/boost-skills`](https://github.com/sandermuller/boost-skills) 1.6.0+ under the `release-automation` opt-in tag. Their content is unchanged; only the publishing vendor changed.

This narrows `package-boost-php`'s scope to what it owns uniquely: package-author CLI infrastructure (`vendor/bin/package-boost-php lean` + `gitattributes`) and skill-authoring tooling for the boost ecosystem (`skill-authoring`, `writing-file-emitter`, `lean-dist`). The release-flow content skills sit better in `boost-skills` where they are tag-gated for any consumer who wants them, not just framework-agnostic-package authors.

#### Migration

```bash
composer require --dev "sandermuller/boost-skills:^1.6"



















```
Then add `'sandermuller/boost-skills'` to `withAllowedVendors([...])` and `'release-automation'` to `withTags(...)` in your `boost.php`. Full migration note + overlap-window `withExcludedSkills` workaround in [UPGRADING.md](https://github.com/sandermuller/package-boost-php/blob/main/UPGRADING.md).

### `sandermuller/boost-core: ^0.8` required

Bumped from `^0.7` for the conventions-schema layer (vendor schemas, `boost validate`, slot resolution). `boost sync` continues to work without a schema file — vendors that ship no `conventions-schema.json` are reported but not blocked. See boost-core's own UPGRADING.md for migration details.

### README rewrite

Full rewrite to the canonical family-README shape (~138 lines). Adds: routing table, L/Boost-comparison callout, three-table "what you get" surfacing commands + guidelines + skills as co-equal value axes, dogfood `boost.php` example + absolute-minimum example, opt-in tags section with mechanism-vs-vocabulary canon split.

Three-peer review cycle: boost-core (`6scam1ri`), boost-skills (`09jhthi9`), project-boost-laravel (`9t8ugveb`) maintainers all weighed in; final edits incorporate their feedback.

### Upgrade

- `package-boost-php: ^0.9` → `^0.10`
- Pair with `sandermuller/boost-skills: ^1.6` if you want the migrated skills.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.9.0...0.10.0

## [0.9.0](https://github.com/sandermuller/package-boost-php/compare/0.8.1...0.9.0) - 2026-05-25

Drops `package-boost-php`'s own Composer plugin. The two subcommands — `lean` and `gitattributes` — move to a standalone `vendor/bin/package-boost-php` binary, matching what `sandermuller/boost-core` did in 0.6.0. Net result: Composer no longer prompts `Do you trust "sandermuller/package-boost-php" to execute code` on `composer install` / `update`.

### Breaking changes

- **`composer package-boost-php:*` subcommands are gone.** Run them through the standalone binary instead:
  
  | Was                                        | Now                                          |
  |--------------------------------------------|----------------------------------------------|
  | `composer package-boost-php:lean`          | `vendor/bin/package-boost-php lean`          |
  | `composer package-boost-php:gitattributes` | `vendor/bin/package-boost-php gitattributes` |
  
  CI configs that called the Composer subcommand need the swap. The arguments and exit codes are unchanged (`--working-dir`, `--check`).
  
- **`config.allow-plugins` entry is dead.** `sandermuller/package-boost-php: true` (or `false`) under your project's `config.allow-plugins` is now a no-op. Composer ignores the stale entry, so leaving it is harmless; remove it on cleanup.
  

### Removed

- `SanderMuller\PackageBoostPhp\PackageBoostPhpPlugin`, `PackageBoostPhpCommandProvider`, and `BaseCommandAdapter` — the three classes existed only to expose `LeanCommand` + `GitattributesCommand` through Composer's `CommandProvider` capability.
- `composer-plugin-api` require dropped from `composer.json` (no longer needed).
- `composer/composer` dev-require dropped (only the plugin scaffolding referenced the Composer API surface).

### Added

- `bin/package-boost-php` — standalone Symfony Console entrypoint that wires `LeanCommand` + `GitattributesCommand` directly. Same source-of-truth command classes; only the invocation surface changed.

See [UPGRADING.md](UPGRADING.md) for the full 0.8 → 0.9 migration.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.8.1...0.9.0

## [0.8.1](https://github.com/sandermuller/package-boost-php/compare/0.8.0...0.8.1) - 2026-05-25

Widens the `sandermuller/boost-core` constraint to `^0.7`. boost-core 0.7.0 is backward-compatible from 0.6.x — the three new surfaces (`withRemoteSkills(...)` for non-Composer skill sources, the `@experimental` `SkillRenderer` plugin contract, and the `SyncEngine::sync()` injection params) are opt-in; existing `boost.php` configs keep working unchanged. package-boost-php itself doesn't consume any of the new surfaces — the bump is purely a constraint widen so consumers can adopt boost-core 0.7.x without holding back this package's version.

### Changed

- **`sandermuller/boost-core` constraint widened to `^0.7`** (was `^0.6`). Pulls in [boost-core 0.7.0](https://github.com/SanderMuller/boost-core/releases/tag/0.7.0). No migration required — additive release; see boost-core's release notes for the new surfaces.

**Full Changelog**: https://github.com/SanderMuller/package-boost-php/compare/0.8.0...0.8.1

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

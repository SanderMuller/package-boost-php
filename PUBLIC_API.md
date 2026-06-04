# Public API

The semver-protected surface of `sandermuller/package-boost-php`. Everything listed under **Stable surface** is covered by [Semantic Versioning](https://semver.org/spec/v2.0.0.html): it will not break in a MINOR or PATCH of the same MAJOR. Everything else — every class marked `@internal`, and all on-disk regenerable state — may change in any release, including patches.

## Versioning

This package follows Semantic Versioning 2.0.0. Pre-`1.0.0`, MINOR bumps may still break the public API (called out in `CHANGELOG.md` / `UPGRADING.md`). From `1.0.0` on, the surface below is locked for the `1.x` line.

## Stable surface

### Composer hooks

- `SanderMuller\PackageBoostPhp\Scripts\AutoSync::run` — the `post-install-cmd` / `post-update-cmd` target. A thin `@api` façade over boost-core's `BoostAutoSync::run`; consumers wire it from their own `composer.json` `scripts` block, referencing only this package's namespace (never the transitive `sandermuller/boost-core`).
- `SanderMuller\PackageBoostPhp\Scripts\AutoSync::runWithSummary` — the user-invoked-script variant (e.g. a `sync-ai` script) that always prints the one-line summary, including on a no-op install.

Both are `(Composer\Script\Event): void`. New parameters on a stable method are always optional-with-default; their absence-vs-presence is not a breaking change. The delegation targets in boost-core (`BoostAutoSync::run` / `runWithSummary`) are themselves `@api` in that package.

### CLI (`bin/package-boost-php`)

The command names, their documented options, and the exit-code contract (`0` ok, `1` failure) are stable. Human-readable output text is NOT a contract.

- **`gitattributes`** — maintain the managed block in `.gitattributes`. Options: `--check` (report drift without writing; non-zero exit if the file would change), `--working-dir`/`-d` (project root). Preserves foreign lines added by other tools.
- **`lean`** — validate that `.gitattributes` excludes non-shipping paths from the published archive (wraps `stolt/lean-package-validator`). Options: `--working-dir`/`-d`.

These commands are built on boost-core's `BoostBaseCommand` `@api` extension point (the `addWorkingDirOption` / `resolveProjectRoot` helpers); the command CLASSES themselves are `@internal` — the invocation contract above is the frozen surface, not the PHP types.

### Textual / wire formats

These aren't PHP types, but consumers and sibling tooling (e.g. `sandermuller/repo-init`) observe them, so they're part of the contract (changing them is a major bump):

- **Managed `.gitattributes` block markers** — `# >>> package-boost (managed) >>>` … `# <<< package-boost (managed) <<<`. The `gitattributes` command owns the lines between these markers and preserves everything else (foreign lines from other tools survive across runs). The marker strings are frozen.

### Shipped boost catalog

The skills and guidelines under `resources/boost/` (discovered by boost-core at the frozen `resources/boost/skills/` ⁄ `resources/boost/guidelines/` roots) are the package's product. Individual skill/guideline NAMES, trigger descriptions, and bodies evolve with the catalog and are NOT frozen as semver symbols — track `CHANGELOG.md` for catalog changes. The publishing-path + tag-filtering contract they rely on is boost-core's (see its `PUBLIC_API.md`).

## Internal (not covered by semver)

- `SanderMuller\PackageBoostPhp\Gitattributes\ManagedBlockWriter` (and its `BLOCK_START` / `BLOCK_END` consts and `sync()` method) — the engine behind the `gitattributes` command. The marker FORMAT is contractual (above); the class is `@internal` — drive it via the command, do not import it.
- `SanderMuller\PackageBoostPhp\Commands\LeanCommand` / `GitattributesCommand` — `@internal` Symfony command classes wired by `bin/package-boost-php`. The CLI invocation contract (above) is the frozen surface, not these classes.

## Deprecation policy

A stable (`@api`) element is deprecated before it is removed: marked `@deprecated` in PHPDoc — and, where it has a runtime code path, emitting `E_USER_DEPRECATED` — in a MINOR release, then removed no earlier than the next MAJOR. Deprecations are listed under `### Deprecated` in `CHANGELOG.md` so they surface in release notes.

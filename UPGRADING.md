# Upgrading

Breaking changes per major/minor bump.

## 0.20 → 1.0

First SemVer-stable release. The public surface declared in
[PUBLIC_API.md](PUBLIC_API.md) — the `AutoSync` composer-hook façade, the
`bin/package-boost-php` CLI contract, and the managed-block marker format —
is now locked for the `1.x` line. Everything marked `@internal` remains
free to change.

### `sandermuller/boost-core: ^1.0` required

1.0.0 narrows the `sandermuller/boost-core` constraint from
`^0.20 || ^0.21 || ^0.22 || ^0.23` to `^1.0`, dropping support for the
boost-core 0.x line. boost-core 1.0.0 is a drop-in over 0.23.3 (no API
break), so the bump is mechanical:

```bash
composer require sandermuller/boost-core:^1.0
```

If you were already on boost-core 0.23.x, 1.0.0 changes nothing about how
this package behaves — the surfaces it consumes (the `BoostAutoSync` hooks,
the `BoostBaseCommand` `@api` helpers, the `boost.php` authoring API) are
frozen and unchanged. No other migration is required.

## 0.17 → 0.18

0.18.0 narrows the `sandermuller/boost-core` constraint from
`^0.18 || ^0.19` to `^0.20`, dropping support for boost-core 0.18 and
0.19. boost-core 0.20 makes a breaking change to the `boost.php` config
API that cannot be expressed compatibly across the 0.19/0.20 boundary, so
the floor moves to 0.20 in lockstep.

### `sandermuller/boost-core: ^0.20` required

Bump your constraint:

```bash
composer require sandermuller/boost-core:^0.20
```

### boost-core 0.20: `withTags()` is now array-typed

boost-core 0.20 changed every `BoostConfig` builder method — including
`withTags()` — to take a single `array` argument. The previous variadic
form fatals on 0.20. Update your `boost.php` (or `.config/boost.php`):

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

`withAgents()`, `withAllowedVendors()`, `withExcludedSkills()`,
`withRemoteSkills()`, and `withConventions()` already took arrays — only
`withTags()` callers using the variadic form need the change. The array
form fatals on boost-core 0.18/0.19, so make this edit and the constraint
bump together.

## 0.16 → 0.17

0.17.0 narrows the `sandermuller/boost-core` constraint from
`^0.16 || ^0.17 || ^0.18 || ^0.19` to `^0.18 || ^0.19`, dropping support
for boost-core 0.16 and 0.17. This package now dogfoods the `.config/`
layout — config at `.config/boost.php`, sync manifest under
`.config/boost/`. Config resolution arrived in boost-core 0.17, but the
manifest relocation from root `.boost/` to `.config/boost/` landed in
0.18; on 0.17 a sync still writes `.boost/` and rewrites the managed
`.gitignore` block accordingly, so the committed `.config/` layout is only
consistent on boost-core 0.18+.

### `sandermuller/boost-core: ^0.18` required

If your `composer.json` requires `sandermuller/boost-core` below `^0.18`,
bump it:

```bash
composer require sandermuller/boost-core:^0.18
```

If you were already on `boost-core ^0.18` or later, no action is required.

### Optional: move your config to `.config/boost.php`

With boost-core 0.18+ guaranteed, you may relocate your own `boost.php`
into `.config/boost.php` to keep the repo root tidy. boost-core resolves
either location (root or `.config/`), but **not both** — keep exactly one
or it fails loud. This is optional; a root `boost.php` keeps working
unchanged.

## 0.16.1 → 0.16.2

> Shipped as a patch, but it carries a breaking constraint change — it
> should have been `0.17.0`. Documented here so the migration isn't lost.

0.16.2 narrows the `sandermuller/boost-core` constraint from
`^0.13 || ^0.14 || ^0.15 || ^0.16` to `^0.16`, dropping support for
boost-core 0.13–0.15. The package consumes only stable boost-core symbols
(`BoostBaseCommand`, `BoostAutoSync`), and the family had converged on
`^0.16` (boost-skills 2.0 requires it; project-boost-laravel pins it), so
this sheds unverified support for boost-core versions no longer in use.

### `sandermuller/boost-core: ^0.16` required

If your `composer.json` requires `sandermuller/boost-core` below `^0.16`,
bump it:

```bash
composer require sandermuller/boost-core:^0.16
```

If you were already on `boost-core ^0.16`, no action is required. The
rest of 0.16.2 (managed-block writer correctness fixes + CLI command test
coverage) is non-breaking.

## 0.15 → 0.16

0.16.0 adds a one-package auto-sync entry point. The package now ships
`SanderMuller\PackageBoostPhp\Scripts\AutoSync` — a thin façade over
boost-core's `BoostAutoSync` — so your `composer.json` scripts can wire
auto-sync while referencing only this package's own namespace, never the
transitive `sandermuller/boost-core` dependency.

**Non-breaking.** The previous
`SanderMuller\BoostCore\Scripts\BoostAutoSync::run` wiring still resolves
(boost-core comes in transitively). The migration below is optional but
recommended for a clean one-package install.

### Optional: swap the auto-sync callback to this package's namespace

In your project's `composer.json` `scripts`:

| Was                                                  | Now                                                        |
|------------------------------------------------------|------------------------------------------------------------|
| `SanderMuller\BoostCore\Scripts\BoostAutoSync::run`  | `SanderMuller\PackageBoostPhp\Scripts\AutoSync::run`       |

`AutoSync::run` delegates to boost-core's engine and inherits every guard
unchanged (`--no-dev`, `BOOST_SKIP_AUTOSYNC`, the no-op silence). For a
user-invoked script that should always print the one-line summary (e.g.
`composer sync-ai`), use `AutoSync::runWithSummary`. Once swapped, drop
any explicit `sandermuller/boost-core` entry from your `require` — it
resolves through `package-boost-php`.

## 0.14 → 0.15

0.15.0 narrows the `sandermuller/boost-core` constraint from `^0.12`
to `^0.13` — a retention-policy bump that drops `boost-core 0.12` from
the supported matrix. As with the `0.14.0` narrowing, this is a
deliberate maintenance choice to shrink the test surface, not the
absorption of a load-bearing `boost-core 0.13` feature: nothing in
0.13.0 requires a package-side code change here. `package-boost-php`'s
runtime behaves identically against boost-core 0.12 / 0.13.

### `sandermuller/boost-core: ^0.13` required

If your `composer.json` requires `sandermuller/boost-core: ^0.12`,
bump it:

```bash
composer require sandermuller/boost-core:^0.13
```

boost-core 0.13.0 ships guideline-shadow annotation — `boost where`
marks a host guideline that shadows a same-named vendor guideline with
`(shadows <vendor>)`, counts it in the NOTE, resolves the vendor copy
via `--diff=<name>`, and `boost doctor` reports it. That surface is
diagnostic only. On your first `boost sync` against 0.13, the
boost-managed `.gitignore` block also gains a `.boost/` entry. See
boost-core's own UPGRADING.md for details.

The package's public surface is unchanged. The `writing-file-emitter`
and `lean-dist` skills and the `foundation` guideline gained content
(emitter teardown/reaping lifecycle, lean-validation-is-opt-in
clarification, and a `boost-extension` discoverability note) — additive,
no behavioural break. If you were already on `boost-core ^0.13`, no
action is required.

## 0.13 → 0.14

0.14.0 narrows the `sandermuller/boost-core` constraint from
`^0.10 || ^0.11` to `^0.12` — a retention-policy bump that drops
`boost-core 0.10` and `0.11` from the supported matrix. This is a
deliberate maintenance choice to shrink the test surface, not the
absorption of a load-bearing `boost-core 0.12` feature: nothing in
0.12.0 requires a package-side code change here. `package-boost-php`'s
runtime behaves identically against boost-core 0.10 / 0.11 / 0.12.

### `sandermuller/boost-core: ^0.12` required

If your `composer.json` requires `sandermuller/boost-core: ^0.10` or
`^0.11`, bump it:

```bash
composer require sandermuller/boost-core:^0.12
```

boost-core 0.12.0 ships markerless agent-guidance files — `CLAUDE.md`,
`AGENTS.md`, etc. are now wholesale boost-owned and regenerated in full
each sync (no `<!-- boost-core:guidelines -->` markers), with an
empty-assembly guard that never blanks a non-empty guidance file. On
your first `boost sync` against 0.12, the marker comments are stripped
from your tracked guidance files; the guideline content itself is
preserved. See boost-core's own UPGRADING.md for the full migration.

The package's public surface and skills are unchanged. If you were
already on `boost-core ^0.12`, no action is required.

## 0.12 → 0.13

0.13.0 widens the `sandermuller/boost-core` constraint from `^0.10`
to `^0.10 || ^0.11` — no floor-bump, gentle absorption only.
boost-core 0.11.0 adds drift-comparison wrapper-injection awareness
(`BoostWrapperContract` + `WrapperEmitDiscovery`), a consumer-side
correctness fix for vendors that inject wrapper files. That capability
is additive and framework-agnostic-package-author-irrelevant —
`package-boost-php` ships no wrapper, so the change is not load-bearing
here. The widened OR lets consumers move to boost-core `^0.11`
(typically alongside `project-boost-laravel ^0.4`) without pinning
`package-boost-php` back.

No code, API, or skill changes. If you stay on `boost-core ^0.10`, no
action is required.

## 0.11 → 0.12

0.12.0 floor-bumps `sandermuller/boost-core` to `^0.10`. boost-core
0.10.0 is a load-bearing minor (closes a silent-capability-loss bug
class via the wrong-entry-point ergonomics cycle), and the family's
load-bearing-only floor-pin discipline supports floor-bumping over
absorb-via-widened-OR when the load-bearing condition holds.

### `sandermuller/boost-core: ^0.10` required

Bump your boost-core constraint to `^0.10`:

```bash
composer require sandermuller/boost-core:^0.10
```

boost-core 0.10.0 ships the wrong-entry-point ergonomics cycle —
`boost doctor` entry-point mismatch banner + three-case diagnostic
split. The engine surface stays framework-agnostic (Laravel-aware
features gated on `project-boost-laravel` presence). No public-API
breaks within boost-core itself; the floor-bump on this side is the
load-bearing absorb.

The package's public surface and skills are unchanged. If you were
already on `boost-core ^0.10`, no action is required.

## 0.10 → 0.11

0.11.0 drops `sandermuller/boost-core ^0.8` support — constraint
narrows from `^0.8 || ^0.9` to `^0.9`. No code or API changes;
constraint-range narrowing only. The widened-OR shipped in 0.10.1
was the gentle-absorption window for consumers crossing the
boost-core 0.8 → 0.9 line; that window closes in 0.11.0.

### `sandermuller/boost-core: ^0.9` required

If your `composer.json` requires `sandermuller/boost-core: ^0.8`,
bump it:

```bash
composer require sandermuller/boost-core:^0.9
```

boost-core 0.9.0 shipped the Project Conventions surface change
(operator-edit moves from CLAUDE.md's YAML body to `boost.php`'s
`->withConventions([...])` chain), the Copilot/AGENTS.md merge per
[GitHub Changelog 2025-08-28](https://github.blog/changelog/), and
marker-bounded guideline writes that survive operator edits across
sync. See boost-core's UPGRADING.md for the full migration.

The package's public surface and skills are unchanged. If you were
already on `boost-core ^0.9`, no action is required.

## 0.9 → 0.10

0.10.0 (a) moves three skills out of `package-boost-php` into
`sandermuller/boost-skills` and (b) bumps the `sandermuller/boost-core`
constraint to `^0.8` (was `^0.7`). The skills themselves are unchanged
— only the publishing vendor moves.

### `sandermuller/boost-core: ^0.8` required

Consumers locked on `boost-core ^0.7` must upgrade. boost-core 0.8.0
ships the conventions-schema layer (vendor schemas, `boost validate`,
slot resolution); see boost-core's own UPGRADING.md for migration
details. `boost sync` continues to work without a schema file —
vendors that ship no `conventions-schema.json` are reported but not
blocked.

### `readme`, `release-notes`, `upgrading` skills moved to `sandermuller/boost-skills`

These three skills now ship from
[`sandermuller/boost-skills`](https://github.com/sandermuller/boost-skills)
1.6.0+ under the `release-automation` opt-in tag. To continue
receiving them:

1. Require `sandermuller/boost-skills` as a dev dependency:
   ```bash
   composer require --dev "sandermuller/boost-skills:^1.6"
   ```
2. Add `'sandermuller/boost-skills'` to `withAllowedVendors([...])`
   in your `boost.php` (if not already present).
3. Add `'release-automation'` to `withTags(...)` in your `boost.php`.

Without the tag, the skills do not sync — the opt-in is intentional
so consumers who do not author packages can keep these skills off
their agents.

The `lean-dist`, `skill-authoring`, and `writing-file-emitter` skills
plus the `foundation` and `release-automation` guidelines continue to
ship from `package-boost-php`.

### Overlap-window workaround

During the window where a consumer runs `package-boost-php < 0.10.0`
alongside `boost-skills >= 1.6.0`, `boost-core` errors on the
vendor-vs-vendor skill collision:

```
[ERROR] Skill "readme" is published by multiple vendors:
        sandermuller/boost-skills, sandermuller/package-boost-php.
```

Either upgrade `package-boost-php` to `^0.10` (preferred), or add an
explicit exclusion to `boost.php`:

```php
->withExcludedSkills([
    'sandermuller/package-boost-php:readme',
    'sandermuller/package-boost-php:release-notes',
    'sandermuller/package-boost-php:upgrading',
])
```

Drop the exclusions once `package-boost-php >= 0.10.0` is required.

## 0.8 → 0.9

0.9.0 drops `package-boost-php`'s own Composer plugin. The two
`composer package-boost-php:*` subcommands move to the standalone
`vendor/bin/package-boost-php` binary. boost-core had the same change
in 0.6.0 — this is the package-boost-php counterpart.

### `composer package-boost-php:*` commands are gone — use `vendor/bin/package-boost-php`

| Was                                       | Now                                          |
|-------------------------------------------|----------------------------------------------|
| `composer package-boost-php:lean`         | `vendor/bin/package-boost-php lean`          |
| `composer package-boost-php:gitattributes`| `vendor/bin/package-boost-php gitattributes` |

Composer no longer prompts to allow this package as a plugin on
`composer install` / `update`.

### Optional: drop the dead `allow-plugins` entry

If your `composer.json` lists `sandermuller/package-boost-php` under
`config.allow-plugins`, remove it — the package is no longer a plugin.
Composer ignores the stale entry, so leaving it is harmless.

## 0.6 → 0.7

0.7.0 requires `sandermuller/boost-core ^0.6`. boost-core 0.6.0 retired its
Composer plugin, which changes two consumer-facing things. package-boost-php
itself remains a `composer-plugin` — its own commands are unaffected.

### `composer boost:*` commands are gone — use `vendor/bin/boost`

boost-core's plugin registered `composer boost:install`, `composer boost:sync`,
etc. as Composer subcommands. With the plugin removed, run the standalone
binary instead — the `boost:` prefix is dropped:

| Was | Now |
|---|---|
| `composer boost:install` | `vendor/bin/boost install` |
| `composer boost:sync` | `vendor/bin/boost sync` |
| `composer boost:doctor` | `vendor/bin/boost doctor` |
| `composer boost:tags` | `vendor/bin/boost tags` |

package-boost-php's own commands — `composer package-boost-php:lean` and
`composer package-boost-php:gitattributes` — are unchanged.

### Auto-sync is no longer automatic

boost-core's plugin re-ran `boost sync` on every `composer install` /
`composer update`. With the plugin gone, wire the callback into your own
project's `composer.json` to keep that behaviour:

```json
"scripts": {
    "post-install-cmd": ["SanderMuller\\BoostCore\\Scripts\\BoostAutoSync::run"],
    "post-update-cmd": ["SanderMuller\\BoostCore\\Scripts\\BoostAutoSync::run"]
}
```

Only the root package's scripts run during a Composer command, so this must
live in your project's `composer.json` — a dependency's scripts do not fire.
Otherwise, run `vendor/bin/boost sync` yourself (e.g. in CI). Setting
`BOOST_SKIP_AUTOSYNC=1` disables the callback.

### Optional: drop the dead `allow-plugins` entry

If your `composer.json` lists `sandermuller/boost-core` under
`config.allow-plugins`, remove it — boost-core is no longer a plugin.
Composer ignores the stale entry, so leaving it is harmless.

---
name: writing-file-emitter
description: Implement a FileEmitter for boost-core to emit a custom file (e.g. .mcp.json, .editorconfig) into the host project during boost:sync.
metadata:
  boost-tags: "boost-extension"
---

# Writing a FileEmitter

## When to apply

- Adding a new plugin to a Composer package that publishes skills via boost-core
- Asked "how do I make boost-core write file X when condition Y holds?"
- Reviewing a PR that adds an emitter

## The contract

A FileEmitter is a single-method interface in `SanderMuller\BoostCore\Contracts\FileEmitter`:

```php
public function emit(SyncContext $ctx): ?EmittedFile;
```

Return `EmittedFile` to write a file. Return `null` to skip (e.g. an
optional dependency isn't installed). Throwing is recorded as `errored`
and sync continues with other emitters.

The contract is `@experimental` — the shape may change before v1.0
stable. Pin to an exact boost-core version if you build against this.

## Minimal example

```php
namespace YourVendor\YourPackage\Emitters;

use SanderMuller\BoostCore\Contracts\FileEmitter;
use SanderMuller\BoostCore\Sync\EmittedFile;
use SanderMuller\BoostCore\Sync\SyncContext;

final class YourEmitter implements FileEmitter
{
    public function emit(SyncContext $ctx): ?EmittedFile
    {
        if (! $ctx->packages->has('some/required-dep')) {
            return null;
        }

        return new EmittedFile(
            relativePath: '.your-config.json',
            content: json_encode(['key' => 'value']) . "\n",
        );
    }
}
```

## Registration

In your package's `composer.json`:

```json
{
    "extra": {
        "boost": {
            "emitters": [
                "YourVendor\\YourPackage\\Emitters\\YourEmitter"
            ]
        }
    }
}
```

Emitters are only loaded from **allowlisted vendors** (per the host's
`boost.php` `withAllowedVendors([])` declaration). Untrusted vendors'
emitters never instantiate.

## Lifecycle: returning `null` skips, it does not reap

`emit()` returning `null` skips *writing* on this sync — it does **not**
remove a file an earlier sync already wrote. If your emitter wrote
`.mcp.json` while an optional dependency was installed, then that
dependency is dropped and `emit()` starts returning `null`, the earlier
`.mcp.json` is left behind: stale, possibly pointing at tooling that no
longer exists.

There is no uninstall hook to react to — boost-core retired its
Composer plugin, so no `PACKAGE_UNINSTALL` event fires. Reaping is the
sync reconcile's job instead. **As of boost-core 0.14.0**, the reconcile
records each emitter-emitted path in the sync manifest under the `file`
category (`SyncManifest::CATEGORY_FILE`) and prunes the orphaned ones —
a boost-managed path that drops out of the intended set is reaped on a
later sync. On boost-core 0.13 and earlier, emitter outputs are not
tracked and nothing reaps them: the stale file is left behind until the
consumer upgrades. Design for the reconcile either way:

- **Emit through the managed write path only.** Return an `EmittedFile`
  under the project root and let boost-core write it — never write the
  file yourself out-of-band. Only paths boost-core writes for you can be
  recorded in the manifest and later reaped; a file written outside that
  path is invisible to reconcile and will be left stale.
- **Do not hand-roll teardown.** There is no `emit()` counterpart called
  on removal, and stashing cleanup state on the instance does not work
  (`emit()` runs once per sync, never on uninstall). Model removal as
  "next sync, this path is no longer in the intended set," not as an
  event you handle.
- **Go dormant by returning `null` — not by throwing, not by disabling.**
  The reconcile reaps on a clean `null` (dormant), but *preserves*
  (never reaps) in two cases: an `errored` result (a thrown `emit()`) —
  a half-broken sync must not delete a still-wanted file — and an emitter
  switched off with `withDisabledEmitters()` — disabling means "stop
  regenerating," not "delete." Throwing or disabling to signal "remove my
  file" leaves it in place; return `null` to deregister the path.
- **Operator edits are safe — reaping is sha-gated.** A dormant
  emitter's output is reaped only if its on-disk content still matches
  what boost wrote. If an operator hand-edited it (e.g. tweaked your
  emitted `.mcp.json`), the reconcile preserves it rather than deleting
  their work — emitter outputs are operator-editable, the same as
  guidance files.

If your emitter overwrites a file boost never owned, boost does *not*
claim it — it warns on that sync, and the path is never reaped.
Ownership is recorded only for files boost created fresh or already
owned, so a take-over of pre-existing operator content can never be
silently deleted later.

## Anti-patterns

- **Assuming a skipped emit cleans up after itself.** Returning `null`
  does not delete the file on *this* sync (see Lifecycle above) — the
  reconcile reaps it on a *later* sync, once the path has dropped from
  the intended set. On boost-core 0.13 and earlier there is no reaping at
  all, so the file is orphaned until the consumer upgrades; emit through
  the managed write path only so the reconcile can claim it when present.
- **Expensive constructors.** The constructor runs during discovery —
  as soon as an allowlisted vendor's emitter is found — before `emit()`
  is ever called. There is no separate guard method; do skip-checks
  inside `emit()` and return `null`. Keep constructors parameterless
  and side-effect-free.
- **Writing outside the project root.** Path traversal (`../`,
  absolute paths) is rejected by `FileWriter`. Always emit a relative
  path under the project root.
- **Emitting a reserved path.** As of boost-core 0.14.0 an emitter path
  is canonicalized and case-folded before the denylist, then rejected
  with a diagnostic if it resolves to a reserved path: agent-guidance
  files (any case — `claude.md` resolves to `CLAUDE.md`), `.gitignore`,
  `.boost/`, any agent skill/command root (active or not), `.ai/`,
  `resources/boost/`, or a wrapper-claimed path or its descendants.
  Don't rely on `./` prefixes or case variants to slip past it. Emit to
  a path that is yours alone.
- **Assuming `emit()` runs more than once.** It is called exactly once
  per sync. Don't stash state on the instance expecting a later call —
  there isn't one. Do all detection (`$ctx->packages->has(...)`) inline.
- **Multiple emitters claiming the same path.** First wins; subsequent
  emitters get an `errored` result. Either pick distinct paths or use
  one emitter that branches internally.

## See also

- `SanderMuller\BoostCore\Sync\SyncContext` for what's available on `$ctx`
- `package-boost-laravel`'s `McpJsonEmitter` for a real working example

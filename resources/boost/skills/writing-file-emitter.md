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

boost-core does not reap it automatically today, and there is no
uninstall hook to react to — boost-core retired its Composer plugin, so
no `PACKAGE_UNINSTALL` event fires. An emitter's output isn't even
tracked in the sync manifest yet (only guidance, skills, and commands
are recorded), which is precisely why nothing reaps it. A sync-time
reconcile that records emitter-emitted paths and prunes the orphaned
ones (a boost-owned path that drops out of the intended set on a later
sync) is in progress upstream; track boost-core for when it lands.
Design for it now:

- **Emit through the managed write path only.** Return an `EmittedFile`
  under the project root and let boost-core write it — never write the
  file yourself out-of-band. Only managed, boost-owned paths can be
  recorded in the manifest and later reaped; a file written outside that
  path is invisible to reconcile and will be left stale.
- **Do not hand-roll teardown.** There is no `emit()` counterpart called
  on removal, and stashing cleanup state on the instance does not work
  (`emit()` runs once per sync, never on uninstall). Model removal as
  "next sync, this path is no longer in the intended set," not as an
  event you handle.
- **Go dormant by returning `null`, never by throwing.** Reconcile will
  treat a clean `null` as "dormant — reap the orphan" but an `errored`
  result (a thrown `emit()`) as a transient failure that must *not*
  reap — a half-broken sync must never delete a still-wanted file. So
  throwing to signal "remove my file" leaves it orphaned; return `null`
  to deregister the path.

## Anti-patterns

- **Assuming a skipped emit cleans up after itself.** Returning `null`
  does not delete a previously-emitted file (see Lifecycle above). Until
  boost-core's reconcile lands, a dropped-dependency emitter orphans its
  file — emit boost-owned paths only, and tell consumers the manual
  removal step if one is needed.
- **Expensive constructors.** The constructor runs during discovery —
  as soon as an allowlisted vendor's emitter is found — before `emit()`
  is ever called. There is no separate guard method; do skip-checks
  inside `emit()` and return `null`. Keep constructors parameterless
  and side-effect-free.
- **Writing outside the project root.** Path traversal (`../`,
  absolute paths) is rejected by `FileWriter`. Always emit a relative
  path under the project root.
- **Assuming `emit()` runs more than once.** It is called exactly once
  per sync. Don't stash state on the instance expecting a later call —
  there isn't one. Do all detection (`$ctx->packages->has(...)`) inline.
- **Multiple emitters claiming the same path.** First wins; subsequent
  emitters get an `errored` result. Either pick distinct paths or use
  one emitter that branches internally.

## See also

- `SanderMuller\BoostCore\Sync\SyncContext` for what's available on `$ctx`
- `package-boost-laravel`'s `McpJsonEmitter` for a real working example

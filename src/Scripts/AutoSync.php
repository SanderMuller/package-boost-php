<?php declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp\Scripts;

use Composer\Script\Event;
use SanderMuller\BoostCore\Scripts\BoostAutoSync;

/**
 * One-package auto-sync entry point.
 *
 * A thin façade over boost-core's {@see BoostAutoSync} so a consumer can
 * wire auto-sync from their `composer.json` `scripts` block while
 * referencing only the `package-boost-php` namespace — and therefore
 * `require` only `sandermuller/package-boost-php`, never naming the
 * transitive `sandermuller/boost-core` dependency.
 *
 * Delegation is total: every guard (`--no-dev` short-circuit,
 * `BOOST_SKIP_AUTOSYNC`, the binary-not-executable skip, the failure
 * warning) lives in {@see BoostAutoSync} and fires unchanged through this
 * seam — the façade adds no behaviour of its own.
 *
 * boost-core treats `BoostAutoSync::run()` / `runWithSummary()` (the
 * `(Event): void` Composer-callback signatures) as semver-stable
 * delegation targets and evolves them only additively, so this façade is
 * safe to expose as stable public API.
 */
final class AutoSync
{
    /**
     * `post-install-cmd` / `post-update-cmd` callback — silent on a no-op
     * install, streaming boost-core's one-line summary only when the sync
     * wrote or deleted a file. Delegates to {@see BoostAutoSync::run()}.
     */
    public static function run(Event $event): void
    {
        BoostAutoSync::run($event);
    }

    /**
     * User-invoked-script callback (e.g. `composer sync-ai`) — always
     * streams the one-line summary, including on a no-op install, where
     * silence would read as nothing having happened. Delegates to
     * {@see BoostAutoSync::runWithSummary()}.
     */
    public static function runWithSummary(Event $event): void
    {
        BoostAutoSync::runWithSummary($event);
    }
}

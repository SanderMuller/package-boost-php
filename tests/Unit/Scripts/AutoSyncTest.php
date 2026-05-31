<?php

declare(strict_types=1);

use Composer\Composer;
use Composer\IO\BufferIO;
use Composer\Script\Event;
use SanderMuller\PackageBoostPhp\Scripts\AutoSync;

/*
 * The façade adds no behaviour of its own — it delegates to boost-core's
 * BoostAutoSync. These tests prove the delegation is *transparent*: the
 * inherited --no-dev guard fires through the seam and skips before any
 * binary is resolved or spawned, so the suite never shells out.
 *
 * A real Event subtype counts which accessors the delegate touched —
 * a real Event keeps the type contract honest, and the call counts prove
 * the guard ran. (We assert the --no-dev short-circuit rather than the
 * BOOST_SKIP_AUTOSYNC one because driving that guard needs putenv, which
 * the disallowed-calls ruleset forbids; --no-dev proves transparency
 * just as well — both short-circuit inside the delegated BoostAutoSync.)
 */

it('delegates run() so the --no-dev guard skips before any sync work', function (): void {
    $event = new class ('post-install-cmd', new Composer(), new BufferIO(), false) extends Event {
        public int $isDevModeCalls = 0;

        public int $getComposerCalls = 0;

        public function isDevMode(): bool
        {
            $this->isDevModeCalls++;

            return parent::isDevMode();
        }

        public function getComposer(): Composer
        {
            $this->getComposerCalls++;

            return parent::getComposer();
        }
    };

    AutoSync::run($event);

    // isDevMode reached → the façade delegated into BoostAutoSync's guard;
    // getComposer untouched → it skipped before resolving/spawning the binary.
    expect($event->isDevModeCalls)->toBe(1)
        ->and($event->getComposerCalls)->toBe(0);
});

it('delegates runWithSummary() through the same guard chain', function (): void {
    $event = new class ('post-install-cmd', new Composer(), new BufferIO(), false) extends Event {
        public int $isDevModeCalls = 0;

        public int $getComposerCalls = 0;

        public function isDevMode(): bool
        {
            $this->isDevModeCalls++;

            return parent::isDevMode();
        }

        public function getComposer(): Composer
        {
            $this->getComposerCalls++;

            return parent::getComposer();
        }
    };

    AutoSync::runWithSummary($event);

    expect($event->isDevModeCalls)->toBe(1)
        ->and($event->getComposerCalls)->toBe(0);
});

<?php

declare(strict_types=1);

use Composer\Composer;
use Composer\IO\BufferIO;
use Composer\Script\Event;
use SanderMuller\BoostCore\Env;
use SanderMuller\PackageBoostPhp\Scripts\AutoSync;

/*
 * The façade adds no behaviour of its own — it delegates to boost-core's
 * BoostAutoSync. These tests prove the delegation is *transparent*: the
 * inherited guards still fire through the seam, and neither guard path
 * reaches binary resolution (so the suite never shells out to `boost
 * sync`). We use a real Event subtype that counts which accessors the
 * delegate touched rather than a framework mock — a real Event keeps the
 * type contract honest, and the call counts prove the guards ran.
 */

beforeEach(function (): void {
    // Baseline: auto-sync allowed to reach its guards (skip-env unset).
    putenv(Env::SKIP_AUTOSYNC);
});

afterEach(function (): void {
    putenv(Env::SKIP_AUTOSYNC);
});

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

it('honors BOOST_SKIP_AUTOSYNC through the façade, before the dev-mode check', function (): void {
    putenv(Env::SKIP_AUTOSYNC . '=1');

    $event = new class ('post-install-cmd', new Composer(), new BufferIO(), true) extends Event {
        public int $isDevModeCalls = 0;

        public function isDevMode(): bool
        {
            $this->isDevModeCalls++;

            return parent::isDevMode();
        }
    };

    AutoSync::run($event);

    // The skip-env guard short-circuits first — isDevMode is never reached.
    expect($event->isDevModeCalls)->toBe(0);
});

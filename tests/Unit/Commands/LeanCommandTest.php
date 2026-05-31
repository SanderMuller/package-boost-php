<?php

declare(strict_types=1);

use SanderMuller\PackageBoostPhp\Commands\LeanCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Install a fake lean-package-validator at <root>/vendor/bin that exits
 * with the given code, so the command's success/failure mapping can be
 * exercised without the real validator. POSIX shell script.
 */
function installValidatorStub(string $root, int $exitCode): void
{
    $binDir = $root . '/vendor/bin';
    if (! mkdir($binDir, 0o777, true) && ! is_dir($binDir)) {
        throw new RuntimeException("Failed to create {$binDir}");
    }

    $path = $binDir . '/lean-package-validator';
    file_put_contents($path, "#!/bin/sh\nexit {$exitCode}\n");
    chmod($path, 0o755);
}

it('fails with exit 1 when the validator binary is not installed', function (): void {
    withTempDir('pbp-lean', function (string $root): void {
        $tester = new CommandTester(new LeanCommand());
        $tester->execute(['--working-dir' => $root]);

        expect($tester->getStatusCode())->toBe(1);
        expect($tester->getDisplay())->toContain('not found');
    });
});

it('maps a passing validator to success (exit 0)', function (): void {
    withTempDir('pbp-lean', function (string $root): void {
        installValidatorStub($root, 0);

        $tester = new CommandTester(new LeanCommand());
        $tester->execute(['--working-dir' => $root]);

        expect($tester->getStatusCode())->toBe(0);
    });
})->skip(PHP_OS_FAMILY === 'Windows', 'POSIX shell stub not runnable on Windows');

it('maps a failing validator to failure (exit 1)', function (): void {
    withTempDir('pbp-lean', function (string $root): void {
        installValidatorStub($root, 1);

        $tester = new CommandTester(new LeanCommand());
        $tester->execute(['--working-dir' => $root]);

        expect($tester->getStatusCode())->toBe(1);
    });
})->skip(PHP_OS_FAMILY === 'Windows', 'POSIX shell stub not runnable on Windows');

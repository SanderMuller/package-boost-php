<?php

declare(strict_types=1);

use SanderMuller\PackageBoostPhp\Commands\GitattributesCommand;
use Symfony\Component\Console\Tester\CommandTester;

it('creates .gitattributes with the managed block when absent (exit 0)', function (): void {
    withTempDir('pbp-gitattr', function (string $root): void {
        $tester = new CommandTester(new GitattributesCommand());
        $tester->execute(['--working-dir' => $root]);

        expect($tester->getStatusCode())->toBe(0);
        $path = $root . '/.gitattributes';
        expect($path)->toBeFile();
        expect((string) file_get_contents($path))->toContain('# >>> package-boost (managed) >>>');
    });
});

it('reports up to date with exit 0 when already synced', function (): void {
    withTempDir('pbp-gitattr', function (string $root): void {
        (new CommandTester(new GitattributesCommand()))->execute(['--working-dir' => $root]);

        $tester = new CommandTester(new GitattributesCommand());
        $tester->execute(['--working-dir' => $root]);

        expect($tester->getStatusCode())->toBe(0);
        expect($tester->getDisplay())->toContain('up to date');
    });
});

it('rewrites a drifted file and exits 0 without --check', function (): void {
    withTempDir('pbp-gitattr', function (string $root): void {
        file_put_contents($root . '/.gitattributes', "*.php text=auto\n");

        $tester = new CommandTester(new GitattributesCommand());
        $tester->execute(['--working-dir' => $root]);

        expect($tester->getStatusCode())->toBe(0);
        expect((string) file_get_contents($root . '/.gitattributes'))
            ->toContain('# >>> package-boost (managed) >>>')
            ->toContain('*.php text=auto');
    });
});

it('does not write under --check on drift and exits non-zero with a warning', function (): void {
    withTempDir('pbp-gitattr', function (string $root): void {
        $path = $root . '/.gitattributes';
        file_put_contents($path, "*.php text=auto\n");
        $before = (string) file_get_contents($path);

        $tester = new CommandTester(new GitattributesCommand());
        $tester->execute(['--working-dir' => $root, '--check' => true]);

        expect($tester->getStatusCode())->toBe(1);
        expect($tester->getDisplay())->toContain('would change');
        // The file on disk is unchanged under --check.
        expect((string) file_get_contents($path))->toBe($before);
    });
});

it('passes --check with exit 0 when already in sync', function (): void {
    withTempDir('pbp-gitattr', function (string $root): void {
        (new CommandTester(new GitattributesCommand()))->execute(['--working-dir' => $root]);

        $tester = new CommandTester(new GitattributesCommand());
        $tester->execute(['--working-dir' => $root, '--check' => true]);

        expect($tester->getStatusCode())->toBe(0);
    });
});

it('preserves foreign lines end-to-end through the command', function (): void {
    withTempDir('pbp-gitattr', function (string $root): void {
        $path = $root . '/.gitattributes';
        file_put_contents(
            $path,
            "# >>> package-boost (managed) >>>\n/.lpv                   export-ignore\n# <<< package-boost (managed) <<<\n",
        );

        (new CommandTester(new GitattributesCommand()))->execute(['--working-dir' => $root]);

        expect((string) file_get_contents($path))
            ->toContain('/.lpv                   export-ignore')
            ->toContain('CLAUDE.md               export-ignore');
    });
});

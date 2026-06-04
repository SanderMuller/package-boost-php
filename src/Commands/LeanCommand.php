<?php

declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp\Commands;

use SanderMuller\BoostCore\Commands\BoostBaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Validate that the package's `.gitattributes` excludes dev/test/AI dirs
 * from the published archive.
 *
 * Delegates to `vendor/bin/lean-package-validator validate`. Thin wrapper
 * that finds the binary, runs it, surfaces exit code via SymfonyStyle.
 *
 * @internal Wired by `bin/package-boost-php`. The CLI invocation contract
 * (command name, options, exit codes — see PUBLIC_API.md) is the frozen
 * surface, not this class.
 */
final class LeanCommand extends BoostBaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('package-boost-php:lean')
            ->setDescription('Validate .gitattributes excludes non-shipping paths from the published archive.');
        $this->addWorkingDirOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $projectRoot = $this->resolveProjectRoot($input);
        $binary = $this->locateValidatorBinary($projectRoot);

        if ($binary === null) {
            $io->error('lean-package-validator binary not found. Ensure stolt/lean-package-validator is installed.');

            return self::FAILURE;
        }

        $process = new Process([$binary, 'validate'], $projectRoot);
        $process->run(static function (string $type, string $buffer) use ($output): void {
            $output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $io->success('.gitattributes is lean.');

            return self::SUCCESS;
        }

        $io->warning('Lean validation failed — see output above for details.');

        return self::FAILURE;
    }

    private function locateValidatorBinary(string $projectRoot): ?string
    {
        // The validated project's own vendor/bin is the only correct
        // location: when this package is installed as a dependency the
        // binary lands in the consumer's vendor/bin; when run against the
        // package itself, the project root is the cwd and resolves here too.
        $binary = $projectRoot . '/vendor/bin/lean-package-validator';

        return is_file($binary) && is_executable($binary) ? $binary : null;
    }
}

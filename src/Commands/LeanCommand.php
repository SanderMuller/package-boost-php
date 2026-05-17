<?php

declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp\Commands;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Validate that the package's `.gitattributes` excludes dev/test/AI dirs
 * from the published archive.
 *
 * Delegates to `vendor/bin/lean-package-validator validate`. Thin wrapper
 * that finds the binary, runs it, surfaces exit code via SymfonyStyle.
 */
final class LeanCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('package-boost-php:lean')
            ->setDescription('Validate .gitattributes excludes non-shipping paths from the published archive.')
            ->addOption(
                'working-dir',
                'd',
                InputOption::VALUE_REQUIRED,
                'Project root. Defaults to current working directory.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $workingDir = $input->getOption('working-dir');
        if (is_string($workingDir)) {
            $projectRoot = $workingDir;
        } else {
            $cwd = getcwd();
            $projectRoot = $cwd === false ? '.' : $cwd;
        }

        $projectRoot = rtrim($projectRoot, '/');
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
        $candidates = [
            $projectRoot.'/vendor/bin/lean-package-validator',
            __DIR__.'/../../vendor/bin/lean-package-validator',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp\Commands;

use Composer\Command\BaseCommand;
use SanderMuller\PackageBoostPhp\Gitattributes\ManagedBlockWriter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * Maintain the `# >>> package-boost (managed) >>>` block in .gitattributes.
 *
 * Contract (per repo-init's gitattributes-managed-block.md): walks the
 * existing block, identifies our canonical lines (by exact match against
 * the declared list), leaves all other lines in place. This lets repo-init
 * (and any other tool) append additional export-ignore entries inside the
 * same managed block — foreign lines are preserved across syncs.
 */
final class GitattributesCommand extends BaseCommand
{
    public function __construct(
        private readonly ManagedBlockWriter $writer = new ManagedBlockWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('package-boost-php:gitattributes')
            ->setDescription('Maintain the package-boost managed block in .gitattributes. Preserves foreign lines added by other tools.')
            ->addOption(
                'check',
                null,
                InputOption::VALUE_NONE,
                'Report drift without writing. Non-zero exit if the file would change.',
            )
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
        $path = $projectRoot.'/.gitattributes';

        $checkOnly = (bool) $input->getOption('check');

        try {
            $original = is_file($path) ? (string) file_get_contents($path) : '';
            $updated = $this->writer->sync($original);
        } catch (Throwable $e) {
            $io->error('Failed to sync .gitattributes: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($updated === $original) {
            $io->success('.gitattributes managed block is up to date.');

            return self::SUCCESS;
        }

        if ($checkOnly) {
            $io->warning(sprintf('.gitattributes would change. Run without --check to apply.'));

            return self::FAILURE;
        }

        if (file_put_contents($path, $updated) === false) {
            $io->error(sprintf('Failed to write %s.', $path));

            return self::FAILURE;
        }

        $io->success(sprintf('Updated managed block in %s', $path));

        return self::SUCCESS;
    }
}

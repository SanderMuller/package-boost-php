<?php

declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp;

use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider;
use SanderMuller\PackageBoostPhp\Commands\GitattributesCommand;
use SanderMuller\PackageBoostPhp\Commands\LeanCommand;

final class PackageBoostPhpCommandProvider implements CommandProvider
{
    /**
     * @return array<BaseCommand>
     */
    public function getCommands(): array
    {
        return [
            new LeanCommand,
            new GitattributesCommand,
        ];
    }
}

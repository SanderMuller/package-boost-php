<?php

declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp;

use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider;
use Override;
use SanderMuller\PackageBoostPhp\Commands\GitattributesCommand;
use SanderMuller\PackageBoostPhp\Commands\LeanCommand;

final class PackageBoostPhpCommandProvider implements CommandProvider
{
    /**
     * Wrap inner Symfony commands in BaseCommandAdapter so they satisfy
     * Composer's CommandProvider capability contract (which rejects anything
     * that isn't a Composer\Command\BaseCommand).
     *
     * @return array<BaseCommand>
     */
    #[Override]
    public function getCommands(): array
    {
        return [
            new BaseCommandAdapter(new LeanCommand()),
            new BaseCommandAdapter(new GitattributesCommand()),
        ];
    }
}

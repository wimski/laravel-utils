<?php

declare(strict_types=1);

namespace Wimski\LaravelPackageTemplate\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class PlaceholderCommand extends Command
{
    protected $signature = 'placeholder';

    public function handle(): int
    {
        return SymfonyCommand::SUCCESS;
    }
}

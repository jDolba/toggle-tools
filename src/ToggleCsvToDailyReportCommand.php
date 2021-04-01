<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToggleCsvToDailyReportCommand extends Command
{

    public function configure(): void
    {
        $this->setName('toggle-tools:csv2daily');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(42);

        return 0;
    }
}
<?php

declare(strict_types=1);

namespace App\Command;

use App\ProjectImport\ImportProjectTask;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-project',
    description: 'Imports project and extracts relevant composer dependency information.',
)]
class ImportProjectCommand extends Command
{
    public function __construct(private ImportProjectTask $importProjectTask)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('vcsUrl', InputArgument::REQUIRED, 'http Url of the vcs repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vcsUrl = $input->getArgument('vcsUrl');
        $importSucess = $this->importProjectTask->run($vcsUrl);

        if ($importSucess) {
            $output->writeln('Successfully imported '.$vcsUrl);
        } else {
            $output->writeln('Import failed for '.$vcsUrl.'. Make sure you have sufficient repository access and that it contains a composer.lock file. See logs for details.');
        }

        return Command::SUCCESS;
    }
}

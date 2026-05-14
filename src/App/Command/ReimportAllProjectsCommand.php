<?php

declare(strict_types=1);

namespace App\Command;

use App\ProjectImport\ImportProjectTask;
use App\Repository\ProjectRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:reimport-all-projects',
    description: 'Re-Imports all projects that have already been imported and updates metadata and relevant composer dependency information.',
)]
class ReimportAllProjectsCommand extends Command
{
    public function __construct(
        private readonly ImportProjectTask $importProjectTask,
        private readonly ProjectRepository $projectRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $returnCode = Command::SUCCESS;

        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            $importSuccess = $this->importProjectTask->run($project->getVcsUrl());

            if ($importSuccess) {
                $output->writeln('Successfully re-imported '.$project->getVcsUrl());
            } else {
                $output->writeln('Re-import failed for '.$project->getVcsUrl().'. Make sure you have sufficient repository access and that it contains a composer.lock file. See logs for details.');
                $returnCode = Command::FAILURE;
            }
        }

        return $returnCode;
    }
}

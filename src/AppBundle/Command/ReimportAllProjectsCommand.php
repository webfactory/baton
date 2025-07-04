<?php

namespace AppBundle\Command;

use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\ProjectImport\ImportProjectTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReimportAllProjectsCommand extends Command
{

    public function __construct(
        private readonly ImportProjectTask $importProjectTask,
        private readonly ProjectRepository $projectRepository,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:reimport-all-projects')
            ->setDescription('Re-Imports all projects that have already been imported and updates metadata and relevant composer dependency information.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorCode = 0;

        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            $importSuccess = $this->importProjectTask->run($project->getVcsUrl());

            if ($importSuccess) {
                $output->writeln('Successfully re-imported '.$project->getVcsUrl());
            } else {
                $output->writeln('Re-import failed for '.$project->getVcsUrl().'. Make sure you have sufficient repository access and that it contains a composer.lock file. See logs for details.');
                $errorCode = 1;
            }
        }

        return $errorCode;
    }
}
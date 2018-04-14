<?php

namespace AppBundle\Command;

use AppBundle\ProjectImport\ImportProjectTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProjectCommand extends Command
{
    /** @var ImportProjectTask */
    private $importProjectTask;

    public function __construct(ImportProjectTask $importProjectTask)
    {
        $this->importProjectTask = $importProjectTask;

        parent::__construct();
    }

    protected function configure()
    {
        $this
          ->setName('app:import-project')
          ->setDescription('Imports project and extracts relevant composer dependency information.')
          ->addArgument('vcsUrl', InputArgument::REQUIRED, 'http Url of the vcs repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vcsUrl = $input->getArgument('vcsUrl');
        $importSucess = $this->importProjectTask->run($vcsUrl);

        if ($importSucess) {
            $output->writeln('Successfully imported '.$vcsUrl);
        } else {
            $output->writeln('Import failed for '.$vcsUrl.'. Make sure you have sufficient repository access and that it contains a composer.lock file. See logs for details.');
        }
      }
}

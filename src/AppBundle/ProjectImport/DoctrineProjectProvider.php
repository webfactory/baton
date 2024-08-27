<?php

namespace AppBundle\ProjectImport;

use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\ProjectRepository;

/**
 * Tries to fetch existing Project entity or creates a new one.
 */
class DoctrineProjectProvider implements ProjectProviderInterface
{
    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function provideProject($name)
    {
        $project = $this->projectRepository->findOneBy(['name' => $name]);
        if (null === $project) {
            $project = new Project($name);
        }
        $this->projectRepository->add($project);

        return $project;
    }
}

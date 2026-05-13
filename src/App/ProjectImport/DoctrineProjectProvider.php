<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * Tries to fetch existing Project entity or creates a new one.
 */
#[AsAlias(ProjectProviderInterface::class)]
class DoctrineProjectProvider implements ProjectProviderInterface
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

    public function provideProject($name)
    {
        $project = $this->projectRepository->findOneBy(['name' => $name]);
        if (null === $project) {
            $project = new Project($name);
            $this->projectRepository->add($project);
        }

        return $project;
    }
}

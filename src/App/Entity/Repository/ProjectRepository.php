<?php

namespace App\Entity\Repository;

use App\Entity\Project;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    public function add(Project $project)
    {
        $this->getEntityManager()->persist($project);
    }
}

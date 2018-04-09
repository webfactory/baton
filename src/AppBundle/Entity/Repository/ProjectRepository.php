<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    public function add(Project $project)
    {
        $this->getEntityManager()->persist($project);
    }
}

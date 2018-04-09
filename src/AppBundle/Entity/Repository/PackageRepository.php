<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Package;
use Doctrine\ORM\EntityRepository;

class PackageRepository extends EntityRepository
{
    public function add(Package $package)
    {
        $this->getEntityManager()->persist($package);
    }
}

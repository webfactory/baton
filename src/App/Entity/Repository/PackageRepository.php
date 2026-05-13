<?php

namespace App\Entity\Repository;

use App\Entity\Package;
use Doctrine\ORM\EntityRepository;

class PackageRepository extends EntityRepository
{
    public function add(Package $package)
    {
        $this->getEntityManager()->persist($package);
    }
}

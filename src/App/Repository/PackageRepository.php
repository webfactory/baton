<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function add(Package $package): void
    {
        $this->getEntityManager()->persist($package);
    }

    public function findOneByName(string $name): ?Package
    {
        /* @var Package|null */
        return $this->findOneBy(['name' => $name]);
    }
}

<?php

namespace AppBundle\ProjectImport;

use AppBundle\Entity\Package;
use AppBundle\Entity\Repository\PackageRepository;

/**
 * Tries to fetch existing Package entity or creates a new one.
 */
class DoctrinePackageProvider implements PackageProviderInterface
{
    /** @var PackageRepository */
    private $packageRepository;

    public function __construct(PackageRepository $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function providePackage($name)
    {
        $package = $this->packageRepository->findOneBy(['name' => $name]);
        if (null === $package) {
            $package = new Package($name);
        }
        $this->packageRepository->add($package);

        return $package;
    }
}

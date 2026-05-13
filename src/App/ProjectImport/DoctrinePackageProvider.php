<?php

namespace App\ProjectImport;

use App\Entity\Package;
use App\Entity\Repository\PackageRepository;

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

<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Entity\Package;
use App\Repository\PackageRepository;

/**
 * Tries to fetch existing Package entity or creates a new one.
 */
class DoctrinePackageProvider implements PackageProviderInterface
{
    public function __construct(private PackageRepository $packageRepository)
    {
    }

    public function providePackage(string $name): Package
    {
        $package = $this->packageRepository->findOneBy(['name' => $name]);
        if (null === $package) {
            $package = new Package($name);
        }
        $this->packageRepository->add($package);

        return $package;
    }
}

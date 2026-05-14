<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Entity\PackageVersion;
use Doctrine\Common\Collections\ArrayCollection;

class PackageVersionFetcher
{
    public function __construct(
        private PackageProviderInterface $packageProvider,
        private ComposerPackageFetcher $composerPackageFetcher,
    ) {
    }

    /**
     * @return ArrayCollection<array-key, PackageVersion>
     */
    public function fetch(string $vcsUrl): ArrayCollection
    {
        $usages = new ArrayCollection();

        foreach ($this->composerPackageFetcher->fetchPackages($vcsUrl) as $composerPackage) {
            $package = $this->packageProvider->providePackage($composerPackage->getName());
            $usages->add(
                $package->getVersion($composerPackage->getPrettyVersion())
            );
        }

        return $usages;
    }
}

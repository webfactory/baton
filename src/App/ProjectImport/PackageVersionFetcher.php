<?php

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
     * @param string $vcsUrl
     *
     * @return ArrayCollection|PackageVersion[]
     */
    public function fetch($vcsUrl)
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

<?php

namespace AppBundle\ProjectImport;

use AppBundle\Entity\PackageVersion;
use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use Doctrine\Common\Collections\ArrayCollection;

class PackageVersionFetcher
{
    /** @var PackageProviderInterface */
    private $packageProvider;

    /** @var ComposerPackageFetcher */
    private $composerPackageFetcher;

    public function __construct(PackageProviderInterface $packageProvider,ComposerPackageFetcher $composerPackageFetcher)
    {
        $this->packageProvider = $packageProvider;
        $this->composerPackageFetcher = $composerPackageFetcher;
    }

    /**
     * @param string $vcsUrl
     * @return ArrayCollection|PackageVersion[]
     */
    public function fetch($vcsUrl)
    {
        $usages = new ArrayCollection();
        foreach($this->composerPackageFetcher->fetchPackages($vcsUrl) as $composerPackage) {
            $package = $this->packageProvider->providePackage($composerPackage->getName());
            $usages->add(
                $package->getVersion($composerPackage->getPrettyVersion())
            );
        }

        if ($usages->count() === 0) {
            throw new ProjectHasNoComposerPackageUsageInfoException();
        }

        return $usages;
    }
}

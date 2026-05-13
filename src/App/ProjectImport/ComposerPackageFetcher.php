<?php

namespace App\ProjectImport;

use App\Exception\ProjectHasNoComposerPackageUsageInfoException;
use Composer\Package\Package;

class ComposerPackageFetcher
{
    public function __construct(private LockFileFetcher $lockFileFetcher)
    {
    }

    /**
     * @param string $vcsUrl
     *
     * @return Package[]
     */
    public function fetchPackages($vcsUrl)
    {
        $lockContents = $this->lockFileFetcher->getLockContents($vcsUrl);

        if (null === $lockContents) {
            throw new ProjectHasNoComposerPackageUsageInfoException();
        }

        return LockFileParser::getPackages($lockContents);
    }
}

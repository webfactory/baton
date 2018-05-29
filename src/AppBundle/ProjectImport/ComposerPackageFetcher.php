<?php

namespace AppBundle\ProjectImport;

use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use Composer\Package\Package;

class ComposerPackageFetcher
{
    /**
     * @var LockFileFetcher
     */
    private $lockFileFetcher;

    public function __construct(LockFileFetcher $lockFileFetcher)
    {
        $this->lockFileFetcher = $lockFileFetcher;
    }

    /**
     * @param string $vcsUrl
     * @return Package[]
     */
    public function fetchPackages($vcsUrl)
    {
        $lockContents = $this->lockFileFetcher->getLockContents($vcsUrl);

        if ($lockContents === null) {
            throw new ProjectHasNoComposerPackageUsageInfoException();
        }

        return LockFileParser::getPackages($lockContents);
    }
}

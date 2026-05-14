<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Exception\ProjectHasNoComposerPackageUsageInfoException;

class ComposerPackageFetcher
{
    public function __construct(private readonly LockFileFetcher $lockFileFetcher)
    {
    }

    public function fetchPackages(string $vcsUrl): array
    {
        $lockContents = $this->lockFileFetcher->getLockContents($vcsUrl);

        if (null === $lockContents) {
            throw new ProjectHasNoComposerPackageUsageInfoException();
        }

        return LockFileParser::getPackages($lockContents);
    }
}

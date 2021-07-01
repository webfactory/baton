<?php

namespace AppBundle\ProjectImport;

use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use Composer\Json\JsonFile;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Package;

class LockFileParser
{
    /**
     * @param string $lockContents JSON string
     *
     * @return Package[]
     */
    public static function getPackages($lockContents)
    {
        $lockData = JsonFile::parseJson($lockContents);
        $packages = [];
        $packageLoader = new ArrayLoader();

        if (!array_key_exists('packages', $lockData)) {
            throw new ProjectHasNoComposerPackageUsageInfoException();
        }

        $lockedPackages = $lockData['packages'];

        if (empty($lockedPackages)) {
            throw new ProjectHasNoComposerPackageUsageInfoException();
        }

        foreach ($lockedPackages as $packageConfig) {
            $package = $packageLoader->load($packageConfig, Package::class);
            if ($package instanceof Package) {
                $packages[] = $package;
            }
        }

        return $packages;
    }
}

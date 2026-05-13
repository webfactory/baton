<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Exception\ProjectHasNoComposerPackageUsageInfoException;
use Composer\Json\JsonFile;
use Composer\Package\CompletePackage;
use Composer\Package\Loader\ArrayLoader;

class LockFileParser
{
    /**
     * @param string $lockContents JSON string
     *
     * @return CompletePackage[]
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
            $package = $packageLoader->load($packageConfig, CompletePackage::class);
            if ($package instanceof CompletePackage) {
                $packages[] = $package;
            }
        }

        return $packages;
    }
}

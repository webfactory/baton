<?php

namespace AppBundle\ProjectImport;


use Composer\Json\JsonFile;
use Composer\Package\Package;
use Composer\Package\Loader\ArrayLoader;

class LockFileParser
{
    /**
     * @param string $lockContents
     * @return Package[]
     */
    public static function getPackages($lockContents)
    {
        $lockData = JsonFile::parseJson($lockContents);
        $packages = [];
        $packageLoader = new ArrayLoader();

        $lockedPackages = $lockData['packages'];

        if (empty($lockedPackages)) {
            return $packages;
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

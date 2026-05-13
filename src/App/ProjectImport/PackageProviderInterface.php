<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Entity\Package;

interface PackageProviderInterface
{
    /**
     * @param string $name
     *
     * @return Package
     */
    public function providePackage($name);
}

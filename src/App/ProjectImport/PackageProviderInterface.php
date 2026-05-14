<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Entity\Package;

interface PackageProviderInterface
{
    public function providePackage(string $name): Package;
}

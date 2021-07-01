<?php

namespace AppBundle\ProjectImport;

use AppBundle\Entity\Package;

interface PackageProviderInterface
{
    /**
     * @param string $name
     *
     * @return Package
     */
    public function providePackage($name);
}

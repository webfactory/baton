<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PackageController
{
    /**
     * @Route(
     *     "/package/{name};{_format}",
     *     name="package",
     *     requirements={"name"="[^;]+", "_format": "json|html"},
     *     defaults={"_format"="html"}
     * )
     * @ParamConverter("package", class="AppBundle:Package", options={"repository_method" = "findOneByName"})
     * @Template()
     */
    public function detailAction(Package $package)
    {
        return ['package' => $package];
    }

    /**
     * @Route(
     *     "/package-versions/{name};{_format}",
     *     name="package-versions",
     *     requirements={"name"="[^;]*"},
     *     defaults={"_format"="json"}
     * )
     * @ParamConverter("package", class="AppBundle:Package", options={"repository_method" = "findOneByName"})
     * @Template()
     */
    public function versionsAction(Package $package)
    {
        return ['packageVersions' => $package->getVersions()];
    }
}

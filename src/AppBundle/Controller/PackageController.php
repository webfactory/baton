<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class PackageController
{
    /**
     * @Route("/package/{slug}.{id}", name="packageDetail")
     * @ParamConverter("package", class="AppBundle:Package")
     * @Template()
     */
    public function detailAction(Package $package)
    {
        return ['package' => $package];
    }

    /**
     * @Route("/api/package/{id}/versions", name="api-package-versions")
     * @ParamConverter("package", class="AppBundle:Package")
     * @return JsonResponse
     */
    public function apiPackageVersionsAction(Package $package)
    {
        $versions = [];
        foreach($package->getVersions() as $version) {
            $versions[] = $version->getNormalizedVersion();
        }
        $versions = array_values(array_unique($versions));
        return new JsonResponse($versions);
    }
}

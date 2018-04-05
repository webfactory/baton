<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route(service="app.controller.package")
 */
class PackageController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

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
    public function detailAction(Package $package, Request $request, $_format)
    {
        $operator = $request->query->get('operator');
        $versionString = $request->query->get('versionString');

        if (null !== $operator && 'html' === $_format) {
            // an older version of Baton used this URL to render search results. To keep the URLs intact, we redirect
            $url = $this->urlGenerator->generate('main', MainController::getUrlParametersForSearchSubmitPage(
                $package->getName(),
                $operator,
                $versionString
            ));

            return new RedirectResponse($url);
        }

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

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Entity\VersionConstraint;
use Composer\Semver\VersionParser;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * This controller has historically been used to get the plain search results, either as HTML or JSON, since the main
 * page was a contained client side logic to download the results via AJAX. Nowadays, the main page is rendered on
 * server side, and the link is sharable. Still, we want to keep shared links intact, so this has not yet been removed.
 *
 * @Route(service="app.controller.usageSearch")
 */
class UsageSearchController
{
    /**
     * @var Twig_Environment
     */
    private $twigEnvironment;

    public function __construct(Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @Route(
     *     "/usage-search/{package};{_format}/{operator}/{versionString}",
     *     name="search-usages",
     *     requirements={"package"="[^;]+", "_format": "json|html"},
     *     defaults={"_format"="html"}
     * )
     * @Template()
     * @ParamConverter("package", class="AppBundle:Package", options={"repository_method" = "findOneByName"})
     */
    public function searchResultsAction(Request $request, Package $package, $operator, $versionString)
    {
        if (!preg_match(VersionConstraint::VALID_OPERATORS, $operator)) {
            throw new InvalidArgumentException('Operator query parameter must match '.VersionConstraint::VALID_OPERATORS);
        }

        $normalizedVersionString = (new VersionParser())->normalize($versionString);

        $versionConstraint = new VersionConstraint($operator, $normalizedVersionString);

        if ($request->query->get('sharing')) {
            return new Response(
                $this->twigEnvironment->render(
                    '@App/UsageSearch/searchResultsSharing.html.twig',
                    [
                        'matchingPackageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
                        'package' => $package,
                        'operator' => $operator,
                        'versionString' => $versionString,
                    ]
                )
            );
        }

        return [
            'matchingPackageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
            'package' => $package,
            'operator' => $operator,
            'versionString' => $versionString,
        ];
    }
}

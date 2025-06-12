<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Entity\VersionConstraint;
use Composer\Semver\VersionParser;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This controller provides a JSON API for search results. It is not being used internally in the web interface of Baton.
 *
 * Historically, this controller rendered all search results – hence the redirect in case of HTML – and JSON was just an
 * "extra".
 *
 * @Route(service="app.controller.usageSearch")
 */
class UsageSearchController
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
     *     "/usage-search/{package};{_format}/{operator}/{versionString}",
     *     name="search-usages",
     *     requirements={"package"="[^;]+", "_format": "json|html"},
     *     defaults={"_format"="html"}
     * )
     * @Template()
     * @ParamConverter("package", class="AppBundle:Package", options={"repository_method" = "findOneByName"})
     */
    public function searchResultsAction(Package $package, $operator, $versionString, $_format)
    {
        if ('html' === $_format) {
            // an older version of Baton used this URL to render search results. To keep the URLs intact, we redirect
            $url = $this->urlGenerator->generate('main', MainController::getUrlParametersForSearchSubmitPage(
                $package->getName(),
                $operator,
                $versionString
            ));

            return new RedirectResponse($url);
        }

        if (!preg_match(VersionConstraint::VALID_OPERATORS, $operator)) {
            throw new InvalidArgumentException('Operator query parameter must match '.VersionConstraint::VALID_OPERATORS);
        }

        $normalizedVersionString = (new VersionParser())->normalize($versionString);

        $versionConstraint = new VersionConstraint($operator, $normalizedVersionString);

        return [
            'matchingPackageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
            'package' => $package,
            'operator' => $operator,
            'versionString' => $versionString,
        ];
    }
}

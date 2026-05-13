<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use App\Entity\VersionConstraint;
use Composer\Semver\VersionParser;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * This controller provides a JSON API for search results. It is not being used internally in the web interface of Baton.
 *
 * Historically, this controller rendered all search results – hence the redirect in case of HTML – and JSON was just an
 * "extra".
 */
class UsageSearchController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private PackageRepository $packageRepository,
        private Environment $twig,
    ) {
    }

    #[Route(
        '/usage-search/{package};{_format}/{operator}/{versionString}',
        name: 'search-usages',
        requirements: ['package' => '[^;]+', '_format' => 'json|html'],
        defaults: ['_format' => 'html']
    )]
    public function searchResultsAction(string $package, string $operator, string $versionString, string $_format): Response
    {
        if ('html' === $_format) {
            // an older version of Baton used this URL to render search results. To keep the URLs intact, we redirect
            $url = $this->urlGenerator->generate(
                'main',
                MainController::getUrlParametersForSearchSubmitPage($package, $operator, $versionString)
            );

            return new RedirectResponse($url);
        }

        $packageEntity = $this->packageRepository->findOneByName($package);
        if (!$packageEntity) {
            throw new NotFoundHttpException();
        }

        if (!preg_match(VersionConstraint::VALID_OPERATORS, $operator)) {
            throw new InvalidArgumentException('Operator query parameter must match '.VersionConstraint::VALID_OPERATORS);
        }

        $normalizedVersionString = (new VersionParser())->normalize($versionString);
        $versionConstraint = new VersionConstraint($operator, $normalizedVersionString);

        return new Response(
            $this->twig->render(
                'usage_search/search_results.json.twig',
                [
                    'matchingPackageVersions' => $packageEntity->getMatchingVersionsWithProjects($versionConstraint),
                    'package' => $packageEntity,
                    'operator' => $operator,
                    'versionString' => $versionString,
                ]
            )
        );
    }
}

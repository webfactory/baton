<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class PackageController
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PackageRepository $packageRepository,
        private readonly Environment $twig,
    ) {
    }

    #[Route(
        'package/{name};{_format}',
        name: 'package',
        requirements: ['name' => '[^;]+', '_format' => 'json|html'],
        defaults: ['_format' => 'html']
    )]
    public function detailAction(string $name, Request $request, string $_format): Response
    {
        $package = $this->packageRepository->findOneByName($name);
        if (!$package) {
            throw new NotFoundHttpException();
        }

        $operator = $request->query->get('operator');
        $versionString = $request->query->get('versionString');

        if ($operator && $versionString && 'html' === $_format) {
            // an older version of Baton used this URL to render search results. To keep the URLs intact, we redirect
            $url = $this->urlGenerator->generate(
                'main',
                MainController::getUrlParametersForSearchSubmitPage($package->getName(), $operator, $versionString)
            );

            return new RedirectResponse($url);
        }

        return new Response(
            $this->twig->render(
                'package/detail.'.$_format.'.twig',
                ['package' => $package]
            )
        );
    }

    #[Route(
        'package-versions/{name};{_format}',
        name: 'package-versions',
        requirements: ['name' => '[^;]*'],
        defaults: ['_format' => 'json']
    )]
    public function versionsAction(string $name): Response
    {
        $package = $this->packageRepository->findOneByName($name);
        if (!$package) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                'package/versions.json.twig',
                ['packageVersions' => $package->getVersions()]
            )
        );
    }
}

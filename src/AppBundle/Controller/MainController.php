<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\SearchPackageType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class MainController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private Environment $twig,
    ) {
    }

    #[Route('/', name: 'main')]
    public function mainAction(Request $request): Response
    {
        $searchPackageForm = $this->formFactory->create(SearchPackageType::class, null, ['method' => 'GET']);
        $searchPackageForm->handleRequest($request);

        if ($searchPackageForm->isSubmitted() && $searchPackageForm->isValid()) {
            $data = $searchPackageForm->getData();
            $package = $data['package'];
            $versionConstraint = $data['versionConstraint'];

            return new Response(
                $this->twig->render(
                    '@AppBundle/main/main.html.twig',
                    [
                        'searchPackageForm' => $searchPackageForm->createView(),
                        'matchingPackageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
                        'package' => $package,
                    ]
                )
            );
        }

        return new Response(
            $this->twig->render(
                '@AppBundle/main/main.html.twig',
                [
                    'searchPackageForm' => $searchPackageForm->createView(),
                ]
            )
        );
    }

    /**
     * Used to redirect to the form submit page from legacy routes.
     */
    public static function getUrlParametersForSearchSubmitPage($package, $operator, $versionString): array
    {
        return [
            'search_package' => [
                'package' => $package,
                'versionConstraint' => [
                    'operator' => $operator,
                    'value' => $versionString,
                ],
            ],
        ];
    }
}

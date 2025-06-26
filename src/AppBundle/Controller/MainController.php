<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\SearchPackageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/", name="main")
     * @Template()
     */
    public function mainAction(Request $request)
    {
        $searchPackageForm = $this->formFactory->create(SearchPackageType::class, null, ['method' => 'GET']);
        $searchPackageForm->handleRequest($request);

        if ($searchPackageForm->isSubmitted() && $searchPackageForm->isValid()) {
            $data = $searchPackageForm->getData();
            $package = $data['package'];
            $versionConstraint = $data['versionConstraint'];

            return [
                'searchPackageForm' => $searchPackageForm->createView(),
                'matchingPackageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
                'package' => $package,
            ];
        }

        return [
            'searchPackageForm' => $searchPackageForm->createView(),
        ];
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

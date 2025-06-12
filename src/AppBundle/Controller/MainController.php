<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\SearchPackageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="app.controller.main")
 */
class MainController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
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
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\PackageRepository;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\Form\Type\SearchPackageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactory;

/**
 * @Route(service="app.controller.main")
 */
class MainController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepo;

    /**
     * @var PackageRepository
     */
    private $packageRepo;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(ProjectRepository $projectRepository, PackageRepository $packageRepository, FormFactory $formFactory)
    {
        $this->projectRepo = $projectRepository;
        $this->packageRepo = $packageRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/", name="main")
     * @Template()
     */
    public function mainAction()
    {
        $searchPackageForm = $this->formFactory->create(SearchPackageType::class, null, ['method' => 'POST']);

        return [
            'projects' => $this->projectRepo->findBy([], ['name' => 'ASC']),
            'packages' => $this->packageRepo->findBy([], ['name' => 'ASC']),
            'searchPackageForm' => $searchPackageForm->createView(),
        ];
    }
}

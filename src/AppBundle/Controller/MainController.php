<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Form\Type\SearchPackageType;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactory;

/**
 * @Route(service="app.controller.main")
 */
class MainController
{
    /**
     * @var ObjectRepository
     */
    private $projectRepo;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(ObjectRepository $projectRepository, FormFactory $formFactory)
    {
        $this->projectRepo = $projectRepository;
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
            'projects' => $this->projectRepo->findAll(),
            'searchPackageForm' => $searchPackageForm->createView(),
        ];
    }

    /**
     * @Route("/package/{packageSlug}.{id}/{operator}/{versionString}", name="searchProjectsThatUsePackage")
     * @ParamConverter("package", class="AppBundle:Package")
     * @Template()
     */
    public function searchProjectsThatUsePackageAction(Package $package, $operator, $versionString)
    {
        $projects = [];
        foreach($package->getVersionsThatMatchVersionConstraint($operator, $versionString) as $packageVersion) {
            foreach($packageVersion->getProjects() as $project) {
                $projects[] = $project;
            }
        }
        return [
            'package' => $package,
            'versionConstraint' => $operator . ' ' . $versionString,
            'projects' => $projects,
        ];
    }
}

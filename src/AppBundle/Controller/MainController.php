<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Entity\VersionConstraint;
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
     * @var ObjectRepository
     */
    private $packageRepo;

    /**
     * @var FormFactory
     */
    private $formFactory;

    public function __construct(ObjectRepository $projectRepository, ObjectRepository $packageRepository, FormFactory $formFactory)
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
            'projects' => $this->projectRepo->findAll(),
            'packages' => $this->packageRepo->findAll(),
            'searchPackageForm' => $searchPackageForm->createView()
        ];
    }

    /**
     * @Route(
     *     "/package/{packageSlug}.{id}/{operator}/{versionString}",
     *     name="searchProjectsThatUsePackage",
     *     defaults={"operator": "all", "versionString": "1.0.0"},
     *     requirements={
     *         "operator": "(==|>=|<=|>|<|all)"
     *     }
     * )
     * @ParamConverter("package", class="AppBundle:Package")
     * @Template()
     */
    public function searchProjectsThatUsePackageAction(Package $package, $operator, $versionString)
    {
        $projects = [];
        $versionConstraint = new VersionConstraint($operator, $versionString);

        foreach($package->getVersions() as $packageVersion) {
            if($versionConstraint->matches($packageVersion)) {
                if(count($projects = $packageVersion->getProjects()) === 0) {
                    continue;
                }
                foreach($projects as $project) {
                    $projects[] = $project;
                }
            }
        }

        return [
            'package' => $package,
            'versionConstraint' => $operator . ' ' . $versionString,
            'projects' => $projects,
        ];
    }
}

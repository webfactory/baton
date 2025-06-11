<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\PackageRepository;
use AppBundle\Entity\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route(service="app.controller.settings")
 */
class SettingsController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepo;

    /**
     * @var PackageRepository
     */
    private $packageRepo;

    public function __construct(ProjectRepository $projectRepository, PackageRepository $packageRepository)
    {
        $this->projectRepo = $projectRepository;
        $this->packageRepo = $packageRepository;
    }

    /**
     * @Route("/settings", name="settings")
     * @Template()
     */
    public function settingsAction(): array
    {
        return [
            'projects' => $this->projectRepo->findBy([], ['name' => 'ASC']),
            'packages' => $this->packageRepo->findBy([], ['name' => 'ASC']),
        ];
    }
}

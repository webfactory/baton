<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class SettingsController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private PackageRepository $packageRepository,
        private Environment $twig,
    ) {
    }

    #[Route('/settings', name: 'settings')]
    public function settingsAction(): Response
    {
        return new Response(
            $this->twig->render(
                'settings/settings.html.twig',
                [
                    'projects' => $this->projectRepository->findBy([], ['name' => 'ASC']),
                    'packages' => $this->packageRepository->findBy([], ['name' => 'ASC']),
                ]
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class ProjectController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private Environment $twig,
    ) {
    }

    #[Route('project/{name}', name: 'project', requirements: ['name' => '.+'])]
    public function detailAction(string $name): Response
    {
        $project = $this->projectRepository->findOneByName($name);
        if (!$project) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render('project/detail.html.twig', ['project' => $project])
        );
    }
}

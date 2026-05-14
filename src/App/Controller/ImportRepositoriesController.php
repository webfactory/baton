<?php

declare(strict_types=1);

namespace App\Controller;

use App\ProjectImport\ImportProjectTask;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Twig\Environment;

class ImportRepositoriesController
{
    private bool $demoMode;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private ImportProjectTask $importProjectTask,
        private Environment $twig,
        #[Autowire(param: 'demo_mode')]
        mixed $demoMode = null,
    ) {
        $this->demoMode = (bool) $demoMode;
    }

    #[Route('import-repositories', name: 'import-repositories')]
    public function importFormAction(Request $request): Response
    {
        $projectImportForm = $this->getProjectImportForm();
        $imports = ['success' => [], 'fail' => []];

        $projectImportForm->handleRequest($request);
        if (!$this->demoMode && $projectImportForm->isSubmitted()) {
            $formData = $projectImportForm->getData();
            $repositoryUrlsSeparatedByComma = preg_replace('/\s+/', '', $formData['repositoryUrls']);
            $repositoryUrls = explode(',', $repositoryUrlsSeparatedByComma);

            foreach ($repositoryUrls as $repositoryUrl) {
                if ($this->importProjectTask->run($repositoryUrl)) {
                    $imports['success'][] = $repositoryUrl;
                } else {
                    $imports['fail'][] = $repositoryUrl;
                }
            }
        }

        return new Response(
            $this->twig->render('import_repositories/import_form.html.twig',
                [
                    'importProjectsForm' => $projectImportForm->createView(),
                    'imports' => $imports,
                    'demoMode' => $this->demoMode,
                ]
            )
        );
    }

    private function getProjectImportForm(): FormInterface
    {
        return $this->formFactory->createBuilder()
            ->add(
                'repositoryUrls',
                TextareaType::class,
                [
                    'label' => 'Repository URLs separated by comma',
                    'constraints' => [new NotBlank()],
                ]
            )->setMethod('POST')
            ->getForm();
    }
}

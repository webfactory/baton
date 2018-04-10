<?php

namespace AppBundle\Controller;

use AppBundle\ProjectImport\ImportProjectTask;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route(service="app.controller.importRepositories")
 */
class ImportRepositoriesController
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ImportProjectTask
     */
    private $importProjectTask;

    /**
     * @var bool
     */
    private $demoMode;

    public function __construct(FormFactory $formFactory, ImportProjectTask $importProjectTask, $demoMode)
    {
        $this->formFactory = $formFactory;
        $this->importProjectTask = $importProjectTask;
        $this->demoMode = $demoMode;
    }

    /**
     * @Route("/import-repositories", name="import-repositories")
     * @Template()
     */
    public function importFormAction(Request $request)
    {
        $projectImportForm = $this->getProjectImportForm();
        $imports = ["success" => [], "fail" => []];

        $projectImportForm->handleRequest($request);
        if (!$this->demoMode && $projectImportForm->isSubmitted()) {
            $formData = $projectImportForm->getData();
            $repositoryUrlsSeparatedByComma = preg_replace('/\s+/', '', $formData['repositoryUrls']);
            $repositoryUrls = explode(",", $repositoryUrlsSeparatedByComma);

            foreach($repositoryUrls as $repositoryUrl) {
                if($this->importProjectTask->run($repositoryUrl)) {
                    $imports['success'][] = $repositoryUrl;
                } else {
                    $imports['fail'][] = $repositoryUrl;
                }
            }
        }

        return [
            'importProjectsForm' => $projectImportForm->createView(),
            'imports' => $imports,
            'demoMode' => $this->demoMode
        ];
    }

    private function getProjectImportForm()
    {
        return $this->formFactory->createBuilder()
            ->add('repositoryUrls', TextareaType::class, ['label' => 'Repository URLs separated by comma', 'constraints' => [new NotBlank()]])
            ->setMethod('POST')->getForm();
    }
}

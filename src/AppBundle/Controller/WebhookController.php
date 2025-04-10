<?php

namespace AppBundle\Controller;

use AppBundle\ProjectImport\ImportProjectTask;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookController
{
    public function __construct(
        private ImportProjectTask $importProjectTask
    ) {
    }

    /**
     * @Route("/webhook", name="webhook")
     * @Method({"POST"})
     */
    public function updateAction(Request $request)
    {
        set_time_limit(500);

        $repositoryWasImported = false;

        // This works for the Kiln webhook API as well as for GitHub webhooks of content-type "application/x-www-form-urlencoded"
        if ($payload = $request->get('payload')) {
            $payload = json_decode($payload, flags: JSON_THROW_ON_ERROR);
            if (isset($payload->repository) && $payload->repository && $payload->repository->html_url) {
                $repositoryWasImported = $this->importProjectTask->run($payload->repository->html_url);
            }
        }

        return new Response('Repository Import erfolgt: '.$repositoryWasImported);
    }
}

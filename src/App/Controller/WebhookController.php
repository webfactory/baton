<?php

declare(strict_types=1);

namespace App\Controller;

use App\ProjectImport\ImportProjectTask;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController
{
    public function __construct(
        private readonly ImportProjectTask $importProjectTask,
        #[Autowire(param: 'app.github.webhook_secret')]
        private readonly ?string $webhookSecret = null,
    ) {
    }

    #[Route('webhook', name: 'webhook', methods: ['POST'])]
    public function updateAction(Request $request): Response
    {
        set_time_limit(500);

        if ($this->webhookSecret) {
            if (!$request->headers->has('X-Hub-Signature-256')) {
                throw new BadRequestHttpException('X-Hub-Signature-256 header missing');
            }

            $requestBody = $request->getContent();
            $hmac = hash_hmac('sha256', $requestBody, $this->webhookSecret);

            if (!hash_equals('sha256='.$hmac, $request->headers->get('X-Hub-Signature-256'))) {
                throw new BadRequestHttpException('Invalid X-Hub-Signature-256 header');
            }
        }
        $repositoryWasImported = false;

        if ($payload = $request->request->get('payload')) {
            $payload = json_decode($payload, flags: JSON_THROW_ON_ERROR);
            if (isset($payload->repository) && $payload->repository && $payload->repository->html_url) {
                $repositoryWasImported = $this->importProjectTask->run($payload->repository->html_url);
            }
        }

        return new Response('Repository Import erfolgt: '.$repositoryWasImported);
    }
}

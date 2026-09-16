<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Factory\VcsDriverFactory;
use Composer\Downloader\TransportException;

class LockFileFetcher
{
    public function __construct(private readonly VcsDriverFactory $vcsDriverFactory)
    {
    }

    public function getLockContents(string $vcsUrl): ?string
    {
        $vcsDriver = $this->vcsDriverFactory->getDriver($vcsUrl);

        foreach (['main', 'master'] as $branch) {
            try {
                return $vcsDriver->getFileContent('composer.lock', $branch);
            } catch (TransportException $exception) {
                if (404 !== $exception->getStatusCode()) {
                    throw $exception;
                }
            }
        }

        return null;
    }
}

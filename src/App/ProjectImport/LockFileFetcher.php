<?php

namespace App\ProjectImport;

use App\Factory\VcsDriverFactory;
use Composer\Downloader\TransportException;

class LockFileFetcher
{
    public function __construct(private VcsDriverFactory $vcsDriverFactory)
    {
    }

    /**
     * @return string|null
     */
    public function getLockContents($vcsUrl)
    {
        try {
            $vcsDriver = $this->vcsDriverFactory->getDriver($vcsUrl);

            return $vcsDriver->getFileContent('composer.lock', 'master');
        } catch (TransportException $exception) {
            if (404 === $exception->getStatusCode()) {
                return null;
            }

            throw $exception;
        }
    }
}

<?php

namespace AppBundle\ProjectImport;

use AppBundle\Factory\VcsDriverFactory;
use Composer\Downloader\TransportException;

class LockFileFetcher
{
    /** @var VcsDriverFactory */
    private $vcsDriverFactory;

    public function __construct(VcsDriverFactory $vcsDriverFactory)
    {
        $this->vcsDriverFactory = $vcsDriverFactory;
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

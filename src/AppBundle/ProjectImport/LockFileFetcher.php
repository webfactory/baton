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
     * @param $vcsUrl
     * @return string|null
     */
    public function getLockContents($vcsUrl)
    {
        try {
            $vcsDriver = $this->vcsDriverFactory->getDriver($vcsUrl);

            return $vcsDriver->getFileContent('composer.lock', 'master');
        } catch (TransportException $exception) {
            if ($exception->getStatusCode() === 404) {
                return null;
            }

            throw $exception;
        }
    }
}

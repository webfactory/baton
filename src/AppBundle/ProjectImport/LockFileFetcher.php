<?php

namespace AppBundle\ProjectImport;

use AppBundle\Factory\VcsDriverFactory;

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
        $vcsDriver = $this->vcsDriverFactory->getDriver($vcsUrl);

        return $vcsDriver->getFileContent('composer.lock', 'master');
    }
}

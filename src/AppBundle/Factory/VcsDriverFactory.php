<?php

namespace AppBundle\Factory;

use AppBundle\Entity\Project;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\Vcs\VcsDriverInterface;
use Composer\Repository\VcsRepository;

class VcsDriverFactory
{
    /** @var string */
    private $githubOAuthToken;

    /** @var string */
    private $kilnOAuthToken;

    public function __construct($githubOAuthToken, $kilnOAuthToken)
    {
        $this->githubOAuthToken = $githubOAuthToken;
        $this->kilnOAuthToken = $kilnOAuthToken;
    }

    /**
     * @return VcsDriverInterface
     */
    public function getDriver(Project $project){
        putenv('COMPOSER_HOME=/var/www/baton');
        $io = new NullIO();
        $io->setAuthentication('github.com', $this->githubOAuthToken, 'x-oauth-basic');

        /** @var VcsRepository $vcsRepository */
        $vcsRepository = new VcsRepository(
            ['url' => $project->getVcsUrl(), 'kiln-token' => $this->kilnOAuthToken],
            $io,
            Factory::createConfig(),
            null,
            ['github' => 'Composer\Repository\Vcs\GitHubDriver', 'kiln' => 'AppBundle\Driver\KilnDriver']
        );

        return $vcsRepository->getDriver();
    }
}

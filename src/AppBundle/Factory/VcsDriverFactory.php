<?php

namespace AppBundle\Factory;

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
     * @param string $vcsUrl
     * @return VcsDriverInterface
     */
    public function getDriver($vcsUrl){
        $io = new NullIO();
        if($this->githubOAuthToken !== null) {
            $io->setAuthentication('github.com', $this->githubOAuthToken, 'x-oauth-basic');
        }

        /** @var VcsRepository $vcsRepository */
        $vcsRepository = new VcsRepository(
            ['url' => $vcsUrl, 'kiln-token' => $this->kilnOAuthToken],
            $io,
            Factory::createConfig(),
            null,
            [
                'github' => 'Composer\Repository\Vcs\GitHubDriver',
                'kiln' => 'AppBundle\Driver\KilnDriver',
                'gitlab' => 'Composer\Repository\Vcs\GitLabDriver',
                'git-bitbucket' => 'Composer\Repository\Vcs\GitBitbucketDriver',
                'hg-bitbucket' => 'Composer\Repository\Vcs\HgBitbucketDriver',
                'git' => 'Composer\Repository\Vcs\GitDriver',
                'hg' => 'Composer\Repository\Vcs\HgDriver'
            ]
        );

        return $vcsRepository->getDriver();
    }
}

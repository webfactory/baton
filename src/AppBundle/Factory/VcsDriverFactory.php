<?php

namespace AppBundle\Factory;

use AppBundle\Exception\InsufficientVcsAccessException;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\Vcs\VcsDriverInterface;

class VcsDriverFactory
{
    /** @var string */
    private $githubOAuthToken;

    /** @var string */
    private $kilnOAuthToken;

    /** @var array ['platform' => VcsDriverInterface class ] */
    private $drivers;

    public function __construct($githubOAuthToken, $kilnOAuthToken, array $drivers = null)
    {
        $this->githubOAuthToken = $githubOAuthToken;
        $this->kilnOAuthToken = $kilnOAuthToken;
        $this->drivers = $drivers ?: [
            'github' => 'Composer\Repository\Vcs\GitHubDriver',
            'kiln' => 'AppBundle\Driver\KilnDriver',
            'gitlab' => 'Composer\Repository\Vcs\GitLabDriver',
            'git-bitbucket' => 'Composer\Repository\Vcs\GitBitbucketDriver',
            'hg-bitbucket' => 'Composer\Repository\Vcs\HgBitbucketDriver',
            'git' => 'Composer\Repository\Vcs\GitDriver',
            'hg' => 'Composer\Repository\Vcs\HgDriver'
        ];
    }

    /**
     * @param string $vcsUrl
     * @return VcsDriverInterface
     * @throws InsufficientVcsAccessException
     */
    public function getDriver($vcsUrl)
    {
        $composerConfig = Factory::createConfig();
        $io = $this->getIO();

        /** @var VcsDriverInterface $driver */
        foreach ($this->drivers as $driver) {
            if ($driver::supports($io, $composerConfig, $vcsUrl)) {
                try {
                    $driver = new $driver(['url' => $vcsUrl], $io, $composerConfig);
                    $driver->initialize();
                } catch (\RuntimeException $exception) {
                    throw new InsufficientVcsAccessException(
                        'Failed to communicate with repository. Check that you have sufficient access with your authentication method.', 0, $exception
                    );
                }

                return $driver;
            }
        }
    }

    /**
     * @return NullIO
     */
    private function getIO()
    {
        $io = new NullIO();
        if($this->githubOAuthToken !== null) {
            $io->setAuthentication('github.com', $this->githubOAuthToken, 'x-oauth-basic');
        }
        if($this->kilnOAuthToken !== null) {
            $io->setAuthentication('webfactory.kilnhg.com', $this->kilnOAuthToken, 'x-oauth-basic');
        }
        return $io;
    }
}

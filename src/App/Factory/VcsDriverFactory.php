<?php

declare(strict_types=1);

namespace App\Factory;

use App\Exception\InsufficientVcsAccessException;
use App\Exception\NoVcsDriverFoundException;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Repository\Vcs\VcsDriverInterface;
use Composer\Util\HttpDownloader;
use Composer\Util\ProcessExecutor;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class VcsDriverFactory
{
    /**
     * @var array<
     *     string, // platform
     *     class-string<VcsDriverInterface>
     * >
     */
    private array $drivers;

    public function __construct(
        #[Autowire(param: 'app.github.token')]
        private ?string $githubOAuthToken = null,
        ?array $drivers = null,
    ) {
        $this->drivers = $drivers ?: [
            'github' => 'Composer\Repository\Vcs\GitHubDriver',
            'gitlab' => 'Composer\Repository\Vcs\GitLabDriver',
            'git-bitbucket' => 'Composer\Repository\Vcs\GitBitbucketDriver',
            'hg-bitbucket' => 'Composer\Repository\Vcs\HgBitbucketDriver',
            'git' => 'Composer\Repository\Vcs\GitDriver',
            'hg' => 'Composer\Repository\Vcs\HgDriver',
        ];
    }

    public function getDriver(string $vcsUrl): VcsDriverInterface
    {
        $composerConfig = Factory::createConfig();
        $io = $this->getIO();
        $httpDownloader = new HttpDownloader($io, $composerConfig);
        $process = new ProcessExecutor($io);

        /** @var VcsDriverInterface $driver */
        foreach ($this->drivers as $driver) {
            if ($driver::supports($io, $composerConfig, $vcsUrl)) {
                try {
                    $driver = new $driver(['url' => $vcsUrl], $io, $composerConfig, $httpDownloader, $process);
                    $driver->initialize();
                } catch (RuntimeException $exception) {
                    throw new InsufficientVcsAccessException('Failed to communicate with repository. Check that you have sufficient access with your authentication method.', 0, $exception);
                }

                return $driver;
            }
        }

        throw new NoVcsDriverFoundException('No VCS driver found for URL: '.$vcsUrl);
    }

    private function getIO(): NullIO
    {
        $io = new NullIO();
        if (null !== $this->githubOAuthToken) {
            $io->setAuthentication('github.com', $this->githubOAuthToken, 'x-oauth-basic');
        }

        return $io;
    }
}

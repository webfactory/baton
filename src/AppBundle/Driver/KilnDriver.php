<?php

namespace AppBundle\Driver;

use AppBundle\Exception\InsufficientVcsAccessException;
use Composer\Cache;
use Composer\Config;
use Composer\Downloader\TransportException;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Repository\Vcs\GitDriver;
use Composer\Repository\Vcs\VcsDriver;
use DateTime;
use RuntimeException;

/**
 * Class KilnDriver.
 *
 * Enables the processing of Kiln repostiories using the Kiln API.
 */
class KilnDriver extends VcsDriver
{
    /** @var Cache */
    protected $cache;
    protected $owner;
    protected $repository;
    protected $tags;
    protected $branches;
    protected $rootIdentifier;
    protected $repoData;
    protected $hasIssues;
    protected $infoCache = [];
    protected $isPrivate = false;

    /** @var GitDriver */
    protected $gitDriver;

    /** @var int|string|false */
    private $repoId;

    /** @var string */
    private $oAuthToken;

    public function initialize()
    {
        preg_match('/^(?:https:\/\/webfactory.kilnhg.com\/(.*)\/(.*?)(?:$|\.))/', $this->url, $match);
        $this->owner = $match[1];
        $this->repository = $match[2];
        $this->originUrl = 'webfactory.kilnhg.com';
        $this->cache = new Cache($this->io, $this->config->get('cache-repo-dir').'/'.$this->originUrl.'/'.$this->owner.'/'.$this->repository);

        if ($this->io->hasAuthentication($this->originUrl)) {
            $auth = $this->io->getAuthentication($this->originUrl);
            if ('x-oauth-basic' === $auth['password']) {
                $this->oAuthToken = $auth['username'];
            }
        } else {
            throw new InsufficientVcsAccessException('You need to configure OAuth authentication to communicate with Kiln. Make sure the KILN_OAUTH_TOKEN environment variable is set correctly.');
        }

        $allRepos = $this->fetchAvailableRepositories();

        $this->repoId = array_search($this->getRepositoryUrl(), $allRepos);

        if (!$this->repoId) {
            throw new RuntimeException('Unknown repository URL '.$this->getRepositoryUrl());
        }

        if (isset($this->repoConfig['no-api']) && $this->repoConfig['no-api']) {
            $this->setupGitDriver($this->url);

            return;
        }
    }

    public function getRepositoryUrl()
    {
        return 'https://'.$this->originUrl.'/'.$this->owner.'/'.$this->repository;
    }

    public function getRootIdentifier()
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getRootIdentifier();
        }

        return $this->rootIdentifier;
    }

    public function getUrl()
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getUrl();
        }

        return 'https://'.$this->originUrl.'/'.$this->owner.'/'.$this->repository.'.git';
    }

    protected function getApiUrl()
    {
        return 'https://'.$this->originUrl.'/Api/1.0/';
    }

    public function getSource($identifier)
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getSource($identifier);
        }

        return ['type' => 'git', 'url' => $this->getUrl(), 'reference' => $identifier];
    }

    /**
     * @throws RuntimeException
     */
    public function getDist($identifier)
    {
        throw new RuntimeException('Zip downloads are not supported by the Kiln API');
    }

    public function getComposerInformation($identifier)
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getComposerInformation($identifier);
        }

        if (!isset($this->infoCache[$identifier])) {
            if ($this->shouldCache($identifier) && $res = $this->cache->read($identifier)) {
                return $this->infoCache[$identifier] = JsonFile::parseJson($res);
            }

            $composer = $this->getBaseComposerInformation($identifier);
            if ($composer) {
                if (!isset($composer['support']['issues']) && $this->hasIssues) {
                    $composer['support']['issues'] = sprintf('https://%s/%s/%s/issues', $this->originUrl, $this->owner, $this->repository);
                }
            }

            if ($this->shouldCache($identifier)) {
                $this->cache->write($identifier, json_encode($composer));
            }

            $this->infoCache[$identifier] = $composer;
        }

        return $this->infoCache[$identifier];
    }

    public function getFileContent($file, $identifier)
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getFileContent($file, $identifier);
        }

        try {
            $resource = $this->getApiUrl().'Repo/'.$this->repoId.'/Raw/File/'.bin2hex($file).'?token='.$this->oAuthToken;

            return $this->getContents($resource);
        } catch (TransportException $e) {
            if (404 !== $e->getCode()) {
                throw $e;
            }

            return null;
        }
    }

    public function getChangeDate($identifier)
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getChangeDate($identifier);
        }

        $resource = $this->getApiUrl().'Repo/'.$this->repoId.'/Raw/History/'.urlencode($identifier).'?token='.$this->oAuthToken;
        $commit = JsonFile::parseJson($this->getContents($resource), $resource);

        return new DateTime($commit['commit']['committer']['date']);
    }

    public function getTags()
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getTags();
        }
        if (null === $this->tags) {
            $this->tags = [];
            $resource = $this->getApiUrl().'Repo/'.$this->repoId.'/Tags?token='.$this->oAuthToken;

            do {
                $tagsData = JsonFile::parseJson($this->getContents($resource), $resource);
                foreach ($tagsData as $tag) {
                    $this->tags[$tag['name']] = $tag['commit']['sha'];
                }
            } while ($resource);
        }

        return $this->tags;
    }

    public function getBranches()
    {
        if ($this->gitDriver) {
            return $this->gitDriver->getBranches();
        }
        if (null === $this->branches) {
            $this->branches = [];
            $resource = $this->getApiUrl().'Repo/'.$this->repoId.'/NamedBranches?token='.$this->oAuthToken;

            $branchBlacklist = ['gh-pages'];

            do {
                $branchData = JsonFile::parseJson($this->getContents($resource), $resource);
                foreach ($branchData as $branch) {
                    $name = substr($branch['ref'], 11);
                    if (!in_array($name, $branchBlacklist)) {
                        $this->branches[$name] = $branch['object']['sha'];
                    }
                }
            } while ($resource);
        }

        return $this->branches;
    }

    public static function supports(IOInterface $io, Config $config, $url, $deep = false)
    {
        return false !== strpos($url, 'kilnhg');
    }

    /**
     * Generate an SSH URL.
     *
     * @return string
     */
    protected function generateSshUrl()
    {
        return 'ssh://webfactory@'.$this->originUrl.'/'.$this->owner.'/'.$this->repository;
    }

    protected function getContents($url, $fetchingRepoData = false)
    {
        $contents = file_get_contents($url);

        if (in_array('HTTP/1.1 200 OK', $http_response_header)) {
            return $contents;
        }

        $this->io->writeError('Kiln-API call failed! HTTP . '.$http_response_header);

        return null;
    }

    /**
     * Extract ratelimit from response.
     *
     * @param array $headers Headers from Composer\Downloader\TransportException.
     *
     * @return array Associative array with the keys limit and reset.
     */
    protected function getRateLimit(array $headers)
    {
        $rateLimit = [
            'limit' => '?',
            'reset' => '?',
        ];

        foreach ($headers as $header) {
            $header = trim($header);
            if (false === strpos($header, 'X-RateLimit-')) {
                continue;
            }
            list($type, $value) = explode(':', $header, 2);
            switch ($type) {
                case 'X-RateLimit-Limit':
                    $rateLimit['limit'] = (int) trim($value);
                    break;
                case 'X-RateLimit-Reset':
                    $rateLimit['reset'] = date('Y-m-d H:i:s', (int) trim($value));
                    break;
            }
        }

        return $rateLimit;
    }

    protected function attemptCloneFallback()
    {
        $this->isPrivate = true;

        try {
            // If this repository may be private (hard to say for sure,
            // GitHub returns 404 for private repositories) and we
            // cannot ask for authentication credentials (because we
            // are not interactive) then we fallback to GitDriver.
            $this->setupGitDriver($this->generateSshUrl());

            return;
        } catch (RuntimeException $e) {
            $this->gitDriver = null;

            $this->io->writeError('<error>Failed to clone the '.$this->generateSshUrl().' repository, try running in interactive mode so that you can enter your GitHub credentials</error>');
            throw $e;
        }
    }

    protected function setupGitDriver($url)
    {
        $this->gitDriver = new GitDriver(
            ['url' => $url],
            $this->io,
            $this->config,
            $this->process,
            $this->remoteFilesystem
        );
        $this->gitDriver->initialize();
    }

    public function fetchAvailableRepositories()
    {
        $list = [];

        $data = json_decode($this->getContents($this->getApiUrl().'Project?token='.$this->oAuthToken));

        foreach ($data as $project) {
            foreach ($project->repoGroups as $repoGroup) {
                foreach ($repoGroup->repos as $repo) {
                    $list[$repo->ixRepo] = $repo->sHgUrl;
                }
            }
        }

        return $list;
    }
}

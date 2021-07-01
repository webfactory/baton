<?php

namespace AppBundle\ProjectImport;

use AppBundle\Exception\InsufficientVcsAccessException;
use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ImportProjectTask
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProjectProviderInterface */
    private $projectProvider;

    /** @var PackageVersionFetcher */
    private $packageVersionFetcher;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProjectProviderInterface $projectProvider,
        PackageVersionFetcher $packageVersionFetcher,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->projectProvider = $projectProvider;
        $this->packageVersionFetcher = $packageVersionFetcher;
        $this->logger = $logger;
    }

    /**
     * @param string $vcsUrl
     *
     * @return bool indicates import success|failure
     */
    public function run($vcsUrl)
    {
        $this->logger->warning('Importing Repository '.$vcsUrl);

        $project = $this->projectProvider->provideProject($this->getProjectNameFromUrl($vcsUrl));
        $project->setVcsUrl($vcsUrl);

        try {
            $project->setUsedPackageVersions($this->packageVersionFetcher->fetch($vcsUrl));
        } catch (InsufficientVcsAccessException $exception) {
            $this->logger->error('Insufficient VCS access for '.$vcsUrl.'. Import failed.', ['exception' => $exception]);

            return false;
        } catch (ProjectHasNoComposerPackageUsageInfoException $exception) {
            $this->logger->error('No composer package usages found in Project '.$project->getName().'. Import failed.', ['exception' => $exception]);

            return false;
        }

        $this->entityManager->flush();

        $this->logger->warning('Imported Project '.$project->getName());

        return true;
    }

    /**
     * @param string $vcsUrl
     *
     * @return string
     */
    private function getProjectNameFromUrl($vcsUrl)
    {
        $fromSlash = substr($vcsUrl, strrpos($vcsUrl, '/') + 1);
        $fromLastSlashToFirstPeriod = explode('.', $fromSlash)[0];

        return $fromLastSlashToFirstPeriod;
    }
}

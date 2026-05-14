<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Exception\InsufficientVcsAccessException;
use App\Exception\ProjectHasNoComposerPackageUsageInfoException;
use App\Factory\VcsDriverFactory;
use Composer\Repository\Vcs\GitHubDriver;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ImportProjectTask
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectProviderInterface $projectProvider,
        private readonly PackageVersionFetcher $packageVersionFetcher,
        private readonly VcsDriverFactory $vcsDriverFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function run(string $vcsUrl): bool
    {
        $this->logger->notice('Importing Repository '.$vcsUrl);

        $project = $this->projectProvider->provideProject($this->getProjectNameFromUrl($vcsUrl));
        $project->setVcsUrl($vcsUrl);

        try {
            $project->setUsedPackageVersions($this->packageVersionFetcher->fetch($vcsUrl));
        } catch (InsufficientVcsAccessException $exception) {
            $this->logger->error('Insufficient VCS access for '.$vcsUrl.'. Import failed.', ['exception' => $exception]);

            return false;
        } catch (ProjectHasNoComposerPackageUsageInfoException $exception) {
            $this->logger->notice('No composer package usages found in Project '.$project->getName().'. Import failed.', ['exception' => $exception]);

            return false;
        }

        $vcsDriver = $this->vcsDriverFactory->getDriver($vcsUrl);
        if ($vcsDriver instanceof GitHubDriver) {
            $repoData = $vcsDriver->getRepoData();
            if (null === $repoData) {
                $this->logger->error('Failed to fetch repository data for '.$vcsUrl.'. Import failed.');

                return false;
            }
            $project->archived = $repoData['archived'] ?? false;
        }

        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            $this->logger->warning('UniqueConstraintViolation during import of '.$vcsUrl.', likely a concurrent request.', ['exception' => $exception]);

            return false;
        }

        $this->logger->notice('Imported Project '.$project->getName());

        return true;
    }

    private function getProjectNameFromUrl(string $vcsUrl): string
    {
        $fromSlash = substr($vcsUrl, strrpos($vcsUrl, '/') + 1);
        $fromLastSlashToFirstPeriod = explode('.', $fromSlash)[0];

        return $fromLastSlashToFirstPeriod;
    }
}

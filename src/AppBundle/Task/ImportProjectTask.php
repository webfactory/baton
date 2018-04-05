<?php

namespace AppBundle\Task;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Package\CompletePackage;
use Composer\Package\Loader\ArrayLoader;
use Composer\Repository\ArrayRepository;
use Composer\Repository\VcsRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportProjectTask
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $projectRepository;

    /** @var ObjectRepository */
    private $packageRepository;

    /** @var string */
    private $githubOAuthToken;

    /** @var string */
    private $kilnOAuthToken;

    /** @var IOInterface|null */
    private $io;

    /** @var Project */
    private $project;

    public function __construct(EntityManagerInterface $entityManager, $githubOAuthToken, $kilnOAuthToken)
    {
        $this->entityManager = $entityManager;
        $this->projectRepository = $entityManager->getRepository(Project::class);
        $this->packageRepository = $entityManager->getRepository(Package::class);
        $this->githubOAuthToken = $githubOAuthToken;
        $this->kilnOAuthToken = $kilnOAuthToken;
        //TODO: logging
    }

    /**
     * @param string $vcsUrl
     * @param IOInterface|null $io
     * @return bool indicates import success|failure
     */
    public function run($vcsUrl, IOInterface $io = null)
    {
        $io !== null ? $this->io = $io : $this->io = new NullIO();
        $this->io->setAuthentication('github.com', $this->githubOAuthToken, 'x-oauth-basic');

        $this->project = $this->projectRepository->findOneBy(['name' => $this->getProjectNameFromUrl($vcsUrl)]);
        if ($this->project !== null) {
            echo "Exisitng repository found, removing current usages\n";
            foreach ($this->project->getUsages() as $usage) {
                $this->project->removeUsage($usage);
            }
        } else {
            echo "Importing new project\n";
            $this->project = new Project($this->getProjectNameFromUrl($vcsUrl), $vcsUrl);
        }

        $lockFileContents = $this->getLockFileContent($this->project);

        $completeComposerPackages = $this->getCompletePackagesFromLockFile($lockFileContents);
        if ($completeComposerPackages->count() === 0) {
            $this->io->writeError('The project '.$this->project->getName().' does not contain any lock information for dependencies. Please track the composer.lock file in its repository to import it.');

            return false;
        }

        echo "\nAdding Usages:\n";
        foreach ($completeComposerPackages->getPackages() as $composerPackage) {
            $package = $this->packageRepository->findOneBy(['name' => $composerPackage->getName()]);
            if ($package === null) {
                $package = new Package($composerPackage->getName());
            }
            $this->project->addUsage(new PackageVersion($composerPackage->getPrettyVersion(), $package));
            echo $composerPackage->getName().", ";
        }
        echo "\n\n";

        $this->entityManager->persist($this->project);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param string $contents
     * @return ArrayRepository
     */
    public function getCompletePackagesFromLockFile($contents)
    {
        $lockData = JsonFile::parseJson($contents);
        $packages = new ArrayRepository();
        $loader = new ArrayLoader();

        $lockedPackages = $lockData['packages'];

        if (empty($lockedPackages)) {
            return $packages;
        }

        foreach ($lockedPackages as $packageConfig) {
            $package = $loader->load($packageConfig);
            if ($package instanceof CompletePackage) {
                $packages->addPackage($package);
            }
        }

        return $packages;
    }

    private function getLockFileContent(Project $project)
    {
        putenv('COMPOSER_HOME=/var/www/baton');

        $composerConfig = Factory::createConfig($this->io);

        /** @var VcsRepository $vcsRepository */
        $vcsRepository = new VcsRepository(
            ['url' => $project->getVcsUrl(), 'kiln-token' => $this->kilnOAuthToken],
            $this->io,
            $composerConfig,
            null,
            ['github' => 'Composer\Repository\Vcs\GitHubDriver', 'kiln' => 'AppBundle\Driver\KilnDriver']
        );

        return $vcsRepository->getDriver()->getFileContent('composer.lock', 'master');
    }

    /**
     * @param string $vcsUrl
     * @return string
     */
    private function getProjectNameFromUrl($vcsUrl)
    {
        $fromSlash = substr($vcsUrl, strrpos($vcsUrl, '/') + 1);
        $fromLastSlashToFirstPeriod = explode('.', $fromSlash)[0];

        return $fromLastSlashToFirstPeriod;
    }
}

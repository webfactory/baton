<?php

namespace AppBundle\Task;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;
use AppBundle\Factory\VcsDriverFactory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Package\CompletePackage;
use Composer\Package\Loader\ArrayLoader;
use Composer\Repository\ArrayRepository;
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

    /** @var VcsDriverFactory */
    private $vcsDriverFactory;

    /** @var IOInterface|null */
    private $io;

    /** @var Project */
    private $project;

    public function __construct(EntityManagerInterface $entityManager, VcsDriverFactory $vcsDriverFactory)
    {
        $this->entityManager = $entityManager;
        $this->projectRepository = $entityManager->getRepository(Project::class);
        $this->packageRepository = $entityManager->getRepository(Package::class);
        $this->vcsDriverFactory = $vcsDriverFactory;
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

        $this->project = $this->projectRepository->findOneBy(['name' => $this->getProjectNameFromUrl($vcsUrl)]);
        if ($this->project !== null) {
            $this->io->write(["Exisitng repository found, removing current usages"]);
            foreach ($this->project->getUsages() as $usage) {
                $this->project->removeUsage($usage);
            }
        } else {
            $this->io->write(["Importing new project"]);
            $this->project = new Project($this->getProjectNameFromUrl($vcsUrl), $vcsUrl);
        }

        $lockFileContents = $this->getLockFileContent($this->project);

        if($lockFileContents === null) {
            $this->io->writeError('The project '.$this->project->getName().' does not contain a composer.lock file. Please track the composer.lock file in its repository to import it.');
            return false;
        }

        $completeComposerPackages = $this->getCompletePackagesFromLockFile($lockFileContents);

        $this->io->write(["Adding Usages:"]);
        foreach ($completeComposerPackages->getPackages() as $composerPackage) {
            $package = $this->packageRepository->findOneBy(['name' => $composerPackage->getName()]);
            if ($package === null) {
                $package = new Package($composerPackage->getName());
            }
            $this->project->addUsage(new PackageVersion($composerPackage->getPrettyVersion(), $package));
            $this->io->write($composerPackage->getName().", ");
        }

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
        $vcsDriver = $this->vcsDriverFactory->getDriver($project);

        return $vcsDriver->getFileContent('composer.lock', 'master');
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

<?php

namespace AppBundle\Tests\Task;

use AppBundle\Entity\Project;
use AppBundle\Factory\VcsDriverFactory;
use AppBundle\ProjectImport\ImportProjectTask;
use AppBundle\ProjectImport\PackageVersionFetcher;
use AppBundle\ProjectImport\ProjectProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportProjectTaskTest extends KernelTestCase
{
    /**
     * @var ImportProjectTask
     */
    private $importProjectTask;

    private PackageVersionFetcher&MockObject $packageVersionFetcher;
    private VcsDriverFactory&MockObject $vcsDriverFactory;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->vcsDriverFactory = $this->createMock(VcsDriverFactory::class);
        $this->packageVersionFetcher = $this->createMock(PackageVersionFetcher::class);
        $this->importProjectTask = new ImportProjectTask(
            self::$container->get(EntityManagerInterface::class),
            self::$container->get(ProjectProviderInterface::class),
            $this->packageVersionFetcher,
            $this->vcsDriverFactory,
            new NullLogger()
        );
    }

    /**
     * @test
     */
    public function import()
    {
        $this->packageVersionFetcher->method('fetch')->willReturn(
            new ArrayCollection()
        );

        $this->assertTrue($this->importProjectTask->run('https://foo.git'));
    }
}

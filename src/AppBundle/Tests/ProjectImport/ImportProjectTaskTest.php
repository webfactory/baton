<?php

namespace AppBundle\Tests\Task;

use AppBundle\Entity\Project;
use AppBundle\ProjectImport\ImportProjectTask;
use AppBundle\ProjectImport\PackageVersionFetcher;
use AppBundle\ProjectImport\ProjectProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ImportProjectTaskTest extends TestCase
{
    /**
     * @var ImportProjectTask
     */
    private $importProjectTask;

    protected function setUp(): void
    {
        $this->importProjectTask = new ImportProjectTask(
            $this->getEntityManagerMock(),
            $this->getProjectProviderMock(),
            $this->getPackageVersionFetcherMock(),
            new NullLogger()
        );
    }

    /**
     * @test
     */
    public function import()
    {
        $this->assertTrue($this->importProjectTask->run('https://foo.git'));
    }

    /**
     * @return EntityManagerInterface|MockObject
     */
    private function getEntityManagerMock()
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class, [], [], '', false);
        $entityManagerMock->expects($this->once())
          ->method('flush')
          ->will($this->returnValue(null));

        return $entityManagerMock;
    }

    /**
     * @return ProjectProviderInterface|MockObject
     */
    private function getProjectProviderMock()
    {
        $projectProviderMock = $this->createMock(ProjectProviderInterface::class, [], [], '', false);
        $projectProviderMock->expects($this->once())
            ->method('provideProject')
            ->willReturn(new Project('Foo'));

        return $projectProviderMock;
    }

    /**
     * @return PackageVersionFetcher|MockObject
     */
    private function getPackageVersionFetcherMock()
    {
        $packageVersionFetcherMock = $this->createMock(PackageVersionFetcher::class, [], [], '', false);
        $packageVersionFetcherMock->expects($this->once())
            ->method('fetch')
            ->willReturn(new ArrayCollection());

        return $packageVersionFetcherMock;
    }
}

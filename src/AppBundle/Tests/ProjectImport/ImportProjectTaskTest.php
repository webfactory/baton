<?php

namespace AppBundle\Tests\Task;

use AppBundle\Entity\Project;
use AppBundle\ProjectImport\ImportProjectTask;
use AppBundle\ProjectImport\PackageVersionFetcher;
use AppBundle\ProjectImport\ProjectProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Log\NullLogger;

class ImportProjectTaskTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ImportProjectTask
     */
    private $importProjectTask;

    protected function setUp()
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
     * @return EntityManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEntityManagerMock()
    {
        $entityManagerMock = $this->getMock(EntityManagerInterface::class, [], [], '', false);
        $entityManagerMock->expects($this->once())
          ->method('flush')
          ->will($this->returnValue(null));

        return $entityManagerMock;
    }

    /**
     * @return ProjectProviderInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function getProjectProviderMock()
    {
        $projectProviderMock = $this->getMock(ProjectProviderInterface::class, [], [], '', false);
        $projectProviderMock->expects($this->once())
            ->method('provideProject')
            ->willReturn(new Project('Foo'));

        return $projectProviderMock;
    }

    /**
     * @return PackageVersionFetcher|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPackageVersionFetcherMock()
    {
        $packageVersionFetcherMock = $this->getMock(PackageVersionFetcher::class, [], [], '', false);
        $packageVersionFetcherMock->expects($this->once())
            ->method('fetch')
            ->willReturn(new ArrayCollection());

        return $packageVersionFetcherMock;
    }
}

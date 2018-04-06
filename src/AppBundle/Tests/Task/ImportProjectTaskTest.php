<?php

namespace AppBundle\Tests\Task;

use AppBundle\Factory\VcsDriverFactory;
use AppBundle\Task\ImportProjectTask;
use Composer\Repository\ArrayRepository;
use Composer\Repository\Vcs\VcsDriver;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportProjectTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImportProjectTask
     */
    private $importProjectTask;

    protected function setUp()
    {
        $this->importProjectTask = new ImportProjectTask($this->getEntityManagerMock(), $this->getVcsDriverFactoryMock());
    }

    public function testGetCompletePackagesFromLockFileReturnsArrayRepositoryOfCompletePackage()
    {
        $completePackages = $this->importProjectTask->getCompletePackagesFromLockFile(file_get_contents(__DIR__ . '/composer_test.lock'));

        $this->assertInstanceOf(ArrayRepository::class, $completePackages);
        $this->assertTrue(count($completePackages->getPackages()) > 0);
    }

    /**
     * Getting the composer.lock from the repository url is mocked away.
     * What is being tested here is the logic for importing the project without checking the persisted project.
     */
    public function testRunTask()
    {
        $this->assertTrue($this->importProjectTask->run("https://foo.git"));
    }

    /**
     * @return EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEntityManagerMock()
    {
        $entityManagerMock = $this->getMock('\Doctrine\ORM\EntityManager',
          array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
        $entityManagerMock->expects($this->any())
          ->method('getRepository')
          ->will($this->returnValue($this->getMock(ObjectRepository::class)));
        $entityManagerMock->expects($this->any())
          ->method('getClassMetadata')
          ->will($this->returnValue((object)array('name' => 'aClass')));
        $entityManagerMock->expects($this->any())
          ->method('persist')
          ->will($this->returnValue(null));
        $entityManagerMock->expects($this->any())
          ->method('flush')
          ->will($this->returnValue(null));

        return $entityManagerMock;
    }

    /**
     * @return VcsDriverFactory|\PHPUnit_Framework_MockObject_MockObject $vcsDriverFactory
     */
    private function getVcsDriverFactoryMock()
    {
        $vcsDriverMock = $this->getMockBuilder(VcsDriver::class)
          ->disableOriginalConstructor()
          ->getMock();
        $vcsDriverMock->method('getFileContent')->willReturn(file_get_contents(__DIR__ . '/composer_test.lock'));

        $vcsDriverFactoryMock = $this->getMockBuilder(VcsDriverFactory::class)
          ->disableOriginalConstructor()
          ->getMock();
        $vcsDriverFactoryMock->method('getDriver')->willReturn($vcsDriverMock);

        return $vcsDriverFactoryMock;
    }
}

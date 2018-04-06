<?php

namespace AppBundle\Tests\Task;

use AppBundle\Task\ImportProjectTask;
use Composer\Repository\ArrayRepository;
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
      $this->importProjectTask = new ImportProjectTask($this->getEntityManagerMock(), 'foo', 'bar');
    }

    public function testGetCompletePackagesFromLockFileReturnsArrayRepositoryOfCompletePackage()
    {
        $completePackages = $this->importProjectTask->getCompletePackagesFromLockFile(file_get_contents(getcwd() . '/composer.lock'));

        $this->assertInstanceOf(ArrayRepository::class, $completePackages);
        $this->assertTrue(count($completePackages->getPackages()) > 0);
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
}

<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\ProjectImport\DoctrineProjectProvider;

class DoctrineProjectProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProjectRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $projectRepository;

    protected function setUp()
    {
        $this->projectRepository = $this->getMock(ProjectRepository::class, [], [], '', false);
    }

    public function testProvideProjectReturnsNewProjectObjectIfNoneFound()
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $package = $doctrineProjectProvider->provideProject("Foo");

        $this->assertInstanceOf(Project::class, $package);
        $this->assertSame("Foo", $package->getName());
    }

    public function testProvideProjectReturnsExistingProjectObjectIfFound()
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new Project("Bar"));
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $project = $doctrineProjectProvider->provideProject("Bar");

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame("Bar", $project->getName());
    }

    public function testProvideProjectTellsEntityManagerToPersistProvidedProjectObject()
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('add');
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $doctrineProjectProvider->provideProject("Baz");
    }
}

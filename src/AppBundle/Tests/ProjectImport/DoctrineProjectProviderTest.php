<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\ProjectImport\DoctrineProjectProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrineProjectProviderTest extends TestCase
{
    /**
     * @var ProjectRepository|MockObject
     */
    private $projectRepository;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    /**
     * @test
     */
    public function provideProjectReturnsNewProjectObjectIfNoneFound()
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $package = $doctrineProjectProvider->provideProject('Foo');

        $this->assertInstanceOf(Project::class, $package);
        $this->assertSame('Foo', $package->getName());
    }

    /**
     * @test
     */
    public function provideProjectReturnsExistingProjectObjectIfFound()
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new Project('Bar'));
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $project = $doctrineProjectProvider->provideProject('Bar');

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame('Bar', $project->getName());
    }

    /**
     * @test
     */
    public function provideProjectTellsEntityManagerToPersistProvidedProjectObject()
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('add');
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $doctrineProjectProvider->provideProject('Baz');
    }
}

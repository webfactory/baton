<?php

declare(strict_types=1);

namespace App\Tests\ProjectImport;

use App\Entity\Project;
use App\ProjectImport\DoctrineProjectProvider;
use App\Repository\ProjectRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrineProjectProviderTest extends TestCase
{
    private ProjectRepository&MockObject $projectRepository;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    #[Test]
    public function provideProjectReturnsNewProjectObjectIfNoneFound(): void
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $project = $doctrineProjectProvider->provideProject('Foo');

        $this->assertSame('Foo', $project->getName());
    }

    #[Test]
    public function provideProjectReturnsExistingProjectObjectIfFound(): void
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new Project('Bar'));
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $project = $doctrineProjectProvider->provideProject('Bar');

        $this->assertSame('Bar', $project->getName());
    }

    #[Test]
    public function provideProjectTellsEntityManagerToPersistProvidedProjectObject(): void
    {
        $this->projectRepository
            ->expects($this->once())
            ->method('add');
        $doctrineProjectProvider = new DoctrineProjectProvider($this->projectRepository);

        $doctrineProjectProvider->provideProject('Baz');
    }
}

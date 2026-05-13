<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Package;
use App\Entity\PackageVersion;
use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public const name = 'foo';

    private Project $project;

    protected function setUp(): void
    {
        $this->project = new Project(self::name);
    }

    #[Test]
    public function addUsageAddsUsageToProjectAndProjectToPackageVersion(): void
    {
        $packageVersion = new PackageVersion('1.0.0', new Package('foo'));
        $this->project->addUsage($packageVersion);

        $this->assertNotEmpty($this->project->getPackageVersions());
        $this->assertSame(
            self::name,
            $this->project->getPackageVersions()[0]->getProjects()[0]->getName()
        );
    }

    #[Test]
    public function setUsedPackageVersionsSetsAssociationOnProjectAndPackageVersion(): void
    {
        $packageVersion = new PackageVersion('1.0.0', new Package('foo/bar'));

        $this->project->setUsedPackageVersions(new ArrayCollection([$packageVersion]));

        $this->assertContains($packageVersion, $this->project->getPackageVersions());
        $this->assertContains($this->project, $packageVersion->getProjects());
    }
}

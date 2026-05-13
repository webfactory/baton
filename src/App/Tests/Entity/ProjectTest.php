<?php

namespace App\Tests\Entity;

use App\Entity\Package;
use App\Entity\PackageVersion;
use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public const name = 'foo';

    /**
     * @var Project
     */
    private $project;

    protected function setUp(): void
    {
        $this->project = new Project(self::name);
    }

    /**
     * @test
     */
    public function addUsageAddsUsageToProjectAndProjectToPackageVersion()
    {
        $packageVersion = new PackageVersion('1.0.0', new Package('foo'));
        $this->project->addUsage($packageVersion);

        $this->assertTrue(count($this->project->getPackageVersions()) > 0);
        $this->assertSame(
            self::name,
            $this->project->getPackageVersions()[0]->getProjects()[0]->getName()
        );
    }

    /**
     * @test
     */
    public function setUsedPackageVersionsSetsAssociationOnProjectAndPackageVersion(): void
    {
        $packageVersion = new PackageVersion('1.0.0', new Package('foo/bar'));

        $this->project->setUsedPackageVersions(new ArrayCollection([$packageVersion]));

        $this->assertContains($packageVersion, $this->project->getPackageVersions());
        $this->assertContains($this->project, $packageVersion->getProjects());
    }
}

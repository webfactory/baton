<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;
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
}

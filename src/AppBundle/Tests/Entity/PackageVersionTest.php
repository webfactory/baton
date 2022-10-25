<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PackageVersionTest extends TestCase
{
    public const version = '1.0.0';

    /**
     * @var PackageVersion
     */
    private $packageVersion;

    protected function setUp(): void
    {
        $this->packageVersion = new PackageVersion(self::version, new Package('webfactory/foo'));
    }

    /**
     * @test
     */
    public function addProjectAddsProject()
    {
        $this->packageVersion->addUsingProject(new Project('bar'));

        $this->assertTrue(count($this->packageVersion->getProjects()) > 0);
    }

    /**
     * @test
     */
    public function getVersionReturnsVersion()
    {
        $this->assertSame(self::version, $this->packageVersion->getPrettyVersion());
    }

    /**
     * @test
     */
    public function getPackageReturnsAssociatedPackage()
    {
        $packageName = $this->packageVersion->getPackage()->getName();

        $this->assertSame('webfactory/foo', $packageName);
    }

    /**
     * @test
     */
    public function getProjectsReturnsArrayOfProjects()
    {
        $this->packageVersion->addUsingProject(new Project('bar'));

        $this->assertInstanceOf(ArrayCollection::class, $this->packageVersion->getProjects());
    }
}

<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;

class PackageVersionTest extends \PHPUnit_Framework_TestCase
{
    const version = '1.0.0';

    /**
     * @var PackageVersion
     */
    private $packageVersion;

    protected function setUp()
    {
      $this->packageVersion = new PackageVersion(self::version, new Package('webfactory/foo'));
    }

    public function testAddProjectAddsProject()
    {
        $this->packageVersion->addProject(new Project('bar', 'vcs@vcshub.com:bar/foo.vcs'));

        $this->assertTrue(count($this->packageVersion->getProjects()) > 0);
    }

    public function testGetVersionReturnsVersion()
    {
        $this->assertSame(self::version, $this->packageVersion->getVersion());
    }

    public function testGetPackageReturnsAssociatedPackage()
    {
        $packageName = $this->packageVersion->getPackage()->getName();

        $this->assertSame('webfactory/foo', $packageName);
    }

    public function testGetProjectsReturnsArrayOfProjects()
    {
        $this->packageVersion->addProject(new Project('bar', 'vcs@vcshub.com:bar/foo.vcs'));

        $this->assertInternalType('array', $this->packageVersion->getProjects());
    }
}

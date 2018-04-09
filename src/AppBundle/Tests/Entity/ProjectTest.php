<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    const name = 'foo';

    /**
     * @var Project
     */
    private $project;

    protected function setUp()
    {
      $this->project = new Project(self::name);
    }

    public function testAddUsageAddsUsageToProjectAndProjectToPackageVersion()
    {
      $packageVersion = new PackageVersion('1.0.0', new Package('foo'));
      $this->project->addUsage($packageVersion);

      $this->assertTrue(count($this->project->getUsages()) > 0);
      $this->assertSame(
        self::name,
        $this->project->getUsages()[0]->getProjects()[0]->getName()
      );
    }
}

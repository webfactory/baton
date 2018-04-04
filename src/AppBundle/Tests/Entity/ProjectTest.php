<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    const name = 'foo';
    const vcsUrl = 'vcs@vcshub.com:bar/foo.vcs';

    /**
     * @var Project
     */
    private $project;

    protected function setUp()
    {
      $this->project = new Project(self::name, self::vcsUrl);
    }

    public function testAddUsageAddsUsageToProjectAndProjectToPackageVersion()
    {
      $packageVersion = new PackageVersion('1.0.0', new Package('webfactory/bar'));
      $this->project->addUsage($packageVersion);

      $this->assertTrue(count($this->project->getUsages()) > 0);
      $this->assertSame(
        self::vcsUrl,
        $this->project->getUsages()[0]->getProjects()[0]->getVcsUrl()
      );
    }
}

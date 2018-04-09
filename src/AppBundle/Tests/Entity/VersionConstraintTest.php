<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\VersionConstraint;

class VersionConstraintTest extends \PHPUnit_Framework_TestCase
{
    public function testVersionConstraintWithSmallerThanOperatorMatchesPackageVersionsWithSmallerVersionThanTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('<', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('3.0.0', new Package('foo'))
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(1, $matches);
        $this->assertSame('1.0.0', $matches[0]->getPrettyVersion());
    }

    public function testVersionConstraintWithSmallerEqualsOperatorMatchesPackageVersionsWithSmallerEqualVersionThanTheVersionConstraintsVersion()
    {
      $versionConstraint = new VersionConstraint('<=', '2.0.0');
      $packageVersions = [
        new PackageVersion('1.0.0', new Package('foo')),
        new PackageVersion('2.0.0', new Package('foo')),
        new PackageVersion('3.0.0', new Package('foo'))
      ];

      $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

      $this->assertCount(2, $matches);
      $this->assertSame('1.0.0', $matches[0]->getPrettyVersion());
      $this->assertSame('2.0.0', $matches[1]->getPrettyVersion());
    }

    public function testVersionConstraintWithLargerThanOperatorMatchesPackageVersionsWithLargerVersionThanTheVersionConstraintsVersion()
    {
      $versionConstraint = new VersionConstraint('>', '2.0.0');
      $packageVersions = [
        new PackageVersion('1.0.0', new Package('foo')),
        new PackageVersion('3.0.0', new Package('foo'))
      ];

      $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

      $this->assertCount(1, $matches);
      $this->assertSame('3.0.0', $matches[0]->getPrettyVersion());
    }

    public function testVersionConstraintWithLargerEqualsOperatorMatchesPackageVersionsWithLargerEqualVersionThanTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('>=', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('2.0.0', new Package('foo')),
          new PackageVersion('3.0.0', new Package('foo'))
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(2, $matches);
        $this->assertSame('2.0.0', $matches[0]->getPrettyVersion());
        $this->assertSame('3.0.0', $matches[1]->getPrettyVersion());
    }

    public function testVersionConstraintWithEqualsOperatorMatchesPackageVersionsWithEqualVersionAsTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('==', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('2.0.0', new Package('foo'))
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(1, $matches);
        $this->assertSame('2.0.0', $matches[0]->getPrettyVersion());
    }

    public function testVersionConstraintWithAllOperatorMatchesAllPackagesVersions()
    {
        $versionConstraint = new VersionConstraint('all', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('2.0.0', new Package('foo')),
          new PackageVersion('3.0.0', new Package('foo'))
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(3, $matches);
    }

    /**
     * @param VersionConstraint $versionConstraint
     * @param PackageVersion[] $packageVersions
     * @return PackageVersion[]
     */
    private function matchPackageVersions(VersionConstraint $versionConstraint, array $packageVersions)
    {
        $matches = [];
        foreach($packageVersions as $packageVersion) {
            if ($versionConstraint->matches($packageVersion)) {
              $matches[] = $packageVersion;
            }
        }
        return $matches;
    }
}

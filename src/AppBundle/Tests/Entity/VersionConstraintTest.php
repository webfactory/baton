<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\VersionConstraint;
use PHPUnit_Framework_TestCase;

class VersionConstraintTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function versionConstraintWithSmallerThanOperatorMatchesPackageVersionsWithSmallerVersionThanTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('<', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('3.0.0', new Package('foo')),
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(1, $matches);
        $this->assertSame('1.0.0', $matches[0]->getPrettyVersion());
    }

    /**
     * @test
     */
    public function versionConstraintWithSmallerEqualsOperatorMatchesPackageVersionsWithSmallerEqualVersionThanTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('<=', '2.0.0');
        $packageVersions = [
        new PackageVersion('1.0.0', new Package('foo')),
        new PackageVersion('2.0.0', new Package('foo')),
        new PackageVersion('3.0.0', new Package('foo')),
      ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(2, $matches);
        $this->assertSame('1.0.0', $matches[0]->getPrettyVersion());
        $this->assertSame('2.0.0', $matches[1]->getPrettyVersion());
    }

    /**
     * @test
     */
    public function versionConstraintWithLargerThanOperatorMatchesPackageVersionsWithLargerVersionThanTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('>', '2.0.0');
        $packageVersions = [
        new PackageVersion('1.0.0', new Package('foo')),
        new PackageVersion('3.0.0', new Package('foo')),
      ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(1, $matches);
        $this->assertSame('3.0.0', $matches[0]->getPrettyVersion());
    }

    /**
     * @test
     */
    public function versionConstraintWithLargerEqualsOperatorMatchesPackageVersionsWithLargerEqualVersionThanTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('>=', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('2.0.0', new Package('foo')),
          new PackageVersion('3.0.0', new Package('foo')),
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(2, $matches);
        $this->assertSame('2.0.0', $matches[0]->getPrettyVersion());
        $this->assertSame('3.0.0', $matches[1]->getPrettyVersion());
    }

    /**
     * @test
     */
    public function versionConstraintWithEqualsOperatorMatchesPackageVersionsWithEqualVersionAsTheVersionConstraintsVersion()
    {
        $versionConstraint = new VersionConstraint('==', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('2.0.0', new Package('foo')),
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(1, $matches);
        $this->assertSame('2.0.0', $matches[0]->getPrettyVersion());
    }

    /**
     * @test
     */
    public function versionConstraintWithAllOperatorMatchesAllPackagesVersions()
    {
        $versionConstraint = new VersionConstraint('all', '2.0.0');
        $packageVersions = [
          new PackageVersion('1.0.0', new Package('foo')),
          new PackageVersion('2.0.0', new Package('foo')),
          new PackageVersion('3.0.0', new Package('foo')),
        ];

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $this->assertCount(3, $matches);
    }

    /**
     * @param PackageVersion[] $packageVersions
     *
     * @return PackageVersion[]
     */
    private function matchPackageVersions(VersionConstraint $versionConstraint, array $packageVersions)
    {
        $matches = [];
        foreach ($packageVersions as $packageVersion) {
            if ($versionConstraint->matches($packageVersion)) {
                $matches[] = $packageVersion;
            }
        }

        return $matches;
    }
}

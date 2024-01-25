<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\VersionConstraint;
use Generator;
use PHPUnit\Framework\TestCase;

class VersionConstraintTest extends TestCase
{
    public static function provideCases(): Generator
    {
        yield ['<', '2.0.0', ['1.0.0'], ['3.0.0']];
        yield ['<', '2.2.0', ['2.1.0'], ['2.10.0']];
        yield ['<', '2.10.0', ['2.2.0'], ['2.11.0']];
        yield ['<=', '2.0.0', ['1.0.0', '2.0.0'], ['3.0.0']];
        yield ['<=', '2.2.0', ['2.1.0', '2.2.0'], ['2.10.0']];
        yield ['<=', '2.10.0', ['2.2.0', '2.10.0'], ['2.11.0']];
        yield ['>', '2.0.0', ['3.0.0'], ['1.0.0']];
        yield ['>', '2.2.0', ['2.10.0'], ['2.1.0']];
        yield ['>', '2.10.0', ['2.11.0'], ['2.2.0']];
        yield ['>=', '2.0.0', ['2.0.0', '3.0.0'], ['1.0.0']];
        yield ['>=', '2.2.0', ['2.10.0', '2.2.0'], ['2.1.0']];
        yield ['>=', '2.10.0', ['2.11.0', '2.10.0'], ['2.2.0']];
        yield ['==', '2.0.0', ['2.0.0'], ['1.0.0', '3.0.0']];
        yield ['all', '2.0.0', ['1.0.0', '2.0.0', '3.0.0'], []];
    }

    /**
     * @test
     *
     * @dataProvider provideCases
     */
    public function versionConstraintMatches(string $operator, string $version, array $expectedMatches, array $nonMatches)
    {
        $versionConstraint = new VersionConstraint($operator, $version);
        $packageVersions = array_merge(
            array_map(function ($v) { return new PackageVersion($v, new Package('foo')); }, $expectedMatches),
            array_map(function ($v) { return new PackageVersion($v, new Package('foo')); }, $nonMatches)
        );

        $matches = $this->matchPackageVersions($versionConstraint, $packageVersions);

        $matches = array_map(function ($match) {
            return $match->getPrettyVersion();
        }, $matches);

        foreach ($expectedMatches as $expected) {
            $this->assertContains($expected, $matches);
        }

        foreach ($nonMatches as $nonMatch) {
            $this->assertNotContains($nonMatch, $matches);
        }

        $this->assertCount(count($expectedMatches), $matches);
    }

    /**
     * @param PackageVersion[] $packageVersions
     *
     * @return PackageVersion[]
     */
    private function matchPackageVersions(VersionConstraint $versionConstraint, array $packageVersions): array
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

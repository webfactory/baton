<?php

namespace App\Tests\Entity;

use App\Entity\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    public const name = 'webfactory/bar';
    public const description = 'foo';

    /**
     * @var Package
     */
    private $package;

    protected function setUp(): void
    {
        $this->package = new Package(self::name, self::description);
    }

    /**
     * @test
     */
    public function getNameReturnsName()
    {
        $this->assertSame(self::name, $this->package->getName());
    }

    /**
     * @test
     */
    public function getDescriptionReturnsDescription()
    {
        $this->assertSame(self::description, $this->package->getDescription());
    }

    /**
     * @test
     */
    public function getVersionReturnsSameInstanceOnRepeatedCallsWithSamePrettyVersionString()
    {
        $first = $this->package->getVersion('1.0.0');
        $second = $this->package->getVersion('1.0.0');

        $this->assertSame($first, $second);
    }

    /**
     * @test
     */
    public function getVersionCreatesOnlyOnePackageVersionForSamePrettyVersionString()
    {
        $this->package->getVersion('1.0.0');
        $this->package->getVersion('1.0.0');

        $this->assertCount(1, $this->package->getVersions());
    }
}

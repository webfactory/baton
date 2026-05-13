<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Package;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    public const name = 'webfactory/bar';
    public const description = 'foo';

    private Package $package;

    protected function setUp(): void
    {
        $this->package = new Package(self::name, self::description);
    }

    #[Test]
    public function getNameReturnsName(): void
    {
        $this->assertSame(self::name, $this->package->getName());
    }

    #[Test]
    public function getDescriptionReturnsDescription(): void
    {
        $this->assertSame(self::description, $this->package->getDescription());
    }

    #[Test]
    public function getVersionReturnsSameInstanceOnRepeatedCallsWithSamePrettyVersionString(): void
    {
        $first = $this->package->getVersion('1.0.0');
        $second = $this->package->getVersion('1.0.0');

        $this->assertSame($first, $second);
    }

    #[Test]
    public function getVersionCreatesOnlyOnePackageVersionForSamePrettyVersionString(): void
    {
        $this->package->getVersion('1.0.0');
        $this->package->getVersion('1.0.0');

        $this->assertCount(1, $this->package->getVersions());
    }
}

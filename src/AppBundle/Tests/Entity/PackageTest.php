<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
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
}

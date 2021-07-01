<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;
use PHPUnit_Framework_TestCase;

class PackageTest extends PHPUnit_Framework_TestCase
{
    const name = 'webfactory/bar';
    const description = 'foo';

    /**
     * @var Package
     */
    private $package;

    protected function setUp()
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

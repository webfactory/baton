<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Package;

class PackageTest extends \PHPUnit_Framework_TestCase
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

    public function testGetNameReturnsName()
    {
        $this->assertSame(self::name, $this->package->getName());
    }

    public function testGetDescriptionReturnsDescription()
    {
      $this->assertSame(self::description, $this->package->getDescription());
    }
}

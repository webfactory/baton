<?php

namespace AppBundle\Tests\Factory;

use AppBundle\Factory\VcsDriverFactory;
use Composer\Repository\Vcs\VcsDriverInterface;
use PHPUnit_Framework_TestCase;

class VcsDriverFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var VcsDriverFactory
     */
    private $vcsDriverFactory;

    protected function setUp()
    {
        $this->vcsDriverFactory = new VcsDriverFactory(null, 'bar');
    }

    /**
     * @test
     */
    public function getDriverReturnsInstanceOfVcsDriverInterface()
    {
        $driver = $this->vcsDriverFactory->getDriver('https://github.com/symfony/symfony');
        $this->assertInstanceOf(VcsDriverInterface::class, $driver);
    }
}

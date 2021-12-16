<?php

namespace AppBundle\Tests\Factory;

use AppBundle\Factory\VcsDriverFactory;
use Composer\Repository\Vcs\VcsDriverInterface;
use PHPUnit\Framework\TestCase;

class VcsDriverFactoryTest extends TestCase
{
    /**
     * @var VcsDriverFactory
     */
    private $vcsDriverFactory;

    protected function setUp(): void
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

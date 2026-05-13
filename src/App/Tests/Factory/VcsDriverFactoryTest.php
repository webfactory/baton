<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Factory\VcsDriverFactory;
use Composer\Repository\Vcs\VcsDriverInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VcsDriverFactoryTest extends TestCase
{
    private VcsDriverFactory $vcsDriverFactory;

    protected function setUp(): void
    {
        $this->vcsDriverFactory = new VcsDriverFactory(null);
    }

    #[Test]
    public function getDriverReturnsInstanceOfVcsDriverInterface(): void
    {
        $driver = $this->vcsDriverFactory->getDriver('https://github.com/symfony/symfony');
        $this->assertInstanceOf(VcsDriverInterface::class, $driver);
    }
}

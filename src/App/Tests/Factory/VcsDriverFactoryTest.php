<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Factory\VcsDriverFactory;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
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
    #[DoesNotPerformAssertions]
    public function getDriverCanBeCalled(): void
    {
        $this->vcsDriverFactory->getDriver('https://github.com/symfony/symfony');
    }
}

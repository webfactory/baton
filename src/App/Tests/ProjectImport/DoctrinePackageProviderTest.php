<?php

declare(strict_types=1);

namespace App\Tests\ProjectImport;

use App\Entity\Package;
use App\ProjectImport\DoctrinePackageProvider;
use App\Repository\PackageRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrinePackageProviderTest extends TestCase
{
    private PackageRepository&MockObject $packageRepository;

    protected function setUp(): void
    {
        $this->packageRepository = $this->createMock(PackageRepository::class);
    }

    #[Test]
    public function providePackageReturnsNewPackageObjectIfNoneFound(): void
    {
        $this->packageRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        $doctrinePackageProvider = new DoctrinePackageProvider($this->packageRepository);

        $package = $doctrinePackageProvider->providePackage('Foo');

        $this->assertInstanceOf(Package::class, $package);
        $this->assertSame('Foo', $package->getName());
    }

    #[Test]
    public function providePackageReturnsExistingPackageObjectIfFound(): void
    {
        $this->packageRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new Package('Bar'));
        $doctrinePackageProvider = new DoctrinePackageProvider($this->packageRepository);

        $package = $doctrinePackageProvider->providePackage('Bar');

        $this->assertInstanceOf(Package::class, $package);
        $this->assertSame('Bar', $package->getName());
    }
}

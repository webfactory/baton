<?php

namespace App\Tests\ProjectImport;

use App\Entity\Package;
use App\Entity\Repository\PackageRepository;
use App\ProjectImport\DoctrinePackageProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrinePackageProviderTest extends TestCase
{
    /**
     * @var PackageRepository|MockObject
     */
    private $packageRepository;

    protected function setUp(): void
    {
        $this->packageRepository = $this->createMock(PackageRepository::class);
    }

    /**
     * @test
     */
    public function providePackageReturnsNewPackageObjectIfNoneFound()
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

    /**
     * @test
     */
    public function providePackageReturnsExistingPackageObjectIfFound()
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

<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Entity\Package;
use AppBundle\Entity\Repository\PackageRepository;
use AppBundle\ProjectImport\DoctrinePackageProvider;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class DoctrinePackageProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PackageRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $packageRepository;

    protected function setUp()
    {
        $this->packageRepository = $this->getMock(PackageRepository::class, [], [], '', false);
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

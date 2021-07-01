<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Entity\Package;
use AppBundle\ProjectImport\ComposerPackageFetcher;
use AppBundle\ProjectImport\PackageProviderInterface;
use AppBundle\ProjectImport\PackageVersionFetcher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class PackageVersionFetcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PackageVersionFetcher
     */
    private $packageVersionFetcher;

    protected function setUp()
    {
        $this->packageVersionFetcher = new PackageVersionFetcher($this->getPackageProviderMock(), $this->getComposerPackageFetcherMock());
    }

    /**
     * @test
     */
    public function fetchPackagesReturnsArrayCollectionOfPackageVersion()
    {
        $packageVersions = $this->packageVersionFetcher->fetch('https://foo.git');

        $this->assertInstanceOf(ArrayCollection::class, $packageVersions);
        $this->assertCount(1, $packageVersions);
        $this->assertSame('v1.0.0', $packageVersions[0]->getPrettyVersion());
    }

    /**
     * @return PackageProviderInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPackageProviderMock()
    {
        $packageProviderMock = $this->getMock(PackageProviderInterface::class, [], [], '', false);
        $packageProviderMock->expects($this->once())
            ->method('providePackage')
            ->willReturn(new Package('Bar'));

        return $packageProviderMock;
    }

    /**
     * @return ComposerPackageFetcher|PHPUnit_Framework_MockObject_MockObject
     */
    private function getComposerPackageFetcherMock()
    {
        $composerPackageFetcherMock = $this->getMock(ComposerPackageFetcher::class, [], [], '', false);
        $composerPackageFetcherMock->expects($this->once())
            ->method('fetchPackages')
            ->willReturn([new \Composer\Package\Package('Foo', '1.0.0', 'v1.0.0')]);

        return $composerPackageFetcherMock;
    }
}

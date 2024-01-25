<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Entity\Package;
use AppBundle\ProjectImport\ComposerPackageFetcher;
use AppBundle\ProjectImport\PackageProviderInterface;
use AppBundle\ProjectImport\PackageVersionFetcher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PackageVersionFetcherTest extends TestCase
{
    /**
     * @var PackageVersionFetcher
     */
    private $packageVersionFetcher;

    protected function setUp(): void
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
     * @return PackageProviderInterface|MockObject
     */
    private function getPackageProviderMock()
    {
        $packageProviderMock = $this->createMock(PackageProviderInterface::class);
        $packageProviderMock->expects($this->once())
            ->method('providePackage')
            ->willReturn(new Package('Bar'));

        return $packageProviderMock;
    }

    /**
     * @return ComposerPackageFetcher|MockObject
     */
    private function getComposerPackageFetcherMock()
    {
        $composerPackageFetcherMock = $this->createMock(ComposerPackageFetcher::class);
        $composerPackageFetcherMock->expects($this->once())
            ->method('fetchPackages')
            ->willReturn([new \Composer\Package\Package('Foo', '1.0.0', 'v1.0.0')]);

        return $composerPackageFetcherMock;
    }
}

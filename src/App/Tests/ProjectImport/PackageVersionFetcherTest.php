<?php

declare(strict_types=1);

namespace App\Tests\ProjectImport;

use App\Entity\Package;
use App\ProjectImport\ComposerPackageFetcher;
use App\ProjectImport\PackageProviderInterface;
use App\ProjectImport\PackageVersionFetcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PackageVersionFetcherTest extends TestCase
{
    private PackageVersionFetcher $packageVersionFetcher;

    protected function setUp(): void
    {
        $this->packageVersionFetcher = new PackageVersionFetcher($this->getPackageProviderMock(), $this->getComposerPackageFetcherMock());
    }

    #[Test]
    public function fetchPackagesReturnsArrayCollectionOfPackageVersion(): void
    {
        $packageVersions = $this->packageVersionFetcher->fetch('https://foo.git');

        $this->assertCount(1, $packageVersions);
        $this->assertSame('v1.0.0', $packageVersions[0]->getPrettyVersion());
    }

    private function getPackageProviderMock(): PackageProviderInterface&MockObject
    {
        $packageProviderMock = $this->createMock(PackageProviderInterface::class);
        $packageProviderMock->expects($this->once())
            ->method('providePackage')
            ->willReturn(new Package('Bar'));

        return $packageProviderMock;
    }

    private function getComposerPackageFetcherMock(): ComposerPackageFetcher&MockObject
    {
        $composerPackageFetcherMock = $this->createMock(ComposerPackageFetcher::class);
        $composerPackageFetcherMock->expects($this->once())
            ->method('fetchPackages')
            ->willReturn([new \Composer\Package\Package('Foo', '1.0.0', 'v1.0.0')]);

        return $composerPackageFetcherMock;
    }
}

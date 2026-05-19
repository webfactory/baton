<?php

declare(strict_types=1);

namespace App\Tests\ProjectImport;

use App\Exception\ProjectHasNoComposerPackageUsageInfoException;
use App\ProjectImport\ComposerPackageFetcher;
use App\ProjectImport\LockFileFetcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ComposerPackageFetcherTest extends TestCase
{
    #[Test]
    public function fetchPackagesReturnsArrayOfComposerPackages(): void
    {
        $composerPackageFetcher = new ComposerPackageFetcher(
            $this->getLockFileFetcherMock(file_get_contents(__DIR__.'/composer_test.lock'))
        );

        $packages = $composerPackageFetcher->fetchPackages('https://foo.git');

        $this->assertNotEmpty($packages);
        $this->assertIsArray($packages);
    }

    #[Test]
    public function throwsExceptionIfLockContentsAreNull(): void
    {
        $composerPackageFetcher = new ComposerPackageFetcher(
            $this->getLockFileFetcherMock(null)
        );

        $this->expectException(ProjectHasNoComposerPackageUsageInfoException::class);

        $composerPackageFetcher->fetchPackages('https://foo.git');
    }

    private function getLockFileFetcherMock(?string $lockContentsToReturn): LockFileFetcher&MockObject
    {
        $projectProviderMock = $this->createMock(LockFileFetcher::class);
        $projectProviderMock->expects($this->once())
            ->method('getLockContents')
            ->willReturn($lockContentsToReturn);

        return $projectProviderMock;
    }
}

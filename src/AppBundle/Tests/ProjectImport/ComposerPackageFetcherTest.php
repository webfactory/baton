<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use AppBundle\ProjectImport\ComposerPackageFetcher;
use AppBundle\ProjectImport\LockFileFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ComposerPackageFetcherTest extends TestCase
{
    /**
     * @test
     */
    public function fetchPackagesReturnsArrayOfComposerPackages(): void
    {
        $composerPackageFetcher = new ComposerPackageFetcher(
            $this->getLockFileFetcherMock(file_get_contents(__DIR__.'/composer_test.lock'))
        );

        $packages = $composerPackageFetcher->fetchPackages('https://foo.git');

        $this->assertTrue(count($packages) > 0);
        $this->assertIsArray($packages);
    }

    /**
     * @test
     */
    public function throwsExceptionIfLockContentsAreNull()
    {
        $composerPackageFetcher = new ComposerPackageFetcher(
            $this->getLockFileFetcherMock(null)
        );

        $this->expectException(ProjectHasNoComposerPackageUsageInfoException::class);

        $composerPackageFetcher->fetchPackages('https://foo.git');
    }

    /**
     * @param string|null $lockContentsToReturn
     *
     * @return LockFileFetcher|MockObject
     */
    private function getLockFileFetcherMock($lockContentsToReturn)
    {
        $projectProviderMock = $this->createMock(LockFileFetcher::class);
        $projectProviderMock->expects($this->once())
            ->method('getLockContents')
            ->willReturn($lockContentsToReturn);

        return $projectProviderMock;
    }
}

<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use AppBundle\ProjectImport\ComposerPackageFetcher;
use AppBundle\ProjectImport\LockFileFetcher;

class ComposerPackageFetcherTest extends \PHPUnit_Framework_TestCase
{
    public function testFetchPackagesReturnsArrayOfComposerPackages()
    {
        $composerPackageFetcher = new ComposerPackageFetcher(
            $this->getLockFileFetcherMock(file_get_contents(__DIR__ . '/composer_test.lock'))
        );

        $packages = $composerPackageFetcher->fetchPackages("https://foo.git");

        $this->assertTrue(count($packages) > 0);
        $this->assertInternalType('array', $packages);
    }

    public function testThrowsExceptionIfLockContentsAreNull()
    {
        $composerPackageFetcher = new ComposerPackageFetcher(
            $this->getLockFileFetcherMock(null)
        );

        $this->setExpectedException(ProjectHasNoComposerPackageUsageInfoException::class);

        $composerPackageFetcher->fetchPackages("https://foo.git");
    }

    /**
     * @param string|null $lockContentsToReturn
     * @return LockFileFetcher|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getLockFileFetcherMock($lockContentsToReturn)
    {
        $projectProviderMock = $this->getMock(LockFileFetcher::class, [], [], '', false);
        $projectProviderMock->expects($this->once())
            ->method('getLockContents')
            ->willReturn($lockContentsToReturn);

        return $projectProviderMock;
    }
}

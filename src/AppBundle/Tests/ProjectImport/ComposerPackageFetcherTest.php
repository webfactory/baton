<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\ProjectImport\ComposerPackageFetcher;
use AppBundle\ProjectImport\LockFileFetcher;

class ComposerPackageFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ComposerPackageFetcher
     */
    private $composerPackageFetcher;

    protected function setUp()
    {
        $this->composerPackageFetcher= new ComposerPackageFetcher($this->getLockFileFetcherMock());
    }

    public function testFetchPackagesReturnsArrayOfComposerPackages()
    {
        $packages = $this->composerPackageFetcher->fetchPackages("https://foo.git");

        $this->assertTrue(count($packages) > 0);
        $this->assertInternalType('array', $packages);
    }

    /**
     * @return LockFileFetcher|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getLockFileFetcherMock()
    {
        $projectProviderMock = $this->getMock(LockFileFetcher::class, [], [], '', false);
        $projectProviderMock->expects($this->once())
            ->method('getLockContents')
            ->willReturn(file_get_contents(__DIR__ . '/composer_test.lock'));

        return $projectProviderMock;
    }
}

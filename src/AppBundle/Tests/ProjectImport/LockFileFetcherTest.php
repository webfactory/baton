<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Factory\VcsDriverFactory;
use AppBundle\ProjectImport\LockFileFetcher;
use Composer\Repository\Vcs\VcsDriver;

class LockFileFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LockFileFetcher
     */
    private $lockFileFetcher;

    protected function setUp()
    {
        $this->lockFileFetcher= new LockFileFetcher($this->getVcsDriverFactoryMock());
    }

    public function testGetLockContentsReturnsComposerLockContentsAsString()
    {
        $contents = $this->lockFileFetcher->getLockContents("https://foo.git");
        $composerLockHashFromTestFile = '00ff294db3665b98a4e585c174e10928';

        $this->assertContains($composerLockHashFromTestFile, $contents);
        $this->assertInternalType('string', $contents);
    }

    /**
     * @return VcsDriverFactory|\PHPUnit_Framework_MockObject_MockObject $vcsDriverFactory
     */
    private function getVcsDriverFactoryMock()
    {
        $vcsDriverMock = $this->getMockBuilder(VcsDriver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $vcsDriverMock->expects($this->once())
            ->method('getFileContent')
            ->willReturn(file_get_contents(__DIR__ . '/composer_test.lock'));

        $vcsDriverFactoryMock = $this->getMockBuilder(VcsDriverFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $vcsDriverFactoryMock->method('getDriver')->willReturn($vcsDriverMock);

        return $vcsDriverFactoryMock;
    }
}

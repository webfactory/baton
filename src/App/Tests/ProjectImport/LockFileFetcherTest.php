<?php

declare(strict_types=1);

namespace App\Tests\ProjectImport;

use App\Factory\VcsDriverFactory;
use App\ProjectImport\LockFileFetcher;
use Composer\Repository\Vcs\VcsDriver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class LockFileFetcherTest extends TestCase
{
    private LockFileFetcher $lockFileFetcher;

    protected function setUp(): void
    {
        $this->lockFileFetcher = new LockFileFetcher($this->getVcsDriverFactoryMock());
    }

    #[Test]
    public function getLockContentsReturnsComposerLockContentsAsString(): void
    {
        $contents = $this->lockFileFetcher->getLockContents('https://foo.git');
        $composerLockHashFromTestFile = '00ff294db3665b98a4e585c174e10928';

        $this->assertStringContainsString($composerLockHashFromTestFile, $contents);
        $this->assertIsString($contents);
    }

    private function getVcsDriverFactoryMock(): VcsDriverFactory&Stub
    {
        $vcsDriverMock = $this->getMockBuilder(VcsDriver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $vcsDriverMock->expects($this->once())
            ->method('getFileContent')
            ->willReturn(file_get_contents(__DIR__.'/composer_test.lock'));

        $vcsDriverFactoryStub = $this->createStub(VcsDriverFactory::class);
        $vcsDriverFactoryStub
            ->method('getDriver')
            ->willReturn($vcsDriverMock);

        return $vcsDriverFactoryStub;
    }
}

<?php

namespace AppBundle\Tests\Driver;

use AppBundle\Driver\KilnDriver;
use AppBundle\Exception\InsufficientVcsAccessException;
use Composer\Factory;
use Composer\IO\NullIO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class KilnDriverTest extends TestCase
{
    public const REPO_URL = 'https://webfactory.kilnhg.com/foo/bar';

    /**
     * @var KilnDriver|MockObject
     */
    private $driver = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->buildKilnDriverMock(
            self::REPO_URL,
            [1 => self::REPO_URL]
        );

        $this->driver->initialize();
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown(): void
    {
        $this->driver = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function initializeThrowsExceptionIfIoHasNoAuthentication()
    {
        $driver = $this->buildKilnDriverMock(self::REPO_URL, [1 => self::REPO_URL], false);

        $this->expectException(InsufficientVcsAccessException::class);

        $driver->initialize();
    }

    /**
     * @test
     */
    public function initializeThrowsExceptionIfRepositoryNotInAvailableRepositories()
    {
        $driver = $this->buildKilnDriverMock(self::REPO_URL, [1 => 'foo']);

        $this->expectException(RuntimeException::class);

        $driver->initialize();
    }

    /**
     * @test
     */
    public function getRepositoryUrl()
    {
        $this->assertSame(self::REPO_URL, $this->driver->getRepositoryUrl());
    }

    /**
     * @test
     */
    public function getUrl()
    {
        $this->assertSame(self::REPO_URL.'.git', $this->driver->getUrl());
    }

    /**
     * @test
     */
    public function getFileContentTriesToGetFileFromKilnApi()
    {
        $fileName = 'foo';
        $this->driver->expects($this->once())->method('getContents')
            ->with('https://webfactory.kilnhg.com/Api/1.0/Repo/1/Raw/File/'.bin2hex($fileName).'?token=baz');

        $this->driver->getFileContent($fileName, 'master');
    }

    /**
     * @test
     */
    public function supportsReturnsTrueForKilnUrls()
    {
        $this->assertTrue(KilnDriver::supports(new NullIO(), Factory::createConfig(), self::REPO_URL));
    }

    /**
     * @test
     */
    public function supportsReturnsFalseForNonKilnUrls()
    {
        $this->assertFalse(KilnDriver::supports(new NullIO(), Factory::createConfig(), 'https://github.com/foo'));
    }

    /**
     * @param string $repositoryUrl
     * @param array  $availableRepositories [repoId => url, ...]
     * @param bool   $hasAuthentication
     *
     * @return KilnDriver|MockObject
     */
    private function buildKilnDriverMock($repositoryUrl, $availableRepositories, $hasAuthentication = true)
    {
        $io = new NullIO();
        if ($hasAuthentication) {
            $io->setAuthentication('webfactory.kilnhg.com', 'baz', 'x-oauth-basic');
        }

        $driverMock = $this->getMockBuilder(KilnDriver::class)
          ->setConstructorArgs([['url' => $repositoryUrl], $io, Factory::createConfig()])
          ->setMethods(['fetchAvailableRepositories', 'getContents'])
          ->getMock();
        $driverMock->method('fetchAvailableRepositories')->willReturn($availableRepositories);
        $driverMock->method('getContents')->willReturn('foo');

        return $driverMock;
    }
}

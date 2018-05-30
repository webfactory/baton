<?php

namespace AppBundle\Tests\Driver;

use AppBundle\Driver\KilnDriver;
use AppBundle\Exception\InsufficientVcsAccessException;
use Composer\Factory;
use Composer\IO\NullIO;

class KilnDriverTest extends \PHPUnit_Framework_TestCase
{
    const REPO_URL = 'https://webfactory.kilnhg.com/foo/bar';

    /**
     * @var KilnDriver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $driver = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
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
    protected function tearDown()
    {
        $this->driver = null;
        parent::tearDown();
    }

    public function testInitializeThrowsExceptionIfIoHasNoAuthentication()
    {
        $driver = $this->buildKilnDriverMock(self::REPO_URL, [1 => self::REPO_URL], false);

        $this->setExpectedException(InsufficientVcsAccessException::class);

        $driver->initialize();
    }

    public function testInitializeThrowsExceptionIfRepositoryNotInAvailableRepositories()
    {
        $driver = $this->buildKilnDriverMock(self::REPO_URL, [1 => 'foo']);

        $this->setExpectedException(\RuntimeException::class);

        $driver->initialize();
    }

    public function testGetRepositoryUrl()
    {
        $this->assertSame(self::REPO_URL, $this->driver->getRepositoryUrl());
    }

    public function testGetUrl()
    {
        $this->assertSame(self::REPO_URL . '.git', $this->driver->getUrl());
    }

    public function testGetFileContentTriesToGetFileFromKilnApi()
    {
        $fileName = 'foo';
        $this->driver->expects($this->once())->method('getContents')
            ->with('https://webfactory.kilnhg.com/Api/1.0/Repo/1/Raw/File/' . bin2hex($fileName) . '?token=baz');

        $this->driver->getFileContent($fileName, 'master');
    }

    public function testSupportsReturnsTrueForKilnUrls()
    {
        $this->assertTrue(KilnDriver::supports(new NullIO(), Factory::createConfig(), self::REPO_URL));
    }

    public function testSupportsReturnsFalseForNonKilnUrls()
    {
        $this->assertFalse(KilnDriver::supports(new NullIO(), Factory::createConfig(), 'https://github.com/foo'));
    }

    /**
     * @param string $repositoryUrl
     * @param array $availableRepositories [repoId => url, ...]
     * @param bool $hasAuthentication
     * @return KilnDriver|\PHPUnit_Framework_MockObject_MockObject
     */
    private function buildKilnDriverMock($repositoryUrl , $availableRepositories, $hasAuthentication = true)
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

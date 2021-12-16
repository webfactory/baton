<?php

namespace AppBundle\Tests\ProjectImport;

use AppBundle\Exception\ProjectHasNoComposerPackageUsageInfoException;
use AppBundle\ProjectImport\LockFileParser;
use Composer\Package\Package;
use PHPUnit\Framework\TestCase;

class LockFileParserTest extends TestCase
{
    /**
     * @var string JSON
     */
    private $lockFileContents;

    protected function setUp(): void
    {
        $this->lockFileContents = file_get_contents(__DIR__.'/composer_test.lock');
    }

    /**
     * @test
     */
    public function getPackagesReturnsArrayOfComposerPackages()
    {
        $packages = LockFileParser::getPackages($this->lockFileContents);

        $this->assertIsArray($packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
        $this->assertTrue(count($packages) > 0);
    }

    /**
     * @test
     */
    public function throwsExceptionIfArrayKeyPackagesDoesntExist()
    {
        $this->expectException(ProjectHasNoComposerPackageUsageInfoException::class);

        LockFileParser::getPackages('{"_readme": ["bar"],"content-hash": "foo","aliases": []}');
    }

    /**
     * @test
     */
    public function throwsExceptionIfPackagesArrayInLockContentsIsEmpty()
    {
        $this->expectException(ProjectHasNoComposerPackageUsageInfoException::class);

        LockFileParser::getPackages('{"_readme": ["bar"],"content-hash": "foo","packages": [],"aliases": []}');
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\ProjectImport;

use App\Exception\ProjectHasNoComposerPackageUsageInfoException;
use App\ProjectImport\LockFileParser;
use Composer\Package\Package;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LockFileParserTest extends TestCase
{
    private string $lockFileContents;

    protected function setUp(): void
    {
        $this->lockFileContents = file_get_contents(__DIR__.'/composer_test.lock');
    }

    #[Test]
    public function getPackagesReturnsArrayOfComposerPackages(): void
    {
        $packages = LockFileParser::getPackages($this->lockFileContents);

        $this->assertIsArray($packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
        $this->assertNotEmpty($packages);
    }

    #[Test]
    public function throwsExceptionIfArrayKeyPackagesDoesntExist(): void
    {
        $this->expectException(ProjectHasNoComposerPackageUsageInfoException::class);

        LockFileParser::getPackages('{"_readme": ["bar"],"content-hash": "foo","aliases": []}');
    }

    #[Test]
    public function throwsExceptionIfPackagesArrayInLockContentsIsEmpty(): void
    {
        $this->expectException(ProjectHasNoComposerPackageUsageInfoException::class);

        LockFileParser::getPackages('{"_readme": ["bar"],"content-hash": "foo","packages": [],"aliases": []}');
    }
}

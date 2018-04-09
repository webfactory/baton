<?php

namespace AppBundle\Tests\ProjectImport;


use AppBundle\ProjectImport\LockFileParser;
use Composer\Package\Package;

class LockFileParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string JSON
     */
    private $lockFileContents;

    protected function setUp()
    {
        $this->lockFileContents = file_get_contents(__DIR__ . '/composer_test.lock');
    }

    public function testGetPackagesReturnsArrayOfComposerPackages()
    {
        $packages = LockFileParser::getPackages($this->lockFileContents);

        $this->assertInternalType('array', $packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
        $this->assertTrue(count($packages) > 0);
    }
}

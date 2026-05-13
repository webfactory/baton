<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Package;
use App\Entity\PackageVersion;
use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PackageVersionTest extends TestCase
{
    public const version = '1.0.0';

    private PackageVersion $packageVersion;

    protected function setUp(): void
    {
        $this->packageVersion = new PackageVersion(self::version, new Package('webfactory/foo'));
    }

    #[Test]
    public function addProjectAddsProject(): void
    {
        $this->packageVersion->addUsingProject(new Project('bar'));

        $this->assertNotEmpty($this->packageVersion->getProjects());
    }

    #[Test]
    public function getVersionReturnsVersion(): void
    {
        $this->assertSame(self::version, $this->packageVersion->getPrettyVersion());
    }

    #[Test]
    public function getPackageReturnsAssociatedPackage(): void
    {
        $packageName = $this->packageVersion->getPackage()->getName();

        $this->assertSame('webfactory/foo', $packageName);
    }

    #[Test]
    public function getProjectsReturnsArrayOfProjects(): void
    {
        $this->packageVersion->addUsingProject(new Project('bar'));

        $this->assertInstanceOf(ArrayCollection::class, $this->packageVersion->getProjects());
    }
}

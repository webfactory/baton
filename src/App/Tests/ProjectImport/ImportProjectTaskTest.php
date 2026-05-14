<?php

declare(strict_types=1);

namespace App\Tests\Task;

use App\Factory\VcsDriverFactory;
use App\ProjectImport\ImportProjectTask;
use App\ProjectImport\PackageVersionFetcher;
use App\ProjectImport\ProjectProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportProjectTaskTest extends KernelTestCase
{
    private ImportProjectTask $importProjectTask;

    private PackageVersionFetcher&Stub $packageVersionFetcher;
    private VcsDriverFactory&Stub $vcsDriverFactory;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->vcsDriverFactory = $this->createStub(VcsDriverFactory::class);
        $this->packageVersionFetcher = $this->createStub(PackageVersionFetcher::class);
        $this->importProjectTask = new ImportProjectTask(
            self::getContainer()->get(EntityManagerInterface::class),
            self::getContainer()->get(ProjectProviderInterface::class),
            $this->packageVersionFetcher,
            $this->vcsDriverFactory,
            new NullLogger()
        );
    }

    protected function tearDown(): void
    {
        // Symfony's ErrorHandler::register() adds an exception handler on kernel boot in debug mode.
        // Restore it here so PHPUnit 11's exception-handler-depth check passes.
        restore_exception_handler();
        parent::tearDown();
    }

    #[Test]
    public function import(): void
    {
        $this->packageVersionFetcher
            ->method('fetch')
            ->willReturn(new ArrayCollection());

        $this->assertTrue($this->importProjectTask->run('https://foo.git'));
    }
}

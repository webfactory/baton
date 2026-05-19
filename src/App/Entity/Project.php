<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'Project')]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private readonly string $name;

    #[ORM\Column(name: 'vcsUrl')]
    private ?string $vcsUrl = null;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $archived = false;

    #[ORM\ManyToMany(targetEntity: PackageVersion::class, mappedBy: 'projects', cascade: ['persist'])]
    private Collection $packageVersions;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->packageVersions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVcsUrl(): ?string
    {
        return $this->vcsUrl;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPackageVersions(): Collection
    {
        return $this->packageVersions;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setVcsUrl(string $vcsUrl): void
    {
        $this->vcsUrl = $vcsUrl;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function setUsedPackageVersions(ArrayCollection $importedPackageVersions): void
    {
        foreach ($this->packageVersions as $key => $packageVersion) {
            $packageVersionIsGoingToStay = false;

            foreach ($importedPackageVersions as $importedPackageVersion) {
                if ($packageVersion->equals($importedPackageVersion)) {
                    $packageVersionIsGoingToStay = true;
                    break;
                }
            }

            if (!$packageVersionIsGoingToStay) {
                $packageVersion->removeUsingProject($this);
                $this->packageVersions->remove($key);
            }
        }

        foreach ($importedPackageVersions as $importedPackageVersion) {
            $importedPackageVersionIsAlreadyInUse = false;

            foreach ($this->packageVersions as $packageVersion) {
                if ($packageVersion->equals($importedPackageVersion)) {
                    $importedPackageVersionIsAlreadyInUse = true;
                    break;
                }
            }

            if (!$importedPackageVersionIsAlreadyInUse) {
                $this->packageVersions->add($importedPackageVersion);
                $importedPackageVersion->addUsingProject($this);
            }
        }
    }

    public function addUsage(PackageVersion $packageVersion): void
    {
        // TODO: remove addUsage, then also remove from PackageVersion::addUsingProject()?
        if ($this->packageVersions->contains($packageVersion)) {
            return;
        }
        $this->packageVersions->add($packageVersion);
        $packageVersion->addUsingProject($this);
    }

    public function removeUsage(PackageVersion $usage): void
    {
        // TODO: remove removeUsage, then also remove from PackageVersion::removeUsingProject()?
        if (!$this->packageVersions->contains($usage)) {
            return;
        }
        $this->packageVersions->removeElement($usage);
        $usage->removeUsingProject($this);
    }
}

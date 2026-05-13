<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Describes the mapping for Composer Packages that are used in projects.
 */
#[ORM\Entity(repositoryClass: PackageRepository::class)]
#[ORM\Table(name: 'Package')]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private readonly string $name;

    #[ORM\Column(nullable: true)]
    private ?string $description;

    #[ORM\OneToMany(targetEntity: PackageVersion::class, mappedBy: 'package', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $versions;

    public function __construct(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMatchingVersionsWithProjects(VersionConstraint $versionConstraint): Collection
    {
        return $this->versions->filter(
            fn ($packageVersion) => $versionConstraint->matches($packageVersion) && 0 !== $packageVersion->getProjects()->count()
        );
    }

    public function getVersion(string $prettyVersionString): PackageVersion
    {
        $packageVersion = $this->versions->filter(
            fn ($packageVersion) => $packageVersion->getPrettyVersion() === $prettyVersionString
        );

        if ($packageVersion->isEmpty()) {
            $newVersion = new PackageVersion($prettyVersionString, $this);
            $this->versions->add($newVersion);

            return $newVersion;
        }

        return $packageVersion->first();
    }

    public function getVersions(): Collection
    {
        return $this->versions;
    }
}

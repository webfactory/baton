<?php

declare(strict_types=1);

namespace App\Entity;

use Composer\Semver\VersionParser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Describes a Composer package used in a specific version number.
 */
#[ORM\Entity]
#[ORM\Table(name: 'PackageVersion', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_package_version', columns: ['package_id', 'prettyVersion']),
])]
class PackageVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'prettyVersion')]
    private readonly string $prettyVersion;

    #[ORM\ManyToOne(targetEntity: Package::class, cascade: ['persist'], inversedBy: 'versions')]
    private readonly Package $package;

    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'usages')]
    #[ORM\JoinTable(name: 'packageversion_project',
        joinColumns: [new ORM\JoinColumn(name: 'packageversion_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id')]
    )]
    private Collection $projects;

    public function __construct(string $prettyVersion, Package $package)
    {
        $this->prettyVersion = $prettyVersion;
        $this->package = $package;
        $this->projects = new ArrayCollection();
    }

    public function addUsingProject(Project $project): void
    {
        if ($this->projects->contains($project)) {
            return;
        }
        $this->projects->add($project);
        $project->addUsage($this);
    }

    public function removeUsingProject(Project $project): void
    {
        if (!$this->projects->contains($project)) {
            return;
        }
        $this->projects->removeElement($project);
        $project->removeUsage($this);
    }

    public function equals(PackageVersion $packageVersion): bool
    {
        return $this->package->getId() === $packageVersion->getPackage()->getId()
            && $this->prettyVersion === $packageVersion->getPrettyVersion();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrettyVersion(): string
    {
        return $this->prettyVersion;
    }

    public function getNormalizedVersion(): string
    {
        return new VersionParser()->normalize($this->getPrettyVersion());
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }
}

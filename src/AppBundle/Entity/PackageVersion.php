<?php

namespace AppBundle\Entity;

use Composer\Semver\VersionParser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Describes a Composer package used in a specific version number.
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uniq_package_version", columns={"package_id", "prettyVersion"})})
 */
class PackageVersion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $prettyVersion;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="Package",
     *      inversedBy="versions",
     *      cascade="persist"
     * )
     *
     * @var Package
     */
    private $package;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="Project",
     *      inversedBy="usages"
     * )
     *
     * @var Collection|Project[]
     */
    private $projects;

    /**
     * @param string $prettyVersion
     */
    public function __construct($prettyVersion, Package $package)
    {
        $this->prettyVersion = $prettyVersion;
        $this->package = $package;
        $this->projects = new ArrayCollection();
    }

    public function addUsingProject(Project $project)
    {
        if ($this->projects->contains($project)) {
            return;
        }
        $this->projects->add($project);
        $project->addUsage($this);
    }

    public function removeUsingProject(Project $project)
    {
        if (!$this->projects->contains($project)) {
            return;
        }
        $this->projects->removeElement($project);
        $project->removeUsage($this);
    }

    /**
     * @return bool
     */
    public function equals(PackageVersion $packageVersion)
    {
        return $this->package->getId() === $packageVersion->getPackage()->getId()
            && $this->prettyVersion === $packageVersion->getPrettyVersion();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPrettyVersion()
    {
        return $this->prettyVersion;
    }

    /**
     * @return string
     */
    public function getNormalizedVersion()
    {
        return (new VersionParser())->normalize($this->getPrettyVersion());
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects()
    {
        return $this->projects;
    }
}

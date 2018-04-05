<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Describes a Composer package used in a specific version number.
 *
 * @ORM\Entity
 */
final class PackageVersion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $version;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="Package",
     *      inversedBy="version",
     *      cascade="persist"
     * )
     * @var Package
     */
    private $package;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="Project",
     *      inversedBy="usages"
     * )
     * @var Project[]
     */
    private $projects;

    /**
     * @param string $version
     * @param Package $package
     */
    public function __construct($version, Package $package)
    {
        $this->version = $version;
        $this->package = $package;
    }

    public function addProject(Project $project)
    {
        $this->projects[] = $project;
    }

    public function removeProject(Project $project)
    {
        if (!$this->projects->contains($project)) {
            return;
        }
        $this->projects->removeElement($project);
        $project->removeUsage($this);
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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return Project[]
     */
    public function getProjects()
    {
        return $this->projects;
    }


}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PackageVersion
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
     *      inversedBy="version"
     * )
     * @var Package
     */
    private $package;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="ProjectRepository",
     *      inversedBy="usages"
     * )
     * @var ProjectRepository[]
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

    public function addProject(ProjectRepository $project)
    {
        $this->projects[] = $project;
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
     * @return ProjectRepository[]
     */
    public function getProjects()
    {
        return $this->projects;
    }


}

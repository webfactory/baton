<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProjectRepository
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
    private $name;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $vcsUrl;

    /**
     * @ORM\Column(type="string")
     * @var string|null
     */
    private $description;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="PackageVersion",
     *      mappedBy="projects",
     *     cascade="persist"
     * )
     *
     * @var PackageVersion[]
     */
    private $usages;

    /**
     * @param string $name
     * @param string $vcsUrl
     */
    public function __construct($name, $vcsUrl)
    {
        $this->name = $name;
        $this->vcsUrl = $vcsUrl;
    }

    public function addUsage(PackageVersion $package)
    {
        $package->addProject($this); // synchronously updating inverse side
        $this->usages[] = $package;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVcsUrl()
    {
        return $this->vcsUrl;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return PackageVersion[]
     */
    public function getUsages()
    {
        return $this->usages;
    }
}

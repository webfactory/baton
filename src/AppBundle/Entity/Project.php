<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ProjectRepository")
 */
class Project
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
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $vcsUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    public $archived = false;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="PackageVersion",
     *      mappedBy="projects",
     *      cascade="persist"
     * )
     *
     * @var Collection|PackageVersion[]
     */
    private $packageVersions;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->packageVersions = new ArrayCollection();
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
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Collection|PackageVersion[]
     */
    public function getPackageVersions()
    {
        return $this->packageVersions;
    }

    /**
     * @param string $vcsUrl
     */
    public function setVcsUrl($vcsUrl)
    {
        $this->vcsUrl = $vcsUrl;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function setUsedPackageVersions(ArrayCollection $importedPackageVersions)
    {
        foreach ($this->packageVersions as $packageVersion) {
            // TODO: could contains($usage) falsly return false when $packageVersions contains Proxies? See https://github.com/doctrine/doctrine2/issues/6127 Possible fix: PackageVersion::equals($pVersion)
            if (!$importedPackageVersions->contains($packageVersion)) {
                $packageVersion->removeUsingProject($this);
            }
        }

        $this->packageVersions = $importedPackageVersions;

        foreach ($this->packageVersions as $usage) {
            $usage->addUsingProject($this);
        }
    }

    public function addUsage(PackageVersion $packageVersion)
    {
        // TODO: remove addUsage, then also remove from PackageVersion::addUsingProject()?
        if ($this->packageVersions->contains($packageVersion)) {
            return;
        }
        $this->packageVersions->add($packageVersion);
        $packageVersion->addUsingProject($this);
    }

    public function removeUsage(PackageVersion $usage)
    {
        // TODO: remove removeUsage, then also remove from PackageVersion::removeUsingProject()?
        if (!$this->packageVersions->contains($usage)) {
            return;
        }
        $this->packageVersions->removeElement($usage);
        $usage->removeUsingProject($this);
    }
}

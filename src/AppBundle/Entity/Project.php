<?php

namespace AppBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Webfactory\SlugValidationBundle\Bridge\SluggableInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ProjectRepository")
 */
class Project implements SluggableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $vcsUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="PackageVersion",
     *      mappedBy="projects",
     *      cascade="persist"
     * )
     * @var Collection|PackageVersion[]
     */
    private $usages;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->usages = new ArrayCollection();
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
     * @return Collection|PackageVersion[]
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * Returns the slug for the entity.
     *
     * @return string|null
     */
    public function getSlug()
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->getName());
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

    /**
     * @param ArrayCollection $packageVersions
     */
    public function setUsedPackageVersions(ArrayCollection $packageVersions)
    {
        foreach($this->usages as $usage) {
            // TODO: could contains($usage) falsly return false when $packageVersions contains Proxies? See https://github.com/doctrine/doctrine2/issues/6127 Possible fix: PackageVersion::equals($pVersion)
            if (!$packageVersions->contains($usage)) {
                $usage->removeUsingProject($this);
            }
        }

        $this->usages = $packageVersions;

        foreach($this->usages as $usage) {
            $usage->addUsingProject($this);
        }
    }

    public function addUsage(PackageVersion $packageVersion)
    {
        // TODO: remove addUsage, then also remove from PackageVersion::addUsingProject()?
        if ($this->usages->contains($packageVersion)) {
            return;
        }
        $this->usages->add($packageVersion);
        $packageVersion->addUsingProject($this);
    }

    public function removeUsage(PackageVersion $usage)
    {
        // TODO: remove removeUsage, then also remove from PackageVersion::removeUsingProject()?
        if (!$this->usages->contains($usage)) {
            return;
        }
        $this->usages->removeElement($usage);
        $usage->removeUsingProject($this);
    }
}

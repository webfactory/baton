<?php

namespace AppBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Webfactory\SlugValidationBundle\Bridge\SluggableInterface;

/**
 * @ORM\Entity
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

    public function removeUsage(PackageVersion $usage)
    {
        if (!$this->usages->contains($usage)) {
            return;
        }
        $this->usages->removeElement($usage);
        $usage->removeProject($this);
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
}

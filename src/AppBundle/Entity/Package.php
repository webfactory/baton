<?php

namespace AppBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webfactory\SlugValidationBundle\Bridge\SluggableInterface;

/**
 * Describes the mapping for Composer Packages that are used in projects.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PackageRepository")
 */
class Package implements SluggableInterface
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
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\OneToMany(
     *      targetEntity="PackageVersion",
     *      mappedBy="package",
     *      cascade={"persist", "remove"}
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Collection|PackageVersion[]
     */
    private $versions;

    /**
     * @param string $name
     * @param string|null $description
     */
    public function __construct($name, $description = null)
    {
      $this->name = $name;
      $this->description = $description;
      $this->versions = new ArrayCollection();
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
     * @return null|string
     */
    public function getDescription()
    {
      return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param VersionConstraint $versionConstraint
     * @return Collection|PackageVersion[]
     */
    public function getMatchingVersionsWithProjects(VersionConstraint $versionConstraint)
    {
        return $this->versions->filter(
            function($packageVersion) use ($versionConstraint) {
                /** @var PackageVersion $packageVersion */
                return $versionConstraint->matches($packageVersion) && $packageVersion->getProjects()->count() !== 0;
            }
        );
    }

    /**
     * @return Collection|PackageVersion[]
     */
    public function getVersions()
    {
      return $this->versions;
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

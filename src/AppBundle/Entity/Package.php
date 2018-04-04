<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Describes the mapping for Composer Packages that are used in projects.
 *
 * @ORM\Entity
 */
class Package
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
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @ORM\OneToMany(
     *      targetEntity="PackageVersion",
     *      mappedBy="package",
     *      cascade="persist"
     * )
     *
     * @var PackageVersion[]
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
     * @return PackageVersion[]
     */
    public function getVersions()
    {
      return $this->versions;
    }
}

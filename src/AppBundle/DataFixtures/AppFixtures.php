<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 3; $i++) {
            $project = new Project(
                uniqid('', false)
            );

            $project->setUsedPackageVersions(
                new ArrayCollection(
                    new PackageVersion(
                        '1.0.0',
                        new Package(uniqid('webfactory/', false))
                    )
                )
            );

            $manager->persist($project);
        }

        $manager->flush();
    }
}

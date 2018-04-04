<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Package;
use AppBundle\Entity\PackageVersion;
use AppBundle\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $project = new Project(
                uniqid('', false),
                uniqid('vcs@vcshub.com:', false)
            );

            $project->addUsage(
                new PackageVersion(
                    '1.0.0',
                    new Package(uniqid('webfactory/', false))
                )
            );

            $manager->persist($project);
        }

        $manager->flush();
    }
}

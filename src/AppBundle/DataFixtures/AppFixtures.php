<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Package;
use AppBundle\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $versionStrings = ["1.0.0", "2.0.0", "3.0.0"];
        for ($i = 0; $i < 2; $i++) {
            $project = new Project(
                uniqid('', false)
            );
            $project->setVcsUrl('https://foo.git');

            $packages = [];
            for ($ii = 0; $ii < 5; $ii++) {
                $packages[] = new Package(uniqid('webfactory/', false));
            }


            $packageVersions = new ArrayCollection();
            foreach($packages as $package) {
                $packageVersions->add($package->getVersion(array_rand(array_flip($versionStrings), 1)));
            }
            $project->setUsedPackageVersions($packageVersions);

            $manager->persist($project);
        }

        $manager->flush();
    }
}

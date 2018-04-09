<?php

namespace AppBundle\ProjectImport;

use AppBundle\Entity\Project;

interface ProjectProviderInterface
{
    /**
     * @param string $name
     * @return Project
     */
    public function provideProject($name);
}

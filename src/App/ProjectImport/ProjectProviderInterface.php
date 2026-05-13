<?php

namespace App\ProjectImport;

use App\Entity\Project;

interface ProjectProviderInterface
{
    /**
     * @param string $name
     *
     * @return Project
     */
    public function provideProject($name);
}

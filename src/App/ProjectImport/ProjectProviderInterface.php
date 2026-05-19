<?php

declare(strict_types=1);

namespace App\ProjectImport;

use App\Entity\Project;

interface ProjectProviderInterface
{
    public function provideProject(string $name): Project;
}

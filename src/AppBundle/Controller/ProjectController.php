<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProjectController
{
    /**
     * @Route("/project/{slug}.{id}", name="projectDetail")
     * @ParamConverter("project", class="AppBundle:Project")
     * @Template()
     */
    public function detailAction(Project $project)
    {
        return ['project' => $project];
    }
}

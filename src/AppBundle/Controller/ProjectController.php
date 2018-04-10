<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProjectController
{
    /**
     * @Route(
     *     "/project/{name}",
     *     name="project",
     *     requirements={"name"=".+"}
     * )
     * @ParamConverter("project", class="AppBundle:Project", options={"repository_method" = "findOneByName"})
     * @Template()
     */
    public function detailAction(Project $project)
    {
        return ['project' => $project];
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Entity\Project;
use AppBundle\Entity\VersionConstraint;
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

    /**
     * @Route(
     *     "/project/search/{_format}/{packageSlug}.{id}/{operator}/{versionString}",
     *     defaults={"operator": "all", "versionString": "1.0.0"},
     *     requirements={
     *         "operator": "(==|>=|<=|>|<|all)",
     *          "_format": "json|html"
     *     },
     *     name="search-projects-with-package-matching-versionconstraint"
     * )
     * @ParamConverter("package", class="AppBundle:Package")
     * @Template()
     */
    public function searchResultsAction(Package $package, $operator, $versionString)
    {
        $versionConstraint = new VersionConstraint($operator, $versionString);

        return [
            'packageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
            'package' => $package,
            'versionConstraint' => $operator . ' ' . $versionString
        ];
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
    /**
     * @Route("/api/package/{id}/versions", name="api-package-versions")
     * @ParamConverter("package", class="AppBundle:Package")
     * @return JsonResponse
     */
    public function apiPackageVersionsAction(Package $package)
    {
        $versions = [];
        foreach($package->getVersions() as $version) {
            $versions[] = $version->getNormalizedVersion();
        }
        $versions = array_values(array_unique($versions));
        return new JsonResponse($versions);
    }

    /**
     * @Route("/api/projects/{packageSlug}.{id}/{operator}/{versionString}", name="api-projects-with-package-version")
     * @ParamConverter("package", class="AppBundle:Package")
     * @return JsonResponse
     */
    public function apiProjectsWithPackageVersionAction(Package $package, $operator, $versionString)
    {
        $matchedProjects = [];
        $projectVersions = [];
        foreach($package->getVersionsThatMatchVersionConstraint($operator, $versionString) as $packageVersion) {
            if(count($projects = $packageVersion->getProjects()) === 0) {
                continue;
            }
            foreach($projects as $project) {
                $projectVersions[$project->getId()] = $packageVersion->getVersion();
            }
            $matchedProjects = array_merge($projects->toArray(), $matchedProjects);
        }

        $projectsJson = '{"package": {"name": "' . $package->getName() . '", "id": "' . $package->getId() . '"}, "projects": [';
        /** @var Project $project */
        foreach($matchedProjects as $project) {
            $projectsJson .= '{"id": "' . $project->getId() . '", "name": "' . $project->getName() . '", "packageVersion": "' . $projectVersions[$project->getId()] . '"}';
            if ($project !== end($matchedProjects)) {
                $projectsJson .= ',';
            }
        }
        $projectsJson .= ']}';

        return new JsonResponse($projectsJson, 200, [], true);
    }
}

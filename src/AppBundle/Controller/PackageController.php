<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Package;
use AppBundle\Entity\VersionConstraint;
use Composer\Semver\VersionParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class PackageController
{
    /**
     * @Route(
     *     "/package/{name};{_format}",
     *     name="package",
     *     requirements={"name"="(?:[^;]*|$)", "_format": "json|html"},
     *     defaults={"_format"="html"}
     * )
     * @ParamConverter("package", class="AppBundle:Package", options={"repository_method" = "findOneByName"})
     * @Template()
     * @throws \InvalidArgumentException|\UnexpectedValueException
     */
    public function detailAction(Package $package, Request $request)
    {
        if ($request->attributes->get('_format') === "html") {
            return ['package' => $package];
        }

        $operator = $request->query->get('operator');
        $versionString = $request->query->get('versionString');

        if (!preg_match(VersionConstraint::VALID_OPERATORS, $operator)) {
            throw new \InvalidArgumentException('Operator query parameter must match ' . VersionConstraint::VALID_OPERATORS);
        }

        try {
            $normalizedVersionString = (new VersionParser())->normalize(
                $request->query->get('versionString')
            );
        } catch(\UnexpectedValueException $exception) {
            throw $exception;
        }

        $versionConstraint = new VersionConstraint($operator, $normalizedVersionString);

        return [
            'matchingPackageVersions' => $package->getMatchingVersionsWithProjects($versionConstraint),
            'package' => $package,
            'versionConstraint' => $operator . ' ' . $versionString
        ];
    }

    /**
     * @Route(
     *     "/package/{name};versions.{_format}",
     *     name="package-versions",
     *     requirements={"name"="[^;]*"},
     *     defaults={"_format"="json"}
     * )
     * @ParamConverter("package", class="AppBundle:Package", options={"repository_method" = "findOneByName"})
     * @Template()
     */
    public function versionsAction(Package $package)
    {
        return ['packageVersions' => $package->getVersions()];
    }
}

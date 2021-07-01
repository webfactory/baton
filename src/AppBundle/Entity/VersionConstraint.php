<?php

namespace AppBundle\Entity;

use Composer\Semver\VersionParser;
use InvalidArgumentException;

class VersionConstraint
{
    const VALID_OPERATORS = '(==|>=|<=|>|<|all)';

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $normalizedVersionString;

    /**
     * @param string $operator
     * @param string $versionString e.g. 1.0.0
     *
     * @throws InvalidArgumentException
     */
    public function __construct($operator, $versionString)
    {
        if (!preg_match(self::VALID_OPERATORS, $operator)) {
            throw new InvalidArgumentException('The operator must match the regex expression '.self::VALID_OPERATORS);
        }

        $this->operator = $operator;
        $this->normalizedVersionString = (new VersionParser())->normalize($versionString);
    }

    /**
     * @return bool
     */
    public function matches(PackageVersion $packageVersion)
    {
        switch ($this->operator) {
            case 'all':
                return true;
                break;
            case '<':
                if ($packageVersion->getNormalizedVersion() < $this->normalizedVersionString) {
                    return true;
                }
                break;
            case '<=':
                if ($packageVersion->getNormalizedVersion() <= $this->normalizedVersionString) {
                    return true;
                }
                break;
            case '>':
                if ($packageVersion->getNormalizedVersion() > $this->normalizedVersionString) {
                    return true;
                }
                break;
            case '>=':
                if ($packageVersion->getNormalizedVersion() >= $this->normalizedVersionString) {
                    return true;
                }
                break;
            case '==':
                if ($packageVersion->getNormalizedVersion() == $this->normalizedVersionString) {
                    return true;
                }
                break;
        }

        return false;
    }
}

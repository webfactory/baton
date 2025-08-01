<?php

namespace AppBundle\Entity;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use InvalidArgumentException;

class VersionConstraint
{
    public const VALID_OPERATORS = '(==|>=|<=|>|<|all)';

    /**
     * @var string
     */
    private $operator;

    /**
     * @var ?string
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

        if ('all' !== $operator) {
            $this->normalizedVersionString = (new VersionParser())->normalize($versionString);
        }
    }

    /**
     * @return bool
     */
    public function matches(PackageVersion $packageVersion)
    {
        if ('all' === $this->operator) {
            return true;
        }

        return Comparator::compare($packageVersion->getNormalizedVersion(), $this->operator, $this->normalizedVersionString);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getNormalizedVersionString(): ?string
    {
        return $this->normalizedVersionString;
    }
}

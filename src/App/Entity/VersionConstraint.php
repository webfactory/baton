<?php

declare(strict_types=1);

namespace App\Entity;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use InvalidArgumentException;

class VersionConstraint
{
    public const VALID_OPERATORS = '(==|>=|<=|>|<|all)';

    private string $operator;

    private ?string $normalizedVersionString = null;

    public function __construct(string $operator, string $versionString)
    {
        if (!preg_match(self::VALID_OPERATORS, $operator)) {
            throw new InvalidArgumentException('The operator must match the regex expression '.self::VALID_OPERATORS);
        }

        $this->operator = $operator;

        if ('all' !== $operator) {
            $this->normalizedVersionString = (new VersionParser())->normalize($versionString);
        }
    }

    public function matches(PackageVersion $packageVersion): bool
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

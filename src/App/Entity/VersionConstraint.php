<?php

declare(strict_types=1);

namespace App\Entity;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use InvalidArgumentException;

class VersionConstraint
{
    public const string VALID_OPERATORS = '(==|>=|<=|>|<|all)';

    private readonly string $operator;

    private readonly ?string $normalizedVersionString;

    public function __construct(string $operator, ?string $versionString)
    {
        if (!preg_match(self::VALID_OPERATORS, $operator)) {
            throw new InvalidArgumentException('The operator must match the regex expression '.self::VALID_OPERATORS);
        }

        $this->operator = $operator;
        $this->normalizedVersionString = ('all' !== $operator)
            ? new VersionParser()->normalize($versionString)
            : null;
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

<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\ValueObject;

final class RuleClassWithFilePath
{
    /**
     * @var class-string
     * @readonly
     */
    private string $class;

    /**
     * @readonly
     */
    private string $path;

    /**
     * @readonly
     */
    private bool $isDeprecated;

    /**
     * @param class-string $class
     */
    public function __construct(string $class, string $path, bool $isDeprecated)
    {
        $this->class = $class;
        $this->path = $path;
        $this->isDeprecated = $isDeprecated;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isDeprecated(): bool
    {
        return $this->isDeprecated;
    }
}

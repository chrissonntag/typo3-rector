<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\ValueObject;

use Rector\RuleDoc\RuleDefinition;

final class DocumentedRule
{
    /**
     * @readonly
     */
    private string $ruleClass;

    /**
     * @readonly
     */
    private RuleDefinition $ruleDefinition;

    public function __construct(string $ruleClass, RuleDefinition $ruleDefinition)
    {
        $this->ruleClass = $ruleClass;
        $this->ruleDefinition = $ruleDefinition;
    }

    public function getRuleClass(): string
    {
        return $this->ruleClass;
    }

    public function getRuleShortClass(): string
    {
        return basename(str_replace('\\', '/', $this->ruleClass));
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return $this->ruleDefinition;
    }
}

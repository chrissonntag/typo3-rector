<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator;

use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use ReflectionClass;
use Ssch\TYPO3Rector\RuleDocGenerator\Exception\ShouldNotHappenException;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\DocumentedRule;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

final class RuleDefinitionsResolver
{
    /**
     * @param RuleClassWithFilePath[] $ruleClassWithFilePaths
     * @return DocumentedRule[]
     */
    public function resolveFromClassNames(array $ruleClassWithFilePaths): array
    {
        $documentedRules = [];

        foreach ($ruleClassWithFilePaths as $ruleClassWithFilePath) {
            $reflectionClass = new ReflectionClass($ruleClassWithFilePath->getClass());

            $documentedRule = $reflectionClass->newInstanceWithoutConstructor();
            if (! $documentedRule instanceof DocumentedRuleInterface) {
                throw new ShouldNotHappenException();
            }

            $ruleDefinition = $documentedRule->getRuleDefinition();
            $ruleDefinition->setRuleFilePath($ruleClassWithFilePath->getPath());

            $documentedRules[] = new DocumentedRule($ruleClassWithFilePath->getClass(), $ruleDefinition);
        }

        usort(
            $documentedRules,
            static fn (DocumentedRule $first, DocumentedRule $second): int => $first->getRuleShortClass() <=> $second->getRuleShortClass()
        );

        return $documentedRules;
    }
}

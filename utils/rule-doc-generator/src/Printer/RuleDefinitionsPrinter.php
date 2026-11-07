<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Printer;

use Nette\Utils\Strings;
use Ssch\TYPO3Rector\RuleDocGenerator\Text\KeywordHighlighter;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\DocumentedRule;
use Webmozart\Assert\Assert;

final class RuleDefinitionsPrinter
{
    /**
     * @readonly
     */
    private CodeSamplePrinter $codeSamplePrinter;

    /**
     * @readonly
     */
    private KeywordHighlighter $keywordHighlighter;

    public function __construct(CodeSamplePrinter $codeSamplePrinter, KeywordHighlighter $keywordHighlighter)
    {
        $this->codeSamplePrinter = $codeSamplePrinter;
        $this->keywordHighlighter = $keywordHighlighter;
    }

    /**
     * @param DocumentedRule[] $documentedRules
     * @return string[]
     */
    public function print(array $documentedRules, ?int $categorizeLevel): array
    {
        $ruleCount = count($documentedRules);

        $lines = [];
        $lines[] = sprintf('# %d Rules Overview', $ruleCount);

        if ($categorizeLevel === null || $categorizeLevel === 0) {
            return $this->printRuleDefinitions($documentedRules, $lines, '## ');
        }

        $documentedRulesByCategory = $this->groupDefinitionsByCategory($documentedRules, $categorizeLevel);
        $lines = array_merge($lines, $this->createCategoryMenu($documentedRulesByCategory));

        foreach ($documentedRulesByCategory as $category => $categoryDocumentedRules) {
            $lines[] = '## ' . $category;
            $lines = $this->printRuleDefinitions($categoryDocumentedRules, $lines, '### ');
        }

        return $lines;
    }

    /**
     * @param DocumentedRule[] $documentedRules
     * @param string[] $lines
     * @return string[]
     */
    private function printRuleDefinitions(array $documentedRules, array $lines, string $headlinePrefix): array
    {
        foreach ($documentedRules as $documentedRule) {
            $ruleDefinition = $documentedRule->getRuleDefinition();

            $lines[] = $headlinePrefix . $documentedRule->getRuleShortClass();

            $lines[] = $this->keywordHighlighter->highlight($ruleDefinition->getDescription());

            if ($ruleDefinition->isConfigurable()) {
                $lines[] = ':wrench: **configure it!**';
            }

            $lines[] = '- class: [`' . $documentedRule->getRuleClass() . '`](' . $ruleDefinition->getRuleFilePath() . ')';

            $codeSampleLines = $this->codeSamplePrinter->print($documentedRule);
            $lines = array_merge($lines, $codeSampleLines);
        }

        return $lines;
    }

    /**
     * @param DocumentedRule[] $documentedRules
     * @return array<string, DocumentedRule[]>
     */
    private function groupDefinitionsByCategory(array $documentedRules, int $categorizeLevel): array
    {
        $documentedRulesByCategory = [];
        foreach ($documentedRules as $documentedRule) {
            $category = $this->resolveCategory($documentedRule, $categorizeLevel);
            $documentedRulesByCategory[$category][] = $documentedRule;
        }

        ksort($documentedRulesByCategory);

        return $documentedRulesByCategory;
    }

    /**
     * @param array<string, DocumentedRule[]> $documentedRulesByCategory
     * @return string[]
     */
    private function createCategoryMenu(array $documentedRulesByCategory): array
    {
        $lines = [];
        $lines[] = '<br>';
        $lines[] = '## Categories';

        foreach ($documentedRulesByCategory as $category => $documentedRules) {
            $categoryLink = strtolower(Strings::webalize($category));
            $lines[] = sprintf('- [%s](#%s) (%d)', $category, $categoryLink, count($documentedRules));
        }

        $lines[] = '<br>';

        return $lines;
    }

    /**
     * The category is the namespace part $categorizeLevel levels above the class name,
     * e.g. level 2 turns Ssch\TYPO3Rector\TYPO314\v0\SomeRector into "TYPO314"
     */
    private function resolveCategory(DocumentedRule $documentedRule, int $categorizeLevel): string
    {
        $classNameParts = explode('\\', $documentedRule->getRuleClass());
        array_pop($classNameParts);

        $categoryName = null;
        while ($categorizeLevel > 0) {
            $categoryName = array_pop($classNameParts);
            --$categorizeLevel;
        }

        Assert::string($categoryName);

        return trim($categoryName, '_');
    }
}

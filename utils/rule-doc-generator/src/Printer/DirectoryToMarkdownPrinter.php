<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Printer;

use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Exception\ShouldNotHappenException;
use Ssch\TYPO3Rector\RuleDocGenerator\FileSystem\ClassByTypeFinder;
use Ssch\TYPO3Rector\RuleDocGenerator\RuleDefinitionsResolver;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\RuleClassWithFilePath;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DirectoryToMarkdownPrinter
{
    /**
     * @readonly
     */
    private ClassByTypeFinder $classByTypeFinder;

    /**
     * @readonly
     */
    private SymfonyStyle $symfonyStyle;

    /**
     * @readonly
     */
    private RuleDefinitionsResolver $ruleDefinitionsResolver;

    /**
     * @readonly
     */
    private RuleDefinitionsPrinter $ruleDefinitionsPrinter;

    public function __construct(
        ClassByTypeFinder $classByTypeFinder,
        SymfonyStyle $symfonyStyle,
        RuleDefinitionsResolver $ruleDefinitionsResolver,
        RuleDefinitionsPrinter $ruleDefinitionsPrinter
    ) {
        $this->classByTypeFinder = $classByTypeFinder;
        $this->symfonyStyle = $symfonyStyle;
        $this->ruleDefinitionsResolver = $ruleDefinitionsResolver;
        $this->ruleDefinitionsPrinter = $ruleDefinitionsPrinter;
    }

    /**
     * @param string[] $directories
     */
    public function print(string $workingDirectory, array $directories, ?int $categorizeLevel): string
    {
        $documentedRuleClasses = $this->classByTypeFinder->findByType(
            $workingDirectory,
            $directories,
            DocumentedRuleInterface::class
        );
        $documentedRuleClasses = $this->filterOutDeprecatedRuleClasses($documentedRuleClasses);

        if ($documentedRuleClasses === []) {
            throw new ShouldNotHappenException(sprintf(
                'No documented classes found in "%s" directories',
                implode('","', $directories)
            ));
        }

        $message = sprintf('Found %d documented rule classes', count($documentedRuleClasses));
        $this->symfonyStyle->note($message);

        $classes = array_map(
            static fn (RuleClassWithFilePath $ruleClassWithFilePath): string => $ruleClassWithFilePath->getClass(),
            $documentedRuleClasses
        );

        $this->symfonyStyle->listing($classes);

        $this->symfonyStyle->note('Resolving rule definitions');

        $documentedRules = $this->ruleDefinitionsResolver->resolveFromClassNames($documentedRuleClasses);

        $this->symfonyStyle->note('Printing rule definitions');
        $markdownLines = $this->ruleDefinitionsPrinter->print($documentedRules, $categorizeLevel);

        $fileContent = '';
        foreach ($markdownLines as $markdownLine) {
            $fileContent .= trim($markdownLine) . PHP_EOL . PHP_EOL;
        }

        return rtrim($fileContent) . PHP_EOL;
    }

    /**
     * @param RuleClassWithFilePath[] $ruleClassWithFilePaths
     * @return RuleClassWithFilePath[]
     */
    private function filterOutDeprecatedRuleClasses(array $ruleClassWithFilePaths): array
    {
        return array_values(array_filter(
            $ruleClassWithFilePaths,
            static fn (RuleClassWithFilePath $ruleClassWithFilePath): bool => ! $ruleClassWithFilePath->isDeprecated()
        ));
    }
}

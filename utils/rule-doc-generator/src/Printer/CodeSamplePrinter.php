<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Printer;

use Rector\RuleDoc\CodeSample\ConfiguredCodeSample;
use Rector\RuleDoc\Contract\CodeSampleInterface;
use Rector\RuleDoc\Contract\ConfigurableRuleInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Exception\ConfigurationBoundException;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\DocumentedRule;

final class CodeSamplePrinter
{
    /**
     * @var iterable<RuleCodeSamplePrinterInterface>
     * @readonly
     */
    private iterable $ruleCodeSamplePrinters;

    /**
     * @param iterable<RuleCodeSamplePrinterInterface> $ruleCodeSamplePrinters
     */
    public function __construct(iterable $ruleCodeSamplePrinters)
    {
        $this->ruleCodeSamplePrinters = $ruleCodeSamplePrinters;
    }

    /**
     * @return string[]
     */
    public function print(DocumentedRule $documentedRule): array
    {
        $lines = [];

        foreach ($documentedRule->getRuleDefinition()->getCodeSamples() as $codeSample) {
            $this->ensureConfigureRuleBoundsConfiguredCodeSample($codeSample, $documentedRule);

            foreach ($this->ruleCodeSamplePrinters as $ruleCodeSamplePrinter) {
                if (! $ruleCodeSamplePrinter->isMatch($documentedRule->getRuleClass())) {
                    continue;
                }

                $newLines = $ruleCodeSamplePrinter->print($codeSample, $documentedRule);
                $lines = array_merge($lines, $newLines);
                break;
            }

            $lines[] = '<br>';
        }

        return $lines;
    }

    private function ensureConfigureRuleBoundsConfiguredCodeSample(
        CodeSampleInterface $codeSample,
        DocumentedRule $documentedRule
    ): void {
        if ($codeSample instanceof ConfiguredCodeSample) {
            if (is_a($documentedRule->getRuleClass(), ConfigurableRuleInterface::class, true)) {
                return;
            }

            $errorMessage = sprintf(
                'The "%s" rule has configure code sample and must implements "%s" interface',
                $documentedRule->getRuleClass(),
                ConfigurableRuleInterface::class
            );
            throw new ConfigurationBoundException($errorMessage);
        }

        if (! is_a($documentedRule->getRuleClass(), ConfigurableRuleInterface::class, true)) {
            return;
        }

        $errorMessage = sprintf(
            'The "%s" rule implements "%s" and code sample must be "%s"',
            $documentedRule->getRuleClass(),
            ConfigurableRuleInterface::class,
            ConfiguredCodeSample::class
        );
        throw new ConfigurationBoundException($errorMessage);
    }
}

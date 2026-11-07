<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\CodeSamplePrinter;

use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Exception\ConfigurationBoundException;
use Ssch\TYPO3Rector\RuleDocGenerator\Printer\CodeSamplePrinter;
use Ssch\TYPO3Rector\RuleDocGenerator\Tests\AbstractTestCase;
use Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\CodeSamplePrinter\Source\ConfigurableRuleWithCodeSample;
use Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\CodeSamplePrinter\Source\RuleWithConfiguredCodeSample;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\DocumentedRule;

final class CodeSamplePrinterTest extends AbstractTestCase
{
    public function testConfiguredCodeSampleRequiresConfigurableRule(): void
    {
        $this->expectException(ConfigurationBoundException::class);

        $this->print(new RuleWithConfiguredCodeSample());
    }

    public function testConfigurableRuleRequiresConfiguredCodeSample(): void
    {
        $this->expectException(ConfigurationBoundException::class);

        $this->print(new ConfigurableRuleWithCodeSample());
    }

    private function print(DocumentedRuleInterface $documentedRule): void
    {
        $this->getService(CodeSamplePrinter::class)->print(
            new DocumentedRule(get_class($documentedRule), $documentedRule->getRuleDefinition())
        );
    }
}

<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\CodeSamplePrinter\Source;

use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\Contract\ConfigurableRuleInterface;
use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use Rector\RuleDoc\RuleDefinition;

final class ConfigurableRuleWithCodeSample implements DocumentedRuleInterface, ConfigurableRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Configurable', [new CodeSample('old', 'new')]);
    }
}

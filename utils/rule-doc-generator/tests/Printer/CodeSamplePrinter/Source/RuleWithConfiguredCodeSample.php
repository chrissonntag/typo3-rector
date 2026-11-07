<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\CodeSamplePrinter\Source;

use Rector\RuleDoc\CodeSample\ConfiguredCodeSample;
use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use Rector\RuleDoc\RuleDefinition;

final class RuleWithConfiguredCodeSample implements DocumentedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Not configurable', [
            new ConfiguredCodeSample('old', 'new', [
                'OldClass' => 'NewClass',
            ])]);
    }
}

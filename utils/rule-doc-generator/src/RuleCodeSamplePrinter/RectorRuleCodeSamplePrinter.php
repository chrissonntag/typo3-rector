<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\RuleCodeSamplePrinter;

use Rector\Contract\Rector\RectorInterface;
use Rector\RuleDoc\Contract\CodeSampleInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Printer\DiffCodeSamplePrinter;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\DocumentedRule;

final class RectorRuleCodeSamplePrinter implements RuleCodeSamplePrinterInterface
{
    /**
     * @readonly
     */
    private DiffCodeSamplePrinter $diffCodeSamplePrinter;

    public function __construct(DiffCodeSamplePrinter $diffCodeSamplePrinter)
    {
        $this->diffCodeSamplePrinter = $diffCodeSamplePrinter;
    }

    public function isMatch(string $class): bool
    {
        return is_a($class, RectorInterface::class, true);
    }

    public function print(CodeSampleInterface $codeSample, DocumentedRule $documentedRule): array
    {
        return $this->diffCodeSamplePrinter->print($codeSample);
    }
}

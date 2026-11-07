<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Contract;

use Rector\RuleDoc\Contract\CodeSampleInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\DocumentedRule;

interface RuleCodeSamplePrinterInterface
{
    public function isMatch(string $class): bool;

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, DocumentedRule $documentedRule): array;
}

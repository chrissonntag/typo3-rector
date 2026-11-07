<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Printer;

use Rector\RuleDoc\Contract\CodeSampleInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Printer\Markdown\MarkdownDiffer;

final class DiffCodeSamplePrinter
{
    /**
     * @readonly
     */
    private MarkdownDiffer $markdownDiffer;

    public function __construct(MarkdownDiffer $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
    }

    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample): array
    {
        $diffCode = $this->markdownDiffer->diff($codeSample->getBadCode(), $codeSample->getGoodCode());
        return [$diffCode];
    }
}

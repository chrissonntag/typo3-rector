<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\DirectoryToMarkdownPrinter\Source\Rules\CategoryB\templates;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use Rector\RuleDoc\RuleDefinition;

final class TemplateRector extends AbstractRector implements DocumentedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rules in template directories are not documented', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$old = 1;
$same = 2;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$new = 1;
$same = 2;
CODE_SAMPLE
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        return null;
    }
}

<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\DirectoryToMarkdownPrinter\Source\Rules\CategoryB\v0;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Rector\RuleDoc\CodeSample\ConfiguredCodeSample;
use Rector\RuleDoc\Contract\DocumentedRuleInterface;
use Rector\RuleDoc\RuleDefinition;

final class ConfigurableRector extends AbstractRector implements ConfigurableRectorInterface, DocumentedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename the configured class', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
new OldClass();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
new NewClass();
CODE_SAMPLE
                ,
                [
                    'OldClass' => 'NewClass',
                ]
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

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
    }
}

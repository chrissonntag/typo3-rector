<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\DirectoryToMarkdownPrinter;

use Iterator;
use Ssch\TYPO3Rector\RuleDocGenerator\Printer\DirectoryToMarkdownPrinter;
use Ssch\TYPO3Rector\RuleDocGenerator\Tests\AbstractTestCase;

final class DirectoryToMarkdownPrinterTest extends AbstractTestCase
{
    private DirectoryToMarkdownPrinter $directoryToMarkdownPrinter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directoryToMarkdownPrinter = $this->getService(DirectoryToMarkdownPrinter::class);
    }

    /**
     * @dataProvider provideData
     */
    public function test(?int $categorizeLevel, string $expectedFile): void
    {
        $fileContent = $this->directoryToMarkdownPrinter->print(
            __DIR__,
            [__DIR__ . '/Source/Rules'],
            $categorizeLevel
        );

        self::assertStringEqualsFile($expectedFile, $fileContent);
    }

    /**
     * @return Iterator<string, array{int|null, string}>
     */
    public static function provideData(): Iterator
    {
        yield 'categorized' => [2, __DIR__ . '/Assertions/categorized.md'];
        yield 'not categorized' => [null, __DIR__ . '/Assertions/not_categorized.md'];
    }
}

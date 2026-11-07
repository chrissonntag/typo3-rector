<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Console\Command;

use Nette\Utils\FileSystem;
use Ssch\TYPO3Rector\RuleDocGenerator\Printer\DirectoryToMarkdownPrinter;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GenerateCommand extends Command
{
    /**
     * @readonly
     */
    private DirectoryToMarkdownPrinter $directoryToMarkdownPrinter;

    /**
     * @readonly
     */
    private SymfonyStyle $symfonyStyle;

    public function __construct(DirectoryToMarkdownPrinter $directoryToMarkdownPrinter, SymfonyStyle $symfonyStyle)
    {
        $this->directoryToMarkdownPrinter = $directoryToMarkdownPrinter;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate-rules-overview');
        $this->setDescription('Generates Markdown documentation based on documented rules found in directory');

        $this->addArgument(
            Option::PATHS,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory of your project'
        );

        $this->addOption(
            Option::OUTPUT_FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to output generated markdown file',
            getcwd() . '/docs/rules_overview.md'
        );

        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_REQUIRED, 'Group rules by namespace position');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument(Option::PATHS);

        $categorizeLevel = $input->getOption(Option::CATEGORIZE);
        if ($categorizeLevel !== null) {
            $categorizeLevel = (int) $categorizeLevel;
        }

        $outputFilePath = (string) $input->getOption(Option::OUTPUT_FILE);

        $markdownFileDirectory = dirname($outputFilePath);

        if (! file_exists($markdownFileDirectory)) {
            FileSystem::createDir($markdownFileDirectory);
        }

        $markdownFileContent = $this->directoryToMarkdownPrinter->print(
            $markdownFileDirectory,
            $paths,
            $categorizeLevel
        );

        FileSystem::write($outputFilePath, $markdownFileContent);

        $this->symfonyStyle->success(sprintf('File "%s" was created', $outputFilePath));

        return self::SUCCESS;
    }
}

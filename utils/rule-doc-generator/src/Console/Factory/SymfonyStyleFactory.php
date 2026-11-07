<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Console\Factory;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SymfonyStyleFactory
{
    public static function create(): SymfonyStyle
    {
        if (! isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }

        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        if (self::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return new SymfonyStyle($argvInput, $consoleOutput);
    }

    private static function isPHPUnitRun(): bool
    {
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}

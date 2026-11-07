<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use Ssch\TYPO3Rector\RuleDocGenerator\Console\Factory\SymfonyStyleFactory;
use Ssch\TYPO3Rector\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Ssch\TYPO3Rector\RuleDocGenerator\Differ\DifferFactory;
use Ssch\TYPO3Rector\RuleDocGenerator\Printer\CodeSamplePrinter;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->load('Ssch\\TYPO3Rector\\RuleDocGenerator\\', __DIR__ . '/../src/')
        ->exclude([__DIR__ . '/../src/ValueObject']);
    $services->set(Differ::class)->factory([service(DifferFactory::class), 'create']);

    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);

    $services->set(CodeSamplePrinter::class)->arg(
        '$ruleCodeSamplePrinters',
        tagged_iterator('typo3_rector_rule_doc_generator.rule_code_sample_printer')
    );

    $containerBuilder->registerForAutoconfiguration(RuleCodeSamplePrinterInterface::class)->addTag(
        'typo3_rector_rule_doc_generator.rule_code_sample_printer'
    );
};

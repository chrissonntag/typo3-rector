<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Ssch\TYPO3Rector\Console\Application\Typo3RectorKernel;
use Ssch\TYPO3Rector\RuleDocGenerator\Exception\ShouldNotHappenException;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractTestCase extends TestCase
{
    private ?ContainerInterface $currentContainer = null;

    protected function setUp(): void
    {
        $this->boot();
    }

    protected function boot(): void
    {
        $typo3RectorKernel = new Typo3RectorKernel('test', true);
        $typo3RectorKernel->boot();
        $this->currentContainer = $typo3RectorKernel->getContainer();
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return T
     */
    protected function getService(string $type): object
    {
        if ($this->currentContainer === null) {
            throw new ShouldNotHappenException('First, create container with boot method');
        }

        $object = $this->currentContainer->get($type);
        if (! $object instanceof $type) {
            throw new ShouldNotHappenException(sprintf('Service "%s" was not found', $type));
        }

        return $object;
    }
}

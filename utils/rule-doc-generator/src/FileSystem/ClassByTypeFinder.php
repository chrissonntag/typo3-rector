<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\FileSystem;

use Composer\Autoload\ClassLoader;
use ReflectionClass;
use Ssch\TYPO3Rector\RuleDocGenerator\ValueObject\RuleClassWithFilePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ClassByTypeFinder
{
    /**
     * @var string[]
     */
    private const IGNORED_DIRECTORIES = ['*tests*', '*Fixture*', '*templates*'];

    /**
     * @param string[] $directories
     * @return RuleClassWithFilePath[]
     */
    public function findByType(string $workingDirectory, array $directories, string $type): array
    {
        $desiredClasses = [];
        foreach ($this->findClassesByFilePath($directories) as $filePath => $class) {
            if (! is_a($class, $type, true)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isAbstract()) {
                continue;
            }

            $relativeFilePath = $this->relativeFromDirectory($filePath, $workingDirectory);
            $isDeprecated = strpos((string) $reflectionClass->getDocComment(), '@deprecated') !== false;
            $desiredClasses[] = new RuleClassWithFilePath($class, $relativeFilePath, $isDeprecated);
        }

        usort(
            $desiredClasses,
            static fn (RuleClassWithFilePath $left, RuleClassWithFilePath $right): int => $left->getClass() <=> $right->getClass()
        );

        return $desiredClasses;
    }

    /**
     * @param string[] $directories
     * @return array<string, class-string>
     */
    private function findClassesByFilePath(array $directories): array
    {
        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in($directories)
            ->filter(fn (SplFileInfo $fileInfo): bool => ! $this->isInIgnoredDirectory($fileInfo->getRelativePath()));

        $classesByFilePath = [];
        foreach ($finder as $fileInfo) {
            $filePath = (string) $fileInfo->getRealPath();
            $class = $this->resolveClassFromPsr4($filePath);
            if ($class !== null) {
                $classesByFilePath[$filePath] = $class;
            }
        }

        return $classesByFilePath;
    }

    private function isInIgnoredDirectory(string $relativeDirectory): bool
    {
        foreach (explode('/', $relativeDirectory) as $directoryName) {
            foreach (self::IGNORED_DIRECTORIES as $ignoredDirectory) {
                if (fnmatch($ignoredDirectory, $directoryName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return class-string|null
     */
    private function resolveClassFromPsr4(string $filePath): ?string
    {
        foreach (ClassLoader::getRegisteredLoaders() as $classLoader) {
            foreach ($classLoader->getPrefixesPsr4() as $namespacePrefix => $prefixDirectories) {
                foreach ($prefixDirectories as $prefixDirectory) {
                    $prefixDirectory = rtrim((string) realpath($prefixDirectory), '/') . '/';
                    if (strncmp($filePath, $prefixDirectory, strlen($prefixDirectory)) !== 0) {
                        continue;
                    }

                    $relativeClass = substr($filePath, strlen($prefixDirectory), -strlen('.php'));
                    $class = $namespacePrefix . str_replace('/', '\\', $relativeClass);
                    if (class_exists($class) || interface_exists($class) || trait_exists($class)) {
                        return $class;
                    }
                }
            }
        }

        return null;
    }

    private function relativeFromDirectory(string $filePath, string $directory): string
    {
        $relativeFilePath = (new Filesystem())->makePathRelative(
            (string) realpath($filePath),
            (string) realpath($directory)
        );

        return rtrim($relativeFilePath, '/');
    }
}

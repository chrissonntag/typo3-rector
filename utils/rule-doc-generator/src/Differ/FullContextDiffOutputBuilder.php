<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\RuleDocGenerator\Differ;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOutputBuilderInterface;

/**
 * Renders a diff with full context and without any unified diff metadata, for all sebastian/diff
 * majors: UnifiedDiffOutputBuilder is gone in 9.0, and the StrictUnifiedDiffOutputBuilder options
 * to suppress the metadata only exist from 9.0 on.
 */
final class FullContextDiffOutputBuilder implements DiffOutputBuilderInterface
{
    /**
     * @param list<array{0: mixed, 1: int}> $diff
     */
    public function getDiff(array $diff): string
    {
        if ($diff === [] || ! $this->hasChanges($diff)) {
            return '';
        }

        $output = '';

        foreach ($this->insertEndOfFileMarkers($diff) as $entry) {
            $line = (string) $entry[0];

            switch ($entry[1]) {
                case Differ::ADDED:
                    $output .= '+' . $line;
                    break;
                case Differ::REMOVED:
                    $output .= '-' . $line;
                    break;
                case Differ::NO_LINE_END_EOF_WARNING:
                    $output .= "\n";
                    break;
                default:
                    $output .= ' ' . $line;
            }
        }

        $lastCharacter = substr($output, -1);
        if ($lastCharacter !== "\n" && $lastCharacter !== "\r") {
            $output .= "\n";
        }

        return $output;
    }

    /**
     * @param list<array{0: mixed, 1: int}> $diff
     */
    private function hasChanges(array $diff): bool
    {
        foreach ($diff as $entry) {
            if ($entry[1] === Differ::ADDED || $entry[1] === Differ::REMOVED) {
                return true;
            }
        }

        return false;
    }

    /**
     * Places the markers where UnifiedDiffOutputBuilder did: after an unchanged final entry,
     * otherwise after the last added and the last removed entry.
     *
     * @param list<array{0: mixed, 1: int}> $diff
     * @return list<array{0: mixed, 1: int}>
     */
    private function insertEndOfFileMarkers(array $diff): array
    {
        $marker = ["\n\\ No newline at end of file\n", Differ::NO_LINE_END_EOF_WARNING];
        $lastIndex = count($diff) - 1;

        if ($diff[$lastIndex][1] === Differ::OLD) {
            if (! $this->endsWithNewline($diff[$lastIndex][0])) {
                array_splice($diff, $lastIndex + 1, 0, [$marker]);
            }

            return array_values($diff);
        }

        $pending = [
            Differ::ADDED => true,
            Differ::REMOVED => true,
        ];

        for ($index = $lastIndex; $index >= 0; --$index) {
            if (! isset($pending[$diff[$index][1]])) {
                continue;
            }

            unset($pending[$diff[$index][1]]);

            if (! $this->endsWithNewline($diff[$index][0])) {
                array_splice($diff, $index + 1, 0, [$marker]);
            }

            if ($pending === []) {
                break;
            }
        }

        return array_values($diff);
    }

    /**
     * @param mixed $line
     */
    private function endsWithNewline($line): bool
    {
        return str_ends_with((string) $line, "\n");
    }
}

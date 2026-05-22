<?php

declare(strict_types=1);

namespace SanderMuller\PackageBoostPhp\Gitattributes;

/**
 * Maintains the `# >>> package-boost (managed) >>>` block in .gitattributes.
 *
 * Preserve-foreign-lines contract (per repo-init's
 * gitattributes-managed-block.md): when re-syncing, our canonical lines
 * are rewritten in stable order; any foreign lines inside the block
 * (added by other tools — repo-init in particular) are kept as-is.
 *
 * Outside the block: untouched. Users author their own attributes there
 * (`*.php text=auto eol=lf`, git-LFS pointers, etc.).
 */
final class ManagedBlockWriter
{
    public const string BLOCK_START = '# >>> package-boost (managed) >>>';

    public const string BLOCK_END = '# <<< package-boost (managed) <<<';

    /**
     * Canonical export-ignore entries package-boost-php manages. Exact-match
     * comparison against existing block lines determines "ours" vs "foreign".
     *
     * @var list<string>
     */
    private const array CANONICAL_LINES = [
        '.agents/                export-ignore',
        '.ai/                    export-ignore',
        '.claude/                export-ignore',
        '.cursor/                export-ignore',
        '.cursorrules            export-ignore',
        '.github/                export-ignore',
        '.junie/                 export-ignore',
        '.kiro/                  export-ignore',
        '.windsurfrules          export-ignore',
        'AGENTS.md               export-ignore',
        'CLAUDE.md               export-ignore',
        'GEMINI.md               export-ignore',
    ];

    /**
     * Given the existing .gitattributes content, return the synced version.
     * Caller is responsible for writing back to disk.
     */
    public function sync(string $original): string
    {
        if (! str_contains($original, self::BLOCK_START)) {
            return $this->appendNewBlock($original);
        }

        return $this->rewriteExistingBlock($original);
    }

    private function appendNewBlock(string $original): string
    {
        $block = $this->renderBlock([]);
        if ($original === '') {
            return $block;
        }

        if (! str_ends_with($original, "\n")) {
            $original .= "\n";
        }

        return $original . $block;
    }

    private function rewriteExistingBlock(string $original): string
    {
        $lines = explode("\n", $original);
        $startIdx = null;
        $endIdx = null;

        foreach ($lines as $i => $line) {
            if (trim($line) === self::BLOCK_START && $startIdx === null) {
                $startIdx = $i;
            } elseif (trim($line) === self::BLOCK_END && $startIdx !== null) {
                $endIdx = $i;
                break;
            }
        }

        if ($startIdx === null || $endIdx === null) {
            // Malformed (open without close, or close without open).
            // Treat as no block — append a fresh one.
            return $this->appendNewBlock($original);
        }

        $existingBlockLines = array_slice($lines, $startIdx + 1, $endIdx - $startIdx - 1);
        $foreign = $this->extractForeignLines($existingBlockLines);
        $newBlock = $this->renderBlock($foreign);

        $before = implode("\n", array_slice($lines, 0, $startIdx));
        $after = implode("\n", array_slice($lines, $endIdx + 1));

        return ($before === '' ? '' : $before . "\n")
            . $newBlock
            . ($after === '' ? '' : "\n" . $after);
    }

    /**
     * @param  list<string>  $existingBlockLines
     * @return list<string> Lines that are NOT in CANONICAL_LINES (foreign / external-tool additions).
     */
    private function extractForeignLines(array $existingBlockLines): array
    {
        $foreign = [];
        foreach ($existingBlockLines as $line) {
            $trimmed = rtrim($line);
            if ($trimmed === '') {
                continue;
            }

            if (in_array($trimmed, self::CANONICAL_LINES, true)) {
                continue;
            }

            $foreign[] = $trimmed;
        }

        return $foreign;
    }

    /**
     * @param  list<string>  $foreign  Foreign lines to preserve after the canonical ones.
     */
    private function renderBlock(array $foreign): string
    {
        $lines = [self::BLOCK_START];
        foreach (self::CANONICAL_LINES as $canonical) {
            $lines[] = $canonical;
        }

        foreach ($foreign as $foreignLine) {
            $lines[] = $foreignLine;
        }

        $lines[] = self::BLOCK_END;

        return implode("\n", $lines) . "\n";
    }
}

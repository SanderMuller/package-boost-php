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
 *
 * sync() is idempotent: sync(sync($x)) === sync($x) for any input,
 * including malformed blocks (a stray open marker with no close is
 * self-healed in place rather than accreting a second block). The file's
 * dominant line ending (LF or CRLF) is preserved.
 *
 * @internal Engine behind the `gitattributes` command. The marker FORMAT
 * (BLOCK_START / BLOCK_END) is a contract — see PUBLIC_API.md — but this
 * class is not: drive it via the command, do not import it.
 */
final class ManagedBlockWriter
{
    public const string BLOCK_START = '# >>> package-boost (managed) >>>';

    public const string BLOCK_END = '# <<< package-boost (managed) <<<';

    /**
     * Canonical export-ignore entries package-boost-php manages. A block
     * line is "ours" when its whitespace-normalized form matches one of
     * these; everything else inside the block is foreign and preserved.
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
     * Caller is responsible for writing back to disk. The original file's
     * dominant EOL is detected and re-applied to the result.
     */
    public function sync(string $original): string
    {
        $eol = $this->dominantEol($original);
        $result = $this->syncNormalized(str_replace("\r\n", "\n", $original));

        return $eol === "\r\n" ? str_replace("\n", "\r\n", $result) : $result;
    }

    /**
     * The file's prevailing line ending — CRLF only when it strictly
     * outnumbers bare LF, so a mostly-LF file with a stray CRLF line
     * settles to LF rather than churning every line to CRLF.
     */
    private function dominantEol(string $original): string
    {
        $crlf = substr_count($original, "\r\n");
        $bareLf = substr_count($original, "\n") - $crlf;

        return $crlf > $bareLf ? "\r\n" : "\n";
    }

    private function syncNormalized(string $original): string
    {
        return str_contains($original, self::BLOCK_START)
            ? $this->rewriteExistingBlock($original)
            : $this->appendNewBlock($original);
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
        [$startIdx, $endIdx] = $this->locateBlock($lines);

        if ($startIdx === null) {
            // BLOCK_START occurs only as a substring, never a standalone line.
            return $this->appendNewBlock($original);
        }

        // Self-heal: an open marker with no close runs to end-of-file, so a
        // previously-botched block (or a hand-truncated one) collapses into
        // a single clean block instead of accreting another.
        $interiorEnd = $endIdx ?? count($lines);
        $interior = array_slice($lines, $startIdx + 1, $interiorEnd - $startIdx - 1);
        $newBlock = $this->renderBlock($this->extractForeignLines($interior));

        $before = implode("\n", array_slice($lines, 0, $startIdx));
        $after = $endIdx === null ? '' : implode("\n", array_slice($lines, $endIdx + 1));

        return ($before === '' ? '' : $before . "\n")
            . $newBlock
            . ($after === '' ? '' : "\n" . $after);
    }

    /**
     * Locate the managed region: the first standalone BLOCK_START line and
     * the last BLOCK_END line after it (so stray markers from an earlier
     * botched sync collapse into one block). endIdx is null when no close
     * marker follows the open one.
     *
     * @param  list<string>  $lines
     * @return array{int|null, int|null}
     */
    private function locateBlock(array $lines): array
    {
        $startIdx = null;
        $endIdx = null;

        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            if ($trimmed === self::BLOCK_START) {
                $startIdx ??= $i;
            } elseif ($trimmed === self::BLOCK_END && $startIdx !== null && $i > $startIdx) {
                $endIdx = $i;
            }
        }

        return [$startIdx, $endIdx];
    }

    /**
     * @param  list<string>  $existingBlockLines
     * @return list<string> Foreign lines (not ours), de-duplicated, first-seen order, stray markers dropped.
     */
    private function extractForeignLines(array $existingBlockLines): array
    {
        $canonical = array_map($this->normalizeRule(...), self::CANONICAL_LINES);
        $foreign = [];
        $seen = [];

        foreach ($existingBlockLines as $line) {
            $trimmed = rtrim($line);
            if ($trimmed === '') {
                continue;
            }

            $bare = trim($line);
            if ($bare === self::BLOCK_START || $bare === self::BLOCK_END) {
                continue; // drop stray markers left by a botched block
            }

            if (in_array($this->normalizeRule($trimmed), $canonical, true)) {
                continue; // a (possibly whitespace-variant) canonical line — ours
            }

            if (isset($seen[$trimmed])) {
                continue; // de-duplicate repeated foreign lines
            }

            $seen[$trimmed] = true;
            $foreign[] = $trimmed;
        }

        return $foreign;
    }

    /**
     * Collapse internal runs of whitespace to a single space so that
     * differently-padded forms of the same rule compare equal.
     */
    private function normalizeRule(string $line): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($line));

        return $normalized ?? trim($line);
    }

    /**
     * @param  list<string>  $foreign  Foreign lines to preserve after the canonical ones.
     */
    private function renderBlock(array $foreign): string
    {
        $lines = [self::BLOCK_START, ...self::CANONICAL_LINES, ...$foreign, self::BLOCK_END];

        return implode("\n", $lines) . "\n";
    }
}

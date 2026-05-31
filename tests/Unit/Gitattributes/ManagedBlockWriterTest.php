<?php

declare(strict_types=1);

use SanderMuller\PackageBoostPhp\Gitattributes\ManagedBlockWriter;

it('appends a new block when none exists', function (): void {
    $writer = new ManagedBlockWriter();
    $result = $writer->sync('');

    expect($result)->toContain('# >>> package-boost (managed) >>>');
    expect($result)->toContain('# <<< package-boost (managed) <<<');
    expect($result)->toContain('CLAUDE.md');
    expect($result)->toContain('AGENTS.md');
});

it('appends a new block to an existing .gitattributes with user content', function (): void {
    $original = "*.php text=auto eol=lf\n*.css diff=css\n";
    $result = (new ManagedBlockWriter())->sync($original);

    expect($result)->toContain('*.php text=auto eol=lf');
    expect($result)->toContain('# >>> package-boost (managed) >>>');
    // User content stays before the block
    $phpPos = strpos($result, '*.php');
    $blockPos = strpos($result, '# >>>');
    expect($phpPos)->not->toBeFalse();
    expect($blockPos)->not->toBeFalse();
    expect((int) $phpPos)->toBeLessThan((int) $blockPos);
});

it('preserves foreign lines inside an existing block (repo-init contract)', function (): void {
    $original = <<<'TXT'
# >>> package-boost (managed) >>>
.ai/                    export-ignore
.claude/                export-ignore
CLAUDE.md               export-ignore
/.lpv                   export-ignore
/PUBLIC_API.md          export-ignore
/phpstan-baseline.neon  export-ignore
# <<< package-boost (managed) <<<

TXT;

    $result = (new ManagedBlockWriter())->sync($original);

    // Foreign lines preserved
    expect($result)->toContain('/.lpv                   export-ignore');
    expect($result)->toContain('/PUBLIC_API.md          export-ignore');
    expect($result)->toContain('/phpstan-baseline.neon  export-ignore');

    // Canonical lines present
    expect($result)->toContain('.ai/                    export-ignore');
    expect($result)->toContain('CLAUDE.md               export-ignore');
});

it('does not duplicate canonical lines on re-sync', function (): void {
    $writer = new ManagedBlockWriter();
    $first = $writer->sync('');
    $second = $writer->sync($first);

    expect(substr_count($second, 'CLAUDE.md               export-ignore'))->toBe(1);
    expect($second)->toBe($first);
});

it('leaves content outside the block untouched', function (): void {
    $original = <<<'TXT'
# Header
*.php text=auto eol=lf

# >>> package-boost (managed) >>>
.ai/                    export-ignore
# <<< package-boost (managed) <<<

# Footer
*.bin binary
TXT;

    $result = (new ManagedBlockWriter())->sync($original);

    expect($result)->toContain('# Header');
    expect($result)->toContain('*.php text=auto eol=lf');
    expect($result)->toContain('# Footer');
    expect($result)->toContain('*.bin binary');
});

it('self-heals a malformed block (open marker, no close) into one idempotent block', function (): void {
    $writer = new ManagedBlockWriter();
    $malformed = "# >>> package-boost (managed) >>>\n.ai/ export-ignore\n";
    $once = $writer->sync($malformed);

    // No second block accreted: exactly one start and one close marker.
    expect(substr_count($once, '# >>> package-boost (managed) >>>'))->toBe(1);
    expect(substr_count($once, '# <<< package-boost (managed) <<<'))->toBe(1);

    // Idempotent: re-syncing the healed output is a no-op (sync(sync(x)) === sync(x)).
    expect($writer->sync($once))->toBe($once);
});

it('preserves CRLF line endings and does not duplicate canonical lines', function (): void {
    $result = (new ManagedBlockWriter())->sync("*.php text=auto\r\n");

    // Every newline is CRLF — no bare LF survived.
    expect(preg_match('/(?<!\r)\n/', $result))->toBe(0);
    expect($result)->toContain("\r\n");
    expect(substr_count($result, 'CLAUDE.md'))->toBe(1);
});

it('settles a mostly-LF file with a stray CRLF line to LF (dominant EOL wins)', function (): void {
    // Two LF lines, one CRLF line — LF dominates, so no line churns to CRLF.
    $result = (new ManagedBlockWriter())->sync("*.php text=auto\n*.css diff=css\n*.bin binary\r\n");

    expect($result)->not->toContain("\r\n");
});

it('recognizes a whitespace variant of a canonical line and does not duplicate it', function (): void {
    // '.ai/ export-ignore' is the single-space variant of the aligned canonical entry.
    $original = "# >>> package-boost (managed) >>>\n.ai/ export-ignore\n# <<< package-boost (managed) <<<\n";
    $result = (new ManagedBlockWriter())->sync($original);

    // Exactly the 12 canonical export-ignore entries — the variant did not become a 13th.
    expect(substr_count($result, 'export-ignore'))->toBe(12);
    expect(substr_count($result, '.ai/'))->toBe(1);
});

it('de-duplicates repeated foreign lines inside the block', function (): void {
    $original = "# >>> package-boost (managed) >>>\n"
        . "/.lpv                   export-ignore\n"
        . "/.lpv                   export-ignore\n"
        . "# <<< package-boost (managed) <<<\n";
    $result = (new ManagedBlockWriter())->sync($original);

    expect(substr_count($result, '/.lpv'))->toBe(1);
});

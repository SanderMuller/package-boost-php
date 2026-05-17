<?php

declare(strict_types=1);

use SanderMuller\PackageBoostPhp\Gitattributes\ManagedBlockWriter;

it('appends a new block when none exists', function (): void {
    $writer = new ManagedBlockWriter;
    $result = $writer->sync('');

    expect($result)->toContain('# >>> package-boost (managed) >>>');
    expect($result)->toContain('# <<< package-boost (managed) <<<');
    expect($result)->toContain('CLAUDE.md');
    expect($result)->toContain('AGENTS.md');
});

it('appends a new block to an existing .gitattributes with user content', function (): void {
    $original = "*.php text=auto eol=lf\n*.css diff=css\n";
    $result = (new ManagedBlockWriter)->sync($original);

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

    $result = (new ManagedBlockWriter)->sync($original);

    // Foreign lines preserved
    expect($result)->toContain('/.lpv                   export-ignore');
    expect($result)->toContain('/PUBLIC_API.md          export-ignore');
    expect($result)->toContain('/phpstan-baseline.neon  export-ignore');

    // Canonical lines present
    expect($result)->toContain('.ai/                    export-ignore');
    expect($result)->toContain('CLAUDE.md               export-ignore');
});

it('does not duplicate canonical lines on re-sync', function (): void {
    $writer = new ManagedBlockWriter;
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

    $result = (new ManagedBlockWriter)->sync($original);

    expect($result)->toContain('# Header');
    expect($result)->toContain('*.php text=auto eol=lf');
    expect($result)->toContain('# Footer');
    expect($result)->toContain('*.bin binary');
});

it('treats malformed blocks (open without close) as no-block and appends fresh', function (): void {
    $original = "# >>> package-boost (managed) >>>\n.ai/ export-ignore\n";
    $result = (new ManagedBlockWriter)->sync($original);

    // Original (malformed) preserved, fresh block appended
    expect($result)->toContain('# >>> package-boost (managed) >>>');
    expect($result)->toContain('# <<< package-boost (managed) <<<');
});

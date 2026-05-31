---
name: lean-dist
description: Keep the published Composer archive lean by excluding dev/test/AI dirs via .gitattributes.
---

# Lean dist

## When to apply

- Adding a new top-level directory (tests/, docs/, .ai/, etc.)
- Reviewing a PR that touches .gitattributes
- Package's dist size feels too large

## The contract

Every directory NOT needed at runtime should have `export-ignore` in
`.gitattributes`. Composer's dist installer skips `export-ignore`d paths.

Standard exclusions (package-boost manages these in the `# >>>
package-boost (managed) >>>` block):

```
.agents/                export-ignore
.ai/                    export-ignore
.cache/                 export-ignore
.claude/                export-ignore
.cursor/                export-ignore
.github/                export-ignore
.gitignore              export-ignore
.junie/                 export-ignore
.kiro/                  export-ignore
.lpv                    export-ignore
.mcp.json               export-ignore
.phpunit.cache          export-ignore
AGENTS.md               export-ignore
CHANGELOG.md            export-ignore
CLAUDE.md               export-ignore
composer.lock           export-ignore
phpstan-baseline.neon   export-ignore
phpstan.neon.dist       export-ignore
phpunit.xml.dist        export-ignore
pint.json               export-ignore
rector.php              export-ignore
tests/                  export-ignore
```

## Verifying

Run `vendor/bin/package-boost-php lean` (this package) or directly via
`vendor/bin/lean-package-validator validate`. Both check the
`.gitattributes` against `.lpv` rules.

Add to CI:

```yaml
- name: Validate lean dist
  run: vendor/bin/package-boost-php lean
```

## Validation is opt-in; the managed block is the baseline

Two distinct tools, two roles — don't conflate them:

- `vendor/bin/package-boost-php gitattributes` writes and refreshes the
  managed block. This is what actually makes the archive lean; you run
  it during setup and whenever a new top-level path needs excluding.
  Baseline, not optional.
- `vendor/bin/package-boost-php lean` (the `stolt/lean-package-validator`
  wrapper) only *checks* that the block stays complete. It is opt-in:
  wire it into CI if you want enforcement, but a package whose managed
  block is correct ships lean whether or not the validator ever runs.
  There is no default `.lpv` / composer-script wiring — add it only if
  you want the stricter gate.

If your `.gitattributes` export-ignore is already covered by the managed
block (e.g. confirmed by a repo-init audit), the validator is
redundant-but-harmless, not a missing requirement.

## Anti-patterns

- Editing `.gitattributes` outside the managed block to add boost-managed
  entries — package-boost will rewrite the block on next sync
- Forgetting `export-ignore` on a new top-level dir → users get bloat
- Mixing `export-ignore` with diff/merge attributes on the same line
  (works, but visually noisy; separate the concerns)

## See also

- `.lpv` file in this package's root for the canonical exclusion list
- `references/gitattributes-managed-block.md` in
  `sandermuller/repo-init` for the multi-tool managed-block contract

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

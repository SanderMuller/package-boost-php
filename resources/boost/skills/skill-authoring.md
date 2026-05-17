---
name: skill-authoring
description: Write boost-core skill files — frontmatter shape, collision guards, source-dir selection, body content.
---

# Skill authoring

## When to apply

- Authoring a new skill file in `.ai/skills/` (host project) or
  `resources/boost/skills/` (vendor package)
- Reviewing a PR that adds/modifies a skill
- Asked to "scaffold a skill"

## Frontmatter shape

Required:
- `name` — kebab-case slug. Must match filename (without `.md`) unless
  overridden explicitly.
- `description` — single-sentence summary. Used by AI agents to decide
  when to load the skill.

Optional (loose v1 schema per architecture plan — pass-through):
- `triggers` — keywords/phrases that hint applicability
- `version` — for skills with breaking changes between versions

## Source dir selection

| Where you author | Who sees it |
|---|---|
| `.ai/skills/foo.md` | Just this project |
| `resources/boost/skills/foo.md` (in a vendor package) | Any host project that allowlists this vendor |

Pick one. Don't author the same skill in both. Host always wins on
collision; vendor-vs-vendor collisions are validation errors unless
`--force` is set.

## Body content

A skill body should answer:
1. **When does this apply?** Concrete triggers.
2. **What are the rules?** Imperative, brief.
3. **Examples?** 1-3 concrete cases.
4. **Anti-patterns?** What NOT to do.
5. **Cross-references?** Link other skills.

Keep skills short (100-300 lines). Long skills compete for AI agent
context window — split if it exceeds that.

## Collision guards

`boost-core`'s SkillResolver rejects vendor-vs-vendor collisions on the
same `name`. If you're shipping a skill that might collide, namespace it
descriptively (`vendor-readme-laravel.md`, not `readme.md`).

## Anti-patterns

- Writing a skill that's actually documentation (should be a README link)
- Writing a skill that's actually a guideline (no triggers, applies
  always — those go in `.ai/guidelines/`)
- Cargo-cult frontmatter fields that no agent reads
- Multi-thousand-line philosophical treatments

## See also

- `boost-core`'s FrontmatterParser for what's actually parsed
- The architecture plan's "Defer schema to first real vendor conflict"
  stance — we ship loose, tighten later

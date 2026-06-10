---
name: skill-authoring
description: Write boost-core skill files — frontmatter shape, skill tags, collision guards, source-dir selection, body content.
metadata:
  boost-tags: "boost-extension"
---

# Skill authoring

## When to apply

- Authoring a new skill file in `.ai/skills/` (host project) or
  `resources/boost/skills/` (vendor package)
- Reviewing a PR that adds/modifies a skill
- Asked to "scaffold a skill"

## Frontmatter shape

Required:
- `name` — kebab-case slug. Must match the skill's DIRECTORY name (the
  `<name>` in `<name>/SKILL.md`), not the literal `SKILL` filename — e.g.
  `resources/boost/skills/lean-dist/SKILL.md` declares `name: lean-dist`.
  (A legacy flat `<name>.md` matches its filename without `.md`.)
- `description` — single-sentence summary. AI agents read this to decide
  when to load the skill, so make it specific.

Optional:
- `metadata` — a string→string map; the Agent Skills standard's sanctioned
  extension point for client-specific data. `boost-core` reads one key
  here, `boost-tags` (see below). Other `metadata` keys and any
  non-standard top-level fields pass through untouched (loose v1 schema),
  but no agent acts on them — don't add frontmatter nothing reads.

### Tagging a skill — `metadata.boost-tags`

Tag a skill so a consuming project receives it only when that project opts
in. Tags are a single space-delimited string under `metadata.boost-tags`:

```yaml
---
name: jira-triage
description: Triage and label incoming Jira issues.
metadata:
  boost-tags: "php jira"
---
```

- Tags are normalized (lowercased, trimmed) on both sides before matching.
- A vendor skill ships to a project only when **every** tag it declares is
  among the project's `withTags()` in `boost.php` — `skillTags ⊆ projectTags`.
- An **untagged** skill ships everywhere — tagging is opt-in and additive,
  so introducing the field breaks nothing on its own.
- A **malformed** `boost-tags` (not a string) fails closed: the skill ships
  nowhere, rather than silently degrading to untagged.
- Consumers discover which tags exist with `composer boost:tags`.

> **Tagging an already-shipped skill is consumer-breaking.** Every project
> that has not declared the new tag stops receiving the skill on its next
> sync. Tag a skill from the start, or treat adding a tag as a breaking
> change for the package — a loud release-note callout, not a quiet tweak.

## Source dir selection

| Where you author | Who sees it |
|---|---|
| `.ai/skills/foo/SKILL.md` | Just this project |
| `resources/boost/skills/foo/SKILL.md` (in a vendor package) | Any host project that allowlists this vendor |

Pick one. Don't author the same skill in both. Host always wins on
collision; vendor-vs-vendor collisions are validation errors unless
`--force` is set.

### File layout — directory form, not flat

Author each skill as `<name>/SKILL.md` (directory form), not flat
`<name>.md`. boost-core EMITS the directory form, the reference catalogs
(boost-skills, lean-package-validator, project-boost-php) all use it, and it
future-proofs multi-file skills (a skill can ship sibling assets next to
its `SKILL.md`). boost-core accepts both forever, so this is authoring
uniformity, not correctness — but new skills should be directory-form.
Guidelines stay single flat `.md` files (`resources/boost/guidelines/foo.md`);
the directory rule is skills-only.

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

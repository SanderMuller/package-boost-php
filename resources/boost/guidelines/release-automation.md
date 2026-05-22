# Release Automation

How `CHANGELOG.md`, release notes, and the GitHub release flow are wired
in a Composer package — the convention these repos follow, so a release
stays reproducible and the changelog stays in lockstep with what
shipped.

## `CHANGELOG.md` is updated by CI — do not edit it by hand

The package's `.github/workflows/update-changelog.yml` listens for
`release: released`, uses `stefanzweifel/changelog-updater-action` to
prepend the published release body to `CHANGELOG.md`, and commits the
update back to the release's target branch (the
`release.target_commitish`, typically `main`). That's the single source
of truth for changelog entries.

So:

- Do NOT add a changelog entry manually when preparing a release. The
  release body becomes the entry automatically.
- Do NOT include a `CHANGELOG.md` change in the release PR. The
  post-release commit comes from CI.
- If the entry needs a fix after a release (typo in the auto-generated
  section), edit `CHANGELOG.md` directly and commit. Rare.

## Release notes live in `internal/release-notes-<version>.md`

`internal/` is gitignored — drafts stay local. The notes file is the
input the user passes to `gh release create --notes-file` when they
cut the tag; that publish is what triggers the update-changelog
workflow.

```
internal/release-notes-0.7.0.md      # gitignored draft
```

The first line of the notes file pins the verified-green commit SHA in
an HTML comment — invisible in the rendered release body, but greppable
by the pre-tag gate:

```markdown
<!-- verified-sha: <full SHA that CI proved green> -->

# 0.7.0 — <headline>
...
```

The SHA pin is what lets a "still green at tag time" gate fail closed
if `HEAD` drifts between drafting notes and cutting the tag.

## Release workflow

1. Run the local quality gauntlet (the `pre-release` skill: Rector,
   Pint, full test suite, PHPStan, doc audit).
2. Commit + push.
3. Wait for CI green on the pushed SHA
   (`gh run list --commit "$(git rev-parse HEAD)"`).
4. Draft `internal/release-notes-<version>.md` with the SHA-pinned
   first line. Only after CI is green — notes claim CI-matrix facts.
5. **The user cuts the tag.** They run the pre-tag gate (re-verifies
   SHA hasn't drifted, CI still green) and then
   `gh release create <version> --notes-file
   internal/release-notes-<version>.md --title "v<version>"`. Agents
   stop at the ready-to-tag handoff and do NOT run release-create —
   see the `pre-release` skill.
6. CI's `update-changelog` workflow prepends the release body to
   `CHANGELOG.md` and commits.
7. Watch the tag-ref CI re-fires + the `release`-event decorators
   (`update-changelog`) until terminal + green.

The full procedure (and the agent-handoff rules) lives in the
`pre-release` skill — this guideline pins the conventions that skill
leans on.

## Tag and version naming

- Tags use the bare version: `0.7.0`. Composer and Packagist key off
  the tag verbatim — keep it bare.
- The GitHub release title takes the `v` prefix: `v0.7.0`. Cosmetic
  only — the tag, not the title, is what Composer reads.
- The notes file uses the bare version: `internal/release-notes-0.7.0.md`.

## See also

- `release-notes` skill — how to write the release body (structure,
  voice, breaking-change callouts).
- `pre-release` skill — the full pre-tag gauntlet (this guideline pins
  the file paths and CHANGELOG flow the skill assumes).

# Upgrading

Breaking changes per major/minor bump.

## 0.8 → 0.9

0.9.0 drops `package-boost-php`'s own Composer plugin. The two
`composer package-boost-php:*` subcommands move to the standalone
`vendor/bin/package-boost-php` binary. boost-core had the same change
in 0.6.0 — this is the package-boost-php counterpart.

### `composer package-boost-php:*` commands are gone — use `vendor/bin/package-boost-php`

| Was                                       | Now                                          |
|-------------------------------------------|----------------------------------------------|
| `composer package-boost-php:lean`         | `vendor/bin/package-boost-php lean`          |
| `composer package-boost-php:gitattributes`| `vendor/bin/package-boost-php gitattributes` |

Composer no longer prompts to allow this package as a plugin on
`composer install` / `update`.

### Optional: drop the dead `allow-plugins` entry

If your `composer.json` lists `sandermuller/package-boost-php` under
`config.allow-plugins`, remove it — the package is no longer a plugin.
Composer ignores the stale entry, so leaving it is harmless.

## 0.6 → 0.7

0.7.0 requires `sandermuller/boost-core ^0.6`. boost-core 0.6.0 retired its
Composer plugin, which changes two consumer-facing things. package-boost-php
itself remains a `composer-plugin` — its own commands are unaffected.

### `composer boost:*` commands are gone — use `vendor/bin/boost`

boost-core's plugin registered `composer boost:install`, `composer boost:sync`,
etc. as Composer subcommands. With the plugin removed, run the standalone
binary instead — the `boost:` prefix is dropped:

| Was | Now |
|---|---|
| `composer boost:install` | `vendor/bin/boost install` |
| `composer boost:sync` | `vendor/bin/boost sync` |
| `composer boost:doctor` | `vendor/bin/boost doctor` |
| `composer boost:tags` | `vendor/bin/boost tags` |

package-boost-php's own commands — `composer package-boost-php:lean` and
`composer package-boost-php:gitattributes` — are unchanged.

### Auto-sync is no longer automatic

boost-core's plugin re-ran `boost sync` on every `composer install` /
`composer update`. With the plugin gone, wire the callback into your own
project's `composer.json` to keep that behaviour:

```json
"scripts": {
    "post-install-cmd": ["SanderMuller\\BoostCore\\Scripts\\BoostAutoSync::run"],
    "post-update-cmd": ["SanderMuller\\BoostCore\\Scripts\\BoostAutoSync::run"]
}
```

Only the root package's scripts run during a Composer command, so this must
live in your project's `composer.json` — a dependency's scripts do not fire.
Otherwise, run `vendor/bin/boost sync` yourself (e.g. in CI). Setting
`BOOST_SKIP_AUTOSYNC=1` disables the callback.

### Optional: drop the dead `allow-plugins` entry

If your `composer.json` lists `sandermuller/boost-core` under
`config.allow-plugins`, remove it — boost-core is no longer a plugin.
Composer ignores the stale entry, so leaving it is harmless.

# AGENTS.md — wp-plugin-boilerplate

Guide for AI agents and developers working on WordPress plugins built from
this boilerplate.

## Quick start

```bash
composer install
npm install
npm run build           # production build (JS, CSS, copies PHP)
npm run start           # dev watch mode
npm run plugin-zip      # create distributable .zip
```

## Git workflow (mandatory)

- `main` is the only production branch. No feature branch ever merges into
  `main` directly.
- Create/update features on `develop` only via small, laser-focused PRs:
  `<prefix>/<issue-number>-<slug>` → **`develop`**.
- `develop` is the only branch that merges into `main`.
- The branch prefix matches the work type:
  - `feat/` or `feature/` — new features
  - `fix/` — bugfixes
  - `chore/` — tooling, standards, dependency, and cleanup work
  - `test/` — tests
  - `docs/` — documentation
  One issue, one branch, named after it.
- **No self-merge.** The agent opens the PR; the human reviews and merges.
- Before opening a PR, run `/review` up to three times and fix every valid
  finding (security, bugs, style, WPCS), then open the PR.
- Reference the issue the PR closes (e.g. `Closes #N`). GitHub only
  auto-closes issues when a PR merges into the **default branch** (`main`).
  PRs merge into `develop` here, so after a PR is merged, manually close the
  issue(s) it references with a comment noting the merge commit, e.g.:
  `gh issue close N --comment "Closed by PR #M (merged into develop as <sha>)."`
- **Post-merge branch cleanup.** After the reviewer merges, delete the feature
  branch. Prefer `gh pr merge <n> --merge --delete-branch` so the remote branch
  is removed in the same step. Locally: switch off the branch and refresh with
  `git checkout develop && git pull --ff-only`, then `git branch -d <branch>`.

### Branch promotion (`develop` → `main`)

`develop` is merged into `main` only at a release boundary. Feature PRs never
target `main`.

## Reference codebases

Local checkouts used for WordPress patterns and conventions:

- `~/code/gutenberg` — Gutenberg source and block editor patterns.
- `~/code/wp/wordpress-develop` — WordPress core source and conventions.
- `~/code/wp/woocommerce` — WooCommerce patterns and extendability examples.
- `~/code/wp-movies-demo` — example block theme / Interactivity API project.
- `~/code/wp/agent-skills` — WordPress agent skills (see below).

## Agent skills

Local skill definitions live under:

- `/home/vijayan/code/wp/agent-skills/skills`

Useful skills for plugin work:

- `wp-plugin-development` — plugin architecture, activation/deactivation, packaging.
- `wp-block-development` — `block.json`, `register_block_type`, render callbacks.
- `wp-interactivity-api` — `data-wp-*` directives and stores.
- `wp-rest-api` — REST controllers and routes.
- `wp-phpstan` — static analysis setup.
- `wp-performance` — performance patterns and measurement.
- `wp-wpcli-and-ops` — WP-CLI operations and environment tasks.
- `wp-playground` — local Playground runs and Blueprints.
- `wp-project-triage` — deterministic project scan and tooling hints.

## External documentation

- WordPress Developer Resources (developer.wordpress.org) for the Plugin,
  Block Editor, REST API, and Coding Standards handbooks.
- Gutenberg repository docs for bleeding-edge APIs.
- PHPStan docs (phpstan.org) for rule levels, baselines, and error identifiers.

## Project conventions

- Session state and decisions live in the GitHub issue trail (issue body +
  comments), not in repo files.
- Commit subjects reference the issue when applicable:
  `feat(scope): #N — …`, `fix(scope): #N — …`, `chore(scope): #N — …`.
- One issue, one branch, one PR — see **Git workflow**.

## Architecture

- **Namespace**: `VG\Plugin_Boilerplate` (PSR‑4 autoloaded from `includes/`).
- **Main plugin file `wp-plugin-boilerplate.php`** is the entry point
  WordPress loads; it uses angle-bracketed placeholders
  (`<plugin-name>`, `<text-domain>`, `<namespace>`, `<prefix>`, `<slug>`) that
  must be replaced per project. Never delete it — a plugin folder without it
  is a dead plugin.
- **Third‑party PHP prefix**: PHP‑Scoper outputs to `third-party/` under
  `VG\Plugin_Boilerplate\ThirdParty` (configured in `scoper.inc.php`).
- **DI**: PHP-DI is provided. Use the container for large/interdependent
  service graphs; prefer direct invocation for small ones. See
  `docs/architecture/dependency-injection.md` for the mindset-based guidance
  (no class-count rule).
- **DB schema**: `includes/DB/Installer.php` uses a versioned, idempotent
  `install()` / `maybe_update()` pattern (hooked on activation and
  `admin_init` respectively).
- **Uninstall**: `uninstall.php` cleans up plugin-owned data; keep it inert by
  default and uncomment only what the plugin owns.

## Build

- Uses `@wordpress/scripts` with a custom Webpack config (`webpack.config.js`).
- Custom entries: `block-styles`, `admin-appointments`, and `sample-slot` (add
  more as needed). Flags always passed:
  `--webpack-copy-php --experimental-modules`.
- Source layout:
  - `src/blocks/` — Gutenberg block source (block.json discovered).
  - `src/block-styles/` — block style source.
  - `src/admin/data-views/` — Data Views admin screens (wp.data store).
  - `src/slots/` — non-block, non-block-style custom entries (slotfills).

## Lint / style

> **Rule zero: every `composer`/`npm` quality command must be scoped.**
> Never run a bare `phpcs`/`phpcbf` with an explicit `.` path, and never rely
> on a tool's "no path means the project" default. `phpcs .` ignores the
> `<file>` whitelist in `phpcs.xml` and walks the entire tree — including
> `vendor/` — which exhausts memory and can take the machine down. Always use
> the project scripts below, which are scoped by construction.

| Command | Scope | What |
|---|---|---|
| `composer lint:php` | `phpcs.xml` `<file>` list | PHP_CodeSniffer — WordPress + PHPCompatibilityWP, PHP 8.1+ |
| `composer format:php` | `phpcs.xml` `<file>` list | Auto-fix PHP with `phpcbf` |
| `composer phpstan` | `phpstan.neon.dist` `paths` | PHPStan static analysis (level 5) |
| `npm run lint:js` | `src`, `tests/e2e` | ESLint via `wp-scripts` (flat config, WordPress rules) |
| `npm run lint:css` | `src/**/*.scss` | stylelint via `wp‑scripts` |
| `npm run format` | see **Formatting** below | Prettier (via `@wordpress/prettier-config`) |

PHPCS excludes `WordPress.Files.FileName.InvalidClassFileName` and
`NotHyphenatedLowercase` for PSR‑4 compatibility.

### Never scope by hand — the scripts already are

The three scopes are owned by config, not by the shell:

- **PHPCS/PHPStan** read their whitelists from `phpcs.xml` (`<file>`) and
  `phpstan.neon.dist` (`paths`). Run `composer lint:php` / `composer phpstan`
  with **no path arguments** — adding one (especially `.`) overrides the
  whitelist.
- **`wp-scripts`** lints a whole project when given no path, so the npm scripts
  name their directories: `lint:js` → `src tests/e2e`, `lint:css` →
  `'src/**/*.scss'`.

Do **not** run `vendor/bin/phpcs`, `vendor/bin/phpcbf`, or `vipwpcs`-style
commands directly against `.`, `vendor/`, or the repo root. If you need one
file, pass that single file; if you need the project, use the scripts.

`wp-scripts` parses file args with `minimist(...)._`, so a flag that takes a
value consumes the following token. Put **paths before flags**:

```bash
npx wp-scripts lint-js src/admin/data-views --fix   # right — lints only <path>
npx wp-scripts lint-js --fix src/admin/data-views   # WRONG — consumes the path
```

### ESLint is flat-config only

`@wordpress/scripts` v32+ ships ESLint v9/v10, which no longer reads
`.eslintrc*`. The project uses `eslint.config.js`, which extends the
`@wordpress/scripts` default flat config (recommended rules + test-unit
overrides + Babel parser fallback) and adds the browser globals the project
needs. A legacy `.eslintrc` would be silently ignored (with a warning), so do
not reintroduce one.

### Formatting

`npm run format` is scoped to `src` and `tests/e2e` so it never rewrites
`package-lock.json`, `composer.lock`, `build/`, or test artifacts. Prettier
owns JS/JSON/TS; SCSS is owned by stylelint (`npm run lint:css`), and PHP by
`composer format:php`. Use `npm run format:check` to report drift without
writing.

`wp-scripts format` always passes `--write` (it has no `--check` mode) and
uses `.prettierignore` as `--ignore-path`, which **replaces** Prettier's
default `.gitignore` handling. Every exclusion must therefore be listed
explicitly in `.prettierignore`, or gitignored files will still be formatted.
`format:check` calls `prettier --check` directly with the same file globs.

## Static analysis (PHPStan)

PHPStan complements PHPCS: PHPCS enforces style and security rules, PHPStan
finds correctness and type bugs without running the code. Both gates are run
before a PR.

- **Level**: `5` (see `phpstan.neon.dist`). New code must pass at this level.
- **Extensions** (auto-loaded by `phpstan/extension-installer`):
  - `szepeviktor/phpstan-wordpress` — WordPress stubs, core constants, and
    dynamic return types.
  - `johnbillion/wp-compat` — flags functions/hooks newer than the plugin's
    `Requires at least` (6.5, read from the main plugin file).
  - `swissspidy/phpstan-no-private` — flags WordPress symbols marked
    `@access private`.
  - `phpstan/phpstan-deprecation-rules` — flags deprecated WordPress symbols.
  - `phpstan/phpstan-strict-rules` — extra strictness.
  - `phpstan/phpstan-phpunit` — understands PHPUnit assertions.
- **Paths**: `wp-plugin-boilerplate.php`, `uninstall.php`, `includes/`,
  `tests/phpunit`. `scoper.inc.php` is excluded (its symbols only exist at
  prefix time); generated/dependency trees are excluded.
- **Commands**:
  - `composer phpstan` — analyse (`--memory-limit=2G`).
  - `composer phpstan:baseline` — regenerate `phpstan-baseline.neon`.
- **Cache**: `.phpstan-cache/` is gitignored.

### Baseline policy (how the boilerplate adopts PHPStan)

`phpstan-baseline.neon` records pre-existing findings so the gate can be enabled
without a sweeping refactor. The baseline is a **to-do list, not a permanent
suppression**:

1. New code is held to level 5 with no new baseline entries.
2. Existing findings are grouped by **common pattern** and fixed in small,
   themed batches (each batch shrinks the baseline).
3. A recurring finding that is genuinely unfixable in a WordPress context (for
   example, symbols that only exist after PHP-Scoper runs) is annotated with an
   **inline `@phpstan-ignore <identifier>`** at the call site, or, when it spans
   a whole path and an inline comment would be noise, a **path-bound
   `ignoreErrors`** entry. Prefer these over a broad baseline entry.
4. Never lower the level or add a global `ignoreErrors` entry to make a failure
   disappear.

The committed baseline contains exactly these categories:

- scoped `ThirdParty` container symbols (`class.notFound`) that only exist after
  PHP-Scoper runs;
- conditionally `define()`d plugin constants (`constant.notFound`);
- deliberate placeholder bodies the boilerplate ships (`void.pure`,
  `if.condNotBoolean`);
- `phpstan-strict-rules` findings in the sample test (`staticMethod.dynamicCall`).

Each is either fixed in a themed batch or, once understood, converted to a
targeted inline ignore.

When PHPStan reports an error, read
`https://phpstan.org/error-identifiers/<identifier>` before fixing; fix the
underlying cause rather than widening types or casting to silence it.

## Directory layout

```
includes/         # PHP classes (VG\Plugin_Boilerplate namespace)
src/blocks/       # Gutenberg block source
src/block-styles/ # Custom block style source
src/admin/        # Admin scripts (Data Views)
src/slots/        # Non-block custom entries
build/            # Built JS/CSS (gitignored)
third-party/      # PHP‑Scoper prefixed deps (gitignored; .gitkeep tracked)
vendor/           # Composer deps (gitignored)
tests/            # PHPUnit (phpunit/unit) + Playwright (e2e/specs)
docs/             # Architecture & guides (concept-first, secret-free)
.phpstan-cache/   # PHPStan result cache (gitignored)
```

## Testing

- **PHPUnit**: `npm run test:unit:php` (runs inside wp-env via
  `tests/phpunit/bootstrap.php`). Config: `phpunit.xml.dist`.
- **E2E / Playwright**: `npm run test:e2e` (against a wp-env-managed site).
  Config: `tests/e2e/playwright.config.ts`.
- **Environment**: `.wp-env.json` + `@wordpress/env`; seed via
  `bin/setup-test-env.sh`.
- **CI**: `.github/workflows/ci.yml` runs lint (JS + PHPCS), PHPUnit, and E2E.
  Static analysis runs in a **separate, non-blocking** workflow
  (`.github/workflows/phpstan.yml`, `continue-on-error: true`) — see below.
- **TLS note**: Test scripts set `NODE_TLS_REJECT_UNAUTHORIZED=0` because
  wp-env serves over `https://127.0.0.1` with a self-signed certificate.
  This is safe for local wp-env only — never reuse these flags for external
  hosts.

### Static analysis in CI (non-blocking by design)

This repo is a **boilerplate**: downstream projects clone it and immediately
add their own code, which may not yet be level-5 clean. To avoid turning this
gate into a hard blocker for those adopters, PHPStan runs in its own workflow
with `continue-on-error: true`:

- Local gate: `composer phpstan` is expected to be green before a PR.
- CI gate: `.github/workflows/phpstan.yml` reports findings but never fails
  the build. Raise it to a required check once the baseline is empty and
  downstream usage is established.

## Security (always)

- **Never commit or leak secrets** (passwords, application IDs, web tokens,
  API keys). Use gitignored env vars / `.env` and commit only a `.env.example`
  with placeholders.
- Escape all output (`esc_html_*`, `esc_attr_*`), sanitize input, guard REST
  routes with proper permission callbacks, and nonce requests.
- Do not expose credentials in docs, tests, specs, or CI (use `secrets.*`).

## Notes

- `.opencode/tasks/` stores documentation of completed refactors — read before
  working to understand past decisions.
- `composer.json` `require` is minimal; `phpunit/phpunit` and PHP-DI are
  included. Dev tooling (PHPCS, PHPStan, and their extensions) lives in
  `require-dev`; add dependencies as needed.
- **Packaging** — build the distributable with `npm run release`
  (`composer build && npm run build && npm run plugin-zip`). `wp-scripts
  plugin-zip` reads the `package.json` `files` allowlist via `npm-packlist`;
  without a `files` field it falls back to a WordPress-handbook glob keyed on
  the npm `name` (`vijayan-wp-plugin-boilerplate.php`), which does **not** match
  the entry file (`wp-plugin-boilerplate.php`) and omits `vendor/` and
  `third-party/` — producing a non-installable archive.
- **Always run `composer build` first.** It runs PHP-Scoper into `third-party/`
  and then installs **production-only** dependencies, so the zip does not ship
  PHPUnit/PHPStan/WPCS. Zipping from a dev-populated `vendor/` leaks dev tooling.
- `.distignore` was removed in #62: `plugin-zip` never read it (`npm-packlist`
  reads only `.npmignore`/`.gitignore`), and `files` entries override
  `.gitignore`. Use negations in `files` for packaging excludes
  (`!**/.git/**` must stay **last**; `npm-packlist` applies entries in order, so
  a later glob would re-add paths a prior negation removed — source-installed
  vendor packages otherwise carry `.git/` directories that inflate the zip).

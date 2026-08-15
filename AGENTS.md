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
  `feature/<n>-<slug>` → **`develop`**.
- `develop` is the only branch that merges into `main`.
- Before opening a PR, run `/review` up to three times and fix every valid
  finding (security, bugs, style, WPCS), then open the PR.
- Reference the issue the PR closes (e.g. `Closes #N`).

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

| Command | What |
|---|---|
| `npm run lint:js` | ESLint (WordPress recommended rules) |
| `npm run lint:css` | stylelint via wp‑scripts |
| `npm run format` | Prettier (via `@wordpress/prettier-config`) |
| `vendor/bin/phpcs` | PHP_CodeSniffer (WordPress + PHPCompatibilityWP, PHP 8.0+) |

PHPCS excludes `WordPress.Files.FileName.InvalidClassFileName` and
`NotHyphenatedLowercase` for PSR‑4 compatibility.

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
```

## Testing

- **PHPUnit**: `npm run test:unit:php` (runs inside wp-env via
  `tests/phpunit/bootstrap.php`). Config: `phpunit.xml.dist`.
- **E2E / Playwright**: `npm run test:e2e` (against a wp-env-managed site).
  Config: `tests/e2e/playwright.config.ts`.
- **Environment**: `.wp-env.json` + `@wordpress/env`; seed via
  `bin/setup-test-env.sh`.
- **CI**: `.github/workflows/ci.yml` runs lint (JS + PHPCS) and E2E.

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
  included. Add dependencies as needed.
- `.distignore` controls what goes into the production `.zip` (via
  `plugin-zip`).

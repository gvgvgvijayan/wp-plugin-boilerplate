# AGENTS.md — wp-plugin-boilerplate

## Quick start

```bash
composer install
npm install
npm run build           # production build (JS, CSS, copies PHP)
npm run start           # dev watch mode
npm run plugin-zip      # create distributable .zip
```

## Architecture

- **Namespace**: `VG\Plugin_Boilerplate` (PSR‑4 autoloaded from `includes/`)
- **Third‑party PHP prefix**: PHP‑Scoper outputs to `third-party/` under `VG\Plugin_Boilerplate\ThirdParty` (configured in `scoper.inc.php`)
- **Main plugin file `wp-plugin-boilerplate.php` doesn't exist yet** — must be created when bootstrapping a new project
- **Activation/deactivation/uninstall hooks intentionally omitted** (add in main plugin file as needed)

## Build

- Uses `@wordpress/scripts` with a custom Webpack config (`webpack.config.js`)
- **Custom entry**: `src/block-styles/index.js` in addition to auto‑discovered `block.json` entries
- Flags always passed: `--webpack-copy-php --experimental-modules`

## Lint / style

| Command | What |
|---|---|
| `npm run lint:js` | ESLint (WordPress recommended rules) |
| `npm run lint:css` | stylelint via wp‑scripts |
| `npm run format` | Prettier (via `@wordpress/prettier-config`) |
| `phpcs` (vendored) | PHP_CodeSniffer (WordPress + PHPCompatibilityWP, PHP 7.4+) |

PHPCS excludes `WordPress.Files.FileName.InvalidClassFileName` and `NotHyphenatedLowercase` for PSR‑4 compatibility.

## Directory layout

```
includes/         # PHP classes (VG\Plugin_Boilerplate namespace)
src/blocks/       # Gutenberg block source (block.json discovered by wp-scripts)
src/block-styles/ # Custom block style source (entry: src/block-styles/index.js)
build/            # Built JS/CSS (gitignored)
third-party/      # PHP‑Scoper prefixed deps (gitignored)
vendor/           # Composer deps (gitignored)
```

## Testing

No test suite is set up.

## Notes

- `.opencode/tasks/` stores documentation of completed refactors — read before working to understand past decisions
- `composer.json` `require` is minimal (`php >=7.4`); add dependencies as needed
- `.distignore` controls what goes into the production `.zip` (generated via `plugin-zip`)

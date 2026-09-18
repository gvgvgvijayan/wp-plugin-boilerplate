/**
 * ESLint flat config.
 *
 * `@wordpress/scripts` v32+ (ESLint v9/v10) only supports flat config; the
 * legacy `.eslintrc` was silently ignored with a warning. Rather than
 * re-deriving the defaults, this extends the flat config that ships with
 * `@wordpress/scripts` (recommended rules + test-unit overrides + a Babel
 * parser fallback for projects without a Babel config), then layers on the
 * browser globals this project needs.
 *
 * Usage: `npm run lint:js` (scoped to `src` and `tests/e2e`).
 */
const globals = require( 'globals' );
const wpScriptsConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

// `globals@11` ships at least one key with trailing whitespace
// ("AudioWorkletGlobalScope "), which ESLint 10 rejects. Trim defensively.
const browserGlobals = Object.fromEntries(
	Object.entries( globals.browser ).map( ( [ name, writable ] ) => [
		name.trim(),
		writable,
	] )
);

module.exports = [
	...wpScriptsConfig,
	{
		// `@wordpress/eslint-plugin` only ships a small DOM subset (window,
		// document, navigator, fetch, console, wp). Downstream plugin code
		// routinely uses the rest of the browser API, so declare it: this
		// is a boilerplate and false `no-undef` noise is unacceptable.
		languageOptions: {
			globals: browserGlobals,
		},
	},
];

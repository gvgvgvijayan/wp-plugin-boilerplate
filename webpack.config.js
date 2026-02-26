/**
 * WordPress dependencies
 */
const [
	scriptConfig,
	moduleConfig,
] = require( '@wordpress/scripts/config/webpack.config' );

/**
 * This is the correct way to extend the default WordPress webpack configuration.
 *
 * We de-structure the default config into its two parts: one for standard
 * scripts and one for ES Modules. We then add our custom entry points
 * to the standard script config before exporting both.
 */

// 1. Create a new, modified version of the standard script config.
const customScriptConfig = {
	// 2. Start with all the default settings for scripts.
	...scriptConfig,

	// 3. Define the entry points.
	entry: {
		// 4. IMPORTANT: Call the original entry() function to get all
		//    the default entry points discovered from block.json files.
		...scriptConfig.entry(),

		// 5. Now, add your own custom entry points.
		'block-styles': './src/block-styles/index.js',
	},
};

// 6. Export an array containing your modified script config and the
//    original, untouched module config.
module.exports = [ customScriptConfig, moduleConfig ];

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = [
	// Spread default WordPress Webpack config to maintain base settings
	...defaultConfig,
	{
		...defaultConfig[ 0 ],
		// Define entry points manually instead of using getWebpackEntryPoints()
		// This aligns with the latest best practices in @wordpress/scripts
		entry: {
			...defaultConfig[ 0 ].entry(),
			'block-styles': './src/block-styles/index.js',
		},
	},
];

# WordPress Plugin Boilerplate

A modular WordPress plugin serving as a streamlined and reusable foundation for bespoke site customizations. This plugin is designed to reduce repetitive tasks and provide a modern, efficient codebase for managing client-specific features. It incorporates WordPress coding standards, build processes, and linting/formatting tools, ensuring a consistent and maintainable approach to plugin development.

## Key Features

* **Modular Design**: Organized into logical components for easy extension and maintenance.
* **Customizable**:  Adaptable to a wide range of project requirements.
* **Modern Development Workflow**:
    * Uses `webpack` for asset bundling.
    * Includes `eslint`, `stylelint`, and `PHPCS` for code quality.
    * Uses `composer` for managing PHP dependencies.
* **Block Editor Ready**: Includes structure for registering custom Gutenberg blocks.
* **Consistent Styling**:  Provides a system for managing block styles.

## Directories

The plugin follows this structure:

* `includes/`:  Contains the core PHP logic of the plugin.
* `src/`:  Holds source files for assets (JavaScript, CSS, etc.).
    * `blocks/`: Contains source files for custom Gutenberg blocks.
    * `block-styles/`: Contains source files for custom block styles.
* `vendor/`:  (Managed by Composer)  Contains PHP dependencies.
* `build/`:  (Generated) Contains built and optimized assets.
* `third-party/`: (Generated) Contains third-party libraries prefixed by PHP-Scoper.

## Files

* `composer.json`:  Defines PHP dependencies and autoloading.
* `package.json`:  Defines JavaScript dependencies and build scripts.
* `phpcs.xml`:  Configuration for PHP Code Sniffer.
* `webpack.config.js`:  Configuration for Webpack.
* `scoper.inc.php`: Configuration for PHP-Scoper.
* `wp-plugin-boilerplate.php`:  The main plugin file.

---

# Placeholder Changes:

1. Search & replace wp-plugin-boilerplate to do the renaming work based on the context.

2. Rename namespace from Plugin_Boilerplate prefixed with initial prefix `VG` to your desired namespace in composer.json (VG\\Plugin_Boilerplate\\) & scoper.inc.php (VG\Plugin_Boilerplate\ThirdParty).

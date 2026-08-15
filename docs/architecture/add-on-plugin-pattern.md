# Add-on Plugin Pattern (depending on a core plugin)

Some WordPress plugins are built as **add-ons**: they extend a "core" plugin
rather than standing alone. This documents the recommended pattern for an
add-on that must wait for a core plugin to be ready, degrade gracefully when
the core is missing, and never collide with other copies.

## The pattern

1. **Declare the dependency in the add-on's plugin header** so WordPress
   surfaces a clear message and blocks activation when the core is inactive:

   ```php
   /**
    * Plugin Name: My Add-on
    * Requires Plugins: my-core-plugin
    * ...
    */
   ```

2. **Defer bootstrap to the core plugin's "ready" signal.** The core plugin
   fires a namespaced action (e.g. `my_core_loaded`) after it has initialized.
   The add-on hooks its own bootstrap to that action so it only loads once the
   core is actually ready:

   ```php
   add_action( 'my_core_loaded', __NAMESPACE__ . '\my_addon_load_plugin' );
   ```

3. **Add a dependency guard as a safety net.** The `Requires Plugins` header
   can be bypassed (e.g. the core deactivated via WP-CLI, or removed via
   FTP). Detect when the core's signal never fires and surface a clear admin
   notice instead of silently no-op'ing:

   ```php
   // Runs late so the core plugin's default-priority hook has already fired.
   add_action( 'plugins_loaded', __NAMESPACE__ . '\my_addon_dependency_guard', 999 );

   function my_addon_dependency_guard() {
       if ( did_action( 'my_core_loaded' ) ) {
           return; // Core is present and ready.
       }

       if ( is_admin() ) {
           add_action( 'admin_notices', function () {
               echo '<div class="notice notice-error"><p>' .
                   esc_html__( 'My Add-on requires the My Core Plugin, which is missing or inactive.', 'my-addon' ) .
                   '</p></div>';
           } );
       }

       if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
           error_log( 'My Add-on: core plugin did not fire its ready signal. Add-on halted.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
       }
   }
   ```

## Why this approach

- **Decouples** the add-on from the core's internal loading order.
- **Degrades gracefully** instead of fataling when the core is absent.
- **Avoids hard dependencies** on functions that may not exist yet.

## Naming collisions

To avoid collisions with another plugin's copy of the same libraries, scope
any third-party PHP dependencies into the add-on's own `ThirdParty`
namespace (see `scoper.inc.php`). This is the same isolation applied to the
core plugin's own dependencies.

## Security note

Never expose the core's internal hooks or state as a security boundary.
Always re-validate capabilities and nonces in your own add-on code; do not
trust that the core plugin has already done so on your behalf.

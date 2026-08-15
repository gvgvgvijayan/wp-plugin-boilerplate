#!/bin/bash
#
# Setup the WordPress test environment for E2E tests.
#
# Customize the theme, capabilities, permalinks, and mock data for YOUR
# plugin's needs. This file is a template — adjust the post types, roles,
# and capabilities to match what your plugin registers.
#
# Run after `npm run test:e2e:setup` (wp-env start).
#
# Security: uses only harmless mock data. Never commit real credentials here.

# Exit on error.
set -e

echo "Configuring WordPress test environment..."

# Activate the theme the tests expect (adjust as needed).
wp theme activate twentytwentyfive

# Enable pretty permalinks and flush rewrite rules so REST routes are active.
wp rewrite structure '/%postname%/' --hard
wp rewrite flush --hard

# Example: grant capabilities for a custom post type your plugin registers.
# wp cap add administrator edit_examples edit_others_examples publish_examples \
#   read_private_examples edit_published_examples delete_examples \
#   delete_others_examples delete_published_examples

# Example: clean up and seed mock data for your post types.
# echo "Cleaning up existing mock data..."
# wp post delete "$(wp post list --post_type='example' --format=ids)" --force || true

# echo "Creating mock data..."
# wp post create --post_type=example --post_title='Sample Item' --post_status=publish --porcelain

# Flush cache to clear transients.
wp cache flush
wp transient delete --all

echo "Environment setup complete!"

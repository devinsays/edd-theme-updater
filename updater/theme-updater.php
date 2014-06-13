<?php
/**
 * This is just a demonstration of how theme licensing works with
 * Easy Digital Downloads.
 *
 * @package EDD Theme Updater
 */

/**
 * When using in your own project:
 *
 * 1. Find/replace "prefix" with your theme prefix (in all updater files)
 * 2. Find/replace "textdomain" with your theme textdomain (in all updater files)
 * 3. Update the variables in prefix_updater_settings()
 * 4. Delete this doc block
 */

/**
 * Returns settings required by the theme updater.
 *
 * since 1.0.0
 *
 * @param string $setting
 * @returns string $setting data
 */
function prefix_updater_settings( $setting ) {

	/* URL of site running EDD */
	$data['remote_api_url'] = 'http://yoursite.com';

	/* The name of this theme */
	$data['theme_slug'] = 'edd-theme-updater';

	/* The current theme version we are running */
	$data['version'] = '0.1.0';

	/* The author's name */
	$data['author'] = 'Pippin Williamson';

	if ( isset( $data[$setting] ) ) {
		return $data[$setting];
	}

	return false;
}

/**
 * Includes functions to create the admin page.
 *
 * since 1.0.0
 */
include( dirname( __FILE__ ) . '/theme-updater-functions.php' );

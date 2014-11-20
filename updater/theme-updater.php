<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Theme Updater
 */

// Includes the files needed for the theme updater
if ( !class_exists( 'EDD_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

// Loads the updater classes
$updater = new EDD_Theme_Updater_Admin(

	// Config settings
	$config = array(
		'remote_api_url' => 'https://easydigitaldownloads.com', // Site where EDD is hosted
		'item_name' => 'Theme Name', // Name of theme
		'theme_slug' => 'theme-slug', // Theme slug
		'version' => '1.0.0', // The current version of this theme
		'author' => 'Easy Digital Downloads', // The author of this theme
		'download_id' => '', // Optional, used for generating a license renewal link
		'renew_url' => '' // Optional, allows for a custom license renewal link
	),

	// Strings
	$strings = array(
		'theme-license' => __( 'Theme License', 'edd-theme-updater' ),
		'enter-key' => __( 'Enter your theme license key.', 'edd-theme-updater' ),
		'license-key' => __( 'License Key', 'edd-theme-updater' ),
		'license-action' => __( 'License Action', 'edd-theme-updater' ),
		'deactivate-license' => __( 'Deactivate License', 'edd-theme-updater' ),
		'activate-license' => __( 'Activate License', 'edd-theme-updater' ),
		'status-unknown' => __( 'License status is unknown.', 'edd-theme-updater' ),
		'renew' => __( 'Renew?', 'edd-theme-updater' ),
		'unlimited' => __( 'unlimited', 'edd-theme-updater' ),
		'license-key-is-active' => __( 'License key is active.', 'edd-theme-updater' ),
		'expires%s' => __( 'Expires %s.', 'edd-theme-updater' ),
		'%1$s/%2$-sites' => __( 'You have %1$s / %2$s sites activated.', 'edd-theme-updater' ),
		'license-key-expired-%s' => __( 'License key expired %s.', 'edd-theme-updater' ),
		'license-key-expired' => __( 'License key has expired.', 'edd-theme-updater' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'edd-theme-updater' ),
		'license-is-inactive' => __( 'License is inactive.', 'edd-theme-updater' ),
		'license-key-is-disabled' => __( 'License key is disabled.', 'edd-theme-updater' ),
		'site-is-inactive' => __( 'Site is inactive.', 'edd-theme-updater' ),
		'license-status-unknown' => __( 'License status is unknown.', 'edd-theme-updater' ),
		'update-notice' => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'edd-theme-updater' ),
		'update-available' => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'edd-theme-updater' )
	)

);
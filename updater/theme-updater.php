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
	$strings = array(
		'theme-license' => __( 'Theme License', 'textdomain' ),
		'enter-key' => __( 'Enter your theme license key.', 'textdomain' ),
		'license-key' => __( 'License Key', 'textdomain' ),
		'license-action' => __( 'License Action', 'textdomain' ),
		'deactivate-license' => __( 'Deactivate License', 'textdomain' ),
		'activate-license' => __( 'Activate License', 'textdomain' ),
		'status-unknown' => __( 'License status is unknown.', 'textdomain' ),
		'renew' => __( 'Renew?', 'textdomain' ),
		'unlimited' => __( 'unlimited', 'textdomain' ),
		'license-key-is-active' => __( 'License key is active.', 'textdomain' ),
		'expires%s' => __( 'Expires %s.', 'textdomain' ),
		'%1$s/%2$-sites' => __( 'You have %1$s / %2$s sites activated.', 'textdomain' ),
		'license-key-expired-%s' => __( 'License key expired %s.', 'textdomain' ),
		'license-key-expired' => __( 'License key has expired.', 'textdomain' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'textdomain' ),
		'license-is-inactive' => __( 'License is inactive.', 'textdomain' ),
		'license-key-is-disabled' => __( 'License key is disabled.', 'textdomain' ),
		'site-is-inactive' => __( 'Site is inactive.', 'textdomain' ),
		'license-status-unknown' => __( 'License status is unknown.', 'textdomain' ),
		'update-notice' => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'textdomain' ),
		'update-available' => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'textdomain' )
	)
);
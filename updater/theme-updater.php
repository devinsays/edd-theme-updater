<?php
/**
 * This is just a demonstration of how theme licensing works with
 * Easy Digital Downloads.
 *
 * @package EDD Theme Updater
 */

// Includes the files needed for the theme updater
include( dirname( __FILE__ ) . '/theme-updater-admin.php' );

// Loads the theme updater admin class
$updater = new Prefix_Theme_Updater_Admin;

// Defines variables to be used by the theme updater
$updater->init(
	array(
		'remote_api_url' => 'http://yoursite.com', // URL of site running EDD
		'remote_api_url' => 'http://localhost:8888/devpress',
		'theme_slug' => 'Summit', // The name of this theme
		'version' => '0.1.0', // The current version of this theme
		'author' => 'Devin Price' // The author of this theme
	)
);

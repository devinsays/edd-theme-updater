<?php
/**
 * This is just a demonstration of how theme licensing works with
 * Easy Digital Downloads.
 *
 * @package EDD Theme Updater
 */

// Includes the files needed for the thme updater
include( dirname( __FILE__ ) . '/theme-updater-admin.php' );

// Initalizes the theme updater class
$updater = new Prefix_Theme_Updater_Admin;
$updater->init(
	array(
		'remote_api_url' => 'http://yoursite.com', // URL of site running EDD
		'theme_slug' => 'edd-theme-updater', // The name of this theme
		'version' => '0.1.0', // The current version of this theme
		'author' => 'Pippin Williamson' // The author of this theme
	)
);

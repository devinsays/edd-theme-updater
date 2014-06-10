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
 * 1. Find/replace "prefix" with your theme prefix
 * 2. Find/replace "textdomain" with your theme textdomain
 * 3. Update the variables in prefix_theme_info()
 * 4. Delete this doc block
 */

if ( !class_exists( 'EDD_SL_Theme_Updater' ) ) {
	// Load our custom theme updater
	include( dirname( __FILE__ ) . '/theme-updater-class.php' );
}

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
 * Creates the updater class.
 *
 * since 1.0.0
 */
new EDD_SL_Theme_Updater( array(
		'remote_api_url' 	=> prefix_updater_settings( 'remote_api_url' ),
		'version' 			=> prefix_updater_settings( 'version' ),
		'license' 			=> trim( get_option( 'prefix_license_key' ) ),
		'item_name' 		=> prefix_updater_settings( 'theme_slug' ),
		'author'			=> prefix_updater_settings( 'author' )
	)
);

/**
 * Adds a menu item for the theme license under the appearance menu.
 */
function prefix_license_menu() {
	add_theme_page(
		__( 'Theme License', 'textdomain' ),
		__( 'Theme License', 'textdomain' ),
		'manage_options', 'themename-license',
		'prefix_license_page'
	);
}
add_action( 'admin_menu', 'prefix_license_menu' );

/**
 * Outputs the markup used on the theme license page.
 *
 * since 1.0.0
 */
function prefix_license_page() {

	$license = trim( get_option( 'prefix_license_key' ) );
	$status = get_option( 'prefix_license_key_status', false );

	// Checks license status to display under license key
	if ( ! $license ) {
		$message    = __( 'Enter your theme license key.', 'textdomain' );
	} else {
		delete_transient( 'prefix_license_message' );
		if ( ! get_transient( 'prefix_license_message', false ) ) {
			set_transient( 'prefix_license_message', prefix_check_license(), ( 60 * 60 * 24 ) );
		}
		$message = get_transient( 'prefix_license_message' );
	}
	?>
	<div class="wrap">
		<h2><?php _e( 'Theme License Options', 'textdomain' ); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields( 'prefix_license' ); ?>

			<table class="form-table">
				<tbody>

					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'License Key', 'textdomain' ); ?>
						</th>
						<td>
							<input id="prefix_license_key" name="prefix_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<p class="description">
								<?php echo $message; ?>
							</p>
						</td>
					</tr>

					<?php if ( $license ) { ?>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'License Action', 'textdomain' ); ?>
						</th>
						<td>
							<?php
							wp_nonce_field( 'prefix_sample_nonce', 'prefix_sample_nonce' );
							if ( 'valid' == $status ) { ?>
								<input type="submit" class="button-secondary" name="prefix_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'textdomain' ); ?>"/>
							<?php } else { ?>
								<input type="submit" class="button-secondary" name="prefix_license_activate" value="<?php esc_attr_e( 'Activate License', 'textdomain' ); ?>"/>
							<?php }
							?>
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	<?php
}

/**
 * Registers the option used to store the license key in the options table.
 *
 * since 1.0.0
 */
function prefix_register_option() {
	register_setting( 'prefix_license', 'prefix_license_key', 'prefix_sanitize_license' );
}
add_action( 'admin_init', 'prefix_register_option' );

/**
 * Sanitizes the license key.
 *
 * since 1.0.0
 *
 * @param string $new License key that was submitted.
 * @return string $new Sanitized license key.
 */
function prefix_sanitize_license( $new ) {

	$old = get_option( 'prefix_license_key' );

	if ( $old && $old != $new ) {
		// New license has been entered, so must reactivate
		delete_option( 'prefix_license_key_status' );
		delete_transient( 'prefix_license_message' );
	}

	return $new;
}

/**
 * Activates the license key.
 *
 * @since 1.0.0
 */
function prefix_activate_license() {

	$license = trim( get_option( 'prefix_license_key' ) );

	// Data to send in our API request.
	$api_params = array(
		'edd_action' => 'activate_license',
		'license' => $license,
		'item_name' => urlencode( prefix_updater_settings( 'theme_slug' ) )
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, prefix_updater_settings( 'remote_api_url' ) ), array( 'timeout' => 15, 'sslverify' => false ) );

	// Make sure the response came back okay.
	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// $license_data->license will be either "active" or "inactive"
	update_option( 'prefix_license_key_status', $license_data->license );
	delete_transient( 'prefix_license_message' );

}
add_action( 'update_option_prefix_license_key', 'prefix_activate_license', 10, 2 );

/**
 * Deactivates the license key.
 *
 * @since 1.0.0
 */
function prefix_deactivate_license() {

	// Retrieve the license from the database.
	$license = trim( get_option( 'prefix_license_key' ) );


	// Data to send in our API request.
	$api_params = array(
		'edd_action'=> 'deactivate_license',
		'license' 	=> $license,
		'item_name' => urlencode( prefix_updater_settings( 'theme_slug' ) )
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, prefix_updater_settings( 'remote_api_url' ) ), array( 'timeout' => 15, 'sslverify' => false ) );

	// Make sure the response came back okay
	if ( is_wp_error( $response ) ) {
		return false;
	}

	// decode the license data
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// $license_data->license will be either "deactivated" or "failed"
	if ( $license_data->license == 'deactivated' ) {
		delete_option( 'prefix_license_key_status' );
		delete_transient( 'prefix_license_message' );
	}
}

/**
 * Checks if a license action was submitted.
 *
 * @since 1.0.0
 */
function prefix_license_action() {

	if ( isset( $_POST['prefix_license_activate'] ) ) {
		if ( check_admin_referer( 'prefix_sample_nonce', 'prefix_sample_nonce' ) ) {
			prefix_activate_license();
		}
	}

	if ( isset( $_POST['prefix_license_deactivate'] ) ) {
		if ( check_admin_referer( 'prefix_sample_nonce', 'prefix_sample_nonce' ) ) {
			prefix_deactivate_license();
		}
	}

}
add_action( 'admin_init', 'prefix_license_action' );

/**
 * Checks if license is valid and gets expire date.
 *
 * @since 1.0.0
 *
 * @return string $message License status message.
 */
function prefix_check_license() {

	$license = trim( get_option( 'prefix_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( prefix_updater_settings( 'theme_slug' ) )
	);

	$response = wp_remote_get( add_query_arg( $api_params, prefix_updater_settings( 'remote_api_url' ) ), array( 'timeout' => 15, 'sslverify' => false ) );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// If response doesn't include license data, return
	if ( !isset( $license_data->license ) ) {
		$message =  __( 'License status is unknown.', 'textdomain' );
		return $message;
	}

	// Get expire date
	$expires = false;
	if ( isset( $license_data->expires ) ) {
		$expires = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires ) );
		$renew_link = '<a href="' . esc_url( prefix_updater_settings( 'remote_api_url' ) ) . '">' . __( 'Renew?', 'textdomain' ) . '</a>';
	}

	if ( $license_data->license == 'valid' ) {
		$message = __( 'License key is active.', 'textdomain' );
		if ( $expires ) {
			$message .= sprintf( __( ' Expires %s.', 'textdomain' ), $expires );
		}
	} else if ( $license_data->license == 'expired' ) {
		if ( $expires ) {
			$message = sprintf( __( 'License key expired %s.', 'textdomain' ), $expires );
		} else {
			$message = __( 'License key has expired.', 'textdomain' );
		}
		if ( $renew_link ) {
			$message .= ' ' . $renew_link;
		}
	} else if ( $license_data->license == 'invalid' ) {
		$message =  __( 'License keys do not match.', 'textdomain' );
	} else if ( $license_data->license == 'inactive' ) {
		$message =  __( 'License is inactive.', 'textdomain' );
	} else if ( $license_data->license == 'disabled' ) {
		$message =  __( 'License key is disabled.', 'textdomain' );
	} else if ( $license_data->license == 'site_inactive' ) {
		// Site is inactive
		$message =  __( 'Site is inactive.', 'textdomain' );
	} else {
		$message =  __( 'License status is unknown.', 'textdomain' );
	}

	return $message;
}
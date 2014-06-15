<?php
/**
 * Theme updater admin page and functions.
 *
 * @package EDD Theme Updater
 */

class Prefix_Theme_Updater_Admin {

	/**
	 * Variables required for the theme updater
	 *
	 * @since 1.0.0
	 * @type string
	 */
	 protected $remote_api_url = null;
	 protected $theme_slug = null;
	 protected $version = null;
	 protected $author = null;

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function init( $args ) {

		// Set args
		$this->remote_api_url = $args['remote_api_url'];
		$this->theme_slug = $args['theme_slug'];
		$this->version = $args['version'];
		$this->author = $args['remote_api_url'];

		add_action( 'admin_init', array( $this, 'updater' ) );
		add_action( 'admin_menu', array( $this, 'prefix_license_menu' ) );
		add_action( 'admin_init', array( $this, 'prefix_register_option' ) );
		add_action( 'update_option_prefix_license_key', array( $this, 'activate_license' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'license_action' ) );

	}

	/**
	 * Creates the updater class.
	 *
	 * since 1.0.0
	 */
	function updater() {

		/* If there is no valid license key status, don't allow updates. */
		if ( get_option( 'prefix_license_key_status', false) != 'valid' ) {
			return;
		}

		if ( !class_exists( 'EDD_SL_Theme_Updater' ) ) {
			// Load our custom theme updater
			include( dirname( __FILE__ ) . '/theme-updater-class.php' );
		}

		new Prefix_Theme_Updater( array(
				'remote_api_url' 	=> $this->remote_api_url,
				'version' 			=> updater_settings( 'version' ),
				'license' 			=> trim( get_option( 'prefix_license_key' ) ),
				'item_name' 		=> $this->theme_slug,
				'author'			=> $this->author
			)
		);
	}

	/**
	 * Adds a menu item for the theme license under the appearance menu.
	 *
	 * since 1.0.0
	 */
	function prefix_license_menu() {
		add_theme_page(
			__( 'Theme License', 'textdomain' ),
			__( 'Theme License', 'textdomain' ),
			'manage_options',
			'prefix-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * since 1.0.0
	 */
	function license_page() {

		$license = trim( get_option( 'prefix_license_key' ) );
		$status = get_option( 'prefix_license_key_status', false );

		// Checks license status to display under license key
		if ( ! $license ) {
			$message    = __( 'Enter your theme license key.', 'textdomain' );
		} else {
			// delete_transient( 'prefix_license_message' );
			if ( ! get_transient( 'prefix_license_message', false ) ) {
				set_transient( 'prefix_license_message', $this->prefix_check_license(), ( 60 * 60 * 24 ) );
			}
			$message = get_transient( 'prefix_license_message' );
		}
		?>
		<div class="wrap">
			<h2><?php _e( 'Theme License', 'textdomain' ); ?></h2>
			<form method="post" action="options.php">

				<?php settings_fields( 'prefix_license' ); ?>

				<table class="form-table">
					<tbody>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'License Key', 'textdomain' ); ?>
							</th>
							<td>
								<input id="prefix_license_key" name="prefix_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
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
								wp_nonce_field( 'prefix_nonce', 'prefix_nonce' );
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
		register_setting(
			'prefix_license',
			'prefix_license_key',
			array( $this, 'sanitize_license' )
		);
	}

	/**
	 * Sanitizes the license key.
	 *
	 * since 1.0.0
	 *
	 * @param string $new License key that was submitted.
	 * @return string $new Sanitized license key.
	 */
	function sanitize_license( $new ) {

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
	function activate_license() {

		$license = trim( get_option( 'prefix_license_key' ) );

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->theme_slug )
		);

		// Call the custom API.
		$response = wp_remote_get(
			add_query_arg( $api_params, $this->remote_api_url ),
			array( 'timeout' => 15, 'sslverify' => false )
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data && isset( $license_data->license ) ) {
			// $license_data->license will be either "active" or "inactive"
			update_option( 'prefix_license_key_status', $license_data->license );
			delete_transient( 'prefix_license_message' );
		}

	}

	/**
	 * Deactivates the license key.
	 *
	 * @since 1.0.0
	 */
	function deactivate_license() {

		// Retrieve the license from the database.
		$license = trim( get_option( 'prefix_license_key' ) );


		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->theme_slug )
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg(
			$api_params,
			$this->remote_api_url),
			array( 'timeout' => 15, 'sslverify' => false )
		);

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
	function license_action() {

		if ( isset( $_POST['prefix_license_activate'] ) ) {
			if ( check_admin_referer( 'prefix_nonce', 'prefix_nonce' ) ) {
				$this->activate_license();
			}
		}

		if ( isset( $_POST['prefix_license_deactivate'] ) ) {
			if ( check_admin_referer( 'prefix_nonce', 'prefix_nonce' ) ) {
				$this->deactivate_license();
			}
		}

	}

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
			'license'    => $license,
			'item_name'  => urlencode( $this->theme_slug )
		);

		$response = wp_remote_get(
			add_query_arg( $api_params, $this->remote_api_url ),
			array( 'timeout' => 15, 'sslverify' => false )
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// If response doesn't include license data, return
		if ( !isset( $license_data->license ) ) {
			$message = __( 'License status is unknown.', 'textdomain' );
			return $message;
		}

		// Get expire date
		$expires = false;
		if ( isset( $license_data->expires ) ) {
			$expires = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires ) );
			$renew_link = '<a href="' . esc_url( $this->remote_api_url) . '">' . __( 'Renew?', 'textdomain' ) . '</a>';
		}

		// Get site counts
		$site_count = $license_data->site_count;
		$license_limit = $license_data->license_limit;

		// If unlimited
		if ( 0 == $license_limit ) {
			$license_limit = __( 'unlimited', 'textdomain' );
		}

		if ( $license_data->license == 'valid' ) {
			$message = __( 'License key is active.', 'textdomain' ) . ' ';
			if ( $expires ) {
				$message .= sprintf( __( 'Expires %s.', 'textdomain' ), $expires ) . ' ';
			}
			if ( $site_count && $license_limit ) {
				$message .= sprintf( _n( 'You have %1$s / %2$s site activated.', 'You have %1$s / %2$s sites activated.', $site_count, 'textdomain' ), $site_count, $license_limit );
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
			$message = __( 'License keys do not match.', 'textdomain' );
		} else if ( $license_data->license == 'inactive' ) {
			$message = __( 'License is inactive.', 'textdomain' );
		} else if ( $license_data->license == 'disabled' ) {
			$message = __( 'License key is disabled.', 'textdomain' );
		} else if ( $license_data->license == 'site_inactive' ) {
			// Site is inactive
			$message = __( 'Site is inactive.', 'textdomain' );
		} else {
			$message = __( 'License status is unknown.', 'textdomain' );
		}

		return $message;
	}

}
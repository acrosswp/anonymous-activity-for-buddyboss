<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin license activations
 *
 * @link       https://acrosswp.com
 * @since      0.0.1
 *
 * @package    Post_Anonymously_For_BuddyBoss
 * @subpackage Post_Anonymously_For_BuddyBoss/includes
 */

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	
    /**
     * The class responsible for loading edd updater class
     * core plugin.
     */
    require_once POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_PATH . 'admin/licenses/EDD_SL_Plugin_Updater.php';
}


/**
 * Fired during plugin licenses.
 *
 * This class defines all code necessary to run during the plugin's licenses and update.
 *
 * @since      0.0.1
 * @package    AcrossWP_Main_Menu_Licenses
 * @subpackage AcrossWP_Main_Menu_Licenses/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class AcrossWP_Main_Menu_Licenses {

    /**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_For_BuddyBoss_Loader
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * Load the licenses for the plugins
	 *
	 * @since 0.0.1
	 */
	protected $packages = array();

	/**
	 * Load the licenses for the plugins
	 *
	 * @since 0.0.1
	 */
	protected $store_url = 'https://acrosswp.com';

	/**
	 * Load the licenses for the plugins
	 *
	 * @since 0.0.1
	 */
	protected $licence_key = 'acrosswp_license_details';

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->define( 'ACROSSWP_MAIN_MENU_LICENSES', 'acrosswp-licenses' );

		$this->packages = apply_filters( 'acrosswp_plugins_licenses', $this->packages );

		/**
		 * Add the parent menu into the Admin Dashboard
		 */
		add_action( 'admin_menu', array( $this, 'license_menu' ) );

		/**
		 * Action to do update for the plugins
		 */
		add_action( 'init', array( $this, 'plugin_updater' ) );
	}

	/**
	 * Update plugin if the licenses is valid
	 */
	public function plugin_updater() {

		// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}

		/**
		 * Check if the $this->get_packages() is empty or not
		 */
		if( ! empty( $this->get_packages() ) ) {
			foreach ( $this->get_packages() as $package ) {

				// retrieve our license key from the DB
				$license_key = $this->get_license_key( $package['key'] );
				$product_id = $package['id'];
				$product_version = $package['version'];

				if( ! empty( $license_key ) && ! empty( $product_id ) && ! empty( $product_version ) ) {
					// setup the updater
					$edd_updater = new EDD_SL_Plugin_Updater(
						$this->store_url,
						POST_ANONYMOUSLY_FOR_BUDDYBOSS_FILES,
						array(
							'version' => $product_version,
							'license' => $license_key,
							'item_id' => $product_version,
							'author'  => 'AcrossWP',
							'beta'    => false,
						)
					);
				}
			}
		}
	}

	/**
	 * Get specficy the AcrossWP Licenses
	 */
	public function update_license_key( $key, $license ) {

		$license_key = $this->get_license_keys();

		/**
		 * Add licences key into the DB
		 */
		if( ! empty( $license ) ) {
			$license_key[ $key ]['license'] = trim( $license );
		} elseif( isset( $license_key[ $key ] ) ) {
			/**
			 * Remove licences key into the DB
			 */
			unset( $license_key[ $key ] );
		}

		$this->update_option( $license_key );
	}

	/**
	 * Get specficy the AcrossWP Licenses
	 */
	public function update_license_status( $key, $status ) {

		$license_key = $this->get_license_keys();

		/**
		 * Add licences status into the DB
		 */
		if( ! empty( $status ) ) {
			$license_key[ $key ]['status'] = trim( $status );
		} elseif( isset( $license_key[ $key ]['status'] ) ) {
			/**
			 * Remove licences status into the DB
			 */
			unset( $license_key[ $key ]['status'] );
		}

		$this->update_option( $license_key );
	}

	/**
	 * Update options
	 */
	public function update_option( $license_key ) {
		update_option( $this->licence_key, $license_key );
	}

	/**
	 * Get specficy the AcrossWP Licenses
	 */
	public function get_license_key( $key ) {

		$license_key = $this->get_license_keys();

		/**
		 * Check if the key exits or not
		 */
		if( empty( $license_key[ $key ] ) ) {
			return false;
		}

		if( empty( $license_key[ $key ]['license'] ) ) {
			return false;
		}

		return trim( $license_key[ $key ]['license'] );
	}

	/**
	 * Get specficy the AcrossWP Licenses
	 */
	public function get_license_status( $key ) {

		$license_key = $this->get_license_keys();

		/**
		 * Check if the key exits or not
		 */
		if( empty( $license_key[ $key ] ) ) {
			return false;
		}

		if( empty( $license_key[ $key ]['status'] ) ) {
			return false;
		}

		return trim( $license_key[ $key ]['status'] );
	}

	/**
	 * Get all the AcrossWP Licenses key
	 */
	public function get_license_keys() {
		return get_option( $this->licence_key, false );
	}

	/**
	 * Get all the AcrossWP Licenses key
	 */
	public function get_package( $key ) {

		$plugin_package = false;

		$packages = $this->get_packages();

		if( empty( $packages ) ) {
			return $plugin_package;
		}

		foreach( $packages as $package ) {
			if( ! empty( $package['key'] ) && $key == $package['key'] ) {
				$plugin_package = $package;
				break;
			}
		}

		return $plugin_package;
	}

	/**
	 * Get all the AcrossWP Licenses key
	 */
	public function get_packages() {
		return $this->packages;
	}

	/**
	 * Main Post_Anonymously_For_BuddyBoss_Loader Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 0.0.1
	 * @static
	 * @see Post_Anonymously_For_BuddyBoss_Loader()
	 * @return Post_Anonymously_For_BuddyBoss_Loader - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
     * Adds the plugin license page to the admin menu.
     *
     * @return void
     */
    function license_menu() {

		/**
		 * Check if the class exits then only add the submenu
		 */
		if ( class_exists( 'AcrossWP_Main_Menu' ) && ! empty( $this->get_packages() ) ) {
			add_submenu_page(
				ACROSSWP_MAIN_MENU,
				__( 'AcrossWP License Keys', 'post-anonymously-for-buddyboss' ),
				__( 'License Keys', 'post-anonymously-for-buddyboss' ),
				'manage_options',
				ACROSSWP_MAIN_MENU_LICENSES,
				array( $this, 'licenses_page' )
			);
		}
    }

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Display the licenses page of the AcrossWP
	 */
	public function licenses_page () {
		?>
		<div class="wrap acrosswp-updater-wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<div class="acrosswp-updater-block-container">
				<div class="acrosswp-updater-block">
					<div class="inside">
						<h2><?php _e( 'Benefits of a License', 'buddyboss-pro' ); ?></h2>
						<ul>
							<li>
								<strong><?php _e( 'Stay Up to Date', 'buddyboss-pro' ); ?></strong><br/>
								<?php _e( 'Get the latest features right away', 'buddyboss-pro' ); ?>
							</li>
							<li>
								<strong><?php _e( 'Admin Notifications', 'buddyboss-pro' ); ?></strong><br/>
								<?php _e( 'Get updates in WordPress', 'buddyboss-pro' ); ?>
							</li>
							<li>
								<strong><?php _e( 'Professional Support', 'buddyboss-pro' ); ?></strong><br/>
								<?php _e( 'Get help with any questions', 'buddyboss-pro' ); ?>
							</li>
						</ul>
					</div>
				</div>

			</div>

			<?php
			$this->show_form_post_response();
			?>
			<div class='acrosswp-updater-settings clearfix'>
				<div class="setting-tabs-wrapper">
					<ul>
						<?php $this->print_settings_tabs(); ?>
					</ul>
				</div>
				<div class='tabs-panel'>
					<?php $this->print_settings_content(); ?>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Show the activity and deactivitu responce
	 */
	public function show_form_post_response() {

		$this->activate_license_main();
		$this->deactivate_license_main();
	}

	public function print_settings_tabs() {
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

		$is_first_tab = true;

		if ( empty( $this->get_packages() ) ) {
			return;
		}

		foreach ( $this->get_packages() as $package ) {
			$active = $active_tab == $package['key'] ? 'active' : '';
			if ( ! $active_tab && $is_first_tab ) {
				$active = 'active';
			}
			$is_first_tab = false;

			$dashicon_class = 'lock';

			$package_status = $this->get_package_status( $package['key'] );
			switch ( $package_status ) {
				case 'active':
					$dashicon_class = 'yes-alt';
					break;
				case 'inactive':
					$dashicon_class = 'warning';
					break;
				case 'active_indirect':
					$dashicon_class = 'yes-alt indirect';
					break;
			}

			$dashicon = "<span class='dashicons dashicons-{$dashicon_class}'></span>";
			echo '<li class="' . $active . '"><a href="?page=' . ACROSSWP_MAIN_MENU_LICENSES . '&tab=' . $package['key'] . '">' . $dashicon . $package['name'] . '</a></li>';
		}
	}

	/**
	 * Print the licences fields
	 */
	public function print_settings_content() {

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

		$currnet_package = false;

		$packages = $this->get_packages();

		if ( ! $active_tab ) {
			// get first package. That becomes the active tab
			if ( ! empty( $packages ) ) {
				foreach ( $packages as $package ) {
					$active_tab = $package['key'];
					$currnet_package = $package;
					break;
				}
			}
		} else {
			$currnet_package = reset( $packages );
		}

		$this->load_license_key_settings_field( $currnet_package );
	}

	/**
	 * Load the licenses fields setting form
	 */
	function load_license_key_settings_field( $license ) {
		$license_key = $license['key'];
		$license_status = $license['id'];
		$name = $license['name'];

		$license = $this->get_license_key( $license_key );
		$status  = $this->get_license_status( $license_key );

	?>
	<p class="description"><?php echo $name; ?></p>
		<form method="post" action="">
			<?php
			printf(
				'<input type="text" class="regular-text" id="acrosswp_license_details" name="acrosswp_license_details" value="%s" />',
				$license
			);

			printf(
				'<input type="hidden" class="regular-text" id="acrosswp_licenses_key" name="acrosswp_licenses_key" value="%s" />',
				$license_key
			);

			$button = array(
				'name'  => 'acrosswp_license_deactivate',
				'label' => __( 'Deactivate License' ),
			);

			if ( 'valid' !== $status ) {
				$button = array(
					'name'  => 'acrosswp_license_activate',
					'label' => __( 'Activate License' ),
				);
			}

			wp_nonce_field( 'acrosswp_licenses_nonce_action', 'acrosswp_licenses_nonce' );
			?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>"/>
		</form><?php
	}

	function get_package_status( $key ) {
		return false;
	}

	/**
	 * Deactivates the license key.
	 */
	public function deactivate_license_main() {
		// listen for our activate button to be clicked
		if ( isset( $_POST['acrosswp_license_deactivate'] ) ) {

			// run a quick security check
			if ( empty( wp_verify_nonce( $_POST['acrosswp_licenses_nonce'], 'acrosswp_licenses_nonce_action' ) ) ) {
				return; // get out if we didn't click the Activate button
			}

			$key = isset( $_POST['acrosswp_licenses_key'] ) ? sanitize_text_field( $_POST['acrosswp_licenses_key'] ) : false;
		
			// retrieve the license from the database
			$license = ! empty( $_POST['acrosswp_license_details'] ) ? sanitize_text_field( $_POST['acrosswp_license_details'] ) : false;
			if ( empty( $license ) && ! empty( $key ) ) {
				$license = $this->get_license_key( $key );
			}

			$package = $this->get_package( $key );
			
			// Call the custom API.
			$response = $this->deactivate_license( $license, $package );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}
			} else {
				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed"
				if ( 'deactivated' === $license_data->license ) {
					$this->update_license_status( $key, false );
				}
			}
		}
	}

	/**
	 * Send the request to the licences
	 */
	function deactivate_license( $license, $package ) {

		$api_params = $this->parse_args( $license, $package, array( 'edd_action' => 'deactivate_license' ) );

		// Call the custom API.
		return $this->wp_remote_post( $api_params );
	}
		
	/**
	 * Activates the license key.
	 *
	 * @return void
	 */
	public function activate_license_main() {

		// listen for our activate button to be clicked
		if ( ! isset( $_POST['acrosswp_license_activate'] ) ) {
			return;
		}

		// run a quick security check
		if ( empty( wp_verify_nonce( $_POST['acrosswp_licenses_nonce'], 'acrosswp_licenses_nonce_action' ) ) ) {
			return; // get out if we didn't click the Activate button
		}
		
		$key = isset( $_POST['acrosswp_licenses_key'] ) ? sanitize_text_field( $_POST['acrosswp_licenses_key'] ) : false;
		
		// retrieve the license from the database
		$license = ! empty( $_POST['acrosswp_license_details'] ) ? sanitize_text_field( $_POST['acrosswp_license_details'] ) : false;
		if ( empty( $license ) && ! empty( $key ) ) {
			$license = $this->get_license_key( $key );
		}
		
		$package = $this->get_package( $key );

		if ( empty( $license ) || empty( $package ) ) {
			return;
		}

		// Call the custom API.
		$response = $this->activate_license( $license, $package );

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = sprintf(
							/* translators: the license key expiration date */
							__( 'Your license key expired on %s.', 'post-anonymously-for-buddyboss' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'post-anonymously-for-buddyboss' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'post-anonymously-for-buddyboss' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'post-anonymously-for-buddyboss' );
						break;

					case 'item_name_mismatch':
						/* translators: the plugin name */
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'post-anonymously-for-buddyboss' ), EDD_SAMPLE_ITEM_NAME );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'post-anonymously-for-buddyboss' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'post-anonymously-for-buddyboss' );
						break;
				}
			}
		}

			// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			?>
			<div class="error">
				<p><?php echo wp_kses_post( $message ); ?></p>
			</div>
			<?php
		} elseif ( 'valid' === $license_data->license ) {
			$this->update_license_key( $key, $license );
			$this->update_license_status( $key, $license_data->license );

			?>
			<div class="success">
				<p><?php echo wp_kses_post( $message ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Send the request to the licences
	 */
	function activate_license( $license, $package ) {

		$api_params = $this->parse_args( $license, $package, array( 'edd_action' => 'activate_license' ) );

		// Call the custom API.
		return $this->wp_remote_post( $api_params );
	}

	/**
	 * Checks if a license key is still valid.
	 * The updater does this for you, so this is only needed if you want
	 * to do somemthing custom.
	 *
	 * @return void
	 */
	public function check_license_main( $key ) {

		$package = $this->get_package( $key );
		$license = $this->get_license_key( $key );

		// Call the custom API.
		$response = $this->check_license( $license, $package );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 'valid' === $license_data->license ) {
			return 'valid';
		} else {
			return 'invalid';
		}
	}

	/**
	 * Checks if a license key is still valid.
	 * The updater does this for you, so this is only needed if you want
	 * to do somemthing custom.
	 *
	 * @return void
	 */
	public function check_license( $license, $package ) {

		$api_params = $this->parse_args( $license, $package, array( 'edd_action' => 'check_license' ) );

		return $this->wp_remote_post( $api_params );
	}

	/**
	 * Add the default args
	 * 
	 * Default it will Check licence status
	 */
	public function parse_args( $license, $package, $args = array() ) {
		
		$default = array(
			'edd_action'  => 'check_license',
			'license'     => $license,
			'item_id'     => $package['id'],
			'item_name'   => rawurlencode( $package['name'] ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		return wp_parse_args( $args, $default );
	}

	/**
	 * Send the request to the licences
	 */
	function wp_remote_post( $api_params ) {

		// Call the custom API.
		return wp_remote_post(
			$this->store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);
	}
}
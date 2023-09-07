<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin license activations
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
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
 * @since      1.0.0
 * @package    AcrossWP_Main_Menu_Licenses
 * @subpackage AcrossWP_Main_Menu_Licenses/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class AcrossWP_Main_Menu_Licenses {

    /**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_For_BuddyBoss_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Load the licences for the plugins
	 *
	 * @since 1.0.0
	 */
	protected $packages = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->define( 'ACROSSWP_MAIN_MENU_LICENSES', 'acrosswp-licenses' );

		$this->packages = apply_filters( 'acrosswp_plugins_licenses', $this->packages );

		/**
		 * Add the parent menu into the Admin Dashboard
		 */
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
	}

	/**
	 * Main Post_Anonymously_For_BuddyBoss_Loader Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
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
		if ( class_exists( 'AcrossWP_Main_Menu' ) && ! empty( $this->packages ) ) {
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
			</div><!-- .acrosswp-updater-settings -->

		</div>
		<?php
	}

	function show_form_post_response() {
		echo "show_form_post_response";
	}

	public function print_settings_tabs() {
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

		$is_first_tab = true;

		if ( empty( $this->packages ) ) {
			return;
		}

		foreach ( $this->packages as $package ) {
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

	public function print_settings_content() {

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

		$license = false;

		if ( ! $active_tab ) {
			// get first package. That becomes the active tab
			if ( ! empty( $this->packages ) ) {
				foreach ( $this->packages as $package ) {
					$active_tab = $package['key'];
					$license = $package;
					break;
				}
			}
		} else {
			$license = $this->packages[0];
		}

		$this->load_license_key_settings_field( $license );
	}

	/**
	 * Load the licences fields setting form
	 */
	function load_license_key_settings_field( $license ) {
		$key = $license['key'];
		$name = $license['name'];
		$license_key = $key . '_key';
		$license_status = $key . '_status';

		$license = get_option( $license_key );
		$status  = get_option( $license_status );

	?>
	<p class="description"><?php echo $name; ?></p>
		<form method="post" action="options.php">
			<?php
			printf(
				'<input type="text" class="regular-text" id="%s" name="%s" value="%s" />',
				$license_key,
				$license_key,
				esc_attr( $license )
			);

			$button = array(
				'name'  => 'edd_license_deactivate',
				'label' => __( 'Deactivate License' ),
			);

			if ( 'valid' !== $status ) {
				$button = array(
					'name'  => 'edd_license_activate',
					'label' => __( 'Activate License' ),
				);
			}

			wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' );
			?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>"/>
		</form><?php
	}

	function get_package_status( $key ) {
		return false;
	}
}
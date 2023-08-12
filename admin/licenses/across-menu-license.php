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
 * Fired during plugin licences.
 *
 * This class defines all code necessary to run during the plugin's licences and update.
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
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->define( 'ACROSSWP_MAIN_MENU_LICENSES', 'acrosswp-licences' );

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
		if ( class_exists( 'AcrossWP_Main_Menu' ) ) {
			add_submenu_page(
				ACROSSWP_MAIN_MENU,
				__( 'AcrossWP License Keys', 'post-anonymously-for-buddyboss' ),
				__( 'License Keys', 'post-anonymously-for-buddyboss' ),
				'manage_options',
				ACROSSWP_MAIN_MENU_LICENSES,
				array( $this, 'licences_page' )
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
	 * Display the licences page of the AcrossWP
	 */
	public function licences_page () {
		echo "licences page HTML";
	}
}

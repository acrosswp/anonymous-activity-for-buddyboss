<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    Post_Anonymously_For_BuddyBoss
 * @subpackage Post_Anonymously_For_BuddyBoss/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Post_Anonymously_For_BuddyBoss
 * @subpackage Post_Anonymously_For_BuddyBoss/public
 * @author     AcrossWP <contact@acrosswp.com>
 */
class Post_Anonymously_For_BuddyBoss_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Use to load all the class and files
	 */
	public function load_class() {
		/**
		 * The class responsible for defining all functions that can be use
		 * side of the site.
		 */
		require_once POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_PATH . 'public/partials/post-anonymously-for-buddyboss-public-common.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing save group and activity meta
		 * side of the site.
		 */
		require_once POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_PATH . 'public/partials/post-anonymously-for-buddyboss-public-save-meta.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing for rendering activity
		 * side of the site.
		 */
		require_once POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_PATH . 'public/partials/post-anonymously-for-buddyboss-public-render-activity.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing for rendering notifications
		 * side of the site.
		 */
		require_once POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_PATH . 'public/partials/post-anonymously-for-buddyboss-public-render-notifications.php';
	}

	/**
	 * Register the hooks that are going to load into the bp_init
	 *
	 * @since    1.0.0
	 */
	public function bp_init() {

		/**
		 * Load all the files first
		 */
		$this->load_class();

		Post_Anonymously_For_BuddyBoss_Public_Save_Meta::instance()->hooks();


		Post_Anonymously_For_BuddyBoss_Public_Render_Activity::instance()->hooks();


		Post_Anonymously_For_BuddyBoss_Public_Render_Notifications::instance()->hooks();
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Post_Anonymously_For_BuddyBoss_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Post_Anonymously_For_BuddyBoss_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_URL . 'assets/dist/css/frontend-style.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Post_Anonymously_For_BuddyBoss_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Post_Anonymously_For_BuddyBoss_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, POST_ANONYMOUSLY_FOR_BUDDYBOSS_PLUGIN_URL . 'assets/dist/js/frontend-script.js', array( 'bp-nouveau-activity-post-form' ), $this->version, true );

	}

}

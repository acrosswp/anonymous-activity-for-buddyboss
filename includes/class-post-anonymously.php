<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/WPBoilerplate/post-anonymously
 * @since      1.0.0
 *
 * @package    Post_Anonymously
 * @subpackage Post_Anonymously/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Post_Anonymously
 * @subpackage Post_Anonymously/includes
 * @author     WPBoilerplate <contact@wpboilerplate.com>
 */
final class Post_Anonymously {
	
	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Post_Anonymously_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The plugin dir path
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_path    The string for plugin dir path
	 */
	protected $plugin_path;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'post-anonymously';

		$this->define_constants();

		if ( defined( 'POST_ANONYMOUSLY_VERSION' ) ) {
			$this->version = POST_ANONYMOUSLY_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->load_composer_dependencies();

		$this->load_dependencies();

		$this->set_locale();

		$this->load_hooks();

	}

	/**
	 * Main Post_Anonymously Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Post_Anonymously()
	 * @return Post_Anonymously - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Define WCE Constants
	 */
	private function define_constants() {

		$this->define( 'POST_ANONYMOUSLY_PLUGIN_FILE', POST_ANONYMOUSLY_FILES );
		$this->define( 'POST_ANONYMOUSLY_PLUGIN_BASENAME', plugin_basename( POST_ANONYMOUSLY_FILES ) );
		$this->define( 'POST_ANONYMOUSLY_PLUGIN_PATH', plugin_dir_path( POST_ANONYMOUSLY_FILES ) );
		$this->define( 'POST_ANONYMOUSLY_PLUGIN_URL', plugin_dir_url( POST_ANONYMOUSLY_FILES ) );
		$this->define( 'POST_ANONYMOUSLY_PLUGIN_NAME_SLUG', $this->plugin_name );
		$this->define( 'POST_ANONYMOUSLY_PLUGIN_NAME', 'Post Anonymously' );

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data( POST_ANONYMOUSLY_PLUGIN_FILE );
		$version = $plugin_data['Version'];
		$this->define( 'POST_ANONYMOUSLY_VERSION', $version );

		$this->define( 'POST_ANONYMOUSLY_PLUGIN_URL', $version );

		$this->plugin_dir = POST_ANONYMOUSLY_PLUGIN_PATH;
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
	 * Register all the hook once all the active plugins are loaded
	 *
	 * Uses the plugins_loaded to load all the hooks and filters
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function load_hooks() {

		/**
		 * Check if plugin can be loaded safely or not
		 * 
		 * @since    1.0.0
		 */
		if ( apply_filters( 'post-anonymously-load', true ) ) {
			$this->define_admin_hooks();
			$this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Post_Anonymously_Loader. Orchestrates the hooks of the plugin.
	 * - Post_Anonymously_i18n. Defines internationalization functionality.
	 * - Post_Anonymously_Admin. Defines all hooks for the admin area.
	 * - Post_Anonymously_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_composer_dependencies() {

		/**
		 * Add composer file
		 */
		if ( file_exists( POST_ANONYMOUSLY_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
			require_once( POST_ANONYMOUSLY_PLUGIN_PATH . 'vendor/autoload.php' );
		}

		/**
		 * For Plugin to check if BuddyBoss Platform plugin is active or not
		 */
		if ( class_exists( 'WPBoilerplate_BuddyBoss_Platform_Dependency' ) ) {
			new WPBoilerplate_BuddyBoss_Platform_Dependency( $this->get_plugin_name(), POST_ANONYMOUSLY_FILES, array( 'activity' ) );
		}

		/**
		 * For Plugin Update via Github
		 */
		if ( class_exists( 'WPBoilerplate_Updater_Checker_Github' ) ) {

			$package = array(
				'repo' 		        => 'https://github.com/acrosswp/post-anonymously',
				'file_path' 		=> POST_ANONYMOUSLY_FILES,
				'name_slug'			=> POST_ANONYMOUSLY_PLUGIN_NAME_SLUG,
				'release_branch' 	=> 'main',
				'release-assets' 	=> false
			);

			new WPBoilerplate_Updater_Checker_Github( $package );
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Post_Anonymously_Loader. Orchestrates the hooks of the plugin.
	 * - Post_Anonymously_i18n. Defines internationalization functionality.
	 * - Post_Anonymously_Admin. Defines all hooks for the admin area.
	 * - Post_Anonymously_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once POST_ANONYMOUSLY_PLUGIN_PATH . 'includes/class-post-anonymously-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once POST_ANONYMOUSLY_PLUGIN_PATH . 'includes/class-post-anonymously-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once POST_ANONYMOUSLY_PLUGIN_PATH . 'admin/class-post-anonymously-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once POST_ANONYMOUSLY_PLUGIN_PATH . 'public/class-post-anonymously-public.php';

		$this->loader = Post_Anonymously_Loader::instance();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Post_Anonymously_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Post_Anonymously_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		
		$plugin_admin = new Post_Anonymously_Admin( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Post_Anonymously_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', -1 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', -1 );

		$this->loader->add_action( 'bp_init', $plugin_public, 'bp_init', 100 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Post_Anonymously_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

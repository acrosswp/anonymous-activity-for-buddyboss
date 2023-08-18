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
	 * Register the hooks that are going to load into the bp_init
	 *
	 * @since    1.0.0
	 */
	public function bp_init() {

		/**
		 * Add post meta into the Group Meta
		 */
		add_action( 'bp_groups_posted_update', array( $this, 'groups_posted_update' ), 1000, 4 );

		/**
		 * Add post meta into the Activity Meta
		 */
		add_action( 'bp_activity_posted_update', array( $this, 'activity_posted_update' ), 1000, 3 );


		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 */
		add_action( 'bp_before_activity_entry', array( $this, 'before_activity_entry' ), 1000 );


		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 */
		add_action( 'bp_after_activity_entry', array( $this, 'after_activity_entry' ), 1000 );
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    1.0.0
	 */
	public function before_activity_entry() {

		$anonymously_post = bp_activity_get_meta( bp_get_activity_id(), 'anonymously-post', true );

		if( ! empty( $anonymously_post ) ) {

			/**
			 * For User Link on the Avatar
			 */
			add_filter( 'bp_get_activity_user_link', array( $this, 'activity_user_link' ), 1000 );

			/**
			 * For User Avatar
			 */
			add_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );

			echo "Test 1";
			/**
			 * For user link and the username in the Activity  after avatar
			 */
			add_filter( 'bp_core_get_userlink', array( $this, 'activity_userlink' ), 1000, 2 );
		}
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    1.0.0
	 */
	public function after_activity_entry() {

		echo "Test 2";

		remove_filter( 'bp_get_activity_user_link', array( $this, 'activity_user_link' ), 1000 );

		remove_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );

		remove_filter( 'bp_core_get_userlink', array( $this, 'activity_userlink' ), 1000 );
	}

	/**
	 * Register the hooks on group post update
	 *
	 * @since    1.0.0
	 */
	public function activity_posted_update( $content, $user_id, $activity_id ) {

		if( ! empty( $_REQUEST['anonymously-post'] ) ) {
			bp_activity_update_meta( $activity_id, 'anonymously-post', 1 );
		}

	}

	/**
	 * Register the hooks on group post update
	 *
	 * @since    1.0.0
	 */
	public function groups_posted_update( $content, $user_id, $group_id, $activity_id ) {

		if( ! empty( $_REQUEST['anonymously-post'] ) ) {
			bp_activity_update_meta( $activity_id, 'anonymously-post', 1 );
		}

	}

	/**
	 * Remove the User Profile Link
	 */
	public function activity_user_link( $link ) {
		return '';
	}

	/**
	 * Remove the User Profile Link
	 */
	public function activity_avatar( $link ) {
		$defaults = array(
			'alt'     => $alt_default,
			'class'   => 'avatar',
			'email'   => false,
			'type'    => $type_default,
			'user_id' => false,
		);

		return bp_core_fetch_avatar( $defaults );
	}

	/**
	 * Remove the User Profile Link
	 */
	public function activity_userlink( $link, $user_id ) {
		return __( 'Anonymous member', 'post-anonymously-for-buddyboss' );
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

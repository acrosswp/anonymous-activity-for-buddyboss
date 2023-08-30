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
		 * Add post meta into the Group Activity Meta
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



		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 */
		add_filter( 'bp_groups_format_activity_action_activity_update', array( $this, 'group_activity_update' ), 1000, 2 );
		
	}


	/**
	 * Check where the activity post author is the login user 
	 */
	function login_user_is_activity_author( $activity_user_id ) {

		$value = false;

		$user_id = get_current_user_id();

		if( ! empty( $user_id ) && $activity_user_id == $user_id ) {
			$value = true;
		}

		return $value;
	}

	/**
	 * Check where the login user is the group moderator
	 */
	function is_group_mods( $activity_user_id, $group_id ) {

		$value = false;

		$user_id = get_current_user_id();
		$group_mods = groups_get_group_mods( $group_id );

		if( ! empty( $user_id ) && in_array( $user_id, $group_mods ) ) {
			$value = true;
		}

		return $value;
	}

	/**
	 * Check where the login user is the group admins
	 */
	function is_group_admins( $activity_user_id, $group_id ) {

		$value = false;

		$user_id = get_current_user_id();
		$group_admins = groups_get_group_admins( $group_id );

		if( ! empty( $user_id ) && in_array( $user_id, $group_admins ) ) {
			$value = true;
		}

		return $value;
	}

	/**
	 * Check if the activity is the anonymously activity
	 */
	function is_anonymously_activity( $activity_id, $activity_user_id ) {

		$value = false;
		$user_id = get_current_user_id();

		if( ! empty( bp_activity_get_meta( $activity_id, 'anonymously-post', true ) ) ) {
			$value = true;
		}

		return $value;
	}


	function group_activity_update( $action, $activity ) {

		/**
		 * Check if the activity is anonymously or not
		 */
		if( 
			! empty( $activity->id ) 
			&& ! empty( $activity->user_id ) 
			&& $this->is_anonymously_activity( $activity->id, $activity->user_id ) 
		) {
			$user_link = $this->anonymous_user_label();
			if( 
				$this->login_user_is_activity_author( $activity->user_id )
				|| $this->is_group_mods( $activity->user_id, $activity->item_id )
				|| $this->is_group_admins( $activity->user_id, $activity->item_id )
			) {
				$user_link = bp_core_get_userlink( $activity->user_id );
				$user_link .= $this->anonymous_author_user_label();
			}

			$group      = groups_get_group( $activity->item_id );
			$group_link = '<a href="' . esc_url( bp_get_group_permalink( $group ) ) . '">' . bp_get_group_name( $group ) . '</a>';

			$action = sprintf( __( '%1$s posted an update in the group %2$s', 'post-anonymously-for-buddyboss' ), $user_link, $group_link );
		}

		return $action;
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    1.0.0
	 */
	public function before_activity_entry() {
		
		/**
		 * Check if the activity is anonymously or not
		 */
		if( $this->is_anonymously_activity( bp_get_activity_id(), bp_get_activity_user_id() ) ) {

			/**
			 * For User Link on the Avatar
			 */
			add_filter( 'bp_get_activity_user_link', array( $this, 'activity_user_link' ), 1000 );

			// /**
			//  * For User Avatar
			//  */
			add_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );

			// /**
			//  * For user link and the username in the Activity  after avatar
			//  */
			add_filter( 'bp_core_get_userlink', array( $this, 'activity_userlink' ), 1000, 2 );
		}
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    1.0.0
	 */
	public function after_activity_entry() {

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
		return $this->anonymous_user_label();
	}

	/**
	 * Remove the User Profile Link
	 */
	public function anonymous_user_label() {
		return __( 'Anonymous member', 'post-anonymously-for-buddyboss' );
	}

	/**
	 * Remove the User Profile Link
	 */
	public function anonymous_author_user_label() {
		return __( ' ( Anonymous Post )', 'post-anonymously-for-buddyboss' );
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

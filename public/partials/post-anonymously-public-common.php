<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Post_Anonymously
 * @subpackage Post_Anonymously/public
 * @author     AcrossWP <contact@acrosswp.com>
 */
class Post_Anonymously_Public_Common {

    /**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Public_Common
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {}

    /**
	 * Main Post_Anonymously Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 0.0.1
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
	 * Check if the activity is the anonymously activity
	 */
	public function is_anonymously_activity( $activity_id ) {

		$value = false;
		$user_id = get_current_user_id();

		if( ! empty( bp_activity_get_meta( $activity_id, 'anonymously-post', true ) ) ) {
			$value = true;
		}

		return $value;
	}

	/**
	 * Show the anonymously user name to the admin, Moderator and to the anonymously user itself
	 */
	public function show_anonymously_users( $activity_user_id, $activity_group_id ) {

		$show = false;

		if( 
			$this->login_user_is_activity_author( $activity_user_id )
			|| $this->is_group_mods( $activity_user_id, $activity_group_id )
			|| $this->is_group_admins( $activity_user_id, $activity_group_id )
		) {
			$show = true;
		}

		return $show;
	}

	/**
	 * Remove the User Profile Link
	 */
	public function anonymous_user_label() {
		return __( 'Anonymous Member', 'post-anonymously' );
	}

	/**
	 * get the global activity 
	 */
	public function get_activity() {
		global $activities_template;
		return $activities_template->activity;
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
		$group_mods = wp_list_pluck( $group_mods, 'user_id' );

		if( ! empty( $user_id ) && ! empty( $group_mods ) && in_array( $user_id, $group_mods ) ) {
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
		$group_admins = wp_list_pluck( $group_admins, 'user_id' );

		if( ! empty( $user_id ) && ! empty( $group_admins ) && in_array( $user_id, $group_admins ) ) {
			$value = true;
		}

		return $value;
	}

	/**
	 * Remove the User Profile Link on activity
	 */
	public function anonymous_author_user_label() {
		return __( ' ( Anonymous Post )', 'post-anonymously' );
	}

	/**
	 * Remove the User Profile Link on activity comment
	 */
	public function anonymous_author_user_commnet_label() {
		return __( ' ( Anonymous Comment )', 'post-anonymously' );
	}

}


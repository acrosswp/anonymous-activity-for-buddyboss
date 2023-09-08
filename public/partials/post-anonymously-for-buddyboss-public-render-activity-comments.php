<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

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
class Post_Anonymously_For_BuddyBoss_Public_Render_Activity_Comments {

    /**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_For_BuddyBoss_Public_Render_Activity_Comments
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_For_BuddyBoss_Public_Render_Activity_Comments
	 * @since 0.0.1
	 */
	protected $_functions = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

		$this->_functions = Post_Anonymously_For_BuddyBoss_Public_Common::instance();

	}

    /**
	 * Main Post_Anonymously_For_BuddyBoss Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 0.0.1
	 * @static
	 * @see Post_Anonymously_For_BuddyBoss()
	 * @return Post_Anonymously_For_BuddyBoss - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Contain all the function are use to save the meta
	 */
	public function hooks() {
		
		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 */
		add_action( 'bp_before_activity_comment_entry', array( $this, 'activity_comment_entry' ), 1000 );

		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 * Previous was using this bp_after_activity_entry but find out this hook create an issue into the comment area where the other user profile is not visiable
		 */
		add_action( 'bp_activity_after_comment_content', array( $this, 'after_comment_content' ), 1000 );
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    0.0.1
	 */
	public function activity_comment_entry() {
		
		/**
		 * Check if the activity is anonymously or not
		 */
		if( 
			$this->_functions->is_anonymously_activity( bp_get_activity_id() )
			&& bp_get_activity_user_id() == bp_get_activity_comment_user_id()
		) {

			$activity = $this->_functions->get_activity();
			if( empty( $this->_functions->show_anonymously_users( $activity->user_id, $activity->item_id ) ) ) {
				/**
				 * For User Link on the Avatar
				 */
				add_filter( 'bp_activity_comment_user_link', array( $this, 'comment_user_link' ), 1000 );

				// /**
				//  * For User Avatar
				//  */
				add_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );
			}

			/**
			 * For activity comment user name
			 */
			add_filter( 'bp_activity_comment_name', array( $this, 'activity_comment_name' ), 1000 );

		}
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    0.0.1
	 */
	public function after_comment_content() {
		/**
		 * For User Link on the Avatar
		 */
		remove_filter( 'bp_activity_comment_user_link', array( $this, 'comment_user_link' ), 1000 );

		// /**
		//  * For User Avatar
		//  */
		remove_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );

		/**
		 * For activity comment user name
		 */
		remove_filter( 'bp_activity_comment_name', array( $this, 'activity_comment_name' ), 1000 );

	}

	/**
	 * Remove the User Profile Link
	 */
	public function comment_user_link( $link ) {
		return '';
	}

	/**
	 * Remove the User Profile Link
	 */
	public function activity_avatar( $link ) {
		$defaults = array(
			'alt'     => '',
			'class'   => 'avatar',
			'email'   => false,
			'type'    => '',
			'user_id' => false,
		);

		return bp_core_fetch_avatar( $defaults );
	}

	/**
	 * Remove the User Profile Link
	 */
	public function activity_comment_name( $name ) {
		
		$activity = $this->_functions->get_activity();
		if( $this->_functions->show_anonymously_users( $activity->user_id, $activity->item_id ) ) {
			$name .= $this->_functions->anonymous_author_user_commnet_label();
		} else {
			$name = $this->_functions->anonymous_user_label();
		}

		return $name;
	}

}


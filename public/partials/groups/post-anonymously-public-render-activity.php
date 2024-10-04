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
class Post_Anonymously_Public_Render_Groups_Activity {

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
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Public_Render_Groups_Activity
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Public_Render_Groups_Activity
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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->_functions = Post_Anonymously_Public_Common::instance( $plugin_name, $version );

	}

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
	public static function instance( $plugin_name, $version ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $version );
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
		add_action( 'bp_before_activity_entry', array( $this, 'before_activity_entry' ), 1000 );

		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 * Previous was using this bp_after_activity_entry but find out this hook create an issue into the comment area where the other user profile is not visiable
		 */
		add_action( 'bp_after_activity_activity_content', array( $this, 'after_activity' ), 1000 );

		/**
		 * Hook to add filter so that the normal user can not see the Post author data
		 */
		add_filter( 'bp_groups_format_activity_action_activity_update', array( $this, 'group_activity_update' ), 1000, 2 );
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    0.0.1
	 */
	public function before_activity_entry() {
		
		/**
		 * Check if the activity is anonymously or not
		 */
		if( $this->_functions->is_anonymously_activity( bp_get_activity_id() ) ) {

			$user_link = $this->_functions->anonymous_user_label();

			$activity = $this->_functions->get_activity();

			if( empty( $this->_functions->show_groups_anonymously_users( $activity->user_id, $activity->item_id ) ) ) {

				/**
				 * For User Link on the Avatar
				 */
				add_filter( 'bp_get_activity_user_link', array( $this, 'activity_user_link' ), 1000 );

				// /**
				//  * For User Avatar
				//  */
				add_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );
			}

			// /**
			//  * For user link and the username in the Activity  after avatar
			//  */
			add_filter( 'bp_core_get_userlink', array( $this, 'activity_userlink' ), 1000, 2 );

		}
	}

	/**
	 * Register the hooks on activity/group post area
	 *
	 * @since    0.0.1
	 */
	public function after_activity() {

		remove_filter( 'bp_get_activity_user_link', array( $this, 'activity_user_link' ), 1000 );

		remove_filter( 'bp_get_activity_avatar', array( $this, 'activity_avatar' ), 1000 );

		remove_filter( 'bp_core_get_userlink', array( $this, 'activity_userlink' ), 1000 );

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
	public function activity_userlink( $link, $user_id ) {
		return $this->_functions->anonymous_user_label();
	}

	/**
	 * Update the activity header seaction
	 */
	function group_activity_update( $action, $activity ) {

		/**
		 * Check if the activity is anonymously or not
		 */
		if( 
			! empty( $activity->id ) 
			&& ! empty( $activity->user_id ) 
			&& $this->_functions->is_anonymously_activity( $activity->id ) 
		) {

			$user_link = $this->_functions->anonymous_user_label();
			if( $this->_functions->show_groups_anonymously_users( $activity->user_id, $activity->item_id ) ) {
				$user_link = bp_core_get_userlink( $activity->user_id );
				$user_link .= $this->_functions->anonymous_author_user_label();
			}


			$group      = groups_get_group( $activity->item_id );
			$group_link = '<a href="' . esc_url( bp_get_group_permalink( $group ) ) . '">' . bp_get_group_name( $group ) . '</a>';

			$action = sprintf( __( '%1$s posted an update in the group %2$s', 'post-anonymously' ), $user_link, $group_link );
		}

		return $action;
	}

}


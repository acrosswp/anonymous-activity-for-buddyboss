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
class Post_Anonymously_Public_Render_Forums_Notifications {

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
	 * @var Post_Anonymously_Public_Render_Forums_Notifications
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Public_Render_Forums_Notifications
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
		 * Hook to hide the author name in the notifications for discussion
		 */
		add_filter( 'bp_get_the_notification_description', array( $this, 'topic_notification' ), 1, 2 );

		add_filter( 'bp_get_the_notification_description', array( $this, 'reply_notification' ), 1, 2 );


		add_action( 'notifications_loop_start', array( $this, 'notifications_loop_start' ) );

		add_action( 'notifications_loop_end', array( $this, 'notifications_loop_end' ) );

	}

	/**
	 * Update the discussion notification
	 */
	public function topic_notification( $description, $notification ) {

		/**
		 * If this is for notifications only
		 */
		if ( 'groups' !== $notification->component_name ) {
			return $description;
		}

		if ( 'bb_groups_subscribed_discussion' !== $notification->component_action ) {
			return $description;
		}

		return $this->content_update( $description, $notification );
	}

	/**
	 * Update the discussion notification
	 */
	public function reply_notification( $description, $notification ) {

		/**
		 * If this is for notifications only
		 */
		if ( 'forums' !== $notification->component_name ) {
			return $description;
		}

		if ( 'bbp_new_reply' !== $notification->component_action ) {
			return $description;
		}

		return $this->content_update( $description, $notification );
	}

	public function content_update( $description, $notification ) {
		$post_id = $notification->item_id;

		/**
		 * Check if the post is a anonymously or not
		 */
		if ( $this->_functions->show_post_anonymously_users( $post_id ) ) {
			
			$user_fullname = bp_core_get_user_displayname( $notification->secondary_item_id );

			$description = str_replace( $user_fullname, $user_fullname . ' ' .$this->_functions->anonymous_author_user_commnet_label(), $description );

			return $description;
		}


		if ( $this->_functions->is_anonymously_post( $post_id ) ) {

			$user_fullname = bp_core_get_user_displayname( $notification->secondary_item_id );

			$description = str_replace( $user_fullname, $this->_functions->anonymous_user_label(), $description );

			return $description;
		}

		return $description;

	}

	/**
	 * Nofitication loop started
	 */
	public function notifications_loop_start() {

		/**
		 * Remove the link from the User notification
		 * Used for: BuddyBoss Theme Only
		 */
		add_filter( 'bp_core_get_user_domain', array( $this, 'notifications_user_domain' ), 100, 4 );


		/**
		 * Remove the img from the User notification
		 * Used for: BuddyBoss Theme Only
		 */
		add_filter( 'bp_core_fetch_avatar_url_check', array( $this, 'notifications_user_avatar_url' ), 100, 2 );
	}

	/**
	 * Nofitication loop end
	 */
	public function notifications_loop_end() {

		/**
		 * Remove the link from the User notification
		 * Used for: BuddyBoss Theme Only
		 */
		remove_filter( 'bp_core_get_user_domain', array( $this, 'notifications_user_domain' ), 100 );


		/**
		 * Remove the img from the User notification
		 * Used for: BuddyBoss Theme Only
		 */
		remove_filter( 'bp_core_fetch_avatar_url_check', array( $this, 'notifications_user_avatar_url' ), 100 );
	}

	/**
	 * Change the link into the notification area
	 */
	public function notifications_user_domain( $domain, $user_id, $user_nicename, $user_login ) {

		if ( $this->is_anonymously_notifications( $user_id ) ) {
			$domain = '';
		}

		return $domain;
	}

	/**
	 * Change the link into the notification area
	 */
	public function notifications_user_avatar_url( $avatar_url, $params ) {

		if ( ! empty( $params['item_id'] ) && $this->is_anonymously_notifications( $params['item_id'] ) ) {
			$avatar_url = '';
		}

		return $avatar_url;
	}

	/**
	 * Check if the current notification is the anonymously or not
	 */
	public function is_anonymously_notifications( $user_id ) {

		$value =  false;

		$notification     	= buddypress()->notifications->query_loop->notification;

		if( ! empty( $notification ) ) {

			$component        	= $notification->component_name;
			$component_action	= $notification->component_action;
			$activity_user_id 	= $notification->secondary_item_id;

			if( 
				( 'forums' == $component && 'bbp_new_reply' == $component_action )
				|| ( 'groups' == $component && 'bb_groups_subscribed_discussion' == $component_action )
			) {

				/**
				 * For Normal Activity notification
				 */
				$post_id 		= $notification->item_id;
				$activity_user_id 	= $notification->secondary_item_id;

				if( 
					$activity_user_id 
					&& empty( $this->_functions->show_post_anonymously_users( $post_id ) )
				) {
					if( $this->_functions->is_anonymously_post( $post_id ) ) {
						$value =  true;
					}
				}
			}
		}

		return $value;
	}

}

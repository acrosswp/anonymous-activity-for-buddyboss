<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    Post_Anonymously_For_BuddyBoss
 * @subpackage Post_Anonymously_For_BuddyBoss/public/partials
 */
?>

<?php
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
class Post_Anonymously_For_BuddyBoss_Public_Save_Meta {

    /**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_For_BuddyBoss_Public_Save_Meta
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_For_BuddyBoss_Public_Render_Activity
	 * @since 1.0.0
	 */
	protected $_functions = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
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
	 * @since 1.0.0
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
		 * Add post meta into the Group Activity Meta
		 */
		add_action( 'bp_groups_posted_update', array( $this, 'groups_posted_update' ), 1000, 4 );

		/**
		 * Add post meta into the Activity Meta
		 */
		add_action( 'bp_activity_posted_update', array( $this, 'activity_posted_update' ), 1000, 3 );


		/**
		 * Save meta when post author comment on the activity post
		 */
		add_action( 'bp_activity_comment_posted', array( $this, 'activity_comment_posted' ), 1000, 3 );
		add_action( 'bp_activity_comment_posted_notification_skipped', array( $this, 'activity_comment_posted' ), 1000, 3 );

	}

	/**
	 * Register the hooks on group post update
	 *
	 * @since    1.0.0
	 */
	public function activity_comment_posted( $comment_id, $r, $activity ) {

		if( 
			! empty( $comment_id ) 
			&& ! empty( $activity->id )  
			&& $this->_functions->is_anonymously_activity( $activity->id ) 
			&& $this->_functions->login_user_is_activity_author( $activity->user_id )
		) {
			bp_activity_update_meta( $comment_id, 'anonymously-post', 1 );
		}
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
}


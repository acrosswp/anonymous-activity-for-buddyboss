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
class Post_Anonymously_Public_Save_Meta_Forums {

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
	 * @var Post_Anonymously_Public_Save_Meta_Forums
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Public_Common
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

		add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'show_button_on_topic' ) );
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $this, 'show_button_on_reply' ) );

		/**
		 * Add post meta into the Topic Meta
		 */
		add_action( 'bbp_new_topic_post_extras', array( $this, 'topic_post_update' ), 1, 1 );
		add_action( 'bbp_new_topic_pre_extras', array( $this, 'skip_topic_activity' ) );

		/**
		 * Add post meta into the Reply Meta
		 */
		add_action( 'bbp_new_reply_post_extras', array( $this, 'reply_post_update' ), 1, 1 );
		add_action( 'bbp_new_reply_pre_extras', array( $this, 'skip_reply_activity' ) );

		/**
		 * Stop email for the forums new activity of anonymou post.
		 */
		// add_action( 'bp_send_email', array( $this, 'discussion_email_sending' ), 1000, 4 );

		// add_action( 'bp_send_email', array( $this, 'reply_email_sending' ), 1000, 4 );

	}

	/**
	 * Do not send email for the anonymou post in groups
	 */
	public function discussion_email_sending( $email, $email_type, $to, $args ) {

		if ( 'groups-new-discussion' != $email_type ) {
			return;
		}

		if ( ! isset( $args['tokens']['discussion.id'] ) ) {
			return;
		}

		$discussion_id = $args['tokens']['discussion.id'];
		if ( ! isset( $discussion_id ) ) {
			return;
		}

		if ( ! $this->_functions->is_anonymously_post( $discussion_id )  ) {
			return;
		}

		$email->set_to( '' );
	}

	/**
	 * Do not send email for the anonymou post in groups
	 */
	public function reply_email_sending( $email, $email_type, $to, $args ) {

		if ( 'groups-new-discussion' != $email_type ) {
			return;
		}

		if ( ! isset( $args['tokens']['discussion.id'] ) ) {
			return;
		}

		$discussion_id = $args['tokens']['discussion.id'];
		if ( ! isset( $discussion_id ) ) {
			return;
		}

		if ( ! $this->_functions->is_anonymously_post( $discussion_id )  ) {
			return;
		}

		$email->set_to( '' );
	}

	/**
	 * Skip the topic acitivty creation
	 */
	public function skip_topic_activity() {

		if ( ! empty( $_REQUEST['anonymously-post'] ) ) {

			/**
			 * Remove hook
			 */
			if ( bp_is_active( 'activity' ) ) {
				remove_action( 'bbp_new_topic', array( bbpress()->extend->buddypress->activity, 'topic_create' ), 10 );
			}
		}
	}

	/**
	 * Skip the reply acitivty creation
	 */
	public function skip_reply_activity() {

		if ( ! empty( $_REQUEST['anonymously-post'] ) ) {

			/**
			 * Remove hook
			 */
			if ( bp_is_active( 'activity' ) ) {
				remove_action( 'bbp_new_reply', array( bbpress()->extend->buddypress->activity, 'reply_create' ), 10 );
			}
		}
	}

	/**
	 * Show Anonymous button HTMl on the topic page
	 */
	public function show_button_on_topic() {
		$this->_functions->anonymous_button();
	}

	/**
	 * Show Anonymous button HTMl on the reply page
	 */
	public function show_button_on_reply() {
		$this->_functions->anonymous_button();
	}

	/**
	 * Register the hooks on topic post update
	 *
	 * @since    0.0.1
	 */
	public function topic_post_update( $topic_id ) {

		if ( ! empty( $_REQUEST['anonymously-post'] ) ) {
			update_post_meta( $topic_id, 'anonymously-post', 1 );
		} else {
			update_post_meta( $topic_id, 'anonymously-post', 0 );

		}

		update_post_meta( $topic_id, 'anonymously-post-forum-id', wp_get_post_parent_id( $topic_id ) );
		update_post_meta( $topic_id, 'anonymously-post-forum-id', wp_get_post_parent_id( $topic_id ) );
	}

	/**
	 * Register the hooks on reply post update
	 *
	 * @since    0.0.1
	 */
	public function reply_post_update( $reply_id ) {

		if ( ! empty( $_REQUEST['anonymously-post'] ) ) {
			update_post_meta( $reply_id, 'anonymously-post', 1 );
		} else {
			update_post_meta( $reply_id, 'anonymously-post', 0 );
		}

		$topic_id = wp_get_post_parent_id( $reply_id );
		$forum_id = wp_get_post_parent_id( $topic_id );

		update_post_meta( $reply_id, 'anonymously-post-topic-id', $topic_id );
		update_post_meta( $reply_id, 'anonymously-post-forum-id', $forum_id );
	}
}


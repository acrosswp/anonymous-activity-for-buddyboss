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
class Post_Anonymously_Public_Render_Forums_Emails {

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
	 * @var Post_Anonymously_Public_Render_Forums_Emails
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Public_Render_Forums_Emails
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

		// set new email tokens added in BuddyBoss 1.0.0.
		add_filter( 'bp_email_set_tokens', array( $this, 'set_tokens' ), 1, 3 );
	}

	public function set_tokens( $formatted_tokens, $tokens, $bp_email ) {

		if ( 'site-name-new-discussion-in-group-name' != $bp_email->get_post_object()->post_name ) {
			return $formatted_tokens;
		}

		$topic_id = $formatted_tokens['discussion.id'];

		if ( ! $this->_functions->is_anonymously_post( $topic_id ) ) {
			return $formatted_tokens;
		}

		$author_id = get_post_field( 'post_author', $topic_id );
		$receiver_user_id = $formatted_tokens['receiver-user.id'];

		return $formatted_tokens;
	}
}

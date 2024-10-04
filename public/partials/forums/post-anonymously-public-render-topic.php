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
class Post_Anonymously_Public_Render_Forums_Topic {

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
	 * @var Post_Anonymously_Public_Render_Forums_Topic
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
		
		add_filter( 'bbp_is_topic_anonymous', array( $this, 'is_topic_anonymous' ), 101, 2 );

		add_filter( 'bbp_get_topic_author_display_name', array( $this, 'topic_author_display_name' ), 101, 2 );
	}

	/**
	 * Check if the topic is anonymous
	 */
	public function is_topic_anonymous( $retval, $topic_id ) {

		/**
		 * If it true then bailout
		 */
		if ( $retval ) {
			return $retval;
		}

		if ( $this->_functions->show_post_anonymously_users( $topic_id ) ) {
			return $retval;
		}

		return $this->_functions->is_anonymously_post( $topic_id );
	}

	/**
	 * Check if the topic is anonymous
	 */
	public function topic_author_display_name( $author_name, $topic_id ) {


		if ( $this->_functions->show_post_anonymously_users( $topic_id ) ) {
			return sprintf( '%s %s', $author_name, $this->_functions->anonymous_author_user_label() );
		}

		return $author_name;
	}

}


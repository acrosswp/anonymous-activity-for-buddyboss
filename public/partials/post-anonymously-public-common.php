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
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
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
	 * Check if the activity is the anonymously activity
	 */
	public function is_anonymously_activity( $activity_id ) {

		$value = false;

		if( ! empty( bp_activity_get_meta( $activity_id, 'anonymously-post', true ) ) ) {
			$value = true;
		}

		return $value;
	}

	/**
	 * Check if the activity is the anonymously post
	 * Can be use for Post like topic, reply
	 */
	public function is_anonymously_post( $post_id ) {
		return get_post_meta( $post_id, 'anonymously-post', true );
	}

	/**
	 * Show the anonymously user name to the admin, Moderator and to the anonymously user itself
	 */
	public function show_groups_anonymously_users( $activity_user_id, $activity_group_id ) {

		$show = false;

		if( 
			$this->login_user_is_author( $activity_user_id )
			|| $this->is_group_mods( $activity_group_id )
			|| $this->is_group_admins( $activity_group_id )
		) {
			$show = true;
		}

		return $show;
	}

	/**
	 * Show the anonymously user name to the admin, Moderator and to the anonymously user itself
	 */
	public function show_post_anonymously_users( $post_id ) {

		if ( $this->login_user_is_post_author( $post_id ) ) {
			return true;
		}

		$forum_id = get_post_meta( $post_id, '_bbp_forum_id', true );
		if ( empty( $forum_id ) ) {
			return false;
		}
		
		$group_ids = get_post_meta( $forum_id, '_bbp_group_ids', true );
		if ( empty( $group_ids ) ) {
			return false;
		}

		foreach( $group_ids as $group_id ) {
			if ( $this->is_group_mods( $group_id ) ) {
				return true;
			}

			if ( $this->is_group_admins( $group_id ) ) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Check if the login user post author
	 */
	public function login_user_is_post_author( $post_id ) {
		$author_id = get_post_field ('post_author', $post_id);
		
		return $this->login_user_is_author( $author_id );
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
	function login_user_is_author( $user_id ) {

		$value = false;

		$current_user_id = get_current_user_id();

		if( ! empty( $current_user_id ) && $user_id == $current_user_id ) {
			$value = true;
		}

		return $value;
	}

		/**
	 * Check where the login user is the group moderator
	 */
	function is_group_mods( $group_id ) {

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
	function is_group_admins( $group_id ) {

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

	/**
	 * Return Anonymous button HTML 
	 */
	public function anonymous_button() {
		wp_enqueue_style( $this->plugin_name );

		$anonymously_checked = apply_filters( 'post_anonymously_default_checked', '' );
		?>
		<div class="anonymously-post-main">
			<label><?php _e( 'Post Anonymously', 'post-anonymously' ); ?></label>
			<div class="anonymously-post-wrap">
				<input type="checkbox" id="anonymously-post" <?php echo $anonymously_checked; ?> class="anonymously-post" name="anonymously-post" value="1">
				<div class="anonymously-post-knobs"></div>
				<div class="anonymously-post-layer"></div>
			</div>
		</div>
		<?php
	}

}


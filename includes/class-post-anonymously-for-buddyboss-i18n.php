<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://acrosswp.com
 * @since      0.0.1
 *
 * @package    Post_Anonymously_For_BuddyBoss
 * @subpackage Post_Anonymously_For_BuddyBoss/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.1
 * @package    Post_Anonymously_For_BuddyBoss
 * @subpackage Post_Anonymously_For_BuddyBoss/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class Post_Anonymously_For_BuddyBoss_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'post-anonymously-for-buddyboss',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

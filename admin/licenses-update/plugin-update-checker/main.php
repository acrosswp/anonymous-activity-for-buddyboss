<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Add Github Plugin update checker into the AcrossWP Github Plugin Update Checker
 */
function post_anonymously_plugins_update_checker_github( $packages ) {

    $packages[1000] = array(
        'repo' 		        => 'https://github.com/acrosswp/post-anonymously',
        'file_path' 		=> POST_ANONYMOUSLY_FILES,
        'plugin_name_slug'	=> POST_ANONYMOUSLY_PLUGIN_PLUGIN_NAME_SLUG,
        'release_branch' 	=> 'main'
    );

    return $packages;
}
add_filter( 'acrosswp_plugins_update_checker_github', 'post_anonymously_plugins_update_checker_github', 100, 1 );

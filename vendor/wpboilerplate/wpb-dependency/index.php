<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if the class does not exits then only allow the file to add
 */
if ( ! class_exists( 'WPBoilerplate_Plugins_Dependency' ) ) {
    abstract class WPBoilerplate_Plugins_Dependency {

        /**
         * The slug of this plugin name.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        public $plugin_name;
    
        /**
         * The plugin file directory.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        public $plugin_files;
    
        function __construct() {
            add_filter( $this->plugin_name . '-load', array( $this, 'boilerplate_load' ) );
        }
    
        /**
         * Get the currnet plugin paths
         */
        public function get_plugin_name() {
    
            $plugin_data = get_plugin_data( $this->plugin_files );
            return $plugin_data['Name'];
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function boilerplate_load( $load ) {
    
            if ( empty( $this->constant_define() ) ) {
                $load = false;
    
                $this->constant_not_define_hook();
    
            } elseif ( $this->constant_define() && empty( $this->constant_mini_version() ) ) {
                $load = false;
    
                $this->constant_mini_version_hook();
    
            }
    
            return $load;
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function constant_define() {
            $string = (string) $this->constant_name();
            if ( defined( $string ) ) {
                return true;
            }
            return false;
        }

    
        /**
         * Load this function on plugin load hook
         */
        function constant_version( $constant_name = false ) {

            if ( empty( $constant_name ) ) {
                $constant_name = $this->constant_name();
            }

            if ( defined( $constant_name ) ) {
                return constant( $constant_name );
            }

            return false;

        }
    
        /**
         * Load this function on plugin load hook
         */
        public function constant_mini_version() {
    
            if ( version_compare( $this->constant_version(), $this->mini_version() , '>=' ) ) {
                return true;
            }
            return false;
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function error_message_hooks( $call ) {
            if ( defined( 'WP_CLI' ) ) {
                WP_CLI::warning( $this->$call() );
            } else {
                add_action( 'admin_notices', array( $this, $call ) );
                add_action( 'network_admin_notices', array( $this, $call ) );
            }
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function constant_not_define_hook() {
            $this->error_message_hooks( 'constant_not_define_message' );
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function constant_mini_version_hook() {
            $this->error_message_hooks( 'constant_mini_version_message' );
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function error_message( $call ) {
            echo '<div class="error fade"><p>';
                $this->$call();
            echo '</p></div>';
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function constant_not_define_message() {
            $this->error_message( 'constant_not_define_text' );
        }
    
        /**
         * Load this function on plugin load hook
         */
        public function constant_mini_version_message() {
            $this->error_message( 'constant_mini_version_text' );
        }
    
        /**
         * Load this function on plugin load hook
         * Example: _e('<strong>BuddyBoss Sorting Option In Network Search</strong></a> requires the BuddyBoss Platform plugin to work. Please <a href="https://buddyboss.com/platform/" target="_blank">install BuddyBoss Platform</a> first.', 'buddyboss-sorting-option-in-network-search');
         */
        abstract function constant_not_define_text();
    
        /**
         * Load this function on plugin load hook
         * Example: printf( __('<strong>BuddyBoss Sorting Option In Network Search</strong></a> requires BuddyBoss Platform plugin version %s or higher to work. Please update BuddyBoss Platform.', 'buddyboss-sorting-option-in-network-search'), BP_PLATFORM_VERSION_MINI_VERSION );
         */
        abstract function constant_mini_version_text();
    
        /**
         * Load this function on plugin load hook
         */
        abstract function constant_name();
    
        /**
         * Load this function on plugin load hook
         */
        abstract function mini_version();
    }
}
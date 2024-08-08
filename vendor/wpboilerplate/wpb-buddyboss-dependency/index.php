<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if the class does not exits then only allow the file to add
 */
if ( ! class_exists( 'WPBoilerplate_BuddyBoss_Platform_Dependency' ) ) {

    class WPBoilerplate_BuddyBoss_Platform_Dependency extends WPBoilerplate_Plugins_Dependency {

        /**
		 * Load the mini version of plugins
		 *
		 * @since 0.0.1
		 */
		public $mini_version;

        /**
		 * Load the mini version of plugins
		 *
		 * @since 0.0.1
		 */
		public $component_required = array();

		/**
		 * Initialize the collections used to maintain the actions and filters.
		 *
		 * @since    0.0.1
		 */
		public function __construct( $plugin_name, $plugin_files, $mini_version = '2.6.70', $component_required = array() ) {

            $this->plugin_name          = $plugin_name;
            $this->plugin_files         = $plugin_files;
            $this->mini_version         = $mini_version;
            $this->component_required   = $component_required;

            parent::__construct();
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
    
            } elseif (
                ! empty( $this->component_required() )
                && $this->constant_define()
                && ! empty( $this->constant_mini_version() )
                && empty( $this->required_component_is_active() )
            ) {
                $load = false;
    
                $this->component_required_hook();
            }
    
            return $load;
        }

        /**
         * Load this function on plugin load hook
         * Example: _e('<strong>BuddyBoss Sorting Option In Network Search</strong></a> requires the BuddyBoss Platform plugin to work. Please <a href="https://buddyboss.com/platform/" target="_blank">install BuddyBoss Platform</a> first.', 'sorting-option-in-network-search-for-buddyboss');
         */
        public function constant_not_define_text() {
            printf( 
                __( 
                    '<strong>%s</strong></a> requires the BuddyBoss Platform plugin to work. Please <a href="https://buddyboss.com/platform/" target="_blank">install BuddyBoss Platform</a> first.',
                    'wordpress-plugin-boilerplate'
                ),
                $this->get_plugin_name()
            );
        }

        /**
         * Load this function on plugin load hook
         * Example: printf( __('<strong>BuddyBoss Sorting Option In Network Search</strong></a> requires BuddyBoss Platform plugin version %s or higher to work. Please update BuddyBoss Platform.', 'sorting-option-in-network-search-for-buddyboss'), $this->mini_version() );
         */
        public function constant_mini_version_text() {
            printf( 
                __( 
                    '<strong>%s</strong></a> requires BuddyBoss Platform plugin version %s or higher to work. Please update BuddyBoss Platform.',
                    'wordpress-plugin-boilerplate'
                ),
                $this->get_plugin_name(),
                $this->mini_version()
            );
        }

        /**
         * Load this function on plugin load hook
         * Example: printf( __('<strong>BuddyBoss Sorting Option In Network Search</strong></a> requires BuddyBoss Platform plugin version %s or higher to work. Please update BuddyBoss Platform.', 'sorting-option-in-network-search-for-buddyboss'), $this->mini_version() );
         */
        public function component_required_text() {

            $bb_components = bp_core_get_components();
            $component_required = $this->component_required();
            $active_components = apply_filters( 'bp_active_components', bp_get_option( 'bp-active-components' ) );
            $component_required_label = array();

            foreach( $bb_components as $key => $bb_component ) {
                if( in_array( $key, $component_required ) ) {
                    $component_required_label[] = '<strong>' . $bb_component['title'] . '</strong>';
                }
            }

            if( count( $component_required_label ) > 1 ) {
                $last = array_pop( $component_required_label );
                $component_required_label = implode( ', ', $component_required_label ) . ' and ' . $last;
            } else {
                $component_required_label = $component_required_label[0];
            }

            printf( 
                __( 
                    '<strong>%s</strong></a> requires BuddyBoss Platform %s Component to work. Please Active the mentions Component.',
                    'wordpress-plugin-boilerplate'
                ),
                $this->get_plugin_name(),
                $component_required_label
            );
        }

        /**
         * Check if the Required Component is Active
         */
        public function required_component_is_active() {
            $is_active = false;
            $component_required = $this->component_required();

            // Active components.
            $active_components = apply_filters( 'bp_active_components', bp_get_option( 'bp-active-components' ) );

            foreach ( $component_required as $component_require ) {
                if ( isset( $active_components[ $component_require ] ) ) {
                    $is_active = true;
                } else {
                    $is_active = false;
                    break;
                }
            }

            return $is_active;

        }

        /**
         * Load this function on plugin load hook
         */
        public function constant_name() {
            return 'BP_PLATFORM_VERSION';
        }

        /**
         * Load this function on plugin load hook
         */
        public function mini_version() {
            return $this->mini_version;
        }

        /**
         * Load this function on plugin load hook
         * Example:
         array(
            'members',
            'xprofile',
            'settings',
            'notifications',
            'groups',
            'forums',
            'activity',
            'media',
            'document',
            'video',
            'messages',
            'friends',
            'invites',
            'moderation',
            'search',
            'blogs',
         );
         */
        public function component_required() {
            return $this->component_required;
        }

        /**
         * Load this function on plugin load hook
         */
        public function component_required_message() {
            $this->error_message( 'component_required_text' );
        }

        /**
         * Load this function on plugin load hook
         */
        public function component_required_hook() {
            $this->error_message_hooks( 'component_required_message' );
        }
    }
}
<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!class_exists('BP_Portfolio_Pro_Plugin')):

    /**
     *
     * BP Portfolio Pro Plugin Main Controller
     * **************************************
     *
     *
     */
    class BP_Portfolio_Pro_Plugin {
        /* Includes
         * ===================================================================
         */

        /**
         * Generic includes
         * @var array
         */
        private $main_includes = array(
            'bpcp-pro-widgets',
        );

        /**
         * BuddyPress includes
         * @var array
         */
        private $bp_includes = array(
            'bpcp-pro-component',
            'bpcp-pro-filters',
            'bpcp-pro-functions',
            'bpcp-pro-screens',
            'bpcp-pro-actions',
            'bpcp-pro-ajax',
            'wp-postviews/wp-postviews'
        );

		/**
         * Admin includes
         * @var array
         */
        private $admin_includes = array(
            'admin',
        );

        /* Plugin Options
         * ===================================================================
         */

        /**
         * This options array is setup during class instantiation, holds
         * default and saved options for the plugin.
         *
         * @var array
         */
        public $options = array();
        public $settings = array();

        /**
         * Is BuddyPress installed and activated?
         * @var boolean
         */
        public $bp_enabled = false;


        /* Version
         * ===================================================================
         */

        /**
         * Plugin codebase version
         * @var string
         */
        public $version = '0.0.0';

        /**
         * Plugin database version
         * @var string
         */
        public $db_version = '0.0.0';

        /* Paths
         * ===================================================================
         */
        public $file = '';
        public $basename = '';
        public $plugin_dir = '';
        public $plugin_url = '';
        public $includes_dir = '';
        public $includes_url = '';
        public $lang_dir = '';
        public $assets_dir = '';
        public $assets_url = '';

        /* Component State
         * ===================================================================
         */
        public $current_type = '';
        public $current_item = '';
        public $current_action = '';
        public $is_single_item = false;


        /* Magic
         * ===================================================================
         */

        /**
         * BP Portfolio Pro uses many variables, most of which can be filtered to
         * customize the way that it works. To prevent unauthorized access,
         * these variables are stored in a private array that is magically
         * updated using PHP 5.2+ methods. This is to prevent third party
         * plugins from tampering with essential information indirectly, which
         * would cause issues later.
         *
         * @see BP_Portfolio_Pro_Plugin::setup_globals()
         * @var array
         */
        private $data;

        /* Singleton
         * ===================================================================
         */

        /**
         * Main BP Portfolio Pro Instance.
         *
         * BP Portfolio Pro is great
         * Please load it only one time
         * For this, we thank you
         *
         * Insures that only one instance of BP Portfolio Pro exists in memory at any
         * one time. Also prevents needing to define globals all over the place.
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @static object $instance
         * @uses BP_Portfolio_Pro_Plugin::setup_globals() Setup the globals needed.
         * @uses BP_Portfolio_Pro_Plugin::setup_actions() Setup the hooks and actions.
         * @uses BP_Portfolio_Pro_Plugin::setup_textdomain() Setup the plugin's language file.
         * @see bp_portfolio_pro()
         *
         * @return BP Portfolio Pro
         */
        public static function instance() {
            // Store the instance locally to avoid private static replication
            static $instance = null;

            // Only run these methods if they haven't been run previously
            if (null === $instance) {
                $instance = new BP_Portfolio_Pro_Plugin();
                $instance->setup_globals();
                $instance->setup_actions();
                $instance->setup_textdomain();
            }

            // Always return the instance
            return $instance;
        }

        /* Magic Methods
         * ===================================================================
         */

        /**
         * A dummy constructor to prevent BP Portfolio Pro from being loaded more than once.
         *
         * @since BP Portfolio Pro (1.0.0)
         * @see BP_Portfolio_Pro_Plugin::instance()
         * @see buddypress()
         */
        private function __construct() { /* nothing here */
        }

        /**
         * A dummy magic method to prevent BP Portfolio Pro from being cloned.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __clone() {
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'bp-portfolio-pro'), '1.7');
        }

        /**
         * A dummy magic method to prevent BP Portfolio Pro from being unserialized.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __wakeup() {
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'bp-portfolio-pro'), '1.7');
        }

        /**
         * Magic method for checking the existence of a certain custom field.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __isset($key) {
            return isset($this->data[$key]);
        }

        /**
         * Magic method for getting BP Portfolio Pro varibles.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __get($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }

        /**
         * Magic method for setting BP Portfolio Pro varibles.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __set($key, $value) {
            $this->data[$key] = $value;
        }

        /**
         * Magic method for unsetting BP Portfolio Pro variables.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __unset($key) {
            if (isset($this->data[$key]))
                unset($this->data[$key]);
        }

        /**
         * Magic method to prevent notices and errors from invalid method calls.
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function __call($name = '', $args = array()) {
            unset($name, $args);
            return null;
        }

        /* Plugin Specific, Setup Globals, Actions, Includes
         * ===================================================================
         */

        /**
         * Setup BP Portfolio Pro plugin global variables.
         *
         * @since 1.0.0
         * @access private
         *
         * @uses plugin_dir_path() To generate BP Portfolio Pro plugin path.
         * @uses plugin_dir_url() To generate BP Portfolio Pro plugin url.
         * @uses apply_filters() Calls various filters.
         */
        private function setup_globals() {

            global $BUDDYPRESS_CREATIVE_PORTFOLIO_PRO;
			
            $saved_options = get_option('bp_portfolio_pro_plugin_options');
            $saved_options = maybe_unserialize($saved_options);

            $this->options = wp_parse_args($saved_options, $this->default_options);

            // Normalize legacy uppercase keys
            foreach ($this->options as $key => $option) {
                // Delete old entry
                unset($this->options[$key]);

                // Override w/ lowercase key
                $this->options[strtolower($key)] = $option;
            }

            // all about settings
            $saved_settings = get_option('bp_portfolio_pro_plugin_settings');
            $saved_settings = maybe_unserialize($saved_settings);
            $this->settings = wp_parse_args($saved_settings, $this->default_settings);
            // Normalize legacy uppercase keys
            foreach ($this->settings as $key => $setting) {
                // Delete old entry
                unset($this->settings[$key]);
                // Override w/ lowercase key
                $this->settings[strtolower($key)] = $setting;
            }

            /** Versions ************************************************* */
            $this->version = BP_PORTFOLIO_PRO_PLUGIN_VERSION;
            $this->db_version = BP_PORTFOLIO_PRO_PLUGIN_DB_VERSION;

            /** Paths ***************************************************** */
            // BP Portfolio Pro root directory
            $this->file = BP_PORTFOLIO_PRO_PLUGIN_FILE;
            $this->basename = plugin_basename($this->file);
            $this->plugin_dir = BP_PORTFOLIO_PRO_PLUGIN_DIR;
            $this->plugin_url = BP_PORTFOLIO_PRO_PLUGIN_URL;

            // Languages
            $this->lang_dir = dirname($this->basename) . '/languages/';

            // Includes
            $this->includes_dir = $this->plugin_dir . 'includes';
            $this->includes_url = $this->plugin_url . 'includes';

            // Templates
            $this->templates_dir = $this->plugin_dir . 'templates';
            $this->templates_url = $this->plugin_url . 'templates';

            // Assets
            $this->assets_dir = $this->plugin_dir . 'assets';
            $this->assets_url = $this->plugin_url . 'assets';

            // Texts
            $this->appreciate_this =  __( 'Appreciate This', 'bp-portfolio-pro' );
            $this->appreciated = __( 'Appreciated', 'bp-portfolio-pro' );
            $this->appreciation = __( 'Appreciation', 'bp-portfolio-pro' );
            $this->appreciations = __( 'Appreciations', 'bp-portfolio-pro' );
            $this->view = __( 'View', 'bp-portfolio-pro' );
            $this->views = __( 'Views', 'bp-portfolio-pro' );

            //Collection thubanail size
            add_image_size( 'collection-thumbnail', 220, 220, true );
            add_image_size( 'revision-thumbnail', 220, 220, true );
            add_image_size( 'popular-thumbnail', 50, 50, true );
        }

        /**
         * Setup BP Portfolio Pro main actions
         *
         * @since  BP Portfolio Pro 1.0
         */
        private function setup_actions() {
			
            // Admin Hook into BuddyPress init
            add_action('bp_init', array($this, 'load_admin'));

            // WordPress generic hooks
            $this->generic_load();

            add_action('bp_init', array($this, 'bp_loaded'), 5 );
//            // Hook into BuddyPress init
//            if (is_multisite() && function_exists('bp_is_network_activated') && bp_is_network_activated() ) {
//                add_action('bp_init', array($this, 'bp_loaded'));
//            } else {
//                add_action('bp_init', array($this, 'bp_loaded'),5);
//            }
        }

        /**
         * Load plugin text domain
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses sprintf() Format .mo file
         * @uses get_locale() Get language
         * @uses file_exists() Check for language file(filename)
         * @uses load_textdomain() Load language file
         */
        public function setup_textdomain() {
            $domain = 'bp-portfolio-pro';
            $locale = apply_filters('plugin_locale', get_locale(), $domain);

            //first try to load from wp-content/languages/plugins/ directory
            load_textdomain($domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo');

            //if not found, then load from bp-portfolio-pro/languages/ directory
            load_plugin_textdomain('bp-portfolio-pro', false, $this->lang_dir);
        }
		
		/**
         * Include required admin files.
         *
         * @since BP Portfolio Pro (1.0.0)
         * @access private
         *
         * @uses $this->do_includes() Loads array of files in the include folder
         */
        public function load_admin() {
			if (( is_admin() || is_network_admin() ) && current_user_can('manage_options')) {
				$this->do_includes($this->admin_includes);
				$this->admin = BP_Portfolio_Pro_Admin::instance();
			}
        }

        /**
         * We require BuddyPress to run the main components, so we attach
         * to the 'bp_loaded' action which BuddyPress calls after it's started
         * up. This ensures any BuddyPress related code is only loaded
         * when BuddyPress is active.
         *
         * @since BP Portfolio Pro (1.0.0)
         * @access public
         *
         * @return void
         */
        public function bp_loaded() {
            global $bp;

            $this->bp_enabled = true;

            $this->load_main();
        }

        /* Load
         * ===================================================================
         */


        /**
         * Include required files.
         *
         * @since BP Portfolio Pro (1.0.0)
         * @access private
         *
         * @uses BP_Portfolio_Pro_Plugin::do_includes() Loads array of files in the include folder
         */
        private function load_main() {
            $this->do_includes($this->bp_includes);
			global $bp;
			if ( $bp ) {
				$bp->portfolio_pro = BPCP_Pro_Components::instance();
			}

            if ( ! is_admin() && ! is_network_admin() )
            {
                $this->assets();
            }
        }

        /**
         * Load WordPress generic functions like Widgets
         */
        private function generic_load() {
            $this->do_includes($this->main_includes);
        }

        /* Activate/Deactivation/Uninstall callbacks
         * ===================================================================
         */

        /**
         * Fires when plugin is activated
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses current_user_can() Checks for user permissions
         * @uses check_admin_referer() Verifies session
         */
        public function activate() {
            if (!current_user_can('activate_plugins')) {
                return;
            }

            $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';

            check_admin_referer("activate-plugin_{$plugin}");
        }

        /**
         * Fires when plugin is de-activated
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses current_user_can() Checks for user permissions
         * @uses check_admin_referer() Verifies session
         */
        public function deactivate() {
            if (!current_user_can('activate_plugins')) {
                return;
            }

            $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';

            check_admin_referer("deactivate-plugin_{$plugin}");
        }

        /**
         * Fires when plugin is uninstalled
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses current_user_can() Checks for user permissions
         * @uses check_admin_referer() Verifies session
         */
        public function uninstall() {
            if (!current_user_can('activate_plugins')) {
                return;
            }

            check_admin_referer('bulk-plugins');

            // Important: Check if the file is the one
            // that was registered during the uninstall hook.
            if ($this->file != WP_UNINSTALL_PLUGIN) {
                return;
            }
        }

        /* Utility functions
         * ===================================================================
         */

        /**
         * Include required array of files in the includes directory
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses require_once() Loads include file
         */
        public function do_includes($includes = array()) {
            foreach ((array) $includes as $include) {
                require_once( $this->includes_dir . '/' . $include . '.php' );
            }
        }

        /**
         * Convenience function to access plugin options, returns false by default
         *
         * @since  BP Portfolio Pro (1.0.0)
         *
         * @param  string $key Option key

         * @uses apply_filters() Filters option values with 'bp_portfolio_pro_option' &
         *                       'bp_portfolio_pro_option_{$option_name}'
         * @uses sprintf() Sanitizes option specific filter
         *
         * @return mixed Option value (false if none/default)
         *
         */
        public function option($key) {
            $key = strtolower($key);
            $option = isset($this->options[$key]) ? $this->options[$key] : null;

            // Apply filters on options as they're called for maximum
            // flexibility. Options are are also run through a filter on
            // class instatiation/load.
            // ------------------------
            // This filter is run for every option
            $option = apply_filters('bp_portfolio_pro_option', $option);

            // Option specific filter name is converted to lowercase
            $filter_name = sprintf('bp_portfolio_pro_option_%s', strtolower($key));
            $option = apply_filters($filter_name, $option);

            return $option;
        }

        public function setting($key) {
            $key = strtolower($key);
            $setting = isset($this->settings[$key]) ? $this->settings[$key] : null;

            // Apply filters on options as they're called for maximum
            // flexibility. Options are are also run through a filter on
            // class instatiation/load.
            // ------------------------
            // This filter is run for every option
            $setting = apply_filters('bp_portfolio_pro_setting', $setting);

            // Option specific filter name is converted to lowercase
            $filter_name = sprintf('bp_portfolio_pro_setting_%s', strtolower($key));
            $setting = apply_filters($filter_name, $setting);

            return $setting;
        }

        /**
         * Load css/js files
         *
         * @since 1.0.0
         * @return void
         */
        public function assets() {

            $class_sufix = '';
            if ( is_rtl() ) {
                $class_sufix = '-rtl';
            }

			if ( wp_script_is( 'wp-mediaelement', 'enqueued' ) ) {
				return;
			} else {
				wp_enqueue_style( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-mediaelement' );
			}

            add_action('wp_print_scripts','bpcp_pro_dequeue_scripts');

            wp_enqueue_style( 'dashicons' );
			wp_enqueue_script('jquery-ui-sortable');

			//wp_enqueue_style('bpcp-magnific-popup', bp_portfolio_pro()->assets_url . '/css/magnific-popup' . $class_sufix . '.css', array(), '1.0.0', 'all');
            //wp_enqueue_style('bp-portfolio-pro-main', bp_portfolio_pro()->assets_url . '/css/bp-portfolio-pro' . $class_sufix . '.css', array(), '1.0.0', 'all');
            wp_register_style('autocomplete', bp_portfolio_pro()->assets_url . '/css/jquery-ui.min.css', array(), '1.0.0', 'all');
            wp_enqueue_style('bp-portfolio-pro-main-css', bp_portfolio_pro()->assets_url . '/css/bp-portfolio-pro-combined' . $class_sufix . '.css', array(), '1.0.0', 'all');

			//wp_enqueue_script( 'bpcp-magnific-popup', bp_portfolio_pro()->assets_url . '/js/jquery.magnific-popup.min.js', array(), '1.0.0', true );
//            wp_register_script('bp-portfolio-pro-main', bp_portfolio_pro()->assets_url . '/js/bp-portfolio-pro.js', array(), '1.0.0', true);
            wp_register_script('autocomplete', bp_portfolio_pro()->assets_url . '/js/jquery-ui.min.js', array(), '1.0.0', true);
            wp_register_script('bp-portfolio-pro-main', bp_portfolio_pro()->assets_url . '/js/bp-portfolio-pro-combined.js', array(), '1.0.0', true);

            // localize the script with our data.
            $translation_array = array(
                'multiselect_placeholder' => __( 'Choose...', 'bp-portfolio-pro' ),
                'project_title_missing' => __( 'Project title is missing', 'bp-portfolio-pro' ),
                'project_content_missing' => __( 'Project content is missing', 'bp-portfolio-pro' ),
                'project_category_required' => bp_portfolio()->setting( 'bpcp-projects-category-required' ),
                'project_category_missing' => sprintf( __( '%s is missing', 'bp-portfolio-pro' ), bpcp_project_category_label() ),
                'project_visibility_missing' => __( 'Please set a visibility', 'bp-portfolio-pro' ),
                'image_upload_text' => __( 'Upload Media', 'bp-portfolio-pro' ),
                'use_this_image_text' => __( 'Use this Media', 'bp-portfolio-pro' ),
                'delete_btn' => __( 'Delete', 'bp-portfolio-pro' ),
                'upload_another_image' => __( 'Add More Media', 'bp-portfolio-pro' ),
                'wip_title_missing' => __( 'WIP title is missing', 'bp-portfolio-pro' ),
                'wip_category_required' => bp_portfolio()->setting( 'bpcp-wip-category-required' ),
                'wip_category_missing' => sprintf( __( '%s is missing', 'bp-portfolio-pro' ), bpcp_pro_wip_category_label() ),
                'video_embed_empty_msg' => __( 'Video embed field cannot be empty.', 'bp-portfolio-pro' ),
                'video_embed_disable_msg' => __( 'This video does now allow embedding. Please try another video.', 'bp-portfolio-pro' ),
                'collection_title_missing' => __( 'Collection title cannot be empty', 'bp-portfolio-pro' ),
                'wip_comment_heading' => __( 'Discuss this Work in Progress', 'bp-portfolio-pro' ),
                'caption_input_label' => __( 'Caption', 'bp-portfolio-pro' ),
                'appreciate_this_text' => bp_portfolio_pro()->appreciate_this,
                'appreciated_text' => bp_portfolio_pro()->appreciated,
            );
            
            if ( isset($_GET['bpcp_action']) && $_GET['bpcp_action'] == 'project_cover' ) {
                if(isset($_GET['project_id']) && $_GET['project_id'] != '' ) {
                    $translation_array['bpcp_project_id'] = $_GET['project_id'];
                }
            }

            wp_localize_script( 'bp-portfolio-pro-main', 'bpcp_object', $translation_array );

            wp_enqueue_script( 'bp-portfolio-pro-main' );

        }

    }

// End class BP_Portfolio_Pro_Plugin

endif;

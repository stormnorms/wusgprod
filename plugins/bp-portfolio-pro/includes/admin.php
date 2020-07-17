<?php
/**
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!class_exists('BP_Portfolio_Pro_Admin')):

    /**
     *
     * BP_Portfolio_Pro_Admin
     * ********************
     *
     *
     */
    class BP_Portfolio_Pro_Admin {
        /* Options/Load
         * ===================================================================
         */

        /**
         * Plugin options
         *
         * @var array
         */
        public $options = array();

        /**
         * Empty constructor function to ensure a single instance
         */
        public function __construct() {
            // ... leave empty, see Singleton below
        }

        /* Singleton
         * ===================================================================
         */

        /**
         * Admin singleton
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @param  array  $options [description]
         *
         * @uses BP_Portfolio_Pro_Admin::setup() Init admin class
         *
         * @return object Admin class
         */
        public static function instance() {
            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Portfolio_Pro_Admin;
                $instance->setup();
            }

            return $instance;
        }

        /* Utility functions
         * ===================================================================
         */

        /**
         * Get option
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @param  string $key Option key
         *
         * @uses BP_Portfolio_Pro_Admin::option() Get option
         *
         * @return mixed      Option value
         */
        public function option($key) {
            $value = BP_Portfolio_Pro()->option($key);
            return $value;
        }

        /* Actions/Init
         * ===================================================================
         */

        /**
         * Setup admin class
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses BP_Portfolio_Pro() Get options from main BP_Portfolio_Pro_Admin class
         * @uses is_admin() Ensures we're in the admin area
         * @uses curent_user_can() Checks for permissions
         * @uses add_action() Add hooks
         */
        public function setup() {
            if ((!is_admin() && !is_network_admin() ) || !current_user_can('manage_options')) {
                return;
            }

            $actions = array(
                'admin_init'
            );

            if (isset($_GET['page']) && ( $_GET['page'] == 'bp-portfolio-pro/includes/admin.php' )) {
                $actions[] = 'admin_enqueue_scripts';
            }

            foreach ($actions as $action) {
                add_action($action, array($this, $action));
            }

            add_action( 'bpcp_component_pages_fields', array($this, 'bpcp_pro_component_pages_extend') );
            add_action( 'bpcp_settings_component_fields', array($this, 'bpcp_pro_settings_component_extend') );
            add_action( 'bpcp_settings_misc_fields', array($this, 'bpcp_pro_settings_misc_extend') );
			
			// add setting link
            $buddyboss = BP_Portfolio_Pro_Plugin::instance();
            $plugin = $buddyboss->basename;
            add_filter("plugin_action_links_$plugin", array($this, 'plugin_settings_link'));
            
            add_filter("bpcp_posttypes_array", array($this, 'posttypes_array'));
            add_action("bpcp_after_metabox_fields", array($this, 'metabox_fields'));
            add_action("bpcp_after_update_metabox_fields", array($this, 'update_metabox_fields'));
        }
        
        public function posttypes_array($array) {
            $array[] = "bb_wip"; 
            $array[] = "bb_collection";
            return $array;
        }
        
        public function metabox_fields($object) {
            if($object->post_type == 'bb_collection') {
            ?>
            <div>
                <label for="collection_visibility"><?php _e( 'Visibility', 'bp-portfolio-pro' ); ?></label>
                <select id="collection_visibility" name="collection_visibility" class="project-visibility">
                    <?php
                    $options = array(
                        'public'	=> __('Everyone', 'bp-portfolio-pro'),
                        'private'	=> __('Only Me', 'bp-portfolio-pro'),
                        'members'	=> __('All Members', 'bp-portfolio-pro'),
                    );

                    $collection_visibility = get_post_meta($object->ID, 'collection_visibility', true);

                    $selected_val = isset($collection_visibility) ? $collection_visibility : '';

                    foreach( $options as $key=>$val ){
                        $selected = $selected_val == $key ? ' selected' : '';
                        echo "<option value='" . esc_attr( $key ) . "' $selected>$val</option>";
                    }
                    ?>
                </select>
            </div>
            <?php
            }
            if($object->post_type == 'bb_wip') {
            ?>
            <div>
                <label for="wip_visibility"><?php _e( 'Visibility', 'bp-portfolio-pro' ); ?></label>
                <select id="wip_visibility" name="wip_visibility" class="project-visibility">
                    <?php
                    $options = array(
                        'public'	=> __('Everyone', 'bp-portfolio-pro'),
                        'private'	=> __('Only Me', 'bp-portfolio-pro'),
                        'members'	=> __('All Members', 'bp-portfolio-pro'),
                    );

                    $wip_visibility = get_post_meta($object->ID, 'wip_visibility', true);

                    $selected_val = isset($wip_visibility) ? $wip_visibility : '';

                    foreach( $options as $key=>$val ){
                        $selected = $selected_val == $key ? ' selected' : '';
                        echo "<option value='" . esc_attr( $key ) . "' $selected>$val</option>";
                    }
                    ?>
                </select>
            </div>
            <?php
            }
        }
        
        public function update_metabox_fields($post) {
            $post_id = $post->ID;
            if($post->post_type == 'bb_collection') {
                $collection_visibility = "";

                if(isset($_POST["collection_visibility"]))
                {
                    $collection_visibility = $_POST["collection_visibility"];
                }   
                update_post_meta($post_id, "collection_visibility", $collection_visibility);
            }
            if($post->post_type == 'bb_wip') {
                $wip_visibility = "";

                if(isset($_POST["wip_visibility"]))
                {
                    $wip_visibility = $_POST["wip_visibility"];
                }   
                update_post_meta($post_id, "wip_visibility", $wip_visibility);
            }
        }

        /**
         * Register admin settings
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses register_setting() Register plugin options
         * @uses add_settings_section() Add settings page option sections
         * @uses add_settings_field() Add settings page option
         */

        /*function bpcp_options_fields(){
            echo 'hello';
        }*/

        function admin_init(){
            register_setting(
                'bp_portfolio_plugin_options',  // settings page
                'bp_portfolio_pro_plugin_options', // option name
                array( $this, 'plugin_options_validate' )  // validation callback
            );
            register_setting(
                'bp_portfolio_plugin_settings',  // settings page
                'bp_portfolio_pro_plugin_settings', // option name
                array( $this, 'plugin_options_validate' )  // validation callback
            );

            // turn off views count if WP-PostViews not activated
            if(!function_exists('bpcp_pro_the_views')) {
                $get_pro_settings = get_option('bp_portfolio_pro_plugin_settings');
                if(!empty($get_pro_settings['bpcp-pro-projects-views-count'])){
                    $get_pro_settings['bpcp-pro-projects-views-count'] = '';
                    update_option('bp_portfolio_pro_plugin_settings', $get_pro_settings);
                }
            }
        }

		// Add settings link on plugin page
        function plugin_settings_link($links) {
            $links[] = '<a href="'.admin_url("admin.php?page=bp_portfolio").'">'.__("Settings","bp-portfolio-pro").'</a>';
            return $links;
        }
		
        /**
         * Register admin scripts
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses wp_enqueue_script() Enqueue admin script
         * @uses wp_enqueue_style() Enqueue admin style
         * @uses BP_Portfolio_Pro()->assets_url Get plugin URL
         */
        public function admin_enqueue_scripts() {
            $js = BP_Portfolio_Pro()->assets_url . '/js/';
            $css = BP_Portfolio_Pro()->assets_url . '/css/';
        }

        /* Settings Page + Sections
         * ===================================================================
         */

        /**
         * Render settings page
         *
         * @since BP Portfolio Pro (1.0.0)
         *
         * @uses do_settings_sections() Render settings sections
         * @uses settings_fields() Render settings fields
         * @uses esc_attr_e() Escape and localize text
         */
        public function options_page() {
            ?>
            <div class="wrap">
                <h2><?php _e( 'BP Portfolio Pro' , 'bp-portfolio-pro' ) ; ?></h2>
                <form action="options.php" method="post">
                <?php settings_fields('bp_portfolio_pro_plugin_options'); ?>
                <?php do_settings_sections(__FILE__); ?>

                    <p class="submit">
                        <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' , 'bp-portfolio-pro' ); ?>" />
                    </p>
                </form>
            </div>

            <?php
        }

        /**
         * General settings section
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function section_general() {

        }

        /**
         * Style settings section
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function section_style() {

        }

        /**
         * Validate plugin option
         *
         * @since BP Portfolio Pro (1.0.0)
         */
        public function plugin_options_validate($input) {
            if ( isset( $input['bpcp-pro-projects-views-count'] ) && $input['bpcp-pro-projects-views-count'] ) {
                if(!function_exists('bpcp_pro_the_views')) {
                    $input['bpcp-pro-projects-views-count'] = false;
                }
            }

            return $input; // return validated input
        }

        /**
         * Setting > all media page
         *
         * @since BuddyBoss Media (1.0.1)
         *
         * @uses BuddyBoss_Media_Admin::option() Get plugin option
         */
        public function bpcp_pro_component_pages_extend()
        {
            include_once( bp_portfolio_pro()->includes_dir . '/admin/views/pages.php' );
        }

        public function bpcp_pro_settings_component_extend(){
            include_once( bp_portfolio_pro()->includes_dir . '/admin/views/settings_component.php' );
        }

        public function bpcp_pro_settings_misc_extend(){
            include_once( bp_portfolio_pro()->includes_dir . '/admin/views/settings_misc.php' );
        }

    }

// End class BP_Portfolio_Pro_Admin

endif;

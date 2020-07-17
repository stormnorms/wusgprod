<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if( !class_exists('BPCP_Pro_Components') ):

    class BPCP_Pro_Components{

        public $wip_post_type_slug = 'bb_wip';
        public $wip_post_type_taxonomy = 'bb_wip_tag';
        public $collections_post_type_slug = 'bb_collection';
        public $collections_post_type_taxonomy = 'bb_collection_tag';

        /**
         * empty constructor function to ensure a single instance
         */
        public function __construct(){
            // leave empty, see singleton below
        }

        public static function instance(){
            static $instance = null;

            if(null === $instance){
                $instance = new BPCP_Pro_Components;
                $instance->setup();
            }
            return $instance;
        }

        /**
         * setup all related to new components
         */
        public function setup(){
            // WIP post type, bp nav, admin bar & global page
            add_action( 'init', array($this, 'bpcp_pro_create_post_type'), 11 );
            //add_action( 'init', array($this, 'bpcp_pro_register_taxonomy'), 0 );
            add_action( 'bp_setup_nav', array($this, 'bpcp_pro_add_new_setup_nav'), 100 );
            add_action( 'bp_setup_admin_bar', array($this, 'bpcp_pro_add_new_admin_bar'), 300 );

            // ajax callback
            add_action('wp_ajax_bpcpproajax', 'bpcp_pro_ajax_callback');
            add_action('wp_ajax_nopriv_bpcpproajax', 'bpcp_pro_ajax_callback');

            //load assets
            add_action('wp_enqueue_scripts', array($this, 'assets' ) );
        }

        public function assets() {
            if ( bpcp_is_portfolio_component() &&
                bp_is_current_action( bpcp_pro_subnav_collections_slug() ) ) {
                wp_enqueue_style('autocomplete');
                wp_enqueue_script('autocomplete');
            }
        }

        /**
         * WIP post type all labels
         * @return array
         */
        public function wip_post_type_labels(){
            $labels_wip = array(
                'name'	             => __( 'WIPs', 'bp-portfolio-pro' ),
                'singular'           => __( 'WIP', 'bp-portfolio-pro' ),
                'menu_name'          => __( 'WIPs', 'bp-portfolio-pro' ),
                'all_items'          => __( 'All WIPs', 'bp-portfolio-pro' ),
                'singular_name'      => __( 'WIP', 'bp-portfolio-pro' ),
                'add_new'            => __( 'Add New WIP', 'bp-portfolio-pro' ),
                'add_new_item'       => __( 'Add New WIP', 'bp-portfolio-pro' ),
                'edit_item'          => __( 'Edit WIP', 'bp-portfolio-pro' ),
                'new_item'           => __( 'New WIP', 'bp-portfolio-pro' ),
                'view_item'          => __( 'View WIP', 'bp-portfolio-pro' ),
                'search_items'       => __( 'Search WIPs', 'bp-portfolio-pro' ),
                'not_found'          => __( 'No WIPs Found', 'bp-portfolio-pro' ),
                'not_found_in_trash' => __( 'No WIPs Found in Trash', 'bp-portfolio-pro' )
            );
            return $labels_wip;
        }

        /**
         * Collections post type all labels
         * @return array
         */
        public function collections_post_type_labels(){
            $labels_collections = array(
                'name'	             => __( 'Collections', 'bp-portfolio-pro' ),
                'singular'           => __( 'Collection', 'bp-portfolio-pro' ),
                'menu_name'          => __( 'Collections', 'bp-portfolio-pro' ),
                'all_items'          => __( 'All Collections', 'bp-portfolio-pro' ),
                'singular_name'      => __( 'Collection', 'bp-portfolio-pro' ),
                'add_new'            => __( 'Add New Collection', 'bp-portfolio-pro' ),
                'add_new_item'       => __( 'Add New Collection', 'bp-portfolio-pro' ),
                'edit_item'          => __( 'Edit Collection', 'bp-portfolio-pro' ),
                'new_item'           => __( 'New Collection', 'bp-portfolio-pro' ),
                'view_item'          => __( 'View Collection', 'bp-portfolio-pro' ),
                'search_items'       => __( 'Search Collections', 'bp-portfolio-pro' ),
                'not_found'          => __( 'No Collections Found', 'bp-portfolio-pro' ),
                'not_found_in_trash' => __( 'No Collections Found in Trash', 'bp-portfolio-pro' )
            );
            return $labels_collections;
        }

        /**
         * registering all custom post types
         */
        public function bpcp_pro_create_post_type() {
            $all_post_types = array(
                'wip' => array(
                    'name' => bpcp_pro_subnav_wip_name(),
                    'slug' => $this->wip_post_type_slug,
                    'taxonomy' => $this->wip_post_type_taxonomy,
                    'labels' => $this->wip_post_type_labels(),
                    'enable_status' => bp_portfolio_pro()->setting( 'bpcp-wip-enable' )

                ),
                'collections' => array(
                    'name' => bpcp_pro_subnav_collections_name(),
                    'slug' => $this->collections_post_type_slug,
                    'taxonomy' => $this->collections_post_type_taxonomy,
                    'labels' => $this->collections_post_type_labels(),
                    'enable_status' => bp_portfolio_pro()->setting( 'bpcp-collections-enable' )
                ),
            );

            foreach($all_post_types as $single){
                if($single['enable_status'] == 'on'){
                    $this->bpcp_pro_register_post_type($single['name'], $single['slug'], $single['labels']);
                    $this->bpcp_pro_register_taxonomy($single['slug'], $single['taxonomy']);
                }
            }

            $this->bpcp_pro_register_taxonomy_category();
            flush_rewrite_rules();
        }

        public function bpcp_pro_register_post_type($post_type_name, $post_type_slug, $labels_array){
            $args = array(
                'label'	=> $post_type_name,
                'labels' => $labels_array,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => true,
				'menu_icon' => 'dashicons-admin-customizer',
                'supports' => array( 'title', 'editor', 'author', 'portfolio-thumbnail', 'custom-fields', 'comments'),
				'show_in_rest' => true,
            );

            $args = apply_filters( 'bpcp_pro_register_post_type_'.$post_type_slug, $args );

            register_post_type( $post_type_slug, $args );
        }

        /**
         * registering wip custom taxonomy
         */
        public function bpcp_pro_register_taxonomy($post_type_slug, $taxonomy_name)
        {
            $labels_tag = array(
                'name' => __('Tags', 'bp-portfolio-pro'),
                'singular_name' => __('Tag', 'bp-portfolio-pro'),
            );
            $args_tag = array(
                'labels' => $labels_tag,
                'hierarchical' => false,
                'show_ui' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => $taxonomy_name),
				'show_in_rest' => true,
            );
            register_taxonomy($taxonomy_name, $post_type_slug, $args_tag);
        }

        public function bpcp_pro_register_taxonomy_category(){
            $labels_tag = array(
                'name' => bpcp_pro_wip_category_label(),
                'singular_name' => bpcp_pro_wip_category_label(),
            );
            $args_tag = array(
                'labels' => $labels_tag,
                'hierarchical' => true,
                'show_ui' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'bb_wip_category'),
				'show_in_rest' => true,
            );
            register_taxonomy( 'bb_wip_category', $this->wip_post_type_slug, $args_tag);
        }

        /**
         * Add new subnav item to portfolio component
         */
        public function bpcp_pro_add_new_setup_nav(){
            $all_post_types = bpcp_pro_portfolio_subcomponents();

            foreach($all_post_types as $single){
                if($single['enable_status'] == 'on'){
                    $this->bpcp_pro_setup_nav($single['name'], $single['slug'], $single['screen']);
                }
            }
        }

        public function bpcp_pro_setup_nav($name, $slug, $screen) {
            global $bp;

            $displayed_user_id = bp_displayed_user_id();
            $user_domain = ( ! empty( $displayed_user_id ) ) ? bp_displayed_user_domain() : bp_loggedin_user_domain();

            $portfolio_link = trailingslashit( $user_domain . bpcp_portfolio_slug() );

            bp_core_new_subnav_item( array(
                'name' => $name,
                'slug' => $slug,
                'parent_url' => $portfolio_link,
                'parent_slug' => bpcp_portfolio_slug(),
                'screen_function' => $screen,
                'position' => 55
            ) );
        }

        /**
         * add new admin bar item for WIP
         */
        public function bpcp_pro_add_new_admin_bar(){
            $all_post_types = bpcp_pro_portfolio_subcomponents();

            foreach($all_post_types as $single){
                if($single['enable_status'] == 'on'){
                    $this->bpcp_pro_wip_setup_admin_bar($single['name'], $single['slug'], $single['root']);
                }
            }
        }

        public function bpcp_pro_wip_setup_admin_bar($name, $slug, $component_root) {
            global $wp_admin_bar;

            $wp_admin_bar->add_menu( array(
                'parent' => 'my-account-' . bpcp_portfolio_slug(),
                'id'     => 'my-account-' . bpcp_portfolio_slug() .'-'.$slug,
                'title'  => $name,
                'href'   => $component_root
            ) );
        }

    }

endif;

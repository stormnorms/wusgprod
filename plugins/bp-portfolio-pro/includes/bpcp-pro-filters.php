<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;


if( !class_exists('BPCP_Pro_Filters') ):

    class BPCP_Pro_Filters{

        public $wip_option_name = 'all-wip-page';
        public $wip_teplate_name = 'global-wip';
        public $collections_option_name = 'all-collections-page';
        public $collections_teplate_name = 'global-collections';

        /**
         * empty constructor function to ensure a single instance
         */
        public function __construct(){
            // leave empty, see singleton below
        }

        public static function instance(){
            static $instance = null;

            if(null === $instance){
                $instance = new BPCP_Pro_Filters;
                $instance->setup();
            }
            return $instance;
        }

        public function setup(){

            add_action( 'wp_head', array($this, 'bpcp_pro_load_global_page_query') );
            add_filter( 'bpcp_templates_dir_filter', array($this, 'bpcp_pro_templates_dir') );
            add_filter( 'bpcp_post_types_filter', array($this, 'bpcp_pro_all_post_types') );
            add_filter( 'body_class', array($this, 'bpcp_pro_body_class_names') );
            add_action( 'init', array( $this, 'wip_tracking_args' ), 20 );
        }

        //Loaded after wp_head for yoast seo compatibility
        public function bpcp_pro_load_global_page_query() {
            add_filter( 'the_content', array($this, 'bpcp_pro_content_global_pages') );
        }

        /**
         * Filter content of global WIP page
         * @param $content
         * @return string
         */
        public function bpcp_pro_content_global_pages( $content ){
            if( !is_main_query() )
                return $content;

            // wip template
            if( bp_portfolio_pro()->setting( 'bpcp-wip-enable' ) == 'on' &&
                bp_portfolio_pro()->option($this->wip_option_name) &&
                is_page( bp_portfolio_pro()->option($this->wip_option_name) )
            ){
                $content = wpautop(apply_filters('bpc_global_wips_content', $content)) . bpcp_pro_buffer_template_part( $this->wip_teplate_name, false );
            }
            // collections template
            if( bp_portfolio_pro()->setting( 'bpcp-collections-enable' ) == 'on' &&
                bp_portfolio_pro()->option($this->collections_option_name) &&
                is_page( bp_portfolio_pro()->option($this->collections_option_name) )
            ){
                $content = wpautop(apply_filters('bpc_global_collections_content', $content)) . bpcp_pro_buffer_template_part( $this->collections_teplate_name, false );
            }
            return $content;
        }

        /**
         * Add specific CSS class by filter
         * @param $classes
         * @return array
         */
        public function bpcp_pro_body_class_names( $classes ) {
            global $post;

            // add 'class-name' to wip page
            if( bp_portfolio_pro()->setting( 'bpcp-wip-enable' ) == 'on' &&
                bp_portfolio_pro()->option($this->wip_option_name) &&
                is_page( bp_portfolio_pro()->option($this->wip_option_name) ) &&
                $post->ID == bp_portfolio_pro()->option($this->wip_option_name)
            ){
                $classes[] = 'wip-global-page';
                $classes[] = 'global-page-layout';
            }

            // add 'class-name' to collection page
            if( bp_portfolio_pro()->setting( 'bpcp-collections-enable' ) == 'on' &&
                bp_portfolio_pro()->option($this->collections_option_name) &&
                is_page( bp_portfolio_pro()->option($this->collections_option_name) ) &&
                $post->ID == bp_portfolio_pro()->option($this->collections_option_name)
            ){
                $classes[] = 'collections-global-page';
                $classes[] = 'global-page-layout';
            }

            // return the $classes array
            return $classes;
        }

        /**
         * Overwrite template directory for BPCP
         * @return null
         */
        public function bpcp_pro_templates_dir(){
            return bp_portfolio_pro()->templates_dir;
        }

        /** filter custom post types
         * @return array
         */
        public function bpcp_pro_all_post_types(){
            return array('bb_project', 'bb_wip', 'bb_collection');
        }

        /**
         * Add custom post to an array
         * which is used for post types
         * activity record by bp default
         * @param $post_types string
         * @return array
         */
        public function bpcp_pro_record_wip_activity( $post_types ) {
            $post_types[] = 'bb_wip'; // Add your custom post type name to the array.
            return $post_types;
        }

        /**
         * Activity record format change
         * @param $activity_action string
         * @param $post object
         * @param $post_permalink string
         * @return string
         */
        public function bpcp_pro_record_wip_activity_action( $activity_action, $post, $post_permalink ) {
            if( $post->post_type == 'bb_wip' ) {
                $new_wip_id = $post->ID;
                if($post->post_parent != 0){
                    $activity_text = __( '%s uploaded a revision to a work in progress, %s', 'bp-portfolio-pro' );
                }else{
                    $activity_text = __( '%s uploaded a new work in progress, %s', 'bp-portfolio-pro' );
                }
                $user_wip_link = '<a href="'.get_permalink($new_wip_id).'">'.$post->post_title.'</a>';
                $activity_action = sprintf( $activity_text,
                    bp_core_get_userlink( (int) $post->post_author ),
                    $user_wip_link
                );
            }
            return $activity_action;
        }

        public function bpcp_pro_record_wip_activity_action_new_blog_post( $action, $activity ) {
            return $activity->action;
        }

        /**
         * Set activity tracking arguments for a bb_wip post type.
         *
         * https://codex.buddypress.org/plugindev/post-types-activities/
         *
         */
        public function wip_tracking_args() {

            // Check if the Activity component is active before using it.
            if ( ! bp_is_active( 'activity' ) ) {
                return;
            }

            // Don't forget to add the 'buddypress-activity' support!
            add_post_type_support( 'bb_wip', 'buddypress-activity' );

            $args = array(
                'format_callback' => 'bpcp_format_activity_action_new_wip',
            );

            $bpcp_pro_wip_comments_sync = bp_portfolio_pro()->setting('bpcp-pro-wip-comments-sync');

            // Only sync activity and posty type comment if "WIP Comments sync" is on
            if ( $bpcp_pro_wip_comments_sync == 'on' ) {
                $args['comment_action_id'] = 'new_bb_wip_comment';
                $args['comment_format_callback'] = 'bpcp_activity_format_activity_action_wip_comment';
                $args['bp_activity_comments_admin_filter'] = __('Commented a work in progress', 'bp-portfolio-pro');
                $args['bp_activity_comments_front_filter'] = __('Work in Progress Comments', 'bp-portfolio-pro');
                $args['bp_activity_new_comment'] = __('%1$s commented on the <a href="%2$s">work in progress</a>', 'bp-portfolio-pro');
                $args['bp_activity_new_comment_ms'] = __('%1$s commented on the <a href="%2$s">work in progress</a>, on the site %3$s', 'bp-portfolio-pro');
            }

            bp_activity_set_post_type_tracking_args('bb_wip', $args );
        }

    }
    BPCP_Pro_Filters::instance();

endif;

/**
 * dequeue scripts in bpcp
 */
function bpcp_pro_dequeue_scripts(){
    wp_dequeue_script('bp-portfolio-main');
    wp_dequeue_style('bp-portfolio-main');
    if(isset($_GET['bpcp_action']) && $_GET['bpcp_action'] == 'project_content'){
        wp_dequeue_script('fitvids');
    }
}


function projects_filter_by_user_choice( $clauses, $query_object ){
    global $wpdb;

    // Now, let's add your table into the SQL
    $join = &$clauses['join'];
    if (! empty( $join ) ) $join .= ' '; // add a space only if we have to (for bonus marks!)
    $join .= "LEFT JOIN {$wpdb->prefix}bpcp_components_like ON {$wpdb->prefix}bpcp_components_like.post_id= {$wpdb->posts}.ID";

    // Add fields
    $fields = &$clauses['fields'];
    $fields .= " ,(SELECT count(*) FROM {$wpdb->prefix}bpcp_components_like WHERE {$wpdb->prefix}bpcp_components_like.post_id = {$wpdb->posts}.ID) AS likecount";

    if(!bp_displayed_user_id()){
        $where = &$clauses['where'];
        $where .= ' OR ( ( ' . $wpdb->posts . '.post_author = ' . bp_loggedin_user_id() . ' ) ';
        $where .= ' AND  ( ' . $wpdb->posts . '.post_type   = "bb_project" ) ';
        $where .= ' AND  ( ' . $wpdb->posts . '.post_status = "publish" ) ) ';
    }

    // change groupby
    $groupby = &$clauses['groupby'];
    $groupby = " {$wpdb->posts}.ID ";

    // change orderby
    $orderby = &$clauses['orderby'];
    $orderby = " likecount DESC";

    return $clauses;
}

function bpcp_pro_wip_by_latest_revision(){
    global $wpdb;
    $post_ids = array();
    $prepare_sql = "
      SELECT cp.post_parent, cp.ID
	  FROM {$wpdb->posts} AS cp
      WHERE cp.post_type = 'bb_wip' AND cp.post_status = 'publish'
      ORDER BY cp.post_date DESC
      ";
    $results = $wpdb->get_results( $prepare_sql, OBJECT );
    if(!empty($results)){
        foreach($results as $single){
            $post_ids[] = $single->post_parent == 0 ? $single->ID : $single->post_parent;
        }
    }
    $post_ids = array_unique($post_ids);
    return $post_ids;
}

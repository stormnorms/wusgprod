<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;


/** portfolio subnav name can be changed from here
 * @return string
 */
function bpcp_pro_subnav_wip_name()
{
    return __( 'WIP', 'bp-portfolio-pro' );
}

/** portfolio subnav slug can be changed from here
 * @return string
 */
function bpcp_pro_subnav_wip_slug()
{
    return __( 'wip', 'bp-portfolio-pro' );
}

function bpcp_pro_wip_root( $user_id = 0 ){
    return trailingslashit( bpcp_portfolio_component_root( $user_id ) . bpcp_pro_subnav_wip_slug() );
}

/** portfolio subnav name can be changed from here
 * @return string
 */
function bpcp_pro_subnav_collections_name()
{
    return __( 'Collections', 'bp-portfolio-pro' );
}

/** portfolio subnav slug can be changed from here
 * @return string
 */
function bpcp_pro_subnav_collections_slug()
{
    return __( 'collections', 'bp-portfolio-pro' );
}

function bpcp_pro_collections_root(){
    return trailingslashit( bpcp_portfolio_component_root() . bpcp_pro_subnav_collections_slug() );
}


/**
 * Get all/one attachment image url array of a post
 * @param int $post_ID
 * @param string /array $thumb_size
 * @param string $num
 * @return array
 */
function bpcp_pro_get_attachment_detail($post_ID, $thumb_size, $num = 'one')
{
    if (empty($post_ID)) {
        return array();
    }

    $args = array(
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'post_type' => 'attachment',
        'post_parent' => $post_ID,
        'post_mime_type' => array('image', 'audio'),
        'post_status' => null,
        'numberposts' => -1,
    );
    $attachments = get_posts($args);

    if ($attachments) {
        $i = 0;
        foreach ($attachments as $attachment) {
            $attachment_type = explode('/',$attachment->post_mime_type);
            $attachment_type = $attachment_type[0];
            if($attachment_type == 'image'){
                $image_attributes_detail = wp_get_attachment_image_src($attachment->ID, $thumb_size);
                $image_list[$i]['full_src'] = wp_get_attachment_url( $attachment->ID );
                $image_list[$i]['src'] = $image_attributes_detail[0];
            }else{
                $image_list[$i]['src'] = $attachment->guid;
            }
            $image_list[$i]['ID'] = $attachment->ID;
            $image_list[$i]['type'] = $attachment_type;
            $image_list[$i]['title'] = $attachment->post_title;
            $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            $image_list[$i]['alt'] = !empty($alt_text) ? $alt_text : $image_list[$i]['title'];
            $image_list[$i]['caption'] = $attachment->post_excerpt;
            $image_list[$i]['description'] = $attachment->post_content;
            $i++;
        }
    }

    if ($num == 'one' && isset($image_list[0])) {
        $image_urls = $image_list[0];
    }
    if ($num == 'all' && isset($image_list)) {
        $image_urls = $image_list;
    }
    return !empty($image_list[0]['src']) ? $image_urls : array();
}

/**
 * Get single attachment detail
 * @param int $post_ID
 * @param string /array $thumb_size
 * @param string $num
 * @return array
 */
function bpcp_pro_single_attachment_detail($attach_ID, $thumb_size)
{
    if (empty($attach_ID)) {
        return array();
    }

    $attachment = get_post($attach_ID);
    if($attachment) {
        $attachment_type = explode('/',$attachment->post_mime_type);
        $attachment_type = $attachment_type[0];
        if($attachment_type == 'image'){
            $image_attributes_detail = wp_get_attachment_image_src($attachment->ID, $thumb_size);
            $image_list['full_src'] = wp_get_attachment_url( $attachment->ID );
            $image_list['src'] = $image_attributes_detail[0];
        }else{
            $image_list['src'] = $attachment->guid;
        }
        $image_list['ID'] = $attachment->ID;
        $image_list['type'] = $attachment_type;
        $image_list['title'] = $attachment->post_title;
        $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
        $image_list['alt'] = !empty($alt_text) ? $alt_text : $image_list['title'];
        $image_list['caption'] = $attachment->post_excerpt;
        $image_list['description'] = $attachment->post_content;

        $image_urls = $image_list;
    }
    return !empty($image_list['src']) ? $image_urls : array();
}

function bpcp_pro_load_template($template){
    $template .= '.php';
    if(file_exists(STYLESHEETPATH.'/bp-portfolio/'.$template)) {
        include_once(STYLESHEETPATH.'/bp-portfolio/'.$template);

    }
    else if(file_exists(TEMPLATEPATH.'/bp-portfolio/'.$template))
        include_once (TEMPLATEPATH.'/bp-portfolio/'.$template);
    else
        include_once bp_portfolio_pro()->templates_dir.'/'.$template;
}

function bpcp_pro_buffer_template_part( $template, $echo=true ){
    ob_start();

    bpcp_pro_load_template( $template );
    // Get the output buffer contents
    $output = ob_get_clean();

    // Echo or return the output buffer contents
    if ( true === $echo ) {
        echo $output;
    } else {
        return $output;
    }
}

/**
 * Where is the add project form location?
 * is it under user profile > projects  or is it on a wordpress page?
 * Check settings for details.
 *
 * @return string 'bp'-> under user profile. 'wp'-> on a wordpress page.
 */
function bpcp_pro_add_wip_location(){
    $location = 'bp';
    if( (int)bp_portfolio_pro()->option( 'add-wip-page' ) ){
        $location = 'wp';
    }

    return $location;
}

function bpcp_pro_is_add_wip_page(){
    $is_our_page = false;
    if( (int)bp_portfolio_pro()->option( 'add-wip-page' ) ){
        if( is_page( bp_portfolio_pro()->option( 'add-wip-page' ) ) ){
            $is_our_page = true;
        }
    }

    return $is_our_page;
}

function bpcp_pro_is_add_wip_routine(){
    $flag = bpcp_pro_is_add_wip_page();
    if( !$flag ){
        if( bpcp_is_portfolio_component() && bp_is_current_action( bpcp_pro_subnav_wip_slug() ) ){
            $flag = true;
        }
    }

    return $flag;
}

function bpcp_pro_wip_add_link(){
    $base_link = bpcp_pro_wip_root();
    if( bpcp_pro_add_wip_location()=='wp' ){
        $base_link = get_permalink( bp_portfolio_pro()->option( 'add-wip-page' ) );
    }
    return add_query_arg( array( 'bpcp_action' => 'add_wip' ), $base_link);
}

/**
 * Get edit wip link
 * @param $wip_id
 * @param int $current_wip_id
 * @return string
 */
function bpcp_pro_wip_edit_link( $wip_id, $current_wip_id = 0 ){
    $target = 'add_wip';
    $base_link = bpcp_pro_wip_root();
    if( bpcp_pro_add_wip_location()=='wp' ){
        $base_link = get_permalink( bp_portfolio_pro()->option( 'add-wip-page' ) );
    }

    if ( $wip_id == $current_wip_id ) {
        $target_url = add_query_arg(array('bpcp_action' => $target, 'current_wip_id' => $wip_id), $base_link);
    } else {
        $query_args = array( 'bpcp_action' => $target, 'wip_id' => $wip_id );
        if ( $current_wip_id ) { $query_args['current_wip_id'] = $current_wip_id; }
        $target_url = add_query_arg( $query_args, $base_link );
    }

    return $target_url;
}

/**
 * Get add revision link
 * @param $wip_id
 * @return string
 */
function bpcp_pro_add_revision_link( $wip_id ) {
    $target = 'add_wip';
    $base_link = bpcp_pro_wip_root();
    if( bpcp_pro_add_wip_location()=='wp' ){
        $base_link = get_permalink( bp_portfolio_pro()->option( 'add-wip-page' ) );
    }
    $target_url = add_query_arg(array('bpcp_action' => $target, 'wip_id' => $wip_id), $base_link);
    return $target_url;
}

function bpcp_valid_wip_ID(){
    $wip_ID = '';
    if( isset($_GET['wip_id']) && !empty($_GET['wip_id']) ){
        $wip_detail = get_post($_GET['wip_id']);
        if( isset($wip_detail->post_type) && $wip_detail->post_type == 'bb_wip' ){
            $wip_ID = $wip_detail->ID;
        }
    }
    return $wip_ID;
}

function bpcp_pro_add_wip_title() {
    if (  !empty( $_GET['current_wip_id'] ) && !empty($_GET['wip_id'] ) ) {
        $add_wip_title = __( 'Edit Revision', 'bp-portfolio-pro' );
    } elseif( !empty($_GET['current_wip_id'] ) ) {
        $add_wip_title = __( 'Edit Work in Progress', 'bp-portfolio-pro' );
    } elseif( !empty($_GET['wip_id'] ) ) {
        $add_wip_title = __( 'Add Revision', 'bp-portfolio-pro' );
    } else{
        $add_wip_title = __( 'Add Work in Progress', 'bp-portfolio-pro' );
    }
    return $add_wip_title;
}

function bpcp_pro_add_wip_button_title(){
    if ( !empty( $_GET['current_wip_id'] ) && !empty($_GET['wip_id'] ) ) {
        $add_wip_button_title = __( 'Update Revision and Continue', 'bp-portfolio-pro' );
    } elseif( !empty($_GET['current_wip_id'] ) ) {
        $add_wip_button_title = __( 'Update WIP and Continue', 'bp-portfolio-pro' );
    } elseif( !empty($_GET['wip_id'] ) ) {
        $add_wip_button_title = __( 'Add Revision and Continue', 'bp-portfolio-pro' );
    }else{
        $add_wip_button_title = __( 'Create WIP and Continue', 'bp-portfolio-pro' );
    }
    return $add_wip_button_title;
}

function bpcp_pro_add_wip_subtitle(){
    $sub_wip_title = '';
    $wip_ID = bpcp_valid_wip_ID();
    $entry_status = get_post_meta($wip_ID, 'entry_status', true);
    if($entry_status == 'completed'){
        $sub_wip_title = __( 'Add a revision of this Work in Progress', 'bp-portfolio-pro' );
    }else{
        $sub_wip_title = __( 'Show what you\'re working on.', 'bp-portfolio-pro' );
    }
    return $sub_wip_title;
}

function bpcp_pro_wip_step_url($target, $wip_id='', $attach_id='')
{
    if ( !bpcp_is_accessible() ) return;
    $wip_id = empty($wip_id) && isset($_GET['wip_id']) ? $_GET['wip_id'] : $wip_id;
    $current_wip_id = !empty( $_GET['current_wip_id'] ) ? $_GET['current_wip_id'] : '';
    $base_link = bpcp_pro_wip_root();
    if( bpcp_pro_add_wip_location()=='wp' ){
        $base_link = get_permalink( bp_portfolio_pro()->option( 'add-wip-page' ) );
    }
    $query_args = array('bpcp_action' => $target);
    if(!empty($wip_id)){
        $query_args['wip_id'] = $wip_id;
    }
    if(!empty($attach_id)){
        $query_args['attach_id'] = $attach_id;
    }
    if(!empty($current_wip_id)){
        $query_args['current_wip_id'] = $current_wip_id;
    }
    $target_url = add_query_arg($query_args, $base_link);
    return $target_url;
}


function bpcp_pro_comment_add($post_id, $content){
    if( empty($post_id) || empty($content) ) return;

    // insert comment
    $time = current_time('mysql');
    $userID = bp_loggedin_user_id();
    $user_link = bp_core_get_userlink($userID);

    $data = array(
        'comment_post_ID' => $post_id,
        'comment_author' => get_the_author_meta( 'display_name', $userID ),
        'comment_author_email' => get_the_author_meta( 'user_email', $userID ),
        'comment_author_url' => $user_link,
        'comment_content' => $content,
        'user_id' => $userID,
        'comment_author_IP' => bpcp_pro_get_user_ip(),
        'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
        'comment_date' => $time,
        'comment_approved' => 1,
    );

    $comment_id = wp_insert_comment($data);
    return $comment_id;
}


function bpcp_pro_get_user_ip() {
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
//check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
//to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return apply_filters( 'bpcp_pro_get_user_ip', $ip );
}


/**
 * Get all/one attachment image url array of a post
 * @param int $post_ID
 * @param string /array $thumb_size
 * @param string $num
 * @return array
 */
function bpcp_pro_latest_attachment($post_ID, $thumb_size)
{
    if (empty($post_ID)) {
        return array();
    }

    $args = array(
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'post_type' => 'attachment',
        'post_parent' => $post_ID,
        'post_mime_type' => 'image',
        'post_status' => null,
        'numberposts' => -1,
    );
    $attachments = get_posts($args);

    $image_list = array();

    if ($attachments) {
        $i = 0;
        foreach ($attachments as $attachment) {
            $image_attributes_detail = wp_get_attachment_image_src($attachment->ID, $thumb_size);
            $image_list[$i]['full_src'] = wp_get_attachment_url( $attachment->ID );
            $image_list[$i]['src'] = $image_attributes_detail[0];
            $image_list[$i]['ID'] = $attachment->ID;
            $image_list[$i]['title'] = $attachment->post_title;
            $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            $image_list[$i]['alt'] = !empty($alt_text) ? $alt_text : $image_list[$i]['title'];
            $image_list[$i]['caption'] = $attachment->post_excerpt;
            $image_list[$i]['description'] = $attachment->post_content;
            $i++;
        }
    }

    $last_index = count($image_list) - 1;
    return !empty($image_list[$last_index]['src']) ? $image_list[$last_index] : array();
}

/**get latest child/revision detail
 * @param $post_id
 */
function bpcp_pro_current_revision($post_id){
    $latest_child = get_post($post_id);
    $args = array(
        'post_type' => 'bb_wip',
        'posts_per_page' => -1,
        'post_parent' => $post_id
    );
    $childs = get_posts($args);
    if(!empty($childs[0])){
        $latest_child = $childs[0];
    }
    return $latest_child;
}

function bpcp_pro_current_revision_num($post_id){
    $revision_num = 1;
    $args = array(
        'post_type' => 'bb_wip',
        'posts_per_page' => -1,
        'post_parent' => $post_id
    );
    $childs = get_posts($args);
    if(!empty($childs)){
        $revision_num = count($childs)+1;
    }
    return $revision_num;
}

function bpcp_pro_revisions_thumbs($post_id, $size = 'revision-thumbnail'){
    $child_thumbs = array();
    // including parent
    $parent_thumb_detail = bpcp_pro_get_attachment_detail($post_id, $size);
    $child_thumbs[] = array(
        'url' => isset( $parent_thumb_detail['src'] ) ? $parent_thumb_detail['src'] : '',
        'revision_id' => $post_id,
    );
    $args = array(
        'post_type' => 'bb_wip',
        'posts_per_page' => -1,
        'post_parent' => $post_id,
        'order' => 'ASC',
    );
    $childs = get_posts($args);
    if(!empty($childs)){
        foreach($childs as $child){
            $thumb_detail = bpcp_pro_get_attachment_detail($child->ID, $size);

                $child_thumbs[] = array(
                    'url' => isset( $thumb_detail['src'] ) ? $thumb_detail['src'] : get_template_directory_uri() . '/images/no-image-wip.png',
                    'revision_id' => $child->ID,
                );

        }
    }
    return $child_thumbs;
}

function bpcp_pro_wip_visibility_filter( $where, &$wp_query )
{   if(bp_loggedin_user_id()){
    global $wpdb;
    $where .= ' OR ( ( ' . $wpdb->posts . '.post_author = ' . bp_loggedin_user_id() . ' ) ';
    $where .= ' AND  ( ' . $wpdb->posts . '.post_type   = "' . $wp_query->query['post_type'] . '" ) ';
    $where .= ' AND  ( ' . $wpdb->posts . '.post_parent = 0 ) ';
    $where .= ' AND  ( ' . $wpdb->posts . '.post_status = "publish" ) ) ';
}
    return $where;
}

function bpcp_pro_global_page_top_menu($selected_item){

    $menu_html = '';

    if( $selected_item == 'projects' ){
        $menu_html .= bpcp_pro_projects_order_filter_items();
    }
    if( $selected_item == 'wip' ){
        $menu_html .= bpcp_pro_wip_order_filter_items();
    }

    $menu_html .= '<div class="bp-project-type">';
    $menu_html .= '<select id="work-type" class="bp-work-filter">';

    $portfolio_selected = $selected_item == 'projects' ? 'selected="selected"' : '';
    $wip_selected = $selected_item == 'wip' ? 'selected="selected"' : '';
    $collections_selected = $selected_item == 'collections' ? 'selected="selected"' : '';

    $projects_enable = bp_portfolio()->setting( 'bpcp-projects-enable' );
    $wip_enable = bp_portfolio_pro()->setting( 'bpcp-wip-enable' );
    $collections_enable = bp_portfolio_pro()->setting( 'bpcp-collections-enable' );

    $global_portfolio = bp_portfolio()->option('all-portfolio-page');
    $global_wip = bp_portfolio_pro()->option('all-wip-page');
    $global_collections = bp_portfolio_pro()->option('all-collections-page');

    if( $projects_enable == 'on' && isset($global_portfolio) && !empty($global_portfolio) ){
        $global_portfolio_permalink = trailingslashit( get_permalink( bp_portfolio()->option('all-portfolio-page') ) );
        $menu_html .= '<option '.$portfolio_selected.' id="projects-all" value="'.esc_url($global_portfolio_permalink).'">'.__( 'Projects', 'bp-portfolio-pro' ).'</option>';
    }
    if( $wip_enable == 'on' && isset($global_wip) && !empty($global_wip) ){
        $global_wip_permalink = trailingslashit( get_permalink( bp_portfolio_pro()->option('all-wip-page') ) );
        $menu_html .= '<option '.$wip_selected.' id="wip-all" value="'.esc_url($global_wip_permalink).'">'.__( 'WIP', 'bp-portfolio-pro' ).'</option>';
    }
    if( $collections_enable == 'on' && isset($global_collections) && !empty($global_collections) ){
        $global_collections_permalink = trailingslashit( get_permalink( bp_portfolio_pro()->option('all-collections-page') ) );
        $menu_html .= '<option '.$collections_selected.' id="collections-all" value="'.esc_url($global_collections_permalink).'">'.__( 'Collections', 'bp-portfolio-pro' ).'</option>';
    }

    $menu_html .= '</select>';
    $menu_html .= '</div>';

    if( !in_array( $selected_item, array( 'projects', 'wip' ) ) ){
        return $menu_html;
    }

    $subnav_options = array(
        'all'           => __( 'All Time', 'bp-portfolio-pro' ),
        'past_year'     => __( 'This Past Year', 'bp-portfolio-pro' ),
        'past_month'    => __( 'This past month', 'bp-portfolio-pro' ),
        'past_week'     => __( 'This past week', 'bp-portfolio-pro' ),
        'today'         => __( 'Today', 'bp-portfolio-pro' ),
    );

    $selected = isset( $_GET['bb_filter'] ) ? $_GET['bb_filter'] : 'all';
    $is_valid = false;
    foreach( $subnav_options as $key=>$label ){
        if( $key==$selected ){
            $is_valid = true;
            break;
        }
    }
    if( !$is_valid ){
        $selected = 'all';
    }

    $page_url = get_permalink();
    if( isset($_GET['bb_orderby']) && !empty( $_GET['bb_orderby']) ){
        $page_url = add_query_arg( array( 'bb_orderby' => $_GET['bb_orderby'] ), $page_url );
    }
    if( isset($_GET['bb_category']) && !empty( $_GET['bb_category']) ){
        $page_url = add_query_arg( array( 'bb_category' => $_GET['bb_category'] ), $page_url );
    }

    $menu_html .= '<div class="bp-project-time">';
    $menu_html .= '<select id="time-filter" class="bp-work-filter">';
    foreach( $subnav_options as $key=>$label ){
        $selected_class = $selected == $key ? 'selected="selected"' : '';
        $url = add_query_arg( array( 'bb_filter' => $key ), $page_url );
        $menu_html .= "<option ".$selected_class." value='{$url}'>{$label}</option>";
    }
    $menu_html .= '</select>';
    $menu_html .= '</div>';

    if( $selected_item == 'projects' || $selected_item == 'wip' ){
        $menu_html .= '<form method="GET" action="'. get_permalink() .'">';
        $menu_html .= '<input type="hidden" name="bb_filter" value="'. esc_attr( @$_GET['bb_filter'] ) .'">';
        $menu_html .= '<input type="hidden" name="bb_orderby" value="'. esc_attr( @$_GET['bb_orderby'] ) .'">';

        $category_label = bpcp_project_category_label();
        if( $selected_item == 'wip' ){
            $category_label = bpcp_pro_wip_category_label();
        }

        $taxonomy = 'bb_project_category';
        if( $selected_item == 'wip' ){
            $taxonomy = 'bb_wip_category';
        }

        $menu_html .= wp_dropdown_categories( array(
            'echo'              => 0,
            'taxonomy'          => $taxonomy,
            'hide_empty'        => 1,
            'show_option_none'  => __( 'Categories', 'bp-portfolio-pro' ),
            'option_none_value' => '',
            'hierarchical'      => 1,
            'orderby'           => 'name',
            'name'              => 'bb_category',
            'class'             => 'auto_submit_pform',
            'selected'          => isset( $_GET['bb_category'] ) ? $_GET['bb_category'] : '',
        ) );
        $menu_html .= '</form>';
    }


    return $menu_html;
}

function bpcp_pro_portfolio_subcomponents(){
    $portfolio_subcomponents = array(
        'wip' => array(
            'name' => bpcp_pro_subnav_wip_name(),
            'slug' => bpcp_pro_subnav_wip_slug(),
            'root' => bpcp_pro_wip_root(),
            'screen' => 'bpcp_pro_portfolio_wip_screen',
            'enable_status' => bp_portfolio_pro()->setting( 'bpcp-wip-enable' )
        ),
        'collections' => array(
            'name' => bpcp_pro_subnav_collections_name(),
            'slug' => bpcp_pro_subnav_collections_slug(),
            'root' => bpcp_pro_collections_root(),
            'screen' => 'bpcp_pro_portfolio_collections_screen',
            'enable_status' => bp_portfolio_pro()->setting( 'bpcp-collections-enable' )
        ),
    );
    return $portfolio_subcomponents;
}

function bpcp_pro_collections_subnavs(){
    $subnav_html = '<ul>';
    $index_selected = !isset($_GET['bpcp_action']) || empty($_GET['bpcp_action']) || $_GET['bpcp_action'] != 'collections_following' ? 'current selected' : '';
    $following_selected = isset($_GET['bpcp_action']) && $_GET['bpcp_action'] == 'collections_following' ? 'current selected' : '';
    $subnav_html .= '<li class="'.$index_selected.'"><a href="?bpcp_action=collections_index">'.__( 'Collections', 'bp-portfolio-pro' ).'</a></li>';
    $subnav_html .= '<li class="'.$following_selected.'"><a href="?bpcp_action=collections_following">'.__( 'Following', 'bp-portfolio-pro' ).'</a></li>';
    $subnav_html .= '</ul>';
    return $subnav_html;
}

function bpcp_pro_collection_following_filter( $where, &$wp_query ){
    global $wpdb;
    //$where .= ' AND  ( ' . $wpdb->posts . '.post_status = "publish" ) ';
    $where .= ' AND ( ( ' . $wpdb->prefix. 'postmeta.meta_key = "followed_users" AND CAST(' . $wpdb->prefix. 'postmeta.meta_value AS CHAR)  LIKE "%'.bp_displayed_user_id().'%" ) ) ';
    print_r($wp_query); exit;
    return $where;
}

function bpcp_pro_collection_follower_check($collection_id, $user_id=''){
    global $wpdb;
    if(!empty($user_id)){
        $condition = "collection_id = $collection_id AND meta_key = 'followed_user' AND meta_value = $user_id";
    }else{
        $condition = "collection_id = $collection_id AND meta_key = 'followed_user' ";
    }
    $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."bpcp_collection_meta WHERE $condition" );
    return $count;
}


function bpcp_pro_following_collection_ids($user_id){
    global $wpdb;
    $condition = "meta_key = 'followed_user' AND meta_value = $user_id";
    $get_cols = $wpdb->get_col( "SELECT collection_id FROM ".$wpdb->prefix."bpcp_collection_meta WHERE $condition" );
    return $get_cols;
}

function bpcp_pro_most_followed_collection_ids(){
    global $wpdb;
    $condition = "meta_key = 'followed_user' ";
    $group_by = "GROUP BY collection_id";
    $oder_by = "ORDER BY follower_count DESC";
    $get_cols = $wpdb->get_col( "SELECT collection_id, count(*) AS follower_count FROM ".$wpdb->prefix."bpcp_collection_meta WHERE $condition $group_by $oder_by" );
    return $get_cols;
}

function bpcp_pro_like_status($post_id, $user_id){
    global $wpdb;
    $condition = "post_id = $post_id AND user_id = $user_id";
    $get_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."bpcp_components_like WHERE $condition" );
    return $get_count;
}

function bpcp_pro_like_count($post_id, $post_type){
    global $wpdb;
    $condition = "post_id = $post_id AND post_type = '$post_type'";
    $get_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."bpcp_components_like WHERE $condition" );
    return $get_count;
}

function bpcp_pro_modified_time_update($post_id){
    $update_post = array(
        'ID'           => $post_id,
        'post_modified' => date("Y-m-d H:i:s"),
        'post_modified_gmt' => date("Y-m-d H:i:s")
    );
    wp_update_post($update_post);
}

function bpcp_pro_project_sort_update($project_id, $data){
    if ( !is_array( $data ) || empty($project_id) ) return false;
    // now reordering
    $attach_arr = array();
    foreach( $data as $key => $values ) {
        foreach( $values as $position => $id ) {
            $attach_arr[] = $id;
        }
    }
    update_post_meta($project_id, 'bpcp_sorted_items',$attach_arr);
}

function bpcp_pro_get_video_by_item_id($attachment, $project_id){
    $get_key = str_replace('video', '', $attachment);
    $embeded_urls = get_post_meta($project_id, 'embeded_urls', true);
    $val = $embeded_urls[$get_key];
    return $val;
}

function bpcp_pro_add_item_in_meta_array($project_id, $meta_key, $item){
    if( empty($project_id) || empty($meta_key) || empty($item) ) return;
    $meta_values = get_post_meta($project_id, $meta_key, true);
    if( !in_array($item, $meta_values) ){
        $meta_values[] = $item;
        update_post_meta($project_id, $meta_key, $meta_values);
    }
}

function bpcp_pro_remove_item_in_meta_array($project_id, $meta_key, $item){
    if( empty($project_id) || empty($meta_key) || empty($item) ) return;
    $meta_values = get_post_meta($project_id, $meta_key, true);
    if( is_array($meta_values) && ($key = array_search($item, $meta_values)) !== false ) {
        unset($meta_values[$key]);
        $meta_values = array_values($meta_values);
        update_post_meta($project_id, $meta_key, $meta_values);
    }
}

function bpcp_pro_get_project_items($project_id){
    $attach_items = array();
    $all_attachments = bpcp_pro_get_attachment_detail($project_id, 'medium', 'all');
    if(isset($all_attachments) && !empty($all_attachments)){
        foreach($all_attachments as $single){
            $attach_items[] = $single['ID'];
        }
    }
    $embeded_urls = get_post_meta($project_id, 'embeded_urls', true);
    $i = 0;
    if(isset($embeded_urls) && !empty($embeded_urls)){
        foreach($embeded_urls as $single){
            $attach_items[] = 'video'.$i;
            $i++;
        }
    }

    $sorted_attachments = get_post_meta($project_id, 'bpcp_sorted_items', true);
    if( is_array($sorted_attachments) && (count($attach_items) == count($sorted_attachments)) ){
        return $sorted_attachments;
    }else{
        bpcp_pro_project_sort_update($project_id, array('attach' => $attach_items) );
        return $attach_items;
    }
}

function bpcp_pro_comment_reform ($arg) {
    global $post;
    if(is_singular('bb_wip')) {
        $arg['title_reply'] = __('Discuss this Work in Progress', 'bp-portfolio-pro');
    }
    if(is_singular('bb_collection')) {
        $arg['title_reply'] = __('Discuss this Collection', 'bp-portfolio-pro');
    }

    return $arg;
}
add_filter('comment_form_defaults','bpcp_pro_comment_reform');


function bpcp_pro_get_comments_template($post_id){
    $comment_template = apply_filters( 'portfolio_comment_template', '/comments.php');
    // check if revision comment template exists
    $rev_comment_tpl = locate_template('comments-revision.php');
    // set revision comment
    $rev_comment = !empty($rev_comment_tpl) ? '/comments-revision.php' : $comment_template;
    // check if parent exists
    $post_parent = wp_get_post_parent_id( $post_id );
    // set comment template
    $comment_template = $post_parent ? $rev_comment : $comment_template;
    // now return
    return $comment_template;
}

function bpcp_pro_projects_order_filter_items(){
    $filter_html = '';
    //$projects_filter_by = isset($_SESSION['projects_filter_by']) && !empty($_SESSION['projects_filter_by']) ? $_SESSION['projects_filter_by'] : 'recent';
    $projects_filter_by = isset($_GET['bb_orderby']) && !empty($_GET['bb_orderby']) ? $_GET['bb_orderby'] : 'recent';
    $recent_selected = $projects_filter_by == "recent" ? "selected" : "" ;
    $commented_selected = $projects_filter_by == "most_commented" ? "selected" : "";
    $popular_selected = $projects_filter_by == "most_popular" ? "selected" : "";
    $viewed_selected = $projects_filter_by == "most_viewed" ? "selected" : "";
    $filter_html .= '<form method="GET" action="">';
    $filter_html .= '<input type="hidden" name="bb_filter" value="'. esc_attr( @$_GET['bb_filter'] ) .'">';
    $filter_html .= '<input type="hidden" name="bb_category" value="'. esc_attr( @$_GET['bb_category'] ) .'">';
    $filter_html .= '<select id="projects-order-by" name="bb_orderby" class="auto_submit_pform">';
    $filter_html .= '<option value="recent" ' .$recent_selected. '>' . __('Recent', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '<option value="most_commented" ' . $commented_selected . '>' . __('Most Commented', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '<option value="most_popular" ' . $popular_selected . '>' . __('Most Popular', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '<option value="most_viewed" ' . $viewed_selected . '>' . __('Most Viewed', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '</select></form>';

    return $filter_html;
}

function bpcp_pro_wip_order_filter_items(){
    $filter_html = '';
    //$projects_filter_by = isset($_SESSION['projects_filter_by']) && !empty($_SESSION['projects_filter_by']) ? $_SESSION['projects_filter_by'] : 'recent';
    $projects_filter_by = isset($_GET['bb_orderby']) && !empty($_GET['bb_orderby']) ? $_GET['bb_orderby'] : 'recent';
    $recent_selected = $projects_filter_by == "recent" ? "selected" : "" ;
    $commented_selected = $projects_filter_by == "most_commented" ? "selected" : "";
    $popular_selected = $projects_filter_by == "most_popular" ? "selected" : "";
    $viewed_selected = $projects_filter_by == "most_viewed" ? "selected" : "";
    $filter_html .= '<form method="GET" action="">';
    $filter_html .= '<input type="hidden" name="bb_filter" value="'. esc_attr( @$_GET['bb_filter'] ) .'">';
    $filter_html .= '<input type="hidden" name="bb_category" value="'. esc_attr( @$_GET['bb_category'] ) .'">';
    $filter_html .= '<select id="wip-order-by" name="bb_orderby" class="auto_submit_pform">';
    $filter_html .= '<option value="recent" ' .$recent_selected. '>' . __('Recent', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '<option value="most_commented" ' . $commented_selected . '>' . __('Most Commented', 'bp-portfolio-pro'). '</option>';
	$filter_html .= '<option value="most_popular" ' . $popular_selected . '>' . __('Most Popular', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '<option value="most_viewed" ' . $viewed_selected . '>' . __('Most Viewed', 'bp-portfolio-pro'). '</option>';
    $filter_html .= '</select></form>';
    return $filter_html;
}

function bpcp_pro_like_count_text(){
    $like_count_text = '';
    $like_count = bpcp_pro_like_count(get_the_ID(), 'bb_project');
    if($like_count){
        $like_count_text = sprintf( _n( '%s <span>Appreciation</span>', '%s <span>Appreciations</span>', $like_count, 'bp-portfolio-pro' ), $like_count );
    }
    return $like_count_text;
}

function bpcp_wip_like_count_text($post_id){
    $like_count_text = '';
    $like_count = bpcp_pro_like_count($post_id, 'bb_wip');
    if($like_count){
        $like_count_text = sprintf( _n( '%s <span>Appreciation</span>', '%s <span>Appreciations</span>', $like_count, 'bp-portfolio-pro' ), $like_count );
    }
    return $like_count_text;
}

function bpcp_pro_view_count_text() {
    $view_count_text = '';
    $view_count = bpcp_pro_the_views(false);
    if($view_count) {
        $view_count_text = sprintf( _n( '%s <span>View</span>', '%s <span>Views</span>', $view_count, 'bp-portfolio-pro' ), $view_count );
    }
    return $view_count_text;
}

function bpcp_pro_wip_category_label(){
    $catg_label = bp_portfolio()->setting( 'wip-category-label' );
    if( !$catg_label ){
        $catg_label = __( 'Category', 'bp-portfolio-pro' );
    }
    return $catg_label;
}

if ( !function_exists( 'pro_project_loop' ) ) {
    function pro_project_loop( $atts ){
        // usage: [pro_project_loop posts_per_page=20 show_filters=true default_order='date'/]
        $a = shortcode_atts( array(
            'posts_per_page' => 20,
            'show_filters' => true,
            'default_order' => 'date'
        ), $atts );
       ?>

       <div id="buddypress">

        <?php if($a['show_filters']): ?>
        <div class="bp-project-filters">
            <?php echo bpcp_pro_global_page_top_menu('projects'); ?>
        </div>
        <?php endif; ?>

        <div class="activity">

            <div class="bpcp-grid-wrapper">
                <?php
                $projects_filter_by = isset( $_GET['bb_orderby'] ) && !empty( $_GET['bb_orderby'] ) ? $_GET['bb_orderby'] : 'recent';
                switch($projects_filter_by){
                    case 'recent':          $order_by = 'date'; break;
                    case 'most_commented':  $order_by = 'comment_count'; break;
                    case 'most_viewed':     $order_by = 'bpcp_pro_views'; break;
                    case 'most_popular':    $order_by = 'like'; break;
                    default:                $order_by = $a['default_order'];
                }
                $paged = bpcp_paged_query_var();
                $meta_query = bpcp_project_meta_args('project_visibility', true);
                if( $order_by != 'like' ){
                    $gp_args = array(
                        'post_type' => 'bb_project',
                        'meta_query' => array($meta_query),
                        'orderby' => $order_by,
                        'order' => 'DESC',
                        'posts_per_page' => $a['posts_per_page'],
                        'paged' => $paged
                    );

                    if( 'bpcp_pro_views'==$order_by ){
                        $gp_args['orderby'] = 'meta_value_num';
                        $gp_args['meta_key'] = $order_by;
                    }

                    $gp_args = bpcp_add_date_query_args( $gp_args );
                    $gp_args = bpcp_add_category_query_args( $gp_args );

                    //add_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
                    $the_query = new WP_Query($gp_args);
                    //remove_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
                } else {
                    $gp_args = array(
                        'post_type' => 'bb_project',
                        'meta_query' => array($meta_query),
                        'posts_per_page' => 20,
                        'paged' => $paged
                    );

                    $gp_args = bpcp_add_date_query_args( $gp_args );
                    $gp_args = bpcp_add_category_query_args( $gp_args );

                    add_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
                    //add_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
                    $the_query = new WP_Query($gp_args);
                    remove_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
                    //remove_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
                }
                $views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );

                if ($the_query->have_posts()): ?>
                <ul id="portfolio-activity" class="portfolio-list">
                    <?php while ($the_query->have_posts()):
                        $the_query->the_post();
                        $default_img_url = BP_PORTFOLIO_PLUGIN_URL.'assets/images/project-placeholder.png';
                        $get_project_thumb = bpcp_featured_image_detail(get_the_ID(), 'portfolio-thumbnail', $default_img_url);
                        $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
                        $project_visibility = get_post_meta(get_the_ID(), 'project_visibility', true);
                        $like_count_text = $like_count_text = bpcp_pro_like_count_text();
                        if($views_count_enabled == 'on') {
                            $view_count_text = bpcp_pro_view_count_text();
                        }
                        ?>
                        <li class="bpcp-grid-item">
                            <div class="bp-inner-wrap">
                                <div class="bb-project-thumb">
                                    <a href="<?php the_permalink();?>"><img src="<?php echo $project_thumb_url;?>" alt="<?php the_title();?>" /></a>
                                </div>
                                <div class="bb-project-title"><h4><a href="<?php the_permalink();?>"><?php the_title();?></a></h4></div>
                                <div class="bb-project-meta">
                                    <?php if(!empty($like_count_text)) { ?><div class="bb-project-like"> <?php echo $like_count_text;?></div><?php } ?>
                                    <?php if(!empty($view_count_text)){ ?><div class="bb-project-views-count"><?php echo $view_count_text;?></div><?php } ?>
                                </div>
                                <div class="bb-project-author"><?php _e( 'by', 'bp-portfolio-pro' );?> <a href="<?php echo bp_core_get_user_domain(get_the_author_meta('ID'));?>"><?php the_author();?></a></div>
                            </div>
                        </li>
                    <?php endwhile;?>
                </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php
    // pagination code
    bpcp_num_pagination($the_query);

    // Restore original Query & Post Data
    wp_reset_query();
    wp_reset_postdata();

    }
}
add_shortcode( 'pro_project_loop', 'pro_project_loop' );

if ( !function_exists( 'pro_wip_loop' ) ) {
function pro_wip_loop( $atts ){
    // usage: [pro_wip_loop posts_per_page=20 show_filters=true default_order='date'/]
    $a = shortcode_atts( array(
        'posts_per_page' => 5,
        'show_filters' => true,
        'default_order' => 'date'
    ), $atts );
   ?>
<div id="buddypress">

    <?php if($a['show_filters']): ?>
    <div class="bp-project-filters">
       <?php echo bpcp_pro_global_page_top_menu('wip'); ?>
    </div>
    <?php endif; ?>

    <div class="activity">

        <div class="bpcp-grid-wrapper">
            <?php
            $projects_filter_by = isset( $_GET['bb_orderby'] ) && !empty( $_GET['bb_orderby'] ) ? $_GET['bb_orderby'] : 'recent';
            switch($projects_filter_by){
                case 'recent':          $order_by = 'date'; break;
                case 'most_commented':  $order_by = 'comment_count'; break;
                case 'most_viewed':     $order_by = 'bpcp_pro_views'; break;
                case 'most_popular':    $order_by = 'like'; break;
                default:                $order_by = $a['default_order'];
            };
            $paged = bpcp_paged_query_var();
            $meta_query = bpcp_project_meta_args('wip_visibility', true);
            $gwip_args = array(
                'post_type' => 'bb_wip',
                'meta_query' => array($meta_query),
                'posts_per_page' => $a['posts_per_page'],
                'orderby' => $order_by,
                'order' => 'DESC',
                'post_parent' => 0,
                'paged' => $paged
            );

            if( 'bpcp_pro_views'==$order_by ){
                $gwip_args['orderby'] = 'meta_value_num';
                $gwip_args['meta_key'] = $order_by;
            }

            $gwip_args = bpcp_add_date_query_args( $gwip_args );
            $gwip_args = bpcp_add_category_query_args( $gwip_args );

            //add_filter( 'posts_where', 'bpcp_pro_wip_visibility_filter', 10, 2 );
            $the_query = new WP_Query($gwip_args);
            //remove_filter( 'posts_where', 'bpcp_pro_wip_visibility_filter', 10, 2 );
            $views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
            if ( $the_query->have_posts() ): ?>
            <ul id="portfolio-activity" class="portfolio-list">
                <?php while ($the_query->have_posts()):
                    $the_query->the_post();
                    $default_img_url = BP_PORTFOLIO_PLUGIN_URL.'assets/images/project-placeholder.png';
                    $current_revision = bpcp_pro_current_revision(get_the_ID());
                    $post_id = !empty($current_revision->ID) ? $current_revision->ID : get_the_ID();
                    $author_id = !empty($current_revision->post_author) ? $current_revision->post_author : get_the_author_meta('ID');
                    $get_wip_thumb = bpcp_pro_latest_attachment($post_id, 'portfolio-thumbnail');
                    $wip_thumb_url = ( isset($get_wip_thumb['src']) && !empty($get_wip_thumb['src']) ) ? $get_wip_thumb['src'] : $default_img_url;
                    $current_revision_num = bpcp_pro_current_revision_num(get_the_ID());
                    $like_count_text = bpcp_wip_like_count_text($post_id);
                    if($views_count_enabled == 'on') {
                        $view_count_text = bpcp_pro_view_count_text();
                    }
                    ?>
                    <li class="bpcp-grid-item">
                        <div class="bp-inner-wrap">
                            <div class="bb-project-thumb">
                                <a href="<?php echo get_permalink($post_id);?>"><img src="<?php echo $wip_thumb_url;?>" alt="<?php echo get_the_title($post_id);?>" /></a>
                            </div>
                            <div class="bb-project-title"><h4><a href="<?php echo get_permalink($post_id);?>"><?php echo get_the_title($post_id);?></a></h4></div>
                            <div class="bb-project-meta">
                                <?php if(!empty($like_count_text)) { ?><div class="bb-wip-like"> <?php echo $like_count_text;?></div><?php } ?>
                                <?php if(!empty($view_count_text)){ ?><div class="bb-wip-views-count"><?php echo $view_count_text;?></div><?php } ?>
                            </div>
                            <div class="bb-project-author"><?php _e( 'by', 'bp-portfolio-pro' );?> <a href="<?php echo bp_core_get_user_domain($author_id);?>"><?php echo get_the_author_meta('user_login', $author_id);?></a></div>
                            <div class="bb-project-revision">Revision <?php echo $current_revision_num; ?></div>
                        </div>
                    </li>
                <?php endwhile;?>
            </ul>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php
// pagination code
bpcp_num_pagination($the_query);

// Restore original Query & Post Data
wp_reset_query();
wp_reset_postdata();

}
}
add_shortcode( 'pro_wip_loop', 'pro_wip_loop' );

if ( !function_exists( 'pro_collections_loop' ) ) {
    function pro_collections_loop( $atts ){
        // usage: [pro_collections_loop posts_per_page=20 show_filters=true/]
        $a = shortcode_atts( array(
            'posts_per_page' => 20,
            'show_filters' => true
        ), $atts );
       ?>
    <div id="buddypress">

        <?php if($a['show_filters']): ?>
        <div class="bp-project-filters">
            <?php echo bpcp_pro_global_page_top_menu('collections'); ?>
        </div>
        <?php endif; ?>

        <div class="activity">

            <div class="bpcp-grid-wrapper">

                <?php
                $user_id = bp_loggedin_user_id();
                $meta_query = bpcp_project_meta_args('collection_visibility');
                $paged = bpcp_paged_query_var();
                $pp_args = array(
                    'post_type' => 'bb_collection',
                    'meta_query' => array($meta_query),
                    'posts_per_page' => $a['posts_per_page'],
                    'paged' => $paged
                );
                $the_query = new WP_Query($pp_args);
                if ($the_query->have_posts()): ?>
                    <div class="bb-display-projects collection-projects">
                        <?php while ($the_query->have_posts()):
                            $the_query->the_post();
                            $default_img_url = 'http://placehold.it/150x150';
                            $project_ids = get_post_meta(get_the_ID(), 'project_ids', true);
                            if( is_array($project_ids) && !empty($project_ids) ){
                                $project_ids = array_reverse($project_ids);
                            }
                            ?>

                            <div class="collection-row">
                                <div class="collection-items">
                                    <ul class="bb-project-items bb-collections-list">

                                        <?php for($index=0; $index<3; $index++): ?>
                                            <li class="bb-project-item">
                                                <?php
                                                $collection_thumb_url = $default_img_url;
                                                if( isset($project_ids[$index]) && !empty($project_ids[$index]) ){
                                                    $collection_thumb_url = bpcp_featured_image_detail($project_ids[$index], 'collection-thumbnail', $default_img_url);
                                                    $collection_thumb_url = !empty($collection_thumb_url['src']) ? $collection_thumb_url['src'] : $default_img_url;
                                                }
                                                ?>
                                                <img src="<?php echo $collection_thumb_url;?>" alt="<?php the_title();?>"/>
                                            </li>
                                        <?php endfor; ?>

                                    </ul>
                                </div>

                                <div class="collection-details">
                                    <div class="collection-author">
                                        <div class="bb-project-title"><h4><a href="<?php the_permalink();?>"><?php the_title();?></a></h4></div>
                                        <div class="bb-project-description"><?php the_content();?></div>
                                        <div class="bb-project-author"><?php _e( 'by', 'bp-portfolio-pro' );?> <a href="<?php echo bp_core_get_user_domain(get_the_author_meta('ID'));?>"><?php the_author();?></a></div>
                                    </div>

                                    <?php if( is_user_logged_in() && $user_id != get_the_author_meta('ID') ): ?>
                                    <div class="collection-delete-btn">
                                        <form name="user_collection_follow" action="" method="post">
                                            <input type="hidden" name="collection_id" value="<?php the_ID()?>" />
                                            <?php
                                            $user_following = bpcp_pro_collection_follower_check(get_the_ID(), $user_id);
                                            if($user_following < 1){
                                            ?>
                                                <input type="submit" name="follow_collection" value="<?php _e( 'follow', 'bp-portfolio-pro' ); ?>" />
                                            <?php } else { ?>
                                                <input type="submit" name="unfollow_collection" value="<?php _e( 'unfollow', 'bp-portfolio-pro' ); ?>" />
                                            <?php } ?>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

    <?php
    // pagination code
    bpcp_num_pagination($the_query);
    // Restore original Query & Post Data
    wp_reset_query();
    wp_reset_postdata();

    }
}
add_shortcode( 'pro_collections_loop', 'pro_collections_loop' );

/**
 * Format 'new_bb_wip' activity actions.
 *
 *
 * @param string $action   Activity action string.
 * @param object $activity Activity data.
 * @return string $action Formatted activity action.
 */
function bpcp_format_activity_action_new_wip( $action, $activity ) {

    $user_link  = bp_core_get_userlink( $activity->user_id );
    $wip_post   = get_post( $activity->secondary_item_id );

    if ( ! is_a( $wip_post, 'WP_Post' ) ) {
        return $action;
    }

    $post_link  = '<a href="'. get_permalink( $wip_post ).'">'. $wip_post->post_title . '</a>';

    // If post has a parent then it is a revision to a work in progress
    if ( 0 == $wip_post->post_parent )
        $action = sprintf( __( '%1$s uploaded a new work in progress, %2$s', 'bp-portfolio-pro' ), $user_link, $post_link );
     else
         $action = sprintf( __( '%1$s uploaded a revision to a work in progress, %2$s', 'bp-portfolio-pro' ), $user_link, $post_link );

    return apply_filters( 'bpcp_format_activity_action_new_wip', $action, $activity );
}

/**
 * Format activity action strings for wip comments.
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity data object.
 *
 * @return string
 */
function bpcp_activity_format_activity_action_wip_comment( $action, $activity ) {
    global $wpdb, $bp;

    //SQL statement for get the bb_wip post_id from comment's parnet activity
    $SQL            = "SELECT secondary_item_id FROM {$bp->activity->table_name} WHERE id = (SELECT secondary_item_id FROM {$bp->activity->table_name} WHERE id = %d) AND type = %s";
    $wip_post_id    = $wpdb->get_var( $wpdb->prepare( $SQL, $activity->id, 'new_bb_wip' ) );

    $wip_post       = get_post( $wip_post_id );

    if ( ! is_a( $wip_post, 'WP_Post' ) ) {
        return $action;
    }

    $post_link      = '<a href="'. get_permalink( $wip_post ).'">'. $wip_post->post_title . '</a>';
    $user_link      = bp_core_get_userlink( $activity->user_id );

    // If post has a parent then it is a revision to a work in progress
    if ( 0 == $wip_post->post_parent )
        $action = sprintf( _x( '%1$s commented on the work in progress, %2$s', 'Activity bb_wip Post Type post comment action', 'bp-portfolio-pro' ), $user_link, $post_link  );
    else
        $action = sprintf( _x( '%1$s commented on the revision to a work in progress, %2$s', 'Activity bb_wip Post Type post comment action', 'bp-portfolio-pro' ), $user_link, $post_link  );

    return apply_filters( 'bpcp_activity_format_activity_action_wip_comment', $action, $activity );
}
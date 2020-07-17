<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * wip screen definition
 */
function bpcp_pro_portfolio_wip_screen() {
	global $bp;

    $get_action = ( isset($_GET['bpcp_action']) && !empty($_GET['bpcp_action']) ) ? $_GET['bpcp_action'] : '';
    switch($get_action){
        case 'add_wip':
            add_action('wp_enqueue_scripts', 'bpcp_media_upload_scripts');
            add_action( 'bp_template_content', 'bpcp_pro_template_add_wip' );
            break;

        case 'wip_content':
            add_action( 'bp_template_content', 'bpcp_pro_template_wip_content' );
            break;

        default:
            add_action( 'bp_template_content', 'bpcp_pro_template_wip_index' );
            break;
    }

    bp_core_load_template( apply_filters( 'bpcp_pro_portfolio_wip_screen', 'members/single/plugins' ) );
}

/**
 * collections screen definitions
 */
function bpcp_pro_portfolio_collections_screen() {
    global $bp;

    $get_action = ( isset($_GET['bpcp_action']) && !empty($_GET['bpcp_action']) ) ? $_GET['bpcp_action'] : '';
    switch($get_action){
        case 'collections_following':
            add_action( 'bp_template_content', 'bpcp_pro_template_collections_following' );
            break;

        default:
            add_action( 'bp_template_content', 'bpcp_pro_template_collections_index' );
            break;
    }

    bp_core_load_template( apply_filters( 'bpcp_pro_portfolio_collections_screen', 'members/single/plugins' ) );
}

/**
 * all wip templates
 */
function bpcp_pro_template_add_wip(){
    do_action('template_notices');
    bpcp_pro_load_template( 'wip/add_wip' );
}

function bpcp_pro_template_wip_content(){
    do_action('template_notices');
    bpcp_pro_load_template( 'wip/wip_content' );
}

function bpcp_pro_template_wip_index(){
    do_action('template_notices');
    bpcp_pro_load_template( 'wip/index' );
}

/**
 * all collections templates
 */
function bpcp_pro_template_collections_following(){
    do_action('template_notices');
    add_thickbox();
    bpcp_pro_load_template( 'collections/following' );
}

function bpcp_pro_template_collections_index(){
    add_thickbox();
    bpcp_pro_load_template( 'collections/index' );
}

//Loaded after wp_head for yoast seo compatibility
function bpcp_pro_load_add_wip() {
    if ( 'on' == bp_portfolio_pro()->setting('bpcp-wip-enable')  ) {
        add_filter( 'the_content', 'bpcp_pro_content_add_wip_page' );
    }
}
add_action( 'wp_head','bpcp_pro_load_add_wip', 21 );

function bpcp_pro_content_add_wip_page( $content ){
    if( !is_main_query() )
        return $content;
    
    if( bpcp_pro_is_add_wip_page() ){
        ob_start();

        echo "<div id='buddypress'><!-- to automatically apply all css rules -->";
        $get_action = ( isset($_GET['bpcp_action']) && !empty($_GET['bpcp_action']) ) ? $_GET['bpcp_action'] : '';
        switch($get_action){
            case 'wip_content':
                bpcp_pro_template_wip_content();
                break;

            case 'add_wip':
            default:
                add_action('wp_enqueue_scripts', 'bpcp_media_upload_scripts');
                bpcp_pro_template_add_wip();
                break;
        }
        echo "</div><!-- #buddypress -->";

        $content .= ob_get_clean();
    }
    
    return $content;
}

add_filter( 'body_class', 'bpcp_pro_add_wip_page_body_class' );
function bpcp_pro_add_wip_page_body_class( $classes ){
    if( bpcp_pro_is_add_wip_page() ){
        $classes[] = 'bpcp_pro_add_wip';
    }
    return $classes;
}
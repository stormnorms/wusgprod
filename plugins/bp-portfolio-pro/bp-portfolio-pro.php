<?php
/**
 * Plugin Name: BP Portfolio PRO
 * Plugin URI:  https://www.buddyboss.com/product/social-portfolio/
 * Description: Add Works in Progress, Collections, MP3s and video embeds to portfolios.
 * Author:      BuddyBoss
 * Author URI:  https://www.buddyboss.com
 * Version:     1.2.4
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if (!defined( 'BP_PORTFOLIO_PRO_PLUGIN_VERSION' ) ) {
    define( 'BP_PORTFOLIO_PRO_PLUGIN_VERSION', '1.2.4' );
}

// Database version
if (!defined( 'BP_PORTFOLIO_PRO_PLUGIN_DB_VERSION' ) ) {
    define( 'BP_PORTFOLIO_PRO_PLUGIN_DB_VERSION', 1 );
}

// Directory
if (!defined( 'BP_PORTFOLIO_PRO_PLUGIN_DIR' ) ) {
    define( 'BP_PORTFOLIO_PRO_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if (!defined( 'BP_PORTFOLIO_PRO_PLUGIN_URL' ) ) {
    $plugin_url = plugin_dir_url( __FILE__ );

    // If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
    if ( is_ssl() )
        $plugin_url = str_replace( 'http://', 'https://', $plugin_url );

    define( 'BP_PORTFOLIO_PRO_PLUGIN_URL', $plugin_url );
}

// File
if (!defined( 'BP_PORTFOLIO_PRO_PLUGIN_FILE' ) ) {
    define( 'BP_PORTFOLIO_PRO_PLUGIN_FILE', __FILE__ );
}


/**
 * Check whether
 * it meets all requirements
 * @return void
 */
function bp_portfolio_pro_requirements()
{

    global $Plugin_Requirements_Check;

    $requirements_Check_include  = BP_PORTFOLIO_PRO_PLUGIN_DIR  . 'includes/requirements-class.php';

    try
    {
        if ( file_exists( $requirements_Check_include ) )
        {
            require( $requirements_Check_include );
        }
        else{
            $msg = sprintf( __( "Couldn't load BPCP_Plugin_Check class at:<br/>%s", 'bp-portfolio-pro' ), $requirements_Check_include );
            throw new Exception( $msg, 404 );
        }
    }
    catch( Exception $e )
    {
        $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'bp-portfolio-pro' ), $e->getMessage() );
        echo $msg;
    }

    $Plugin_Requirements_Check = new Plugin_Requirements_Check();
    $Plugin_Requirements_Check->activation_check();

}
register_activation_hook( __FILE__, 'bp_portfolio_pro_requirements' );


/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Main
 *
 * @return void
 */
function BP_PORTFOLIO_PRO_init()
{
    // Ensure is_plugin_active function is loaded
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include ABSPATH . '/wp-admin/includes/plugin.php';
    }

	if ( !function_exists('bp_portfolio') || !is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		add_action('admin_notices','bpcp_admin_notice');
		return;
	}

    global $bp, $BUDDYPRESS_CREATIVE_PORTFOLIO_PRO;

    $main_include  = BP_PORTFOLIO_PRO_PLUGIN_DIR  . 'includes/main-class.php';

    try
    {
        if ( file_exists( $main_include ) )
        {
            require( $main_include );
        }
        else{
            $msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'bp-portfolio-pro' ), $main_include );
            throw new Exception( $msg, 404 );
        }
    }
    catch( Exception $e )
    {
        $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'bp-portfolio-pro' ), $e->getMessage() );
        echo $msg;
    }

    $BUDDYPRESS_CREATIVE_PORTFOLIO_PRO = BP_Portfolio_Pro_Plugin::instance();

}
add_action( 'plugins_loaded', 'BP_PORTFOLIO_PRO_init',20);

/**
 * Must be called after hook 'plugins_loaded'
 * @return BP Portfolio Pro Plugin main controller object
 */
function bp_portfolio_pro()
{
    global $BUDDYPRESS_CREATIVE_PORTFOLIO_PRO;

    return $BUDDYPRESS_CREATIVE_PORTFOLIO_PRO;
}

register_activation_hook( __FILE__, 'bpcp_pro_setup_db_tables' );
/**
 * Setup database table for for collections items & followers.
 * Runs on plugin activation.
 */
function bpcp_pro_setup_db_tables( $network_wide=false ){
    // Add Options
    $option_name = 'bpcp_pro_views_options';
    $option = array(
      'count'                   => 0
    , 'exclude_bots'            => 0
    , 'display_home'            => 0
    , 'display_single'          => 0
    , 'display_page'            => 0
    , 'display_archive'         => 0
    , 'display_search'          => 0
    , 'display_other'           => 0
    , 'use_ajax'                => 1
    , 'template'                => __('%VIEW_COUNT%', 'bp-portfolio-pro')
    , 'most_viewed_template'    => '<li><a href="%POST_URL%"  title="%POST_TITLE%">%POST_TITLE%</a> - %VIEW_COUNT% '.__('views', 'bp-portfolio-pro').'</li>'
    );

    if ( is_multisite() && $network_wide ) {
        // Get all blogs in the network and activate plugin on each one
        $ms_sites = wp_get_sites();
        if( 0 < sizeof( $ms_sites ) )
        {
            foreach ( $ms_sites as $ms_site )
            {
                switch_to_blog( $ms_site['blog_id'] );
                // create tables
                bpcp_pro_create_db_tables();
                bpcp_pro_create_db_like_tables();
                // add options data for views count
                add_option( $option_name, $option );
            }
        }
        restore_current_blog();
    } else {
        // add options data for views count
        add_option( $option_name, $option );
        // create tables
        bpcp_pro_create_db_tables();
        bpcp_pro_create_db_like_tables();
    }
}

/**
 * Create database table for collections items & followers.
 */
function bpcp_pro_create_db_tables(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bpcp_collection_meta';

    $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
    `collection_meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `collection_id` bigint(20) unsigned NOT NULL DEFAULT '0',
    `meta_key` varchar(255) DEFAULT NULL,
    `meta_value` longtext,
    PRIMARY KEY (`collection_meta_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    update_option( 'bp_portfolio_pro_db_version', BP_PORTFOLIO_PRO_PLUGIN_DB_VERSION );
}


/**
 * Create database table for collections items & followers.
 */
function bpcp_pro_create_db_like_tables(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bpcp_components_like';

    $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
    `bpcp_like_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
    `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
    `post_type` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`bpcp_like_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    update_option( 'bp_portfolio_pro_db_version', BP_PORTFOLIO_PRO_PLUGIN_DB_VERSION );
}

function bpcp_admin_notice() {
	echo "<div class='error'><p>Please first download and activate the regular plugin for BP Portfolio PRO</p></div>";
}

/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once('includes/buddyboss-plugin-updater.php');
//new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 157);

require_once BP_PORTFOLIO_PRO_PLUGIN_DIR  . 'includes/bpcp-pro-wp-user-export-gdpr.php';

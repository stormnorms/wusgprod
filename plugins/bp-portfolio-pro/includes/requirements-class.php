<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;


class Plugin_Requirements_Check {
    function __construct() {
        add_action( 'admin_init', array( $this, 'backup_activation_check' ) );

        // Don't run anything else in the plugin, if we're on an incompatible WordPress version
        if ( ! self::bpcp_activation_check() ) {
            return;
        }
    }

    // The primary sanity check, automatically disable the plugin on activation if it doesn't
    // meet minimum requirements.
    static function activation_check() {
        if ( ! self::bpcp_activation_check() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'Please first download and activate the regular plugin.', 'bp-portfolio-pro' ) );
        }
    }

    // The backup sanity check, in case the plugin is activated in a weird way
    function backup_activation_check() {
        if ( ! self::bpcp_activation_check() ) {
            if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }

    function disabled_notice() {
        echo '<strong>' . esc_html__( 'Please first download and activate the regular plugin.', 'bp-portfolio-pro' ) . '</strong>';
    }

    static function bpcp_activation_check() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( !is_plugin_active( 'bp-portfolio/bp-portfolio.php' ) ) {
            return false;
        }

        // Add sanity checks for other version requirements here

        return true;
    }
}

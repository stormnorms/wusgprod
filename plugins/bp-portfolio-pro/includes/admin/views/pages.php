<?php
$bpcp_wip_enable = bp_portfolio_pro()->setting( 'bpcp-wip-enable' );
$bpcp_collections_enable = bp_portfolio_pro()->setting( 'bpcp-collections-enable' );

if($bpcp_wip_enable =='on'):

// add wip settings page
$all_wip_page = bp_portfolio_pro()->option( 'all-wip-page' );

echo '<table class="form-table"><tbody><tr><th scope="row">' . __( 'Works In Progress (WIP)', 'bp-portfolio-pro') . '</th><td>';
            echo wp_dropdown_pages( array(
            'name'             => 'bp_portfolio_pro_plugin_options[all-wip-page]',
            'echo'             => false,
            'show_option_none' => __( '- None -', 'bp-portfolio-pro' ),
            'selected'         => $all_wip_page
            ) );
            echo '<a href="' . admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ) . '" class="button-secondary">' . __( 'New Page', 'bp-portfolio-pro' ) .'</a>';
            if(!empty($all_wip_page)){
            echo ' <a href="' . get_the_permalink($all_wip_page) . '" class="button-secondary" target="_blank">' . __( 'View', 'bp-portfolio-pro' ) .'</a>';
            }
            echo '<p class="description">' . __( 'Use a WordPress page to display all Works In Progress uploaded by all users. (Optional)', 'bp-portfolio-pro' ) . '</p>';
            echo '</td></tr></tbody></table>';
			
// add wip page
$add_wip_page = bp_portfolio_pro()->option( 'add-wip-page' );

echo '<table class="form-table"><tbody><tr><th scope="row">' . __( 'Add WIP', 'bp-portfolio-pro') . '</th><td>';
            echo wp_dropdown_pages( array(
            'name'             => 'bp_portfolio_pro_plugin_options[add-wip-page]',
            'echo'             => false,
            'show_option_none' => __( '- None -', 'bp-portfolio-pro' ),
            'selected'         => $add_wip_page
            ) );
            echo '<a href="' . admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ) . '" class="button-secondary">' . __( 'New Page', 'bp-portfolio-pro' ) .'</a>';
            if(!empty($add_wip_page)){
            echo ' <a href="' . get_the_permalink($add_wip_page) . '" class="button-secondary" target="_blank">' . __( 'View', 'bp-portfolio-pro' ) .'</a>';
            }
            echo '<p class="description">' . __( 'Use a WordPress page to display Add WIP form. (Optional)<br /> If not selected, Add WIP form will be displayed at <code>/members/username/portfolio/wip</code>.', 'bp-portfolio-pro' ) . '</p>';
            echo '</td></tr></tbody></table>';
endif;

if($bpcp_collections_enable =='on'):

// add collections settings page
$all_collections_page = bp_portfolio_pro()->option( 'all-collections-page' );

echo '<table class="form-table"><tbody><tr><th scope="row">' . __( 'Collections', 'bp-portfolio-pro') . '</th><td>';
            echo wp_dropdown_pages( array(
            'name'             => 'bp_portfolio_pro_plugin_options[all-collections-page]',
            'echo'             => false,
            'show_option_none' => __( '- None -', 'bp-portfolio-pro' ),
            'selected'         => $all_collections_page
            ) );
            echo '<a href="' . admin_url( add_query_arg( array( 'post_type' => 'page' ), 'post-new.php' ) ) . '" class="button-secondary">' . __( 'New Page', 'bp-portfolio-pro' ) .'</a>';
            if(!empty($all_collections_page)){
            echo ' <a href="' . get_the_permalink($all_collections_page) . '" class="button-secondary" target="_blank">' . __( 'View', 'bp-portfolio-pro' ) .'</a>';
            }
            echo '<p class="description">' . __( 'Use a WordPress page to display all Collections uploaded by all users. (Optional)', 'bp-portfolio-pro' ) . '</p>';
            echo '</td></tr></tbody></table>';
endif;


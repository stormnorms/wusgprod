
<?php
$bpcp_wip_enable = bp_portfolio_pro()->setting( 'bpcp-wip-enable' );
$bpcp_projects_enable = bp_portfolio()->setting( 'bpcp-projects-enable' );

if($bpcp_wip_enable):

    // add an option enable/disable comment sync
    $bpcp_pro_wip_comments_sync = bp_portfolio_pro()->setting( 'bpcp-pro-wip-comments-sync' );
    $checked = $bpcp_pro_wip_comments_sync ? 'checked="checked"' : '';
    ?>
        <tr>
            <th scope="row"><?php _e( 'WIP Comments sync', 'bp-portfolio-pro');?></th>
            <td>
                <input type="checkbox" <?php echo $checked;?> name="bp_portfolio_pro_plugin_settings[bpcp-pro-wip-comments-sync]" />
                <span><?php _e( 'Allow WIP activity stream commenting to sync with WIP posts', 'bp-portfolio-pro' );?></span>
                <p class="description"><?php _e( 'Make sure to activate "Site Tracking" component in Settings &gt; BuddyPress, and also check the option "Allow activity stream commenting on blog and forum posts"', 'bp-portfolio-pro' );?></p>
            </td>
        </tr>

<?php endif;

if($bpcp_projects_enable):

    // add an option enable/disable displaying project views count
    $bpcp_pro_projects_views_count = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
    $views_count_checked = $bpcp_pro_projects_views_count ? 'checked="checked"' : '';
    ?>
        <tr>
            <th scope="row"><?php _e( 'Project & WIP views count', 'bp-portfolio-pro');?></th>
            <td>
                <input type="checkbox" <?php echo $views_count_checked;?> name="bp_portfolio_pro_plugin_settings[bpcp-pro-projects-views-count]" />
                <span><?php _e( 'Display total view counts, in Project/WIP list and single Project/WIP page', 'bp-portfolio-pro' );?></span>
            </td>
        </tr>

<?php endif; ?>

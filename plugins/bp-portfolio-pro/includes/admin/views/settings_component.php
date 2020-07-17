<?php
$bpcp_wip_enable = bp_portfolio_pro()->setting( 'bpcp-wip-enable' );
$bpcp_collections_enable = bp_portfolio_pro()->setting( 'bpcp-collections-enable' );
$wip_checked = $bpcp_wip_enable == 'on' ? 'checked="checked"' : '';
$collections_checked = $bpcp_collections_enable == 'on' ? 'checked="checked"' : '';
?>
<table class="form-table">
    <tbody>
    <tr>
        <td>
            <input type="checkbox" <?php echo $wip_checked;?> name="bp_portfolio_pro_plugin_settings[bpcp-wip-enable]" />
            <?php _e( '<b>Works In Progress (WIP)</b> &mdash; Let users share and discuss revisions on incomplete work', 'bp-portfolio-pro' );?>
			<?php if ( $bpcp_wip_enable == 'on' ) : ?>
				<?php _e('<a href="'.admin_url().'edit.php?post_type=bb_wip">[edit]</a>','bp-portfolio-pro'); ?>
			<?php endif; ?>
        </td>
    </tr>
    </tbody>
</table>

<table class="form-table">
    <tbody>
    <tr>
        <td>
            <input type="checkbox" <?php echo $collections_checked;?> name="bp_portfolio_pro_plugin_settings[bpcp-collections-enable]" />
            <?php _e( '<b>Collections</b> &mdash; Let users share collections of their favorite Projects from the site', 'bp-portfolio-pro' );?>
			<?php if ( $bpcp_collections_enable == 'on' ) : ?>
				<?php _e('<a href="'.admin_url().'edit.php?post_type=bb_collection">[edit]</a>','bp-portfolio-pro'); ?>
			<?php endif; ?>
        </td>
    </tr>
    </tbody>
</table>



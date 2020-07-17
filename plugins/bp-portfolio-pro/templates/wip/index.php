<?php
/**
 * BP Portfolio Pro- wip Index
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>
<div class="bpcp-pro-tab-title-filter">
	<ul>
		<li>
			<h2 class="entry-title">
				<span><?php _e( 'Works in Progress', 'bp-portfolio-pro' ) ?></span>
				<?php if(bp_displayed_user_id() == bp_loggedin_user_id()){ ?>
					<?php $add_wip_link = wp_nonce_url( bpcp_pro_wip_add_link(), 'bpcp_add_wip', 'wip_nonce' ); ?>
					<input onclick="location.href='<?php echo $add_wip_link;?>'" type="button" value="<?php _e( 'Add WIP', 'bp-portfolio-pro' );?>" />
				<?php } ?>
			</h2>
		</li>
		<li class="projects-order-filter last" role="navigation">
			<?php echo bpcp_pro_wip_order_filter_items();?>
		</li>
	</ul>
</div>

<?php
$wip_filter_by = isset( $_GET['bb_orderby'] ) && !empty( $_GET['bb_orderby'] ) ? $_GET['bb_orderby'] : 'recent';
switch($wip_filter_by){
	case 'recent':          $order_by = 'date'; break;
	case 'most_commented':  $order_by = 'comment_count'; break;
	case 'most_viewed':     $order_by = 'bpcp_pro_views'; break;
	case 'most_popular':    $order_by = 'like'; break;
	default:                $order_by = 'date';
}
$meta_query = bpcp_project_meta_args('wip_visibility');
$paged = bpcp_paged_query_var();
if ( $order_by != 'like' ) {
	$pp_args = array(
		'post_type' => 'bb_wip',
		'meta_query' => array( $meta_query ),
		'author' => bp_displayed_user_id(),
		'orderby' => $order_by,
		'posts_per_page' => 5,
		'post_parent' => 0,
		'paged' => $paged
	);
	
	if( 'bpcp_pro_views'==$order_by ){
		$pp_args['orderby'] = 'meta_value_num';
		$pp_args['meta_key'] = $order_by;
	}

	$pp_args = bpcp_add_date_query_args( $pp_args );
	$pp_args = bpcp_add_category_query_args( $pp_args );

	$the_query = new WP_Query( $pp_args );
} else {
	$pp_args = array(
		'post_type' => 'bb_wip',
		'meta_query' => array( $meta_query ),
		'author' => bp_displayed_user_id(),
		'posts_per_page' => 5,
		'post_parent' => 0,
		'paged' => $paged
	);
	$the_query = new WP_Query( $pp_args );
}
$views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
if ($the_query->have_posts()): ?>
<div class="bb-display-projects">
    <ul class="bb-project-items">
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
            <li class="bb-project-item">
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
<?php else: ?>
    <div id="message" class="bp-template-notice">
        <?php if( bpcp_is_accessible() ){ ?>
            <p>
                <?php _e( 'You have not added a work in progress yet.', 'bp-portfolio-pro' );?>
                <a href="<?php echo $add_wip_link;?>"><?php _e( 'Add a work in progress', 'bp-portfolio-pro' );?></a>
            </p>
        <?php } else { ?>
            <p><?php _e( 'Sorry, there were no works in progress found.', 'bp-portfolio-pro' );?></p>
        <?php } ?>
    </div>
<?php endif;
// pagination code
bpcp_num_pagination($the_query);
// Restore original Query & Post Data
wp_reset_query();
wp_reset_postdata();
?>

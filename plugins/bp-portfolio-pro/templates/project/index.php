<?php
/**
 * BP Portfolio Pro- Project Index
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>

<div class="bpcp-pro-tab-title-filter">
    <ul>
        <li>
            <h2 class="entry-title">
                <span><?php _e( 'Projects', 'bp-portfolio-pro' ) ?></span>
                <?php if(bp_displayed_user_id() == bp_loggedin_user_id()){ ?>
                    <?php 
                    //$add_project_link = wp_nonce_url( bpcp_portfolio_component_root() . '?bpcp_action=add_project', 'bpcp_add_project', 'project_nonce' ); 
                    $base_link = bpcp_portfolio_component_root();
                    if( bpcp_add_project_location()=='wp' ){
                        $base_link = get_permalink( bp_portfolio()->option('add-project-page-select') );
                    }
                    $add_project_link = wp_nonce_url( add_query_arg( array( 'bpcp_action' => 'add_project' ), $base_link ), 'bpcp_add_project', 'project_nonce' );
                    ?>
                    <input onclick="location.href='<?php echo $add_project_link;?>'" type="button" value="<?php _e( 'Add Project', 'bp-portfolio-pro' );?>" />
                <?php } ?>
            </h2>
        </li>
        <li class="projects-order-filter last" role="navigation">
            <?php echo bpcp_pro_projects_order_filter_items();?>
        </li>
    </ul>
</div>


<?php
$projects_filter_by = isset( $_GET['bb_orderby'] ) && !empty( $_GET['bb_orderby'] ) ? $_GET['bb_orderby'] : 'recent';
switch($projects_filter_by){
	case 'recent':          $order_by = 'date'; break;
	case 'most_commented':  $order_by = 'comment_count'; break;
	case 'most_viewed':     $order_by = 'bpcp_pro_views'; break;
	case 'most_popular':    $order_by = 'like'; break;
	default:                $order_by = 'date';
}
$meta_query = bpcp_project_meta_args('project_visibility');
$paged = bpcp_paged_query_var();
if($order_by != 'like'){
    $pp_args = array(
        'post_type' => 'bb_project',
        'meta_query' => array($meta_query),
        'author' => bp_displayed_user_id(),
        'orderby' => $order_by,
        'order' => 'DESC',
        'posts_per_page' => 20,
        'paged' => $paged
    );
	
	if( 'bpcp_pro_views'==$order_by ){
		$pp_args['orderby'] = 'meta_value_num';
		$pp_args['meta_key'] = $order_by;
	}
	$pp_args = bpcp_add_date_query_args( $pp_args );
	$pp_args = bpcp_add_category_query_args( $pp_args );
	
    $the_query = new WP_Query($pp_args);
	
}else{
    $pp_args = array(
        'post_type' => 'bb_project',
        'meta_query' => array($meta_query),
        'author' => bp_displayed_user_id(),
        'posts_per_page' => 20,
        'paged' => $paged
    );
    add_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
    $the_query = new WP_Query($pp_args);
    remove_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
}
$views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
if ($the_query->have_posts()): ?>
<div class="bb-display-projects">
    <ul class="bb-project-items">
        <?php while ($the_query->have_posts()):
                $the_query->the_post();
                $default_img_url = BP_PORTFOLIO_PLUGIN_URL.'assets/images/project-placeholder.png';
                $get_project_thumb = bpcp_featured_image_detail(get_the_ID(), 'portfolio-thumbnail', $default_img_url);
                $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
                $all_tags = bpcp_custom_taxonomy_terms(get_the_ID(), 'bb_project_tag', true);
                $like_count_text = bpcp_pro_like_count_text();
                if($views_count_enabled == 'on') {
                    $view_count_text = bpcp_pro_view_count_text();
                }
            ?>
            <li class="bb-project-item">
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
                    <div class="bb-project-tags"><?php if(isset($all_tags) && !empty($all_tags)) { echo 'tags: '. implode(', ', $all_tags); }?></div>
                </div>
            </li>
        <?php endwhile;?>
    </ul>
</div>
<?php else: ?>
<div id="message" class="bp-template-notice">
    <?php if(bp_displayed_user_id() == bp_loggedin_user_id()){ ?>
        <p>
            <?php _e( 'You have not added a project yet.', 'bp-portfolio-pro' );?>
            <a href="<?php echo $add_project_link;?>"><?php _e( 'Add a project', 'bp-portfolio-pro' );?></a>
        </p>
    <?php } else { ?>
        <p><?php _e( 'Sorry, there were no projects found.', 'bp-portfolio-pro' );?></p>
    <?php } ?>
</div>
<?php endif;
// pagination code
bpcp_num_pagination($the_query);
// Restore original Query & Post Data
wp_reset_query();
wp_reset_postdata();
?>

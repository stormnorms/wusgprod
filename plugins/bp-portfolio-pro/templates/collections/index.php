<?php
/**
 * BP Portfolio Pro- wip Index
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>

<h2 class="entry-title">
    <span><?php _e( 'Collections', 'bp-portfolio-pro' ) ?></span>
    <?php if(bp_displayed_user_id() == bp_loggedin_user_id()){ ?>
        <a class="btn button bpcp-modal" href="#add_collection"><?php _e( 'Add Collection', 'bp-portfolio-pro' );?></a>
    <?php } ?>
</h2>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
    <?php echo bpcp_pro_collections_subnavs(); ?>
</div>

<?php
$meta_query = bpcp_project_meta_args('collection_visibility');
$paged = bpcp_paged_query_var();
$pp_args = array(
    'post_type' => 'bb_collection',
    'meta_query' => array($meta_query),
    'author' => bp_displayed_user_id(),
    'posts_per_page' => 20,
    'paged' => $paged
);
$the_query = new WP_Query($pp_args);
if ($the_query->have_posts()): ?>
    <div class="bb-display-projects">
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
                        <a href="<?php the_permalink();?>"><img src="<?php echo $collection_thumb_url;?>" alt="<?php the_title();?>"/></a>
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

                <?php if(bp_displayed_user_id() == bp_loggedin_user_id()){ ?>
                <div class="collection-delete-btn">
                    <a class="button bpcp-modal" href="#delete_collection_<?php the_ID(); ?>"><?php _e( 'Delete', 'bp-portfolio-pro' ); ?></a>
                </div>
                <?php } ?>

                <!-- Make ready modal content for delete collection -->
                <div id="delete_collection_<?php the_ID(); ?>" class="bbcp-white-popup-block mfp-hide">
                    <h3><?php _e( 'Delete Collection', 'bp-portfolio-pro' ); ?></h3>
                    <p>
                        <form class="standard-form"  method="post" action="">
                            <p class="delete_confirm">Do you really want to delete this collection?</p>
                            <input name="target_collection" type="hidden" value="<?php the_ID(); ?>" />
                            <input type="submit" name="delete_yes" id="delete_yes" class="delete-yes" value="Delete" />
                            &nbsp;&nbsp;&nbsp;
                            <input type="button" name="bpcp_modal_cancel" id="bpcp_modal_cancel" class="bpcp-modal-cancel" value="Cancel" />
                        </form>
                    </p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div id="message" class="bp-template-notice">
        <?php if(bp_displayed_user_id() == bp_loggedin_user_id()){ ?>
            <p>
                <?php _e( 'You have not added a collection yet.', 'bp-portfolio-pro' );?>
                <a class="bpcp-modal" href="#add_collection"><?php _e( 'Add a collection', 'bp-portfolio-pro' );?></a>
            </p>
        <?php } else { ?>
            <p><?php _e( 'Sorry, there were no collections found.', 'bp-portfolio-pro' );?></p>
        <?php } ?>
    </div>
<?php endif;
// pagination code
bpcp_num_pagination($the_query);
// Restore original Query & Post Data
wp_reset_query();
wp_reset_postdata();
?>


<?php include_once( bp_portfolio_pro()->templates_dir . '/collections/add_collections.php' ); ?>
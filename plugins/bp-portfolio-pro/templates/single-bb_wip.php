<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */

global $post;
$get_parent_id = $post->post_parent == 0 ? $post->ID : $post->post_parent;
?>
<!-- left part -->
<div class="project-single-left">


    <?php if($post->post_author == bp_loggedin_user_id()): ?>
    <div class="project-meta">
        <button class="btn button" onclick="window.location='<?php echo wp_nonce_url( bpcp_pro_wip_edit_link($get_parent_id ), 'bpcp_add_wip', 'wip_nonce' ); ?>';"><?php _e( 'Add Revision', 'bp-portfolio-pro' ); ?></button>
        <button class="btn button bpcp-modal" data-mfp-src="#delete_wip"><?php _e( 'Delete Revision', 'bp-portfolio-pro' ); ?></button>
        <div id="delete_wip" class="bbcp-white-popup-block mfp-hide">
            <h3><?php _e( 'Delete wip', 'bp-portfolio-pro' ); ?></h3>
            <p>
                <form class="standard-form"  method="post" action="">
                    <p class="delete_confirm">Do you really want to delete this wip?</p>
                    <input name="target_wip" type="hidden" value="<?php the_ID(); ?>" />
                    <input type="submit" name="delete_yes" id="delete_yes" class="delete-yes" value="Delete" />
                    &nbsp;&nbsp;&nbsp;
                    <input type="button" name="bpcp_modal_cancel" id="bpcp_modal_cancel" class="bpcp-modal-cancel" value="Cancel" />
                </form>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <div class="project-content">
        <div class="project-description">
            <?php echo $post->post_content; ?>
        </div>

        <?php
        $get_wip_attachment = bpcp_pro_get_attachment_detail($post->ID, 'large');
        if(!empty($get_wip_attachment)): ?>
            <div class="bpcp_pro_wip_single_thumb">
                <p><img data-src="<?php echo $get_wip_attachment['full_src'];?>" class="entry-post-thumbnail" alt="<?php echo $get_wip_attachment['alt'];?>" src="<?php echo $get_wip_attachment['src'];?>"/></p>
            </div>
        <?php endif; ?>

        <?php
        $revisions_thumbs = bpcp_pro_revisions_thumbs($get_parent_id);
        if(!empty($revisions_thumbs)): ?>
            <ul class="bpcp-pro-li-left revision-thumbs">
            <?php $i = 1; foreach($revisions_thumbs as $thumb): ?>
                <?php 
                $selected = $thumb['revision_id'] == $post->ID ? 'active' : ''; 
                if($thumb['url']){
                ?>
                <li class="<?php echo $selected;?>">
                   <div class="revison-th-inner">
                    <a href="#" data-postid="<?php echo get_the_ID();?>" data-revid="<?php echo $thumb['revision_id']?>">
                        <?php 
                        $title = sprintf( '%1s %2s', __('Revision', 'bp-portfolio-pro'), $i); 
                        ?>
                        <img src="<?php echo $thumb['url']?>" alt="<?php echo $title; ?>" />
                    </a>
                    <span><?php echo $title; ?></span>
                   </div>
                </li>
                <?php } ?>
            <?php $i++; endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</div>

<!-- right part -->
<div class="project-single-right">

    <div class="project-single-author">
        <h3><?php _e( 'WIP by', 'bp-portfolio-pro' ); ?></h3>
        <p>
            <?php
            $avatar_args = array(
            'item_id' => get_the_author_meta('ID'),
            'type' => 'thumb',
            'width' => 100,
            'height' => 100,
            'class' => 'author-avatar',
            'html'=>true
            );
            $author_avatar = bp_core_fetch_avatar($avatar_args);
            ?>
            <a class="author-avatar-link" href="<?php echo bpcp_pro_wip_root(); ?>"><?php echo $author_avatar; ?></a>
            <span><a href="<?php echo bpcp_pro_wip_root(); ?>"><?php the_author(); ?></a></span>
        </p>
    </div>

    <?php if ( is_user_logged_in() ): ?>

        <div class="project-single-atc bpcp-components-like">
            <?php
            $like_count			 = bpcp_pro_like_count( get_the_ID(), 'bb_wip' );
            $like_status		 = bpcp_pro_like_status( get_the_ID(), bp_loggedin_user_id() );
            $like_status_id		 = $like_status ? 'unlike_this_post' : 'like_this_post';
            $like_status_text	 = $like_status ? bp_portfolio_pro()->appreciated : bp_portfolio_pro()->appreciate_this;
            $views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
            ?>
            <p><a class="btn" id="<?php echo $like_status_id; ?>" href="#" data-posttype="bb_wip" data-userid="<?php echo bp_loggedin_user_id(); ?>" data-postid="<?php the_ID(); ?>"><b><?php echo $like_status_text; ?></b></a></p>
            <div class="appreciation_count"><?php _e( 'Appreciations ', 'bp-portfolio-pro' ); ?><span><?php echo $like_count; ?></span></div>
            <?php if ( $views_count_enabled == 'on' && function_exists( 'bpcp_pro_the_views' ) ) { ?><div class="wip-views-count"><?php _e( 'Views ', 'bp-portfolio-pro' ); ?><span><?php bpcp_pro_the_views(); ?></span></div><?php } ?>
        </div>
    <?php endif; ?>

    <?php $all_tags = bpcp_custom_taxonomy_terms(get_the_ID(), 'bb_wip_tag', true); ?>
    <?php if(isset($all_tags) && !empty($all_tags)): ?>
        <div class="project-single-tags">
            <h3><?php _e( 'WIP Tags', 'bp-portfolio-pro' ); ?></h3>
            <p><?php echo implode(' ', $all_tags); ?></p>
        </div>
    <?php endif; ?>

</div>

<div class="clearfix"></div>


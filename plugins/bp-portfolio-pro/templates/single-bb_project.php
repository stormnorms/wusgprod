<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
global $post;
?>

<!-- left part -->
<div class="project-single-left">

    <?php if ( $post->post_author == bp_loggedin_user_id() ): ?>
    <div class="project-meta">
        <button class="button" onclick="window.location='<?php echo bpcp_project_edit_link( get_the_ID() ); ?>';"><?php _e( 'Edit Project', 'bp-portfolio-pro' ); ?></button>
        <button class="bpcp-modal button" data-mfp-src="#delete_project"><?php _e( 'Delete Project', 'bp-portfolio-pro' ); ?></button>

        <div id="delete_project" class="bbcp-white-popup-block mfp-hide">
            <h3 class="bbpc-delete-title"><?php _e( 'Delete Project', 'bp-portfolio-pro' ); ?></h3>
            <form class="standard-form"  method="post" action="<?php echo bpcp_portfolio_component_root(); ?>">
                <p class="delete_confirm">Do you really want to delete this project?</p>
                <input name="target_project" type="hidden" value="<?php the_ID(); ?>" />
                <input type="submit" name="delete_yes" id="delete_yes" class="delete-yes" value="Delete" />
                <input type="button" name="bpcp_modal_cancel" id="bpcp_modal_cancel" class="bpcp-modal-cancel" value="Cancel" />
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="project-content">
        <div class="project-description">
            <?php echo $post->post_content; ?>
        </div>
        <?php
        $get_all_attachment = bpcp_pro_get_project_items( get_the_ID() );
        if ( !empty( $get_all_attachment ) ):
            ?>

            <div class="bpcp_project_photo">

                <?php
                foreach ( $get_all_attachment as $attachment ):
                    $single = bpcp_pro_single_attachment_detail( $attachment, 'large' );
                    // for embeded video
                    if ( substr_count( $attachment, 'video' ) > 0 ) {
                        $val			 = bpcp_pro_get_video_by_item_id( $attachment, $post->ID );
                        $video_caption	 = get_post_meta( get_the_ID(), 'attach-' . $attachment, true );
                        $embed_code		 = wp_oembed_get( $val );
                        ?>
                        <p>
                            <?php echo $embed_code; ?>
                            <span class="media-caption"><?php echo $video_caption; ?></span>
                        </p>
                        <?php
                    } else {
                        // for photo & song
                        if ( $single[ 'type' ] == 'image' ) {
                            ?>
                            <div class="bpcp-popup-image">
                                <a href="<?php echo $single[ 'full_src' ]; ?>" title="<?php echo $single[ 'caption' ]; ?>">
                                    <img class="entry-post-thumbnail" alt="<?php echo $single[ 'alt' ]; ?>" src="<?php echo $single[ 'src' ]; ?>"/>
                                    <span class="media-caption"><?php echo $single[ 'caption' ]; ?></span>
                                </a>
                            </div>
                        <?php } ?>

                        <?php if ( $single[ 'type' ] == 'audio' ) { ?>
                            <p>
                                <!--[if lt IE 9]><script>document.createElement('audio');</script><![endif]-->
                                <audio class="wp-audio-shortcode" preload="none" controls="controls"><source type="audio/mpeg" src="<?php echo $single[ 'src' ]; ?>?_=1" /><a href="<?php echo $single[ 'src' ]; ?>"><?php echo $single[ 'src' ]; ?></a></audio>
                                <span class="media-caption"><?php echo $single[ 'caption' ]; ?></span>
                            </p>
                            <?php
                        }
                    }
                endforeach;
                ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<!-- right part -->
<div class="project-single-right">

    <div class="project-single-author">
        <h3><?php _e( 'Project by', 'bp-portfolio-pro' ); ?></h3>
        <p>
            <?php
            $avatar_args	 = array(
                'item_id'	 => get_the_author_meta( 'ID' ),
                'type'		 => 'thumb',
                'width'		 => 100,
                'height'	 => 100,
                'class'		 => 'author-avatar',
                'html'		 => true
            );
            $author_avatar	 = bp_core_fetch_avatar( $avatar_args );
            ?>
            <a class="author-avatar-link" href="<?php echo bpcp_user_domain_portfolio_slug( get_the_author_meta( 'ID' ) ); ?>"><?php echo $author_avatar; ?></a>
            <span><a href="<?php echo bpcp_user_domain_portfolio_slug( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span>
        </p>
    </div>

    <?php if ( is_user_logged_in() ): ?>
        <div class="project-single-atc">
            <p><button class="btn button bpcp-modal" data-mfp-src="#add_collection"><?php _e( 'Add to Collection', 'bp-portfolio-pro' ); ?></button></p>
        </div>

        <div class="project-single-atc bpcp-components-like">
            <?php
            $like_count			 = bpcp_pro_like_count( get_the_ID(), 'bb_project' );
            $like_status		 = bpcp_pro_like_status( get_the_ID(), bp_loggedin_user_id() );
            $like_status_id		 = $like_status ? 'unlike_this_post' : 'like_this_post';
            $like_status_text	 = $like_status ? bp_portfolio_pro()->appreciated : bp_portfolio_pro()->appreciate_this;
            $views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
            ?>
            <p><button class="btn button" id="<?php echo $like_status_id; ?>" href="#" data-posttype="bb_project" data-userid="<?php echo bp_loggedin_user_id(); ?>" data-postid="<?php the_ID(); ?>"><b><?php echo $like_status_text; ?></b></button></p>
            <div class="appreciation_count"><?php _e( 'Appreciations ', 'bp-portfolio-pro' ); ?><span><?php echo $like_count; ?></span></div>
            <?php if ( $views_count_enabled == 'on' && function_exists( 'bpcp_pro_the_views' ) ) { ?><div class="project-views-count"><?php _e( 'Views ', 'bp-portfolio-pro' ); ?><span><?php bpcp_pro_the_views(); ?></span></div><?php } ?>
        </div>
    <?php endif; ?>

    <?php $all_tags = bpcp_custom_taxonomy_terms( get_the_ID(), 'bb_project_tag', true ); ?>
    <?php if ( isset( $all_tags ) && !empty( $all_tags ) ): ?>
        <div class="project-single-tags">
            <h3><?php _e( 'Project Tags', 'bp-portfolio-pro' ); ?></h3>
            <p><?php echo implode( ' ', $all_tags ); ?></p>
        </div>
    <?php endif; ?>

</div>

<div class="clearfix"></div>


<?php include_once( bp_portfolio_pro()->templates_dir . '/collections/add_collections_lists.php' ); ?>

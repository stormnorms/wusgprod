<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */

global $post;
?>
<div id="buddypress">

<div class="project-single-left">
    <div class="project-single-author">
        <?php
        $avatar_args = array(
            'item_id' => get_the_author_meta('ID'),
            'type' => 'thumb',
            'width' => 220,
            'height' => 220,
            'class' => 'author-avatar',
            'html'=>true
        );
        $author_avatar = bp_core_fetch_avatar($avatar_args);
        ?>
        <ul class="bpcp-pro-left">
            <li><a href="<?php echo bpcp_user_domain_portfolio_slug(get_the_author_meta('ID')).'/collections'; ?>"><?php echo $author_avatar; ?></a></li>
            <li>
                <p class="by"><?php _e( 'Collection by', 'bp-portfolio-pro' ); ?> <a href="<?php echo bpcp_user_domain_portfolio_slug(get_the_author_meta('ID')).'/collections'; ?>"><?php the_author(); ?></a></p>
                <p class="entry-description"><?php the_content(); ?></p>
                <p class="upadated-date">Updated: <?php the_modified_time('M d, Y'); ?></p>
            </li>
        </ul>
    </div>
</div>
<div class="project-single-right">
    <?php
    $user_id = bp_loggedin_user_id();
    if( is_user_logged_in() && $user_id != get_the_author_meta('ID') ): ?>
        <form name="user_collection_follow" action="" method="post">
            <input type="hidden" name="collection_id" value="<?php the_ID()?>" />
            <?php
            $user_following = bpcp_pro_collection_follower_check(get_the_ID(), $user_id);
            if($user_following < 1){
                ?>
                <input type="submit" name="follow_collection" value="<?php _e( 'follow', 'bp-portfolio-pro' ); ?>" />
            <?php } else { ?>
                <input type="submit" name="unfollow_collection" value="<?php _e( 'unfollow', 'bp-portfolio-pro' ); ?>" />
            <?php } ?>
        </form>
    <?php endif; ?>
</div>
<div class="clearfix"></div>

<div class="project-content activity">

    <div class="bpcp-grid-wrapper">
        <?php
        $project_ids = get_post_meta(get_the_ID(), 'project_ids', true);
        if (isset($project_ids) && !empty($project_ids)): ?>
        <ul id="portfolio-activity" class="portfolio-list">
            <?php foreach($project_ids as $single):
                $default_img_url = BP_PORTFOLIO_PLUGIN_URL.'assets/images/project-placeholder.png';
                $get_project_thumb = bpcp_featured_image_detail($single, 'portfolio-thumbnail', $default_img_url);
                $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
                $project_title = get_the_title($single);
                $project_url = get_the_permalink($single);
                $author_id = get_post_field('post_author', $single);
                ?>
                <li class="bpcp-grid-item">
                    <div class="bp-inner-wrap">
                        <div class="bb-project-thumb">
                            <a href="<?php echo $project_url;?>"><img src="<?php echo $project_thumb_url;?>" alt="<?php echo $project_title;?>" /></a>
                        </div>
                        <div class="bb-project-title"><h4><a href="<?php echo $project_url;?>"><?php echo $project_title;?></a></h4></div>
                        <div class="bb-project-author"><?php _e( 'by', 'bp-portfolio-pro' );?> <a href="<?php echo bp_core_get_user_domain($author_id);?>"><?php echo get_the_author_meta('user_login', $author_id);?></a></div>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
    <?php endif; ?>

</div>
</div>
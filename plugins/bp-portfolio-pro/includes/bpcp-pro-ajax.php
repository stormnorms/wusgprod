<?php

/**
 * all Ajax callback
 */
function bpcp_pro_ajax_callback()
{

    // projects filtering
    if( isset($_POST['task']) && $_POST['task'] == 'projects_order_filtering' ) {
        $projects_filter_by = $_POST['projects_filter_by'];
        $_SESSION['projects_filter_by'] = $projects_filter_by;

        switch($projects_filter_by){
            case 'recent':         $order_by = 'date'; break;
            case 'most_commented': $order_by = 'comment_count'; break;
            default: $order_by = 'like';
        };
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
        ob_start();
        $views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
        if ($the_query->have_posts()):
            while ($the_query->have_posts()):
                $the_query->the_post();
                $default_img_url = BP_PORTFOLIO_PLUGIN_URL.'assets/images/project-placeholder.png';
                $get_project_thumb = bpcp_featured_image_detail(get_the_ID(), 'portfolio-thumbnail', $default_img_url);
                $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
                $all_tags = bpcp_custom_taxonomy_terms(get_the_ID(), 'bb_project_tag', true);
                $like_count_text = $like_count_text = bpcp_pro_like_count_text();
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
                        <div class="bb-project-tags"><?php if(isset($all_tags) && !empty($all_tags)) { echo implode(', ', $all_tags); }?></div>
                    </div>
                </li>
            <?php
            endwhile;
        endif;
        $get_lists =  ob_get_contents();
        ob_end_clean();
        // Restore original Query & Post Data
        wp_reset_query();
        wp_reset_postdata();

        echo $get_lists;
        exit;
    }


    // projects filtering global
    if( isset($_POST['task']) && $_POST['task'] == 'projects_order_filtering_global' ) {
        $projects_filter_by = $_POST['projects_filter_by'];
        $_SESSION['projects_filter_by'] = $projects_filter_by;

        switch($projects_filter_by){
            case 'recent':         $order_by = 'date'; break;
            case 'most_commented': $order_by = 'comment_count'; break;
            default: $order_by = 'like';
        };
        $meta_query = bpcp_project_meta_args('project_visibility');
        $paged = bpcp_paged_query_var();
        if($order_by != 'like'){
            $gp_args = array(
                'post_type' => 'bb_project',
                'meta_query' => array($meta_query),
                'orderby' => $order_by,
                'order' => 'DESC',
                'posts_per_page' => 20,
                'paged' => $paged
            );
            add_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
            $the_query = new WP_Query($gp_args);
            remove_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
        }else{
            $gp_args = array(
                'post_type' => 'bb_project',
                'meta_query' => array($meta_query),
                'posts_per_page' => 20,
                'paged' => $paged
            );
            add_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
            //add_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
            $the_query = new WP_Query($gp_args);
            remove_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
            //remove_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
        }
        ob_start();
        $views_count_enabled = bp_portfolio_pro()->setting( 'bpcp-pro-projects-views-count' );
        if ($the_query->have_posts()):
            while ($the_query->have_posts()):
                $the_query->the_post();
                $default_img_url = BP_PORTFOLIO_PLUGIN_URL.'assets/images/project-placeholder.png';
                $get_project_thumb = bpcp_featured_image_detail(get_the_ID(), 'portfolio-thumbnail', $default_img_url);
                $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
                $project_visibility = get_post_meta(get_the_ID(), 'project_visibility', true);
                $like_count_text = bpcp_pro_like_count_text();
                if($views_count_enabled == 'on') {
                    $view_count_text = bpcp_pro_view_count_text();
                }
                ?>
                <li class="bpcp-grid-item">
                    <div class="bp-inner-wrap">
                        <div class="bb-project-thumb">
                            <a href="<?php the_permalink();?>"><img src="<?php echo $project_thumb_url;?>" alt="<?php the_title();?>" /></a>
                        </div>
                        <div class="bb-project-title"><h4><a href="<?php the_permalink();?>"><?php the_title();?></a><h4></div>
                        <div class="bb-project-meta">  
                            <?php if(!empty($like_count_text)) { ?><div class="bb-project-like"> <?php echo $like_count_text;?></div><?php } ?>      
                            <?php if(!empty($view_count_text)){ ?><div class="bb-project-views-count"><?php echo $view_count_text;?></div><?php } ?>
                        </div>
                        <div class="bb-project-author"><?php _e( 'by', 'bp-portfolio-pro' );?> <a href="<?php echo bp_core_get_user_domain(get_the_author_meta('ID'));?>"><?php the_author();?></a></div>
                    </div>
                </li>
            <?php
            endwhile;
        endif;
        $get_lists =  ob_get_contents();
        ob_end_clean();
        // Restore original Query & Post Data
        wp_reset_query();
        wp_reset_postdata();

        echo $get_lists;
        exit;
    }

    // project items sorting
    if( isset($_POST['task']) && $_POST['task'] == 'project_items_sorting' ) {
        parse_str( $_POST['sort_data'], $data );
        $current_project_id = $_POST['current_project_id'];
        print_r($data);
        bpcp_pro_project_sort_update($current_project_id, $data);
        exit;
    }

    // components like
    if ( isset($_POST['task']) && $_POST['task'] == 'bpcp_components_like' ) {
        global $wpdb;
        $userid = !empty($_POST['userid']) ? $_POST['userid'] : '';
        $postid = !empty($_POST['postid']) ? $_POST['postid'] : '';
        $post_type = !empty($_POST['post_type']) ? $_POST['post_type'] : '';
        // check if like data added already
        $like_status = bpcp_pro_like_status($postid, $userid);

        if( !empty($userid) && !empty($postid) && !$like_status ){
            $new_values = array(
                'post_id' => $postid,
                'user_id' => $userid,
                'post_type' => $post_type,
            );
            $format = array('%d','%d','%s');
            $result = $wpdb->insert($wpdb->prefix."bpcp_components_like", $new_values, $format);
            $prepare_data = array(
                'status' => $result ? 'success' : '',
            );
            
            update_option( 'emi_debug', 'initiate inside like ation');
            
            do_action( 'bpcp_components_like', $new_values, $result );
            echo json_encode($prepare_data);
        }
        exit;
    }

    // components unlike
    if ( isset($_POST['task']) && $_POST['task'] == 'bpcp_components_unlike' ) {
        global $wpdb;
        $userid = !empty($_POST['userid']) ? $_POST['userid'] : '';
        $postid = !empty($_POST['postid']) ? $_POST['postid'] : '';
        $post_type = !empty($_POST['post_type']) ? $_POST['post_type'] : '';

        if( !empty($userid) && !empty($postid) ){
            $the_values = array(
                'post_id' => $postid,
                'user_id' => $userid,
                'post_type' => $post_type,
            );
            $format = array('%d','%d','%s');
            $result = $wpdb->delete($wpdb->prefix."bpcp_components_like", $the_values, $format);
            $prepare_data = array(
                'status' => $result ? 'success' : '',
            );
            
            do_action( 'bpcp_components_unlike', $the_values, $result );
            echo json_encode($prepare_data);
        }
        exit;
    }

    // adding video embed url to a project as custom field
    if ( isset($_POST['task']) && $_POST['task'] == 'embed_video_url_to_project' ) {
        $video_embed_url = !empty($_POST['video_embed_url']) ? $_POST['video_embed_url'] : '';
        $current_project_id = $_POST['current_project_id'];

        // check if this video is allowed to embed
        $embed_code = wp_oembed_get($video_embed_url, array('width'=>250));
        if(!$embed_code) {
            $prepare_data = array(
                'video_id' => '',
                'video_output' => ''
            );
            echo json_encode($prepare_data);
            exit;
        }

        // adding this video as a meta data to this project
        if( !empty($current_project_id) && !empty($video_embed_url) ){
            $embeded_urls = (array)get_post_meta($current_project_id, 'embeded_urls', true);
            if( !in_array($video_embed_url, $embeded_urls) ){
                $embeded_urls[] = $video_embed_url;
                update_post_meta($current_project_id, 'embeded_urls', $embeded_urls);
            }
            $video_keys = array_keys($embeded_urls);
            $prepare_data = array(
                'video_id' => 'attach-video'.end($video_keys),
                'video_output' => $embed_code
            );
            echo json_encode($prepare_data);
        }
        exit;
    }

    // removing video embed url from a project custom field
    if ( isset($_POST['task']) && $_POST['task'] == 'remove_embeded_video_url_from_project' ) {
        $video_embed_url = !empty($_POST['video_embed_url']) ? $_POST['video_embed_url'] : '';
        $current_project_id = $_POST['current_project_id'];
        $video_ID = !empty($_POST['video_ID']) ? $_POST['video_ID'] : '';
        // removing
        if( !empty($current_project_id) && !empty($video_embed_url) ){
            $embeded_urls = (array)get_post_meta($current_project_id, 'embeded_urls', true);
            if( is_array($embeded_urls) && ($key = array_search($video_embed_url, $embeded_urls)) !== false ) {
                unset($embeded_urls[$key]);
                $embeded_urls = array_values($embeded_urls);
                update_post_meta($current_project_id, 'embeded_urls', $embeded_urls);
                delete_post_meta($current_project_id, $video_ID);
            }
        }
        exit;
    }

    // set project attachment caption - photo & song
    if ( isset($_POST['task']) && $_POST['task'] == 'set_project_attachment_caption' ) {
        $attachment_ID = !empty($_POST['atachment_ID']) ? $_POST['atachment_ID'] : '';
        $caption_value = !empty($_POST['caption_value']) ? $_POST['caption_value'] : '';
        $prepare_data = array(
            'status' => 'failed',
            'message' => __('Something wrong. Please try again', 'bp-portfolio-pro')
        );
        // now editing attach caption
        if( !empty($attachment_ID) && !empty($caption_value) ){
            $edit_attach = array(
                'ID'           => $attachment_ID,
                'post_excerpt' => $caption_value
            );
            $result = wp_update_post($edit_attach);
            if($result){
                $prepare_data['status'] = 'success';
                $prepare_data['message'] = __('Caption Updated!', 'bp-portfolio-pro');
            }
        }
        echo json_encode($prepare_data);
        exit;
    }

    // set project attachment caption - video
    if ( isset($_POST['task']) && $_POST['task'] == 'set_project_video_attachment_caption' ) {
        $current_project_id = !empty($_POST['current_project_id']) ? $_POST['current_project_id'] : '';
        $video_ID = !empty($_POST['video_ID']) ? $_POST['video_ID'] : '';
        $caption_value = !empty($_POST['caption_value']) ? $_POST['caption_value'] : '';
        $prepare_data = array(
            'status' => 'failed',
            'message' => __('Something wrong. Please try again', 'bp-portfolio-pro')
        );
        // now editing attach caption
        if( !empty($current_project_id) && !empty($video_ID) && !empty($caption_value) ){
            $result = update_post_meta($current_project_id, $video_ID, $caption_value);
            if($result){
                $prepare_data['status'] = 'success';
                $prepare_data['message'] = __('Caption Updated!', 'bp-portfolio-pro');
            }else{
                $prepare_data['status'] = 'success';
                $prepare_data['message'] = __('Already saved!', 'bp-portfolio-pro');
            }
        }
        echo json_encode($prepare_data);
        exit;
    }

    // update sortlist project attachments
    if ( isset($_POST['task']) && $_POST['task'] == 'update_sortlist_project_attachments' ) {
        $current_project_id = $_POST['current_project_id'];
        if( !empty($current_project_id) ){
            // update sort lists
            bpcp_pro_get_project_items($current_project_id);
        }
        exit;
    }

    // adding this project as  a collection item
    if ( isset($_POST['task']) && $_POST['task'] == 'add_project_as_collection_item' ) {
        $chosen_collection = !empty($_POST['chosen_collection']) ? explode(',' , $_POST['chosen_collection']) : '';
        $not_chosen_collection = !empty($_POST['not_chosen_collection']) ? explode(',' , $_POST['not_chosen_collection']) : '';
        $current_project_id = $_POST['current_project_id'];

        // remove this project from unchecked collection
        if( !empty($current_project_id) && !empty($not_chosen_collection) ){
            foreach($not_chosen_collection as $single){
                $project_ids = get_post_meta($single, 'project_ids', true);
                if( is_array($project_ids) && ($key = array_search($current_project_id, $project_ids)) !== false ) {
                    unset($project_ids[$key]);
                    $project_ids = array_values($project_ids);
                    update_post_meta($single, 'project_ids', $project_ids);
                    bpcp_pro_modified_time_update($single);
                }
            }
        }
        // adding this project to a list of collections
        if( !empty($current_project_id) && !empty($chosen_collection) ){
            foreach($chosen_collection as $single){
                $project_ids = (array)get_post_meta($single, 'project_ids', true);
                if( !in_array($current_project_id, $project_ids) ){
                    $project_ids[] = $current_project_id;
                    update_post_meta($single, 'project_ids', $project_ids);
                    bpcp_pro_modified_time_update($single);
                }
            }
            echo '<div class="bp-template-notice updated" id="message"><p>'.__('Project added to collection(s) successfully!', 'bp-portfolio-pro').'</p></div>';
        }
        exit;
    }

    // create a new collection from project single page
    if ( isset($_POST['task']) && $_POST['task'] == 'collection_add' ) {
        $author_id = bp_loggedin_user_id();
        $collection_description = !isset($_POST['collection_description']) ? '' : sanitize_text_field($_POST['collection_description']);
        $collection_title = !isset($_POST['collection_title']) ? '' : sanitize_text_field($_POST['collection_title']);
        $collection_visibility = !isset($_POST['collection_visibility']) ? '' : $_POST['collection_visibility'];
        $collection_project_id = !isset($_POST['collection_project_id']) ? '' : $_POST['collection_project_id'];

        // Now create collection post
        if (!empty($collection_title)) {
            $collection_args = array(
                'post_title' => $collection_title,
                'post_content' => $collection_description,
                'post_status' => 'publish',
                'post_author' => $author_id,
                'post_type' => 'bb_collection',
            );
            $new_collection_id = wp_insert_post($collection_args);
            if (isset($new_collection_id) && !empty($new_collection_id)) {

                // add current project into this new collection
                if ( ! empty( $collection_project_id ) ) {
                    $project_ids[] = $collection_project_id;
                    update_post_meta( $new_collection_id, 'project_ids', $project_ids );
                    bpcp_pro_modified_time_update( $new_collection_id );
                }

                // collection visibility update
                if(!empty($collection_visibility)){
                    update_post_meta($new_collection_id, 'collection_visibility', $collection_visibility);
                }
                  echo '<div class="bp-template-notice updated" id="message"><p>'.__('Collection added successfully! ', 'bp-portfolio-pro').'</p></div>';
            } else {
                echo '<div class="bp-template-notice error" id="message"><p>'.__('Failed! ', 'bp-portfolio-pro').'</p></div>';
            }
        }
        exit;
    }

    // switch comment from wip single page
    if ( isset($_POST['task']) && $_POST['task'] == 'revision_switch' ) {
        global $post;
        $thumb_url = '';
        $revision_id = !isset($_POST['revision_id']) ? '' : $_POST['revision_id'];
        if(!empty($revision_id)){
			//get views
			$wip_views = bpcp_pro_the_views_by_id($revision_id,false);
            // get thumb
            $thumb_url = bpcp_pro_get_attachment_detail($revision_id, 'large');
            // get comments
            global $post, $wpdb;
            $post = get_post( $revision_id, OBJECT );
            setup_postdata( $post );

            global $withcomments;
            $withcomments = 1;
            ob_start();

            $comment_template = bpcp_pro_get_comments_template($post->ID);
            comments_template($comment_template, true);

            $comments = ob_get_contents();
            ob_end_clean();
            wp_reset_postdata();
        }
        $prepare_data = array(
            'thumb_url' => empty( $thumb_url['src'] ) ? 'http://via.placeholder.com/800x500/CBE5E5/FFFFFF?text=WIP' : $thumb_url['src'],
            'comments' => $comments,
            'wip_views' => $wip_views,
        );
        echo json_encode($prepare_data);
        exit;
    }

    // get collection data
    if ( isset($_GET['task']) && $_GET['task'] == 'get_collection_data' ) {
        $collection_id = $_GET['collection_id'];

        $collection     = get_post($collection_id);
        $project_ids    = get_post_meta($collection_id, 'project_ids', true); // collection projects

        // project list html
        $project_list_html = '';
        foreach ( $project_ids as $project_id ) {

            if(!$project_id) continue;

            ob_start();
            ?>
<div class="project-<?php echo $project_id ?>">
<input type="hidden" name="choosen_project[]" value="<?php echo $project_id ?>" />
<span>
    <a class="delete-project-btn" title="<?php _e('Remove', 'bp-portfolio-pro') ?>">x</a>
    <?php echo get_post_field('post_title', $project_id) ?>
</span>
</div>
            <?php
            $project_list_html .= ob_get_clean();
        }

        $data = array(
                'title'                 => $collection->post_title,
                'description'           => $collection->post_content,
                'visibility'            => get_post_meta($collection_id, 'collection_visibility', true),
                'projects_list_html'    => $project_list_html
        );

        wp_send_json($data);
    }

    // search for the project
    if ( isset($_GET['task']) && $_GET['task'] == 'search_projects' ) {
        $term = $_GET['term'];

        $project_query = new WP_Query(array(
                'post_type' => 'bb_project',
                's'         => $term,
                'nopaging'  => true
        ));

        if ($project_query->have_posts()) {
            $data = array();

            while ($project_query->have_posts()) {
                $project_query->the_post();
                $data[] = array(
                        'value' => get_the_ID(),
                        'label' => get_the_title()
                );
            }

            wp_send_json($data);
        } else {
           die;
        }
    }

}


function bpcp_pro_comment_callback( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
            // Display trackbacks differently than normal comments.
            ?>
            <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <p><?php _e( 'Pingback:', 'bp-portfolio-pro' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'bp-portfolio-pro' ), '<span class="edit-link">', '</span>' ); ?></p>
            <?php
            break;
        default :
            // Proceed with normal comments.
            global $post;
            ?>
            <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                <article id="comment-<?php comment_ID(); ?>" class="comment">
                    <header class="comment-meta comment-author vcard">
                        <?php
                        echo get_avatar( $comment, 44 );
                        printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
                            get_comment_author_link(),
                            // If current post author is also comment author, make it known visually.
                            ( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'bp-portfolio-pro' ) . '</span>' : ''
                        );
                        printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                            esc_url( get_comment_link( $comment->comment_ID ) ),
                            get_comment_time( 'c' ),
                            /* translators: 1: date, 2: time */
                            sprintf( __( '%1$s at %2$s', 'bp-portfolio-pro' ), get_comment_date(), get_comment_time() )
                        );
                        ?>
                    </header><!-- .comment-meta -->

                    <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'bp-portfolio-pro' ); ?></p>
                    <?php endif; ?>

                    <section class="comment-content comment">
                        <?php comment_text(); ?>
                        <?php //edit_comment_link( __( 'Edit', 'bp-portfolio-pro' ), '<p class="edit-link">', '</p>' ); ?>
                    </section><!-- .comment-content -->

<!--                    <div class="reply">
                        <?php /*comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'bp-portfolio-pro' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); */?>
                    </div>--><!-- .reply -->
                </article><!-- #comment-## -->
            <?php
            break;
    endswitch; // end comment_type check
}

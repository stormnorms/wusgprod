<?php
add_action('bp_actions', 'bpcp_create_wip_save');
add_action('bp_actions', 'bpcp_delete_wip');
add_action('bp_actions', 'bpcp_create_collection_save');
add_action('bp_actions', 'bpcp_pro_global_pages_actions',6);
add_action('bpcp_after_project_udpated', 'bpcp_pro_create_project_save', 10, 2 );

/**
 * Global pages action
 */
function bpcp_pro_global_pages_actions(){
    if(!bp_loggedin_user_id()) return;
    global $wpdb;
    $user_id = bp_loggedin_user_id();

    // follow action
    if( isset($_POST['follow_collection']) &&
        ($_POST['follow_collection'] == __('follow' , 'bp-portfolio-pro') ) &&
        isset($_POST['collection_id']) &&
        !empty($_POST['collection_id'])
    ){
        $collection_id = $_POST['collection_id'];
        $already_exists_count = bpcp_pro_collection_follower_check($collection_id, $user_id);
        // add this user id to this collection metadata
        if($already_exists_count < 1){
            $new_values = array(
                'collection_id' => $collection_id,
                'meta_key' => 'followed_user',
                'meta_value' => $user_id,
            );
            $format = array('%d','%s','%d');
            $wpdb->insert($wpdb->prefix."bpcp_collection_meta", $new_values, $format);
        }
    }
    // unfollow action
    if( isset($_POST['unfollow_collection']) &&
        ($_POST['unfollow_collection'] == __('unfollow', 'bp-portfolio-pro') ) &&
        isset($_POST['collection_id']) &&
        !empty($_POST['collection_id'])
    ){
        $collection_id = $_POST['collection_id'];
        // remove this user id from this collection metadata
        $condition = "collection_id = $collection_id AND meta_key = 'followed_user' AND meta_value = $user_id";
        $wpdb->query("DELETE FROM ".$wpdb->prefix."bpcp_collection_meta WHERE $condition");
    }

}

/**
 * Project extra action - update sort listed data
 */
function bpcp_pro_create_project_save( $project_id, $current_step ) {
    if ( bpcp_is_accessible() && $current_step=='next_cover' ) {
        // for second steps
        
        if (!empty($project_id)) {
            // update custom field "bpcp_sorted_items" value by all attachments
            //bpcp_pro_get_project_items($project_id);

            // update all photo & audio captions
            $photo_ids = isset($_POST['photo_item']['pid']) && !empty($_POST['photo_item']['pid']) ? $_POST['photo_item']['pid'] : array();
            $photo_captions = isset($_POST['photo_item']['pcaption']) && !empty($_POST['photo_item']['pcaption']) ? $_POST['photo_item']['pcaption'] : array();
            if(!empty($photo_ids)){
                foreach($photo_ids as $key => $val){
                    $attachment_ID = !empty($val) ? $val : '';
                    $caption_value = !empty($photo_captions[$key]) ? $photo_captions[$key] : '';
                    // now editing attach caption
                    if( !empty($attachment_ID) && !empty($caption_value) ){
                        $edit_attach = array(
                            'ID'           => $attachment_ID,
                            'post_excerpt' => $caption_value
                        );
                        $result = wp_update_post($edit_attach);
                    }
                }
            }
            // update all video captions
            $video_ids = isset($_POST['video_item']['vid']) && !empty($_POST['video_item']['vid']) ? $_POST['video_item']['vid'] : array();
            $video_captions = isset($_POST['video_item']['vcaption']) && !empty($_POST['video_item']['vcaption']) ? $_POST['video_item']['vcaption'] : array();
            if(!empty($video_ids)){
                foreach($video_ids as $key => $val){
                    $attachment_ID = !empty($val) ? $val : '';
                    $caption_value = !empty($video_captions[$key]) ? $video_captions[$key] : '';
                    // now editing attach caption
                    if( !empty($project_id) &&  !empty($attachment_ID) && !empty($caption_value) ){
                        $result = update_post_meta($project_id, $attachment_ID, $caption_value);
                    }
                }
            }

        }
    }
}

/**
 * WIP add/edit/delete form submission
 */
function bpcp_create_wip_save() {
    global $wpdb, $bp;

    if ( bpcp_is_accessible() ) {
        $portfolio_wip_slug = bpcp_pro_subnav_wip_slug();
        $author_id = bp_loggedin_user_id();
        $user_link = bp_core_get_userlink(bp_loggedin_user_id());
        $wip_id = !isset($_GET['wip_id']) ? '' : $_GET['wip_id'];
        $current_wip_id = !isset($_GET['current_wip_id']) ? '' : $_GET['current_wip_id'];
        $chosen_file = !isset($_POST['chosen_file']) ? '' : $_POST['chosen_file'];
        $wip_attach_id = !isset($_POST['wip_attach_id']) ? '' : $_POST['wip_attach_id'];
        $wip_title = !isset($_POST['wip_title']) ? '' : sanitize_text_field($_POST['wip_title']);
        $wip_comment = !isset($_POST['wip_comment']) ? '' : wp_kses_post($_POST['wip_comment']);
        $wip_tags = !isset($_POST['wip_tags']) ? '' : sanitize_text_field($_POST['wip_tags']);
        $wip_visibility = !isset($_POST['wip_visibility']) ? '' : $_POST['wip_visibility'];
        $bpcp_pro_wip_comments_sync = bp_portfolio()->option('bpcp-pro-wip-comments-sync');

        if ($bpcp_pro_wip_comments_sync == 'on') {
            add_action('wp_insert_comment', 'bp_blogs_record_comment', 10, 2);
        }

        // for first step
        if (bpcp_pro_is_add_wip_routine() &&
            isset($_POST['create_wip']) &&
            !empty($_POST['create_wip'])
        ) {
            // now redirect to third step
            $content_link = bpcp_pro_wip_step_url('wip_content', '', $chosen_file);
            bp_core_redirect($content_link);
        }

        // for fourth steps
        if (bpcp_pro_is_add_wip_routine() &&
            isset($_POST['wip_finish']) &&
            !empty($_POST['wip_finish'])
        ) {
            // while add a revision of an existing wip
            if (!empty($wip_id)) {
                $wip_detail = bpcp_get_post_detail($wip_id);
                $get_revision_num = bpcp_pro_current_revision_num($wip_id);
                $get_revision_num = intval($get_revision_num) + 1;
                $wip_args = array(
                    'post_title' => $wip_detail->post_title,
                    'post_status' => 'publish',
                    'post_author' => $author_id,
                    'post_type' => 'bb_wip',
                    'post_parent' => $wip_id,
                    'post_name' => 'revision-' . $get_revision_num,
                );
                if (!empty($current_wip_id)) {
                    $wip_args['ID'] = $current_wip_id;
                    $new_wip_id = wp_update_post($wip_args);
                }else {
                    $new_wip_id = wp_insert_post($wip_args);
                }

                flush_rewrite_rules();
                // attach uploaded image
                if (!empty($wip_attach_id)) {
                    bpcp_insert_attachment_by_ID($wip_attach_id, $new_wip_id);
                }
                // wip visibility update
                if (!empty($wip_visibility)) {
                    update_post_meta($new_wip_id, 'wip_visibility', $wip_visibility);
                    update_post_meta($wip_id, 'wip_visibility', $wip_visibility);
                }
                // add completed flag

                // add activity
                $get_wip_thumb = bpcp_pro_get_attachment_detail($new_wip_id, 'portfolio-thumbnail');
                $wip_thumb_url = (isset($get_wip_thumb['src']) && !empty($get_wip_thumb['src'])) ? $get_wip_thumb['src'] : '';
                //$activity_content = !empty($wip_thumb_url) ? '<a href="'.get_the_permalink($new_wip_id).'"><img alt="'.get_the_title($new_wip_id).'" src="'.$wip_thumb_url.'" /></a>' : '';

                $activity_content = !empty($wip_thumb_url) ? '<img alt="' . get_the_title($new_wip_id) . '" src="' . $wip_thumb_url . '" />' : '';
                $get_project_full_img = bpcp_pro_get_attachment_detail($new_wip_id, 'full');
                if (!empty($get_project_full_img)) {
                    $project_img_full_url = (isset($get_project_full_img['src']) && !empty($get_project_full_img['src'])) ? $get_project_full_img['src'] : '';
                    if (!empty($project_img_full_url)) {
                        $activity_content = "<a href='" . get_the_permalink($new_wip_id) . "'>" . $activity_content . "</a>";
                    }
                }

                // Fetch the activity_id which has been added by post type activity tracking during wp_insert_post.
                $activity_id = bp_activity_get_activity_id(array('secondary_item_id' => $new_wip_id, 'type' => 'new_bb_wip'));

                //SQL update statement to update activity content
                $SQL = "UPDATE {$bp->activity->table_name} SET content = %s WHERE id = %d";
                $wpdb->query($wpdb->prepare($SQL, $activity_content, $activity_id));

                // set completed flag
                update_post_meta($new_wip_id, 'entry_status', 'completed');

                // set wip activity stream privacy
                if (!empty($wip_visibility))
                    bp_activity_update_meta($activity_id, 'bpcp-visibility', $wip_visibility);

                // add comment to this wip
                if (!empty($wip_comment)) {
                    $comment_id = bpcp_pro_comment_add($new_wip_id, $wip_comment);
                }

                // now redirect to second step
                $base_link = bpcp_pro_wip_root();
                bp_core_redirect($base_link);

            } else {

                // while add a new wip
                if (!empty($wip_title) && !empty($author_id)) {
                    $wip_args = array(
                        'post_title' => $wip_title,
                        'post_status' => 'publish',
                        'post_author' => $author_id,
                        'post_type' => 'bb_wip',
                    );
                    if (!empty($current_wip_id)) {
                        $wip_args['ID'] = $current_wip_id;
                        $new_wip_id = wp_update_post($wip_args);
                    } else {
                        $new_wip_id = wp_insert_post($wip_args);
                    }


                    if (isset($new_wip_id) && !empty($new_wip_id)) {
                        // attach uploaded image
                        if (!empty($wip_attach_id)) {
                            bpcp_insert_attachment_by_ID($wip_attach_id, $new_wip_id);
                        }
                        // add taxonomy term to this wip
                        if (!empty($wip_tags)) {
                            $wip_tags = explode(',', $wip_tags);
                            wp_set_object_terms($new_wip_id, $wip_tags, 'bb_wip_tag');
                        }

                        // edit category term to this project
                        if (!empty($_POST['wip_category'])) {
                            $catg = $_POST['wip_category'];
                            wp_set_object_terms($new_wip_id, $catg, 'bb_wip_category');
                        }

                        // wip visibility update
                        if (!empty($wip_visibility)) {
                            update_post_meta($new_wip_id, 'wip_visibility', $wip_visibility);
                        }
                        // add completed flag
                        //$completed_flag = get_post_meta($new_wip_id, 'entry_status', true);
                        //if ($completed_flag != 'completed') {
                            // add activity
                            $get_wip_thumb = bpcp_pro_get_attachment_detail($new_wip_id, 'portfolio-thumbnail');
                            $wip_thumb_url = (isset($get_wip_thumb['src']) && !empty($get_wip_thumb['src'])) ? $get_wip_thumb['src'] : '';
                            //$activity_content = !empty($wip_thumb_url) ? '<a href="'.get_the_permalink($new_wip_id).'"><img alt="'.get_the_title($new_wip_id).'" src="'.$wip_thumb_url.'" /></a>' : '';

                            $activity_content = !empty($wip_thumb_url) ? '<img alt="' . get_the_title($new_wip_id) . '" src="' . $wip_thumb_url . '" />' : '';
                            $get_project_full_img = bpcp_pro_get_attachment_detail($new_wip_id, 'full');
                            if (!empty($get_project_full_img)) {
                                $project_img_full_url = (isset($get_project_full_img['src']) && !empty($get_project_full_img['src'])) ? $get_project_full_img['src'] : '';
                                if (!empty($project_img_full_url)) {
                                    $activity_content = "<a href='" . get_the_permalink($new_wip_id) . "'>" . $activity_content . "</a>";
                                }
                            }

                            // Fetch the activity_id which has been added by post type activity tracking during wp_insert_post.
                            $activity_id = bp_activity_get_activity_id(array('secondary_item_id' => $new_wip_id, 'type' => 'new_bb_wip'));

                            //SQL update statement to update an activity content
                            $SQL = "UPDATE {$bp->activity->table_name} SET content = %s WHERE id = %d";
                            $wpdb->query($wpdb->prepare($SQL, $activity_content, $activity_id));

                            // set completed flag
                            update_post_meta($new_wip_id, 'entry_status', 'completed');

                            // set wip activity stream privacy
                            if (!empty($wip_visibility))
                                bp_activity_update_meta($activity_id, 'bpcp-visibility', $wip_visibility);

                            // add comment to this wip
                            if (!empty($wip_comment)) {
                                $comment_id = bpcp_pro_comment_add($new_wip_id, $wip_comment);
                            }
                        //}
                        // now redirect to component root page
                        $base_link = bpcp_pro_wip_root();
                        bp_core_redirect($base_link);
                    } else {
                        bp_core_add_message(__('Some error happen while creating post. Please try again.', 'bp-portfolio-pro'), 'error');
                    }
                } else {
                    bp_core_add_message(__('Please insert wip title', 'bp-portfolio-pro'), 'error');
                }
            }
        }
    }
}

/**
 * Handle wip and wip revision delete
 *
 */
function bpcp_delete_wip() {

    $target_wip = !isset($_POST['target_wip']) ? '' : $_POST['target_wip'];

    // for wip delete
    if ( ! empty($target_wip) && ( $_POST['delete_yes'] == 'Delete') ) {

       // Get wip media
        $media = get_children(array(
            'post_parent' => $target_wip,
            'post_type' => 'attachment'
        ));

        // Delete all wip media attachment
        if (!empty($media)) {
            foreach ($media as $file) {
                // pick what you want to do
                wp_delete_attachment($file->ID);
            }
        }

        // Collect wip data before delete
        $wip_post   = get_post( $target_wip );
        $parent_wip = $wip_post->post_parent;

        // Delete wip post
        $deleted_wip = wp_delete_post($target_wip);

        // Delete success message
        if($deleted_wip){
            bp_core_add_message(__( 'wip deleted successfully', 'bp-portfolio-pro' ), 'success');
        }

        // now redirect to the members > username > portfolio > wip
        if ( 0 == $parent_wip ) {
            $base_link = bpcp_pro_wip_root();
            bp_core_redirect($base_link);
        }
    }
}

/**
 * collection add/edit/delete form submission
 */
function bpcp_create_collection_save()
{
    if (bp_displayed_user_id() == bp_loggedin_user_id()) {

        $portfolio_collection_slug = bpcp_pro_subnav_collections_slug();
        $author_id = bp_loggedin_user_id();
        $user_link = bp_core_get_userlink( bp_loggedin_user_id() );
        $target_collection = !isset($_POST['target_collection']) ? '' : $_POST['target_collection'];
        $collection_title = !isset($_POST['collection_title']) ? '' : sanitize_text_field($_POST['collection_title']);
        $collection_description = !isset($_POST['collection_description']) ? '' : sanitize_text_field($_POST['collection_description']);
        $collection_visibility = !isset($_POST['collection_visibility']) ? '' : $_POST['collection_visibility'];
        $project_ids = !isset($_POST['choosen_project']) ? '': $_POST['choosen_project'];

        // for wip delete
        if (bpcp_is_portfolio_component() &&
            bp_is_current_action($portfolio_collection_slug) &&
            !empty($target_collection) &&
            isset($_POST['delete_yes']) &&
            ($_POST['delete_yes'] == 'Delete')
        ) {
            $deleted_collection = wp_delete_post($target_collection);
            if($deleted_collection){
                bp_core_add_message(__( 'Collection deleted successfully', 'bp-portfolio-pro' ), 'success');
            }
        }

        // for first step
        if (bpcp_is_portfolio_component() &&
            bp_is_current_action($portfolio_collection_slug) &&
            isset($_POST['collection_add']) &&
            !empty($_POST['collection_add'])
        ) {

            // while add a new collection
            if (!empty($collection_title)) {
                $collection_args = array(
                    'post_title' => $collection_title,
                    'post_content' => $collection_description,
                    'post_status' => 'publish',
                    'post_author' => $author_id,
                    'post_type' => 'bb_collection',
                );

                if ( !empty($target_collection) ) {
                    $collection_args['ID'] = $target_collection;
                    $new_collection_id = wp_update_post($collection_args);
                } else {
                    $new_collection_id = wp_insert_post($collection_args);
                }

                if (isset($new_collection_id) && !empty($new_collection_id)) {
                    // collection visibility update
                    if(!empty($collection_visibility)){
                        update_post_meta($new_collection_id, 'collection_visibility', $collection_visibility);
                    }

                    // collection project update
                    if (!empty($project_ids)) {
                        update_post_meta($new_collection_id, 'project_ids', $project_ids);
                    } else {
                        delete_post_meta($new_collection_id, 'project_ids');
                    }

                    bp_core_add_message(__( 'Collection saved successfully!', 'bp-portfolio-pro' ), 'success');
                } else {
                    bp_core_add_message(__( 'Some error happen while creating post. Please try again.', 'bp-portfolio-pro' ), 'error');
                }

            } else {
                bp_core_add_message(__( 'Please insert collection title & description', 'bp-portfolio-pro' ), 'error');
            }
        }


    }
}

add_action( 'bpcp_components_like', 'bpcp_components_like_add_activity_post', 10, 2 );
/**
 * Add an activity post when someone likes a project/wip.
 * 
 * @param array $values
 * @param boolean $success
 * @return void
 */
function bpcp_components_like_add_activity_post( $values, $success=true ){
    if( !$success || !bp_is_active( 'activity' ) )
        return;
    
    $post_type_label = $values['post_type'];
    $post_type_obj = get_post_type_object( $values['post_type'] );
    
    if( $post_type_obj && !is_wp_error( $post_type_obj ) ){
        $post_type_label = $post_type_obj->labels->singular_name;
    }
    
    $post_link = sprintf( "<a href='%s'>%s</a>", esc_attr( get_permalink( $values['post_id'] ) ), get_the_title( $values['post_id'] ) );
    
    $like_action = apply_filters( 'bpcp_like_activity_action', __( '%s appreciated the %s %s', 'bp-portfolio-pro' ) );
    $like_action = sprintf( $like_action, bp_core_get_userlink( $values['user_id'] ), $post_type_label, $post_link );
    
    bpcp_record_activity( array( 
        'action'            => $like_action,
		'component'         => bpcp_portfolio_slug(),
		'type'              => 'item-appreciated',
		'user_id'           => $values['user_id'],
		'item_id'           => $values['post_id'],
    ) );
}

add_action( 'bpcp_components_unlike', 'bpcp_components_like_remove_activity_post', 10, 2 );

add_action( 'delete_post', 'bpec_remove_wip_activity', 10, 1 );

/**
 * Remove a wip activity item from the activity stream.
 *
 * @param int $post_id ID of the post to be removed.
 */
function bpec_remove_wip_activity( $post_id ) {

    // Bail if not wip
    if ( 'bb_wip' != get_post_type( $post_id ) ) return;

   // Delete wip created activity
    $r = array(
        'component'         => 'activity',
        'type'              => 'new_bb_wip',
        'secondary_item_id' => $post_id
    );

    bp_activity_delete( $r );

    // Delete wip appreciated activities
    $r = array(
        'component' => 'portfolio',
        'type'      => 'item-appreciated',
        'item_id'   => $post_id
    );

    bp_activity_delete( $r );
}
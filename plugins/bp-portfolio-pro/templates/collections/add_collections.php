<!-- Add collections -->

<div id="add_collection" class="bbcp-white-popup-block mfp-hide">
    <h3><?php _e( 'New Collection', 'bp-portfolio-pro' );?></h3>
    <form class="add-collection-details standard-form"  method="post" action="">
        <div class="bpcp-li-no-margin bpcp-distance-bottom">
            <ul class="bpcp-li-left">
                <li class="field-collection-title">
                    <label for="collection_title"><?php _e( 'Create a new Collection', 'bp-portfolio-pro' ); ?></label>
                    <div><input id="collection_title" name="collection_title" class="collection-title" type="text" value="<?php if(isset($project_detail->post_title)) { echo $project_detail->post_title; }?>"/></div>
                </li>
                <li class="field-collection-description">
                    <label for="collection_description"><?php _e( 'Description', 'bp-portfolio-pro' ); ?></label>
                    <div><textarea id="collection_description" name="collection_description" class="collection-description"><?php if(isset($project_detail->post_content)) { echo $project_detail->post_content; }?></textarea></div>
                </li>
                <li>
                    <label for="collection_visibility"><?php _e( 'Visibility', 'bp-portfolio-pro' ); ?></label>
                    <div>
                        <select id="collection_visibility" name="collection_visibility" class="collection-visibility">
                            <?php
                            $options = array(
                                'public'	=> __('Everyone', 'bp-portfolio-pro'),
                                'private'	=> __('Only Me', 'bp-portfolio-pro'),
                                'members'	=> __('All Members', 'bp-portfolio-pro'),
                            );
                            if( bp_is_active( 'friends' ) ){
                                $options['friends'] = __('My Friends', 'bp-portfolio-pro');
                            }

//                            $wip_visibility = get_post_meta($wip_id, 'wip_visibility', true);
//                            $selected_val = isset($wip_visibility) ? $wip_visibility : '';

                            foreach( $options as $key=>$val ){
//                                $selected = $selected_val == $key ? ' selected' : '';
                                echo "<option value='" . esc_attr( $key ) . "'>$val</option>";
                            }
                            ?>
                        </select>
                    </div>
                </li>
                <li class="project-single-btn">
                    <input id="collection_add" class="collection-add closing" type="submit" name="collection_add" value="<?php _e( 'Add New Collection', 'bp-portfolio-pro' ); ?>" />
                </li>
            </ul>
        </div>
    </form>
</div>
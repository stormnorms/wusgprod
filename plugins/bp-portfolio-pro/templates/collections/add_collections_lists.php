<!-- Add collections -->
<?php
global $post;
$project_id = $post->ID;
?>
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
                            foreach( $options as $key=>$val ){
                                echo "<option value='" . esc_attr( $key ) . "'>$val</option>";
                            }
                            ?>
                        </select>
                    </div>
                </li>
                <!-- add this(current) project into new collection -->
                <li class="field-collection-add-project">
                    <label for="collection_add_project">
                            <input type="checkbox" checked="checked" id="collection_add_project" name="collection_add_project" value="<?php echo $project_id; ?>" />
                            <strong style="cursor: pointer;"><?php _e( 'Add this project into new collection', 'bp-portfolio-pro' ); ?></strong>
                    </label>
                </li>
                <li class="project-single-btn">
                    <input id="collection_add" class="collection-add" type="button" name="collection_add" value="<?php _e( 'Add New Collection', 'bp-portfolio-pro' ); ?>" />
                </li>
            </ul>

            <div class="bpcp-right">
                <div class="bpcp_collections_list">
                    <label><?php _e('Add to existing', 'bp-portfolio-pro'); ?></label>
                    <?php
                    $pp_args = array(
                        'post_type' => 'bb_collection',
                        'author' => bp_loggedin_user_id(),
                    );
                    $the_query = new WP_Query($pp_args);
                    if ($the_query->have_posts()):
                    while ($the_query->have_posts()):
                        $the_query->the_post();
                        $project_ids = get_post_meta(get_the_ID(), 'project_ids', true);
						if ( is_array($project_ids) ) { 
							$checked = in_array($project_id, $project_ids) ? 'checked' : '';
						} else {
							$checked = $project_ids ? 'checked' : '';
						}
                    ?>
                    <div><input type="checkbox" <?php echo $checked?> name="chosen_collection[]" value="<?php the_ID();?>" /><span><?php the_title();?></span></div>
                    <?php endwhile; endif;
                    wp_reset_query();
                    wp_reset_postdata();
                    ?>
                </div>

                <div class="bpcp_collection_save">
                    <input type="hidden" name="current_project_id" id="current_project_id" value="<?php echo $project_id; ?>" />
                    <input id="collection_save" class="collection-save" type="button" name="collection_save" value="<?php _e( 'Save', 'bp-portfolio-pro' ); ?>" />
                </div>
            </div>

        </div>
    </form>
</div>
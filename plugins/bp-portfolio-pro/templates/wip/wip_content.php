<?php
/**
 * BP Portfolio Pro- WIP Content
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>

<?php if ( bpcp_is_accessible() ) { ?>
    <h2 class="entry-title"><?php echo bpcp_pro_add_wip_title();?></h2>
    <p><?php echo bpcp_pro_add_wip_subtitle(); ?></p>

    <?php
    $wip_id = ( isset($_GET['wip_id']) && !empty($_GET['wip_id']) ) ? $_GET['wip_id'] : '';
    $wip_detail = bpcp_get_post_detail($wip_id);
    $all_tags = bpcp_custom_taxonomy_terms($wip_id, 'bb_wip_tag');
	if(!empty($wip_id)) {
		$class_to_apply = 'bbp-is-revision';
	} else {
		$class_to_apply = '';
	}
    ?>

    <form class="add-wip-details standard-form <?php echo $class_to_apply; ?>"  method="post" action="">

        <input type="hidden" name="wip_attach_id" id="wip_attach_id" value="<?php echo $_GET['attach_id']?>" />


        <?php if(empty($wip_id)) : ?>
            <div>
                <label for="wip_title"><?php _e( 'Title (required)', 'bp-portfolio-pro' ); ?></label>
                <div><input id="wip_title" name="wip_title" class="wip-title" type="text" value="<?php if(isset($wip_detail->post_title)) { echo $wip_detail->post_title; }?>"/></div>
            </div>

            <div>
                <label for="wip_tags"><?php _e( 'Tags', 'bp-portfolio-pro' ); ?></label>
                <div><input id="wip_tags" name="wip_tags" class="wip-tags" type="text" value="<?php if(isset($all_tags) && !empty($all_tags)) { echo implode(', ', $all_tags); }?>"/></div>
            </div>

            <?php
            $args = array(
                'echo'  => 0,
                'taxonomy'          => 'bb_wip_category',
                'hide_empty'        => 0,
                'option_none_value' => '',
                'hierarchical'      => 1,
                'hide_if_empty'     => 1,
                'orderby'           => 'name',
                'name'              => 'wip_category[]',
                'class'             => 'bp-category-multiselect',
                'id'                => 'wip-category',
                'value_field'       => 'slug',
                'selected'          => bpcp_custom_taxonomy_single_term_slug( $project_id, 'bb_wip_category' ),
                'walker'            => new Walker_CategoryDropdown_Projects
            );                     
            $select_cats = wp_dropdown_categories($args);                                   
            if(!empty($select_cats)):
            ?>
            <div>
                <label for="wip_category"><?php echo bpcp_pro_wip_category_label(); if( bp_portfolio()->setting( 'bpcp-wip-category-required' )=='yes' ){ echo " (required) ";}?></label>
                <div>
                    <?php
                    $select_cats = str_replace( 'id=', 'multiple="multiple" id=', $select_cats );
                    echo $select_cats;   
                    ?>                   
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div>
                <h4><?php if(isset($wip_detail->post_title)) { echo $wip_detail->post_title; }?></h4>
                <input id="wip_title" name="wip_title" class="wip-title" type="hidden" value="<?php if(isset($wip_detail->post_title)) { echo $wip_detail->post_title; }?>"/>
            </div>
        <?php endif; ?>


        <div>
            <label for="wip_comment"><?php _e( 'Post a comment to start the conversation', 'bp-portfolio-pro' ); ?></label>
            <div><textarea id="wip_comment" name="wip_comment" class="wip-comment"></textarea></div>
        </div>

        <div>
            <label for="wip_visibility"><?php _e( 'Visibility (required)', 'bp-portfolio-pro' ); ?></label>
            <div>
                <select id="wip_visibility" name="wip_visibility" class="wip-visibility">
                    <?php
                    $options = array(
                        'public'	=> __('Everyone', 'bp-portfolio-pro'),
                        'private'	=> __('Only Me', 'bp-portfolio-pro'),
                        'members'	=> __('All Members', 'bp-portfolio-pro'),
                    );
                    if( bp_is_active( 'friends' ) ){
                        $options['friends'] = __('My Friends', 'bp-portfolio-pro');
                    }

                    $wip_visibility = get_post_meta($wip_id, 'wip_visibility', true);
                    $selected_val = isset($wip_visibility) ? $wip_visibility : '';

                    foreach( $options as $key=>$val ){
                        $selected = $selected_val == $key ? ' selected' : '';
                        echo "<option value='" . esc_attr( $key ) . "' $selected>$val</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="bpcp-buttons">
            <input onclick="location.href='<?php echo bpcp_pro_wip_step_url('add_wip');?>'" id="back_add" class="back-button" type="button" name="back_add" value="<?php _e( 'Back to Previous Step', 'bp-portfolio-pro' ); ?>" />
            <?php _e( '&nbsp;&nbsp;&nbsp;', 'bp-portfolio-pro' ); ?>
            <input id="wip_finish" class="wip-finish" type="submit" name="wip_finish" value="<?php _e( 'Finish', 'bp-portfolio-pro' ); ?>" />
        </div>
    </form>


<?php } ?>
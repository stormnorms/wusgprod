<?php
/**
 * BP Portfolio Pro- Project Settings
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>

<?php if( bpcp_is_accessible() ){ ?>
    <h2 class="entry-title"><?php echo bpcp_add_project_title();?></h2>
    <?php echo bpcp_project_top_menu();?>

    <?php
    $project_id = ( isset($_GET['project_id']) && !empty($_GET['project_id']) ) ? $_GET['project_id'] : '';
    $project_detail = bpcp_get_post_detail($project_id);
    $private_selected = $project_detail->post_status == 'private' ? 'selected="selected"' : '';
    $password_selected = ( $project_detail->post_status == 'publish' && !empty($project_detail->post_password) ) ? 'selected="selected"' : '';
    $public_selected = ( $project_detail->post_status == 'publish' && empty($project_detail->post_password) ) ? 'selected="selected"' : '';
    ?>

    <form class="add-project-details standard-form"  method="post" action="">
        <div>
            <label for="project_visibility"><?php _e( 'Visibility (required)', 'bp-portfolio-pro' ); ?></label>
            <select id="project_visibility" name="project_visibility" class="project-visibility">
                <?php
                $options = array(
                    'public'	=> __('Everyone', 'bp-portfolio-pro'),
                    'private'	=> __('Only Me', 'bp-portfolio-pro'),
                    'members'	=> __('All Members', 'bp-portfolio-pro'),
                );
                if( bp_is_active( 'friends' ) ){
                    //$options['friends'] = __('My Friends', 'bp-portfolio-pro');
                }

                $project_visibility = get_post_meta($project_id, 'project_visibility', true);
                $selected_val = isset($project_visibility) ? $project_visibility : '';

                foreach( $options as $key=>$val ){
                    $selected = $selected_val == $key ? ' selected' : '';
                    echo "<option value='" . esc_attr( $key ) . "' $selected>$val</option>";
                }
                ?>
            </select>
        </div>

        <div id="password_div" style="display: none">
            <label for="project_password"><?php _e( 'Password', 'bp-portfolio-pro' ); ?></label>
            <div>
                <input type="text" id="project_password" name="project_password" class="project-password" value="" />
            </div>
        </div>

        <div>
            <label for="project_comment"><?php _e( 'Discussion', 'bp-portfolio-pro' ); ?></label>
            <div><input id="project_comment" type="checkbox" <?php if($project_detail->comment_status == 'open') { echo 'checked';}?> name="project_comment" class="project-comment" value="on"/><?php _e( 'Allow Comments', 'bp-portfolio-pro' ); ?></div>
        </div>

        <div class="bpcp-buttons">
            <input onclick="location.href='<?php echo bpcp_project_step_url('project_cover');?>'" id="back_cover" class="back-button" type="button" name="back_cover" value="<?php _e( 'Back to Previous Step', 'bp-portfolio-pro' ); ?>" />
            <?php _e( '&nbsp;&nbsp;&nbsp;', 'bp-portfolio-pro' ); ?>
            <input id="project_finish" class="project-finish" type="submit" name="project_finish" value="<?php _e( 'Finish', 'bp-portfolio-pro' ); ?>" />
        </div>
    </form>


<?php } ?>

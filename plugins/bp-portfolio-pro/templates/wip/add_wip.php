<?php
/**
 * BP Portfolio Pro- WIP Content
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>

<?php
    if( bpcp_is_accessible() ) { ?>
    <h2 class="entry-title"><?php echo bpcp_pro_add_wip_title();?></h2>
    <p><?php echo bpcp_pro_add_wip_subtitle(); ?></p>

    <form class="add-wip-content standard-form" method="post" action="">

        <div class="bpcp-li-no-margin uploaded-images"></div>

        <div class="bpcp-buttons"><input type="button" id="upload_wip_image" class="upload-image" rel="201" value="<?php _e( 'Upload Your Image', 'bp-portfolio-pro' ); ?>" /></div>

        <div class="bpcp-buttons">
            <?php $wip_id = ( isset($_GET['wip_id']) && !empty($_GET['wip_id']) ) ? $_GET['wip_id'] : ''; ?>
            <input id="create_wip" class="create-wip" type="submit" name="create_wip" value="<?php if(!empty($wip_id)) {  _e( 'Add Revision and Continue', 'bp-portfolio-pro' ); } else{   _e( 'Create WIP and Continue', 'bp-portfolio-pro' ); } ?>"/>
        </div>
    </form>


<?php } ?>

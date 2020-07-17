<?php
/**
 * BP Portfolio Pro- Project Content
 *
 * @package WordPress
 * @subpackage BP Portfolio Pro
 */
?>

<?php if( bpcp_is_accessible() ){ ?>
    <h2 class="entry-title"><?php echo bpcp_add_project_title();?></h2>
    <?php echo bpcp_project_top_menu(); ?>

    <form class="add-project-content standard-form" method="post" action="">

        <div class="bpcp-li-no-margin uploaded-images">
            <?php
            $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';
            if(isset($project_id) && !empty($project_id)):
                $get_all_attachment = bpcp_pro_get_project_items($project_id);
                if( !empty($get_all_attachment) ): ?>
                    <?php
                    foreach($get_all_attachment as $attachment):
                        $single = bpcp_pro_single_attachment_detail($attachment, 'portfolio-thumbnail');
                        ?>

                        <?php if(substr_count($attachment, 'video') > 0){
                        $val = bpcp_pro_get_video_by_item_id($attachment, $project_id);
                        $video_caption = get_post_meta($project_id, 'attach-'.$attachment, true);
                        ?>
                        <ul id="attach-<?php echo $attachment;?>" class="bpcp-li-left">
                            <li class="project_item">
                                <?php $embed_code = wp_oembed_get($val, array('width'=>250)); echo $embed_code; ?>
                            </li>
                            <li>
                                <input type="hidden" name="embeded_url" value="<?php echo $val;?>" />
                                <label for="attachment_caption"><?php _e( 'Caption', 'bp-portfolio-pro' ); ?></label>
                                <input type="hidden" name="video_item[vid][]" value="attach-<?php echo $attachment;?>" />
                                <input type="text" name="video_item[vcaption][]" class="attachment-caption" value="<?php echo $video_caption;?>" />
                                <a href="#" class="delete-video"><?php _e( 'Delete', 'bp-portfolio-pro' ); ?></a>
                            </li>
                        </ul>
                        <?php }else{
                        
                        if( $single['type'] != 'image' && $single['type'] != 'audio' ){ continue; } ?>

                        <ul id="attach-<?php echo $attachment;?>" class="bpcp-li-left">
                            <li class="project_item">
                                <?php if($single['type'] == 'image'){ ?>
                                    <img class="display-image" alt="<?php echo $single['alt'];?>" src="<?php echo $single['src'];?>"/>
                                <?php } ?>

                                <?php if($single['type'] == 'audio'){ ?>
                                    <!--[if lt IE 9]><script>document.createElement('audio');</script><![endif]-->
                                    <audio class="wp-audio-shortcode" preload="none" controls="controls"><source type="audio/mpeg" src="<?php echo $single['src'];?>?_=1" /><a href="<?php echo $single['src'];?>"><?php echo $single['src'];?></a></audio>
                                <?php } ?>
                            </li>
                            <li>
                                <input type="hidden" name="atachment_id" value="<?php echo $single['ID'];?>" />
                                <label for="attachment_caption"><?php _e( 'Caption', 'bp-portfolio-pro' ); ?></label>
                                <input type="hidden" name="photo_item[pid][]" value="<?php echo $single['ID'];?>" />
                                <input type="text" name="photo_item[pcaption][]" class="attachment-caption" value="<?php echo $single['caption'];?>" />
                                <a href="#" class="delete-image"><?php _e( 'Delete', 'bp-portfolio-pro' ); ?></a>
                            </li>
                        </ul>
                        <?php } ?>

                    <?php endforeach; ?>

                <?php endif;

            endif;?>
        </div>


        <div class="bpcp-buttons">
            <div class="bpcp-media-upload-title">
                <?php if(count($get_all_attachment) > 0): _e( 'Add More Media', 'bp-portfolio-pro' ); else: _e( 'Upload Media', 'bp-portfolio-pro' ); endif; ?>
            </div>
            <input type="button" id="upload_image" class="upload-image" rel="201" value="<?php _e('Photo', 'bp-portfolio-pro');?>" />
            <input type="button" id="upload_song" class="upload-image" rel="201" value="<?php _e('Audio', 'bp-portfolio-pro');?>" />
            <a class="btn bpcp-modal button" href="#bpcp_pro_add_video"><?php _e('Video', 'bp-portfolio-pro');?></a>
        </div>

        <div class="bpcp-buttons">
            <input onclick="location.href='<?php echo bpcp_project_step_url('add_project'); ?>'"
                   id="back_details" class="back-button" type="button" name="back_details"
                   value="<?php _e( 'Back to Previous Step', 'bp-portfolio-pro' ); ?>"/>
            <?php _e( '&nbsp;&nbsp;&nbsp;', 'bp-portfolio-pro' ); ?>
            <input id="next_cover" class="next-cover" type="submit" name="next_cover"
                   value="<?php _e( 'Next Step', 'bp-portfolio-pro' ); ?>"/>
        </div>
    </form>

    <!-- Video upload popup -->
    <div id="bpcp_pro_add_video" class="bbcp-white-popup-block mfp-hide add-video">
        <h3><?php _e( 'Add Video URL', 'bp-portfolio-pro' ); ?></h3>
        <p>
            <form class="add-video-details standard-form"  method="post" action="">
                <div class="bpcp-li-no-margin bpcp-distance-bottom">
                    <ul class="bpcp-li-left">
                        <li class="field-video-embed-title">
                            <div>
                                <input type="hidden" name="current_project_id" id="current_project_id" value="<?php echo $_GET['project_id'];?>" />
                                <input id="video_embed_url" name="video_embed_url" class="video-embed-url" type="text" value=""/>
                            </div>
                        </li>
                        <li>
                            <input id="video_add" class="video-add" type="button" name="video_add" value="<?php _e( 'Save', 'bp-portfolio-pro' ); ?>" />
                            <div class="video_error_message"></div>
                        </li>
                    </ul>
                </div>
            </form>
        </p>
    </div>

<?php } ?>
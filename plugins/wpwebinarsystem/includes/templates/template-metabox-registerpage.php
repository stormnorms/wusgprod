<?php $this->previewButton($post, 'register'); ?>
<div class="webinar_clear_fix"></div>
<div id="regp-accordian" class="ws-accordian">
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('General', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-field">
            <label for="regp_bckg_clr"><?php _e('Page Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_bckg_clr" class="color-field" id="regp_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_bckg_img"><?php _e('Page Background image', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_bckg_img" id="regp_bckg_img" class="upload_image_button" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_bckg_img', true)); ?>">
            <button class="button wswebinar_uploader" resultId="regp_bckg_img" uploader_title="<?php _e('Registration Page Background Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="regp_show_content_setion"><?php _e('Show Content Section', WebinarSysteem::$lang_slug); ?></label>
	    <?php $regp_show_contentbox = get_post_meta($post->ID, '_wswebinar_regp_show_content_setion', true); ?>
            <input class="wsweb_listen_enability" data-style-collect="true" type="checkbox" data-switch="true" name="regp_show_content_setion" id="regp_show_content_setion" value="yes" <?php echo ($regp_show_contentbox == "yes" | empty($regp_show_contentbox)) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div id="show_content_settings" style="<?php echo ($regp_show_contentbox == 'yes' ? 'display:block;' : 'display: none;'); ?>;">
            <div class="form-field">
                <label for="regp_vidurl_type"><?php _e('Content type', WebinarSysteem::$lang_slug); ?></label>
		<?php $regp_vidurl_type = get_post_meta($post->ID, '_wswebinar_regp_vidurl_type', true); ?>
                <select data-style-collect="true" class="form-control lookoutImageButton" valueField="regp_vidurl" imageUploadButton="regp_vidurl_upload_button" name="regp_vidurl_type" id="regp_vidurl_type">
                    <option value="youtube" <?php echo $regp_vidurl_type == "youtube" ? 'selected' : ''; ?>>Youtube</option>
                    <option value="vimeo" <?php echo $regp_vidurl_type == "vimeo" ? 'selected' : ''; ?>>Vimeo</option>
                    <option value="image" <?php echo $regp_vidurl_type == "image" ? 'selected' : ''; ?>><?php _e('Image', WebinarSysteem::$lang_slug); ?></option>
                    <option value="file" <?php echo $regp_vidurl_type == "file" ? 'selected' : ''; ?>><?php _e('File', WebinarSysteem::$lang_slug) ?> (MP4/WebM/OGG)</option>
                </select>
            </div>

            <div class="form-field">
                <label for="regp_vidurl"><?php _e('Video or Image URL', WebinarSysteem::$lang_slug); ?></label>
                <input type="text" data-style-collect="true" name="regp_vidurl" id="regp_vidurl" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_vidurl', true)); ?>">

                <button class="button wswebinar_uploader" style="<?php echo (!empty($regp_vidurl_type) && $regp_vidurl_type == 'image') ? '' : 'display:none;' ?>" id="regp_vidurl_upload_button" resultId="regp_vidurl" checktype="yes" uploader_title="<?php _e('Registration Page Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>

                <span class="wswaiticon"><img src="<?php echo plugin_dir_url($this->_FILE_); ?>includes/images/wait.GIF"></span>
                <p class="description regp_vidurl_desc regp_vidurl_for_youtube" style="<?php echo (empty($regp_vidurl_type) || $regp_vidurl_type == 'youtube') ? '' : 'display:none'; ?>"><?php _e('Paste Youtube URL here (Eg: https://www.youtube.com/watch?v=3TkgTEfx9XM)', WebinarSysteem::$lang_slug); ?></p>
                <p class="description regp_vidurl_desc regp_vidurl_for_vimeo" style="<?php echo (empty($regp_vidurl_type) || $regp_vidurl_type == 'vimeo') ? '' : 'display:none'; ?>"><?php _e('Paste Vimeo video ID here (Eg: 129673042)', WebinarSysteem::$lang_slug); ?></p>
                <p class="description regp_vidurl_desc regp_vidurl_for_image" style="<?php echo $regp_vidurl_type == 'image' ? '' : 'display:none'; ?>"><?php _e('Image URL (Eg: https://example.com/images/the_image.jpg)', WebinarSysteem::$lang_slug); ?></p>
                <p class="description regp_vidurl_desc regp_vidurl_for_file" style="<?php echo $regp_vidurl_type == 'file' ? '' : 'display:none'; ?>"><?php _e("It's best to host your video file outside your website to save bandwidth.", WebinarSysteem::$lang_slug); ?></p>
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="regp_video_auto_play_yn"><?php _e('Video autoplay', WebinarSysteem::$lang_slug); ?></label>
		<?php $regp_video_auto_play_yn_value = get_post_meta($post->ID, '_wswebinar_regp_video_auto_play_yn', true); ?>
                <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_video_auto_play_yn" id="regp_video_auto_play_yn" value="yes" <?php echo ($regp_video_auto_play_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
                <p class="description"><?php _e('Some browsers will restrict the autoplay functionality', WebinarSysteem::$lang_slug) ?></p>
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="regp_video_controls_yn"><?php _e('Show controls', WebinarSysteem::$lang_slug); ?></label>
		<?php $regp_video_controls_yn_value = get_post_meta($post->ID, '_wswebinar_regp_video_controls_yn', true); ?>

                <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_video_controls_yn" id="regp_video_controls_yn" value="yes" <?php echo ($regp_video_controls_yn_value == "yes" ) ? 'checked' : ''; ?> >
                <p class="description"><?php _e('Controls will stay visible for webinar host, but will be hidden for your attendees', WebinarSysteem::$lang_slug) ?></p>
                <div class="webinar_clear_fix"></div>
            </div>

	    <div class="form-group bigplaybtn-yn" <?php echo WebinarSysteemHelperFunctions::isMediaElementJSPlayer($regp_vidurl_type) ? '' : 'style="display:none;"' ?>>
		<label for="regp_bigplaybtn_yn"><?php _e('Show big play button', WebinarSysteem::$lang_slug); ?></label>
		<?php $regp_bigplaybtn_value = get_post_meta($post->ID, '_wswebinar_regp_bigplaybtn_yn', true); ?>

		<input data-style-collect="true" type="checkbox" data-switch="true" name="regp_bigplaybtn_yn" id="livep_bigplaybtn_yn" value="yes" <?php echo ($regp_bigplaybtn_value == "yes" || empty($regp_bigplaybtn_value)) ? 'checked="checked"' : ''; ?> >
		<div class="webinar_clear_fix"></div>
	    </div> 
        </div>
        <div class="wsseparator"></div>

        <div class="form-field">
            <label for="regp_regtitle_clr"><?php _e('Title Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regtitle_clr" class="color-field" id="regp_regtitle_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regtitle_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="regp_regmeta_clr"><?php _e('Date/Time Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regmeta_clr" class="color-field" id="regp_regmeta_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regmeta_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>

   
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Custom Registration Fields', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
	<?php
	$savedCustomFieldJSON = get_post_meta($post->ID, '_wswebinar_regp_custom_field_json', true);
	$customFieldJSON = empty($savedCustomFieldJSON) ? '[]' : $savedCustomFieldJSON;
	?>
	<input type="hidden" name="regp_custom_field_json" value='<?php echo $customFieldJSON; ?>'>
	<div class="form-field">
	    <div class="ws-custom-field button" data-type="text"><i class="fa fa-i-cursor"></i> Input Field</div>
	    <div class="ws-custom-field button" data-type="tel"><i class="fa fa-phone-square"></i> Phone Field</div>
<!--	    <div class="ws-custom-field button" data-type="radio"><i class="fa fa-check-square"></i> Radio Box</div>-->
	    <div class="ws-custom-field button" data-type="checkbox"><i class="fa fa-check-circle-o"></i> Check Box</div>
	</div>
	<ul class="ws-custom-field-container">
	    
	</ul>
	<div style="clear: both;"></div>
    </div>
    
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('GDPR Opt-in', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
         <div class="form-group">
            <label for="regp_wc_gdpr_optin_yn"><?php _e('Enable GDPR Opt-in for Paid webinars', WebinarSysteem::$lang_slug); ?></label>
            <?php $regp_wc_gdpr_optin_yn_value = get_post_meta($post->ID, '_wswebinar_regp_wc_gdpr_optin_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_wc_gdpr_optin_yn" id="regp_wc_gdpr_optin_yn" value="yes" <?php echo ($regp_wc_gdpr_optin_yn_value == "yes" ) ? 'checked' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
         <div class="form-group">
            <label for="regp_gdpr_optin_yn"><?php _e('Enable GDPR Opt-in for Non-Paid webinars', WebinarSysteem::$lang_slug); ?></label>
            <?php $regp_gdpr_optin_yn_value = get_post_meta($post->ID, '_wswebinar_regp_gdpr_optin_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_gdpr_optin_yn" id="regp_gdpr_optin_yn" value="yes" <?php echo ($regp_gdpr_optin_yn_value == "yes" ) ? 'checked' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-group">
        <?php $regp_gdpr_optin_text_value = get_post_meta($post->ID, '_wswebinar_regp_gdpr_optin_text', true); 
        if(empty($regp_gdpr_optin_text_value)) {
            $regp_gdpr_optin_text_value = 'I agree my personal information will be stored on your website for the use of this webinar and to send me notifications about the event'; 
		}
		?>
            <label for="regp_gdpr_optin_text"><?php _e('GDPR Opt-in Text', WebinarSysteem::$lang_slug); ?></label>
            <textarea data-style-collect="true" name="regp_gdpr_optin_text" id="regp_gdpr_optin_text" width="325px" height="75px"><?php echo $regp_gdpr_optin_text_value; ?></textarea>
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
    
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Form & Tab Layout', WebinarSysteem::$lang_slug) ?></h3>
    <div style="clear: both;" class="ws-accordian-section">

	<?php
	$show_contentbox = get_post_meta($post->ID, '_wswebinar_regp_show_content_setion', true);
	$show_position = ($show_contentbox == 'no');
	?>
        <div class="form-field" id="regp_position_chooser" style="<?php echo ($show_position ? 'display: block;' : 'display: none;'); ?> ">
            <label for="regp_position"><?php _e('Position', WebinarSysteem::$lang_slug); ?></label>
	    <?php $regp_position = get_post_meta($post->ID, '_wswebinar_regp_position', true); ?>
            <select data-style-collect="true" class="form-control" valueField="regp_position" imageUploadButton="regp_vidurl_upload_button" name="regp_position" id="regp_position">
                <option value="left" <?php echo $regp_position == "left" ? 'selected' : ''; ?>>Left</option>
                <option value="center" <?php echo $regp_position == "center" ? 'selected' : ''; ?>>Center</option>
                <option value="right" <?php echo $regp_position == "right" | empty($regp_position) ? 'selected' : ''; ?>>Right</option>                
            </select>
        </div>
        <div class="form-field">
            <label for="regp_regformbckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformbckg_clr" class="color-field" id="regp_regformbckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformbckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="regp_regformborder_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformborder_clr" class="color-field" id="regp_regformborder_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformborder_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_regformfont_clr"><?php _e('Font color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformfont_clr" class="color-field" id="regp_regformfont_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformfont_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_tabbg_clr"><?php _e('Tab Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_tabbg_clr" class="color-field" id="regp_tabbg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_tabbg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_tabtext_clr"><?php _e('Tab Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_tabtext_clr" class="color-field" id="regp_tabtext_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_tabtext_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_tabone_text"><?php _e('Register Tab Text', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_tabone_text" id="regp_tabone_text" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_tabone_text', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_tabtwo_text"><?php _e('Login Tab Text', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_tabtwo_text" id="regp_tabtwo_text" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_tabtwo_text', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Register Tab', WebinarSysteem::$lang_slug) ?></h3>
        <div style="clear: both;" class="ws-accordian-section">
        <div class="form-field">
        	<label for="regp_hide_regtab"><?php _e('Hide Registration Form', WebinarSysteem::$lang_slug); ?></label>
        	<?php $regp_hide_regtab = get_post_meta($post->ID, '_wswebinar_regp_hide_regtab', true);?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_hide_regtab" id="regp_hide_regtab" value="yes" <?php echo ($regp_hide_regtab == "yes") ? 'checked="checked"' : ''; ?> data-on-text="Yes" data-off-text="No">
            <div class="webinar_clear_fix"></div>
        </div>
        
        <div class="form-field">
            <label for="regp_regformtitle"><?php _e('Register Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformtitle" id="regp_regformtitle" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformtitle', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_regformtxt"><?php _e('Register Text', WebinarSysteem::$lang_slug); ?></label>
            <textarea data-style-collect="true" name="regp_regformtxt" id="regp_regformtxt"><?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformtxt', true)); ?></textarea>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_regformbtn_clr"><?php _e('Button Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformbtn_clr" class="color-field" id="regp_regformbtn_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformbtn_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_regformbtnborder_clr"><?php _e('Button Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformbtnborder_clr" class="color-field" id="regp_regformbtnborder_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformbtnborder_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_regformbtntxt_clr"><?php _e('Button Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_regformbtntxt_clr" class="color-field" id="regp_regformbtntxt_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_regformbtntxt_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_ctatext"><?php _e('CTA Button Text', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_ctatext" id="regp_ctatext" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_ctatext', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Login Tab', WebinarSysteem::$lang_slug) ?></h3>
    <div style="clear: both;" class="ws-accordian-section">
        
        <div class="form-field">
        	<label for="regp_hide_logintab"><?php _e('Hide Login Form', WebinarSysteem::$lang_slug); ?></label>
        	<?php $regp_hide_logintab = get_post_meta($post->ID, '_wswebinar_regp_hide_logintab', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_hide_logintab" id="regp_hide_logintab" value="yes" <?php echo ($regp_hide_logintab == "yes" ) ? 'checked' : ''; ?> data-on-text="Yes" data-off-text="No">
            <div class="webinar_clear_fix"></div>
	
        </div>
        
        <div class="form-field">
            <label for="regp_loginformtitle"><?php _e('Login Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_loginformtitle" id="regp_loginformtitle" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_loginformtitle', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_loginformtxt"><?php _e('Login Text', WebinarSysteem::$lang_slug); ?></label>
            <textarea data-style-collect="true" name="regp_loginformtxt" id="regp_loginformtxt"><?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_loginformtxt', true)); ?></textarea>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_loginformbtn_clr"><?php _e('Button Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_loginformbtn_clr" class="color-field" id="regp_loginformbtn_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_loginformbtn_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_loginformbtnborder_clr"><?php _e('Button Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_loginformbtnborder_clr" class="color-field" id="regp_loginformbtnborder_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_loginformbtnborder_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_loginformbtntxt_clr"><?php _e('Button Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_loginformbtntxt_clr" class="color-field" id="regp_loginformbtntxt_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_loginformbtntxt_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="regp_loginctatext"><?php _e('Login Button Text', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_loginctatext" id="regp_loginctatext" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_loginctatext', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Description', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-group">
            <label for="regp_show_description"><?php _e('Show Description Section', WebinarSysteem::$lang_slug); ?></label>
	    <?php $regp_show_description = get_post_meta($post->ID, '_wswebinar_regp_show_description', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="regp_show_description" id="regp_show_description" value="yes" <?php echo ($regp_show_description == "yes" || empty($regp_show_description)) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="regp_wbndesc_clr"><?php _e('Description text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_wbndesc_clr" class="color-field" id="regp_wbndesc_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_wbndesc_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="regp_wbndescbck_clr"><?php _e('Description background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_wbndescbck_clr" class="color-field" id="regp_wbndescbck_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_wbndescbck_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="regp_wbndescborder_clr"><?php _e('Description border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="regp_wbndescborder_clr" class="color-field" id="regp_wbndescborder_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_regp_wbndescborder_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
</div>
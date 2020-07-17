<?php $this->previewButton($post, 'replay'); ?>
<div class="webinar_clear_fix"></div>
<div id="livep-accordian" class="ws-accordian">
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('General', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-field">
            <label for="replayp_title_show_yn"><?php _e('Hide Title', WebinarSysteem::$lang_slug); ?></label>
	    <?php $replayp_title_show_yn_value = get_post_meta($post->ID, '_wswebinar_replayp_title_show_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="replayp_title_show_yn" id="replayp_title_show_yn" value="yes" <?php echo ($replayp_title_show_yn_value == "yes" ) ? 'checked' : ''; ?> data-on-text="Yes" data-off-text="No">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="replayp_title_clr"><?php _e('Title color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="replayp_title_clr" class="color-field" id="replayp_title_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_title_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="replayp_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="replayp_bckg_clr" class="color-field" id="replayp_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="replayp_bckg_img"><?php _e('Background image', WebinarSysteem::$lang_slug); ?></label>         
            <input data-style-collect="true" type="text" name="replayp_bckg_img" id="replayp_bckg_img" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_bckg_img', true)); ?>">
            <button class="button wswebinar_uploader" resultId="replayp_bckg_img" uploader_title="<?php _e('Replay Page Background Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="wsseparator"></div>

        <div class="form-field">
            <label for="replayp_vidurl_type"><?php _e('Webinar type', WebinarSysteem::$lang_slug); ?></label>
	    <?php $replayp_vidurl_type = get_post_meta($post->ID, '_wswebinar_replayp_vidurl_type', true); ?>
            <select data-style-collect="true" class="form-control lookoutImageButton" valueField="replayp_vidurl" imageUploadButton="replayp_vidurl_upload_button" name="replayp_vidurl_type" id="replayp_vidurl_type">
                <option value="hoa" <?php echo $replayp_vidurl_type == "hoa" ? 'selected' : ''; ?>>Hangouts on Air</option>
                <option value="youtube" <?php echo $replayp_vidurl_type == "youtube" ? 'selected' : ''; ?>>YouTube</option>
                <option value="youtubelive" <?php echo $replayp_vidurl_type == "youtubelive" ? 'selected' : ''; ?>>YouTube <?php _e('Live', WebinarSysteem::$lang_slug) ?></option>
                <option value="vimeo" <?php echo $replayp_vidurl_type == "vimeo" ? 'selected' : ''; ?>>Vimeo</option>
                <option value="image" <?php echo $replayp_vidurl_type == "image" ? 'selected' : ''; ?>><?php _e('Image', WebinarSysteem::$lang_slug) ?></option>
                <option value="file" <?php echo $replayp_vidurl_type == "file" ? 'selected' : ''; ?>><?php _e('File', WebinarSysteem::$lang_slug) ?> (MP4/WebM/OGG)</option>
                <option value="rtmp" <?php echo $replayp_vidurl_type == "rtmp" ? 'selected' : ''; ?>><?php _e('RTMP Stream', WebinarSysteem::$lang_slug) ?></option>
                <option value="hls" <?php echo $replayp_vidurl_type == "hls" ? 'selected' : ''; ?>><?php _e('HLS Stream', WebinarSysteem::$lang_slug) ?></option>
                <option value="iframe" <?php echo $replayp_vidurl_type == "iframe" ? 'selected' : ''; ?>><?php _e('Inline Frame (iframe)', WebinarSysteem::$lang_slug) ?></option>
            </select>
            <p class="description replayp_vidurl_desc replayp_vidurl_for_hoa"  style="margin-top:10px;<?php echo (empty($replayp_vidurl_type) || $replayp_vidurl_type == 'hoa') ? '' : 'display:none'; ?>"><a class="yt_hoa_button" href="https://www.youtube.com/my_live_events?action_create_live_event" target="_blank"><i class="wbn-icon wbnicon-facetime-video"></i> <?php _e('Start HOA broadcast from Youtube website', WebinarSysteem::$lang_slug); ?></a></p>
            <p class="description replayp_vidurl_desc replayp_vidurl_for_youtubelive"  style="margin-top: 10px;<?php echo ($replayp_vidurl_type == 'youtubelive') ? '' : 'display:none'; ?>"><a class="yt_hoa_button" href="https://www.youtube.com/my_live_events?action_create_live_event" target="_blank" class="btn btn-info" role="button"><i class="wbn-icon wbnicon-facetime-video"></i> <?php _e('Start a livestream with Youtube Live', WebinarSysteem::$lang_slug); ?></a></p>
        </div>

        <div class="form-field">
            <label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_hoa" style="<?php echo (empty($replayp_vidurl_type) || $replayp_vidurl_type == 'hoa') ? '' : 'display:none'; ?>"><?php _e('Hangouts on Air URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_youtube replayp_vidurl_for_youtubelive" style="<?php echo ( $replayp_vidurl_type == 'youtube' || $replayp_vidurl_type == 'youtubelive' ) ? '' : 'display:none'; ?>"><?php _e('Youtube URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_vimeo" style="<?php echo $replayp_vidurl_type == 'vimeo' ? '' : 'display:none'; ?>"><?php _e('Vimeo ID', WebinarSysteem::$lang_slug); ?></label>
        	<label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_image" style="<?php echo $replayp_vidurl_type == 'image' ? '' : 'display:none'; ?>"><?php _e('Image URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_iframe" style="<?php echo $replayp_vidurl_type == 'iframe' ? '' : 'display:none'; ?>"><?php _e('Iframe Source URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_file" style="<?php echo $replayp_vidurl_type == 'file' ? '' : 'display:none'; ?>"><?php _e('File URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_rtmp" style="<?php echo $replayp_vidurl_type == 'rtmp' ? '' : 'display:none'; ?>"><?php _e('RTMP Server URL', WebinarSysteem::$lang_slug); ?></label>
            <label for="replayp_vidurl" class="replayp_vidurl_desc replayp_vidurl_for_hls" style="<?php echo $replayp_vidurl_type == 'hls' ? '' : 'display:none'; ?>"><?php _e('HLS Server URL', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="replayp_vidurl" id="replayp_vidurl" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_vidurl', true)); ?>">
            <button id="replayp_vidurl_upload_button" class="button wswebinar_uploader" resultId="replayp_vidurl" checktype="yes" uploader_title="<?php _e('Registration Page Video Image', WebinarSysteem::$lang_slug); ?>" style="<?php echo (!empty($replayp_vidurl_type) && $replayp_vidurl_type == 'image') ? '' : 'display:none;' ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>
            <span class="wswaiticon"><img src="<?php echo plugin_dir_url($this->_FILE_); ?>includes/images/wait.GIF"></span>


            <p id="replayp_yt_description" class="description replayp_vidurl_desc replayp_vidurl_for_youtube" style="<?php echo (empty($replayp_vidurl_type) || $replayp_vidurl_type == 'youtube'  || $replayp_vidurl_type == 'youtubelive' || $replayp_vidurl_type == 'hoa' ) ? '' : 'display:none'; ?>"><?php _e('Paste Youtube URL here (Eg: https://www.youtube.com/watch?v=3TkgTEfx9XM OR http://youtu.be/CqlB2SiG-ac)', WebinarSysteem::$lang_slug); ?></p>
            <p class="description replayp_vidurl_desc replayp_vidurl_for_vimeo" style="<?php echo $replayp_vidurl_type == 'vimeo' ? '' : 'display:none'; ?>"><?php _e('Paste Vimeo video ID here (Eg: 129673042))', WebinarSysteem::$lang_slug); ?></p>
            <p class="description replayp_vidurl_desc replayp_vidurl_for_image" style="<?php echo $replayp_vidurl_type == 'image' ? '' : 'display:none'; ?>"><?php _e('Image URL (Eg: https://example.com/images/the_image.jpg)', WebinarSysteem::$lang_slug); ?></p>
            <p class="description replayp_vidurl_desc replayp_vidurl_for_file replayp_vidurl_for_rtmp" style="<?php echo ($replayp_vidurl_type == 'file' || $replayp_vidurl_type == 'rtmp') ? '' : 'display:none'; ?>"><?php _e("It's best to host your video file outside your website to save bandwidth.", WebinarSysteem::$lang_slug); ?></p>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group video-auto-play-yn" <?php echo $replayp_vidurl_type == 'iframe' ? 'style="display:none;"' : "" ?>>
            <label for="replayp_video_auto_play_yn"><?php _e('Video autoplay', WebinarSysteem::$lang_slug); ?></label>
	    <?php $replayp_video_auto_play_yn_value = get_post_meta($post->ID, '_wswebinar_replayp_video_auto_play_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="replayp_video_auto_play_yn" id="replayp_video_auto_play_yn" value="yes" <?php echo ($replayp_video_auto_play_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <p class="description"><?php _e('Some browsers will restrict the autoplay functionality', WebinarSysteem::$lang_slug) ?></p>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="replayp_video_controls_yn"><?php _e('Show controls', WebinarSysteem::$lang_slug); ?></label>
	    <?php $replayp_video_controls_yn_value = get_post_meta($post->ID, '_wswebinar_replayp_video_controls_yn', true); ?>

            <input class="wsweb_replayp_controls_listen_enability" data-style-collect="true" type="checkbox" data-switch="true" name="replayp_video_controls_yn" id="replayp_video_controls_yn" value="yes" <?php echo ($replayp_video_controls_yn_value == "yes" ) ? 'checked' : ''; ?> >
            <p class="description"><?php _e('Controls will stay visible for webinar host, but will be hidden for your attendees', WebinarSysteem::$lang_slug) ?></p>
            <div class="webinar_clear_fix"></div>
        </div>
    
    <div id="replayp_show_fullscreen_control_setting" style="<?php echo ($replayp_video_controls_yn_value == 'yes' ? 'display:none;' : 'display: block;'); ?>;">
	    <div class="form-field">
	        <label for="replayp_fullscreen_control"><?php _e('Enable Fullscreen', WebinarSysteem::$lang_slug); ?></label>
		<?php $replayp_fullscreen_control_yn_value = get_post_meta($post->ID, '_wswebinar_replayp_fullscreen_control', true); ?>
		    <input data-style-collect="true" type="checkbox" data-switch="true" name="replayp_fullscreen_control" id="replayp_fullscreen_control" value="yes" <?php echo ($replayp_fullscreen_control_yn_value == "yes") ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
    	</div>
	</div>
	
	<div class="form-group bigplaybtn-yn" <?php echo WebinarSysteemHelperFunctions::isMediaElementJSPlayer($replayp_vidurl_type) ? '' : 'style="display:none;"' ?>>
            <label for="replayp_bigplaybtn_yn"><?php _e('Show big play button', WebinarSysteem::$lang_slug); ?></label>
	    <?php $replayp_bigplaybtn_value = get_post_meta($post->ID, '_wswebinar_replayp_bigplaybtn_yn', true); ?>

            <input data-style-collect="true" type="checkbox" data-switch="true" name="replayp_bigplaybtn_yn" id="replayp_bigplaybtn_yn" value="yes" <?php echo ($replayp_bigplaybtn_value == "yes") ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
	<div class="form-group bigplaybtn-yn" <?php echo WebinarSysteemHelperFunctions::isMediaElementJSPlayer($replayp_vidurl_type) ? '' : 'style="display:none;"' ?>>
            <label for="replayp_simulate_video_yn"><?php _e('Simulate webinar content', WebinarSysteem::$lang_slug); ?></label>
	    <?php $replayp_simulate_video_value = get_post_meta($post->ID, '_wswebinar_replayp_simulate_video_yn', true); ?>

            <input data-style-collect="true" type="checkbox" data-switch="true" name="replayp_simulate_video_yn" id="replayp_simulate_video_yn" value="yes" <?php echo ($replayp_simulate_video_value == "yes") ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
    <?php WebinarSysteemMetabox::_page_styling($post, FALSE); ?>
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Call to action', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">

        <div class="form-group">
	    <?php $callto_action_saved = get_post_meta($post->ID, '_wswebinar_replayp_call_action', true); ?>
            <label><?php _e('Activate CTA', WebinarSysteem::$lang_slug); ?></label>

            <label class="radio"><input data-style-collect="true" type="radio" name="replayp_call_action" value="manual" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_action_saved == 'manual' || $callto_action_saved == ''); ?> >Manual</label>
            <label class="radio" ><input data-style-collect="true" type="radio" name="replayp_call_action" value="aftertimer" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_action_saved == 'aftertimer'); ?>>After amount of time</label>
            <div class="webinar_clear_fix"></div>
        </div>

        <div id="replayp_call_action_atertime" style="<?php echo ($callto_action_saved == 'manual' || $callto_action_saved == '' ? 'display: none;' : ''); ?> ">
            <div class="form-group">
                <label for="replayp_cta_show_after"><?php _e('Show CTA after (minutes)', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="number" min="1" name="replayp_cta_show_after" id="replayp_cta_show_after" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_cta_show_after', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <div id="replayp_call_action_manual" style="<?php echo ($callto_action_saved == 'manual' || $callto_action_saved == '' ? 'display: block;' : 'display: none;'); ?>">
            <div class="form-group">
		<label for="replayp_manual_show_cta"><?php _e('Show CTA', WebinarSysteem::$lang_slug); ?></label>
		<?php $replayp_cta_manual_status = get_post_meta($post->ID, '_wswebinar_replayp_manual_show_cta', true); ?>
		<input data-switch="true" data-style-collect="true" type="checkbox" name="replayp_manual_show_cta" id="replayp_manual_show_cta" value="yes" <?php echo ($replayp_cta_manual_status == "yes" ) ? 'checked="checked"' : ''; ?> >
		<div class="webinar_clear_fix"></div>
            </div>
        </div>

        <div class="form-group">
	    <?php $callto_cta_type = get_post_meta($post->ID, '_wswebinar_replayp_call_action_ctatype', true); ?>
            <label><?php _e('CTA Type', WebinarSysteem::$lang_slug); ?></label>

            <label class="radio"><input data-style-collect="true" type="radio" name="replayp_call_action_ctatype" value="button" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_cta_type == 'button' || $callto_cta_type == ''); ?> >Button</label>
            <label class="radio" ><input data-style-collect="true" type="radio" name="replayp_call_action_ctatype" value="txt_field" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_cta_type == 'txt_field'); ?>>Text Field</label>
            <div class="webinar_clear_fix"></div>
        </div>

        <div id="replayp_callto_action_button" style="<?php echo ($callto_cta_type == 'button' || $callto_cta_type == '' ? '' : 'display: none;'); ?>">

            <div class="form-group">
                <label for="replayp_ctabtn_clr"><?php _e('Background Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_clr" class="color-field" id="replayp_ctabtn_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_hover_clr"><?php _e('Hover Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_hover_clr" class="color-field" id="replayp_ctabtn_hover_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_hover_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_border_clr"><?php _e('Border Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_border_clr" class="color-field" id="replayp_ctabtn_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_border_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_txt_clr"><?php _e('Text Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_txt_clr" class="color-field" id="replayp_ctabtn_txt_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_txt_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_hover_txt_clr"><?php _e('Hover Text Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_hover_txt_clr" class="color-field" id="replayp_ctabtn_hover_txt_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_hover_txt_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_brdr_radius"><?php _e('Border Radius', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="number" max="100" min="0" name="replayp_ctabtn_brdr_radius" id="replayp_ctabtn_brdr_radius" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_brdr_radius', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_url"><?php _e('Button URL', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_url" id="replayp_ctabtn_url" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_url', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="replayp_ctabtn_txt"><?php _e('Button Text', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="replayp_ctabtn_txt" id="replayp_ctabtn_txt" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctabtn_txt', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <div id="replayp_callto_action_txtfied" style="<?php echo ($callto_cta_type == 'txt_field' ? '' : 'display: none;'); ?>">

	    <div class="form-field">
		<label for="replayp_ctatxt_fld_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
		<input data-style-collect="true" type="text" name="replayp_ctatxt_fld_bckg_clr" class="color-field" id="replayp_ctatxt_fld_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctatxt_fld_bckg_clr', true)); ?>">
		<div class="webinar_clear_fix"></div>
	    </div>

	    <div class="form-field">
		<label for="replayp_ctatxt_fld_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
		<input data-style-collect="true" type="text" name="replayp_ctatxt_fld_border_clr" class="color-field" id="replayp_ctatxt_fld_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctatxt_fld_border_clr', true)); ?>">
		<div class="webinar_clear_fix"></div>
	    </div>

	    <div class="form-field">
		<label for="replayp_ctatxt_fld_content_clr"><?php _e('Content text color', WebinarSysteem::$lang_slug); ?></label>
		<input data-style-collect="true" type="text" name="replayp_ctatxt_fld_content_clr" class="color-field" id="replayp_ctatxt_fld_content_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_ctatxt_fld_content_clr', true)); ?>">
		<div class="webinar_clear_fix"></div>
	    </div>

	    <div class="form-group">
		<label for="replayp_ctatxt_txt"><?php _e('CTA Content', WebinarSysteem::$lang_slug); ?></label>
		<?php
		$meta = get_post_meta($post->ID, '_wswebinar_replayp_ctatxt_txt', true);
		$content = apply_filters('meta_content', $meta);
		wp_editor($content, 'replayp_ctatxt_txt');
		?>
		<div class="webinar_clear_fix"></div>
	    </div>

	</div>
    </div>
</div>
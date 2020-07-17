<?php $this->previewButton($post, 'live'); ?>
<div class="webinar_clear_fix"></div>
<div id="livep-accordian" class="ws-accordian">
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('General', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-field">
            <label for="livep_title_show_yn"><?php _e('Hide Title', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_title_show_yn_value = get_post_meta($post->ID, '_wswebinar_livep_title_show_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_title_show_yn" id="livep_title_show_yn" value="yes" <?php echo ($livep_title_show_yn_value == "yes" ) ? 'checked' : ''; ?> data-on-text="Yes" data-off-text="No">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_title_clr"><?php _e('Title color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_title_clr" class="color-field" id="livep_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_title_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_bckg_clr" class="color-field" id="livep_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_bckg_img"><?php _e('Background image', WebinarSysteem::$lang_slug); ?></label>         
            <input data-style-collect="true" type="text" name="livep_bckg_img" id="livep_bckg_img" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_bckg_img', true)); ?>">
            <button class="button wswebinar_uploader" resultId="livep_bckg_img" uploader_title="<?php _e('Thankyou Page Background Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>
            <div class="webinar_clear_fix"></div>

        </div>

        <div class="wsseparator"></div>

        <div class="form-field">
            <label for="livep_vidurl"><?php _e('Webinar type', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_vidurl_type = get_post_meta($post->ID, '_wswebinar_livep_vidurl_type', true); ?>
            <select data-style-collect="true" class="form-control lookoutImageButton" valueField="livep_vidurl" imageUploadButton="livep_vidurl_upload_button" name="livep_vidurl_type" id="livep_vidurl_type">
                <option value="hoa" <?php echo $livep_vidurl_type == "hoa" ? 'selected' : ''; ?>>Hangouts on Air</option>
                <option value="youtube" <?php echo $livep_vidurl_type == "youtube" ? 'selected' : ''; ?>>YouTube</option>
                <option value="youtubelive" <?php echo $livep_vidurl_type == "youtubelive" ? 'selected' : ''; ?>>YouTube <?php _e('Live', WebinarSysteem::$lang_slug) ?></option>
                <option value="vimeo" <?php echo $livep_vidurl_type == "vimeo" ? 'selected' : ''; ?>>Vimeo</option>
                <option value="image" <?php echo $livep_vidurl_type == "image" ? 'selected' : ''; ?>><?php _e('Image', WebinarSysteem::$lang_slug) ?></option>
                <option value="file" <?php echo $livep_vidurl_type == "file" ? 'selected' : ''; ?>><?php _e('File', WebinarSysteem::$lang_slug) ?> (MP4/WebM/OGG)</option>           
                <option value="rtmp" <?php echo $livep_vidurl_type == "rtmp" ? 'selected' : ''; ?>><?php _e('RTMP Stream', WebinarSysteem::$lang_slug) ?></option>           
                <option value="hls" <?php echo $livep_vidurl_type == "hls" ? 'selected' : ''; ?>><?php _e('HLS Stream', WebinarSysteem::$lang_slug) ?></option> 
                <option value="iframe" <?php echo $livep_vidurl_type == "iframe" ? 'selected' : ''; ?>><?php _e('Inline Frame (iframe)', WebinarSysteem::$lang_slug) ?></option> 
            </select>
            <p class="description livep_vidurl_desc livep_vidurl_for_hoa"  style="margin-top:10px;<?php echo (empty($livep_vidurl_type) || $livep_vidurl_type == 'hoa') ? '' : 'display:none'; ?>"><a class="yt_hoa_button" href="https://www.youtube.com/my_live_events?action_create_live_event" target="_blank"><i class="wbn-icon wbnicon-facetime-video"></i> <?php _e('Start HOA broadcast from Youtube website', WebinarSysteem::$lang_slug); ?></a></p>
            <p class="description livep_vidurl_desc livep_vidurl_for_youtubelive"  style="margin-top: 10px;<?php echo ($livep_vidurl_type == 'youtubelive') ? '' : 'display:none'; ?>"><a class="yt_hoa_button" href="https://www.youtube.com/my_live_events?action_create_live_event" target="_blank" class="btn btn-info" role="button"><i class="wbn-icon wbnicon-facetime-video"></i> <?php _e('Start a livestream with Youtube Live', WebinarSysteem::$lang_slug); ?></a></p>
        </div>

        <div class="form-field">
            <label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_hoa" style="<?php echo (empty($livep_vidurl_type) || $livep_vidurl_type == 'hoa') ? '' : 'display:none'; ?>"><?php _e('Hangouts on Air URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_youtube livep_vidurl_for_youtubelive" style="<?php echo ( $livep_vidurl_type == 'youtube' || $livep_vidurl_type == 'youtubelive' ) ? '' : 'display:none'; ?>"><?php _e('Youtube URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_vimeo" style="<?php echo $livep_vidurl_type == 'vimeo' ? '' : 'display:none'; ?>"><?php _e('Vimeo ID', WebinarSysteem::$lang_slug); ?></label>
        	<label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_image" style="<?php echo $livep_vidurl_type == 'image' ? '' : 'display:none'; ?>"><?php _e('Image URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_iframe" style="<?php echo $livep_vidurl_type == 'iframe' ? '' : 'display:none'; ?>"><?php _e('Iframe Source URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_file" style="<?php echo $livep_vidurl_type == 'file' ? '' : 'display:none'; ?>"><?php _e('File URL', WebinarSysteem::$lang_slug); ?></label>
        	<label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_rtmp" style="<?php echo $livep_vidurl_type == 'rtmp' ? '' : 'display:none'; ?>"><?php _e('RTMP Server URL', WebinarSysteem::$lang_slug); ?></label>
            <label for="livep_vidurl" class="livep_vidurl_desc livep_vidurl_for_hls" style="<?php echo $livep_vidurl_type == 'hls' ? '' : 'display:none'; ?>"><?php _e('HLS Server URL', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_vidurl" id="livep_vidurl" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_vidurl', true)); ?>">

            <button class="button wswebinar_uploader" style="<?php echo (!empty($livep_vidurl_type) && $livep_vidurl_type == 'image') ? '' : 'display:none;' ?>" id="livep_vidurl_upload_button" resultId="livep_vidurl" checktype="yes" uploader_title="<?php _e('Registration Page Video Image', WebinarSysteem::$lang_slug); ?>"><?php _e('Upload Image', WebinarSysteem::$lang_slug); ?></button>

            <p id="livep_yt_description" class="description livep_vidurl_desc livep_vidurl_for_youtube livep_vidurl_for_hoa livep_vidurl_for_youtubelive" style="<?php echo (empty($livep_vidurl_type) || $livep_vidurl_type == 'youtube' || $livep_vidurl_type == 'youtubelive' || $livep_vidurl_type == 'hoa') ? '' : 'display:none'; ?>"><?php printf(__('Paste Youtube/Hangouts URL here (Eg: %s OR %s)', WebinarSysteem::$lang_slug), 'https://www.youtube.com/watch?v=3TeXaMpLe9XM', 'http://youtu.be/CqlB2SiG-ac'); ?></p>
            <p class="description livep_vidurl_desc livep_vidurl_for_vimeo" style="<?php echo $livep_vidurl_type == 'vimeo' ? '' : 'display:none'; ?>"><?php _e('Paste Vimeo video ID here (Eg: 129673042)', WebinarSysteem::$lang_slug); ?></p>
            <p class="description livep_vidurl_desc livep_vidurl_for_image" style="<?php echo $livep_vidurl_type == 'image' ? '' : 'display:none'; ?>"><?php _e('Image URL (Eg: https://example.com/images/the_image.jpg)', WebinarSysteem::$lang_slug); ?></p>
            <p class="description livep_vidurl_desc livep_vidurl_for_file livep_vidurl_for_rtmp" style="<?php echo ($livep_vidurl_type == 'file' || $livep_vidurl_type == 'rtmp') ? '' : 'display:none'; ?>"><?php _e("It's best to host your video file outside your website to save bandwidth.", WebinarSysteem::$lang_slug); ?></p>
            <div class="webinar_clear_fix"></div>
        </div>      

        <div class="form-group video-auto-play-yn" <?php echo $livep_vidurl_type == 'iframe' ? 'style="display:none;"' : "" ?>>
            <label for="livep_video_auto_play_yn"><?php _e('Video autoplay', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_video_auto_play_yn_value = get_post_meta($post->ID, '_wswebinar_livep_video_auto_play_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_video_auto_play_yn" id="livep_video_auto_play_yn" value="yes" <?php echo ($livep_video_auto_play_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <p class="description"><?php _e('Some browsers will restrict the autoplay functionality', WebinarSysteem::$lang_slug) ?></p>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="livep_video_controls_yn"><?php _e('Show controls', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_video_controls_yn_value = get_post_meta($post->ID, '_wswebinar_livep_video_controls_yn', true); ?>

            <input class="wsweb_livep_controls_listen_enability" data-style-collect="true" type="checkbox" data-switch="true" name="livep_video_controls_yn" id="livep_video_controls_yn" value="yes" <?php echo ($livep_video_controls_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <p class="description"><?php _e('Controls will stay visible for webinar host, but will be hidden for your attendees', WebinarSysteem::$lang_slug) ?></p>
            <div class="webinar_clear_fix"></div>
        </div>
        
    <div id="livep_show_fullscreen_control_setting" style="<?php echo ($livep_video_controls_yn_value == 'yes' ? 'display:none;' : 'display: block;'); ?>;">
	    <div class="form-field">
	        <label for="livep_fullscreen_control"><?php _e('Enable Fullscreen', WebinarSysteem::$lang_slug); ?></label>
		<?php $livep_fullscreen_control_yn_value = get_post_meta($post->ID, '_wswebinar_livep_fullscreen_control', true); ?>
		    <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_fullscreen_control" id="livep_fullscreen_control" value="yes" <?php echo ($livep_fullscreen_control_yn_value == "yes") ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
	    </div>
	</div>
	
	<div class="form-group bigplaybtn-yn" <?php echo WebinarSysteemHelperFunctions::isMediaElementJSPlayer($livep_vidurl_type) ? '' : 'style="display:none;"' ?>>
            <label for="livep_bigplaybtn_yn"><?php _e('Show big play button', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_bigplaybtn_value = get_post_meta($post->ID, '_wswebinar_livep_bigplaybtn_yn', true); ?>

            <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_bigplaybtn_yn" id="livep_bigplaybtn_yn" value="yes" <?php echo ($livep_bigplaybtn_value == "yes") ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
	<div class="form-group bigplaybtn-yn" <?php echo WebinarSysteemHelperFunctions::isMediaElementJSPlayer($livep_vidurl_type) ? '' : 'style="display:none;"' ?>>
            <label for="livep_simulate_video_yn"><?php _e('Simulate webinar content', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_simulate_video_value = get_post_meta($post->ID, '_wswebinar_livep_simulate_video_yn', true); ?>

            <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_simulate_video_yn" id="livep_simulate_video_yn" value="yes" <?php echo ($livep_simulate_video_value == "yes") ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Host & Description Box', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">

        <div class="form-field">
            <label for="livep_leftbox_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_leftbox_bckg_clr" class="color-field" id="livep_leftbox_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_leftbox_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_leftbox_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_leftbox_border_clr" class="color-field" id="livep_leftbox_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_leftbox_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="wsseparator"></div>

        <div class="form-group">
            <label for="livep_hostbox_yn"><?php _e('Show Host Box', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_hostbox_yn_value = get_post_meta($post->ID, '_wswebinar_livep_hostbox_yn', true); ?>
            <input data-switch="true" data-style-collect="true" type="checkbox" name="livep_hostbox_yn" id="livep_hostbox_yn" value="yes" <?php echo ($livep_hostbox_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_hostbox_title_bckg_clr"><?php _e('Host Title Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_hostbox_title_bckg_clr" class="color-field" id="livep_hostbox_title_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_hostbox_title_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_hostbox_title_text_clr"><?php _e('Host Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_hostbox_title_text_clr" class="color-field" id="livep_hostbox_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_hostbox_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_hostbox_content_text_clr"><?php _e('Host Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_hostbox_content_text_clr" class="color-field" id="livep_hostbox_content_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_hostbox_content_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="wsseparator"></div>

        <div class="form-group">
            <label for="livep_webdes_yn"><?php _e('Show Description Box', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_webdes_yn_value = get_post_meta($post->ID, '_wswebinar_livep_webdes_yn', true); ?>
            <input data-switch="true" data-style-collect="true" type="checkbox" name="livep_webdes_yn" id="livep_webdes_yn" value="yes" <?php echo ($livep_webdes_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_descbox_title_bckg_clr"><?php _e('Description Title Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_descbox_title_bckg_clr" class="color-field" id="livep_descbox_title_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_descbox_title_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_descbox_title_text_clr"><?php _e('Description Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_descbox_title_text_clr" class="color-field" id="livep_descbox_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_descbox_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_descbox_content_text_clr"><?php _e('Description Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_descbox_content_text_clr" class="color-field" id="livep_descbox_content_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_descbox_content_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>


    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Question Box', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-group">
            <label for="livep_askq_yn"><?php _e('Show Question box', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_askq_yn_value = get_post_meta($post->ID, '_wswebinar_livep_askq_yn', true); ?>
            <input data-switch="true" class="wsweb_listen_fortabs" data-style-collect="true" type="checkbox" name="livep_askq_yn" id="livep_askq_yn" value="yes" <?php echo ($livep_askq_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-group">
            <label for="livep_askq_send_email_yn"><?php _e('Send an email on every question', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_askq_send_email_yn_value = get_post_meta($post->ID, '_wswebinar_livep_askq_send_email_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_askq_send_email_yn" id="livep_askq_send_email_yn" value="yes" <?php echo ($livep_askq_send_email_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-group <?php echo $livep_askq_send_email_yn_value != "yes" ? 'hidden' : '' ?>">
            <label for="livep_askq_send_email"><?php _e('If then email to', WebinarSysteem::$lang_slug); ?></label>
            <input type="email" name="livep_askq_send_email" size="20" placeholder="you@example.com" id="livep_askq_send_email" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_askq_send_email', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_askq_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_askq_bckg_clr" class="color-field" id="livep_askq_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_askq_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_askq_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_askq_border_clr" class="color-field" id="livep_askq_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_askq_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_askq_title_text_clr"><?php _e('Title Text Color', WebinarSysteem::$lang_slug); ?></label>
            <input type="text" data-style-collect="true" name="livep_askq_title_text_clr" class="color-field" id="livep_askq_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_askq_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_button_radius"><?php _e('Border Radius', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_button_radius" size="20" placeholder="5px" id="livep_button_radius" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_button_radius', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_button_bg_clr"><?php _e('Button Background Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_button_bg_clr" class="color-field" id="livep_button_bg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_button_bg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_buttonhover_bg_clr"><?php _e('Button Hover Background Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_buttonhover_bg_clr" class="color-field" id="livep_buttonhover_bg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_buttonhover_bg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_button_border_clr"><?php _e('Button Border Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_button_border_clr" class="color-field" id="livep_button_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_button_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_buttonhover_border_clr"><?php _e('Button Hover Border Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_buttonhover_border_clr" class="color-field" id="livep_buttonhover_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_buttonhover_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_button_text_clr"><?php _e('Button Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_button_text_clr" class="color-field" id="livep_button_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_button_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_buttonhover_text_clr"><?php _e('Button Hover Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_buttonhover_text_clr" class="color-field" id="livep_buttonhover_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_buttonhover_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Live chatbox', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">

        <div class="form-group">
            <label for="livep_show_chatbox"><?php _e('Show Chatbox', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_chtb_yn_value = get_post_meta($post->ID, '_wswebinar_livep_show_chatbox', true); ?>
            <input data-switch="true" class="wsweb_listen_fortabs" data-style-collect="true" type="checkbox" name="livep_show_chatbox" id="livep_chtb_yn" value="yes" <?php echo ($livep_chtb_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_bckg_clr" class="color-field" id="livep_chtb_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_border_clr" class="color-field" id="livep_chtb_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_title_text_clr"><?php _e('Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_title_text_clr" class="color-field" id="livep_chtb_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-group">
            <label for="livep_show_chatbox_timestmp"><?php _e('Show Timestamps', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_chtb_yn_value = get_post_meta($post->ID, '_wswebinar_livep_show_chatbox_timestmp', true); ?>
            <input data-switch="true" data-style-collect="true" type="checkbox" name="livep_show_chatbox_timestmp" id="livep_chtb_yn" value="yes" <?php echo ($livep_chtb_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="livep_bgclr_chatbtn"><?php _e('Background Color of Button', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_chtbtn_clr = get_post_meta($post->ID, '_wswebinar_livep_bgclr_chatbtn', true); ?>
            <input data-switch="true" class="color-field wp-color-picker" data-style-collect="true" type="text" name="livep_bgclr_chatbtn" id="livep_bgclr_chatbtn" value="<?php echo ($livep_chtbtn_clr) ?>" >
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="livep_txtclr_chatbtn"><?php _e('Text Color of Button', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_txtbtn_clr = get_post_meta($post->ID, '_wswebinar_livep_txtclr_chatbtn', true); ?>
            <input data-switch="true" class="color-field wp-color-picker" data-style-collect="true" type="text" name="livep_txtclr_chatbtn" id="livep_txtclr_chatbtn" value="<?php echo ($livep_txtbtn_clr) ?>" >
            <div class="webinar_clear_fix"></div>
        </div>


    </div>

    <h3 style="display: none;" class="ws-accordian-title wsweb_livep_tablayout"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Tab Layout', WebinarSysteem::$lang_slug) ?></h3>
    <div style="display: none;" class="ws-accordian-section ">
        <span><b><?php _e('Questionbox tab', WebinarSysteem::$lang_slug); ?></b></span><br>
        <div class="wsseparator webinar-seperator-livep"></div>
        <div class="form-field">
            <label for="livep_chtb_quebox_title"><?php _e('Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_quebox_title" id="livep_chtb_quebox_title" value="<?php
            $val = esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_quebox_title', true));
            ?>">

            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_quebox_title_text_clr"><?php _e('Tab Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_quebox_title_text_clr" class="color-field" id="livep_chtb_quebox_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_quebox_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_quebox_bkg_text_clr"><?php _e('Tab Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_quebox_bkg_text_clr" class="color-field" id="livep_chtb_quebox_bkg_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_quebox_bkg_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_quebox_border_clr"><?php _e('Tab Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_quebox_border_clr" class="color-field" id="livep_chtb_quebox_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_quebox_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <span><b><?php _e('Chatbox tab', WebinarSysteem::$lang_slug); ?></b></span><br>
        <div class="wsseparator webinar-seperator-livep"></div>
        <div class="form-field">
            <label for="livep_chtb_chat_title"><?php _e('Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_chat_title" id="livep_chtb_chat_title" value="<?php

            $val = esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_chat_title', true));
            ?>">

            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_chat_title_text_clr"><?php _e('Tab Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_chat_title_text_clr" class="color-field" id="livep_chtb_chat_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_chat_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_chat_bkg_text_clr"><?php _e('Tab Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_chat_bkg_text_clr" class="color-field" id="livep_chtb_chat_bkg_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_chat_bkg_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_chtb_chat_border_clr"><?php _e('Tab Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_chtb_chat_border_clr" class="color-field" id="livep_chtb_chat_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_chtb_chat_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Incentive Box', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">

        <div class="form-group">
            <label for="livep_incentive_yn"><?php _e('Show Incentive Box', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_incentive_yn_value = get_post_meta($post->ID, '_wswebinar_livep_incentive_yn', true); ?>
            <input data-switch="true" data-style-collect="true" type="checkbox" name="livep_incentive_yn" id="livep_incentive_yn" value="yes" <?php echo ($livep_incentive_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_incentive_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_incentive_bckg_clr" class="color-field" id="livep_incentive_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_incentive_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_incentive_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_incentive_border_clr" class="color-field" id="livep_incentive_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_incentive_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_incentive_title"><?php _e('Incentive Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_incentive_title" id="livep_incentive_title" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_incentive_title', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_incentive_title_clr"><?php _e('Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_incentive_title_clr" class="color-field" id="livep_incentive_title_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_incentive_title_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_incentive_title_bckg_clr"><?php _e('Title Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_incentive_title_bckg_clr" class="color-field" id="livep_incentive_title_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_incentive_title_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="livep_incentive_content_clr"><?php _e('Content text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_incentive_content_clr" class="color-field" id="livep_incentive_content_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_incentive_content_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-group">
            <label for="livep_incentive_content"><?php _e('Incentive box content', WebinarSysteem::$lang_slug); ?></label>
	    <?php
	    $meta = get_post_meta($post->ID, '_wswebinar_livep_incentive_content', true);
	    $content = apply_filters('meta_content', $meta);
	    wp_editor($content, 'livep_incentive_content');
	    ?>
            <div class="webinar_clear_fix"></div>
        </div>
    </div>

    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Action Box', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-group">
            <label for="livep_show_actionbox"><?php _e('Show Action Box', WebinarSysteem::$lang_slug); ?></label>
	    <?php $livep_actionbox_show_value = get_post_meta($post->ID, '_wswebinar_livep_show_actionbox', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="livep_show_actionbox" id="livep_show_actionbox" value="yes" <?php echo ($livep_actionbox_show_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_action_raise_hand_clr"><?php _e('Hand button color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_action_raise_hand_clr" class="color-field" id="livep_action_raise_hand_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_action_raise_hand_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_action_raise_hand_hover_clr"><?php _e('Hand button hover color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_action_raise_hand_hover_clr" class="color-field" id="livep_action_raise_hand_hover_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_action_raise_hand_hover_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_action_raise_hand_act_clr"><?php _e('Hand button Active color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_action_raise_hand_act_clr" class="color-field" id="livep_action_raise_hand_act_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_action_raise_hand_act_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_action_bckg_clr"><?php _e('Background Color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_action_bckg_clr" class="color-field" id="livep_action_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_action_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="livep_action_box_border_clr"><?php _e('Border Color  ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="livep_action_box_border_clr" class="color-field" id="livep_action_box_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_action_box_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
    <!--
    Call to action functionality
    -->
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Call to action', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">

        <div class="form-group">
	    <?php $callto_action_saved = get_post_meta($post->ID, '_wswebinar_livep_call_action', true); ?>
            <label><?php _e('Activate CTA', WebinarSysteem::$lang_slug); ?></label>

            <label class="radio"><input data-style-collect="true" type="radio" name="livep_call_action" value="manual" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_action_saved == 'manual' || $callto_action_saved == ''); ?> >Manual</label>
            <label class="radio" ><input data-style-collect="true" type="radio" name="livep_call_action" value="aftertimer" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_action_saved == 'aftertimer'); ?>>After amount of time</label>
            <div class="webinar_clear_fix"></div>
        </div>

        <div id="livep_call_action_atertime" style="<?php echo ($callto_action_saved == 'manual' || $callto_action_saved == '' ? 'display: none;' : ''); ?> ">
            <div class="form-group">
                <label for="livep_cta_show_after"><?php _e('Show CTA after (minutes)', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="number" min="1" name="livep_cta_show_after" id="livep_cta_show_after" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_cta_show_after', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <div id="livep_call_action_manual" style="<?php echo ($callto_action_saved == 'manual' || $callto_action_saved == '' ? 'display: block;' : 'display: none;'); ?>">
            <div class="form-group">
                <label for="livep_manual_show_cta"><?php _e('Show CTA', WebinarSysteem::$lang_slug); ?></label>
		<?php $livep_cta_manual_status = get_post_meta($post->ID, '_wswebinar_livep_manual_show_cta', true); ?>
                <input data-switch="true" data-style-collect="true" type="checkbox" name="livep_manual_show_cta" id="livep_manual_show_cta" value="yes" <?php echo ($livep_cta_manual_status == "yes" ) ? 'checked="checked"' : ''; ?> >
                <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <div class="form-group">
	    <?php $callto_cta_type = get_post_meta($post->ID, '_wswebinar_livep_call_action_ctatype', true); ?>
            <label><?php _e('CTA Type', WebinarSysteem::$lang_slug); ?></label>

            <label class="radio"><input data-style-collect="true" type="radio" name="livep_call_action_ctatype" value="button" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_cta_type == 'button' || $callto_cta_type == ''); ?> >Button</label>
            <label class="radio" ><input data-style-collect="true" type="radio" name="livep_call_action_ctatype" value="txt_field" <?php echo WebinarSysteemMetabox::checkCheckbox($callto_cta_type == 'txt_field'); ?>>Text Field</label>
            <div class="webinar_clear_fix"></div>
        </div>

        <div id="livep_callto_action_button" style="<?php echo ($callto_cta_type == 'button' || $callto_cta_type == '' ? '' : 'display: none;'); ?>">

            <div class="form-group">
                <label for="livep_ctabtn_clr"><?php _e('Background Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_clr" class="color-field" id="livep_ctabtn_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_hover_clr"><?php _e('Hover Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_hover_clr" class="color-field" id="livep_ctabtn_hover_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_hover_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_border_clr"><?php _e('Border Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_border_clr" class="color-field" id="livep_ctabtn_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_border_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_txt_clr"><?php _e('Text Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_txt_clr" class="color-field" id="livep_ctabtn_txt_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_txt_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_hover_txt_clr"><?php _e('Hover Text Color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_hover_txt_clr" class="color-field" id="livep_ctabtn_hover_txt_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_hover_txt_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_brdr_radius"><?php _e('Border Radius', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="number" max="100" min="0" name="livep_ctabtn_brdr_radius" id="livep_ctabtn_brdr_radius" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_brdr_radius', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_url"><?php _e('Button URL', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_url" id="livep_ctabtn_url" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_url', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctabtn_txt"><?php _e('Button Text', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctabtn_txt" id="livep_ctabtn_txt" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctabtn_txt', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

        </div>

        <div id="livep_callto_action_txtfied" style="<?php echo ($callto_cta_type == 'txt_field' ? '' : 'display: none;'); ?>">

            <div class="form-field">
                <label for="livep_ctatxt_fld_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctatxt_fld_bckg_clr" class="color-field" id="livep_ctatxt_fld_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctatxt_fld_bckg_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
                <label for="livep_ctatxt_fld_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctatxt_fld_border_clr" class="color-field" id="livep_ctatxt_fld_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctatxt_fld_border_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
                <label for="livep_ctatxt_fld_content_clr"><?php _e('Content text color', WebinarSysteem::$lang_slug); ?></label>
                <input data-style-collect="true" type="text" name="livep_ctatxt_fld_content_clr" class="color-field" id="livep_ctatxt_fld_content_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_livep_ctatxt_fld_content_clr', true)); ?>">
                <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
                <label for="livep_ctatxt_txt"><?php _e('CTA Content', WebinarSysteem::$lang_slug); ?></label>
		<?php
		$meta = get_post_meta($post->ID, '_wswebinar_livep_ctatxt_txt', true);
		$content = apply_filters('meta_content', $meta);
		wp_editor($content, 'livep_ctatxt_txt');
		?>
                <div class="webinar_clear_fix"></div>
            </div>

        </div>

    </div>

</div>
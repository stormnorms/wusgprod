<?php

Class WebinarSysteemMails extends WebinarSysteem {

    function __construct() {
	parent::setAttributes();
	add_action('wp', array($this, 'registerMailSender'));
	add_action('wswebinarsendscheduledmails', array($this, 'wswebinarsendscheduledmails'));
	add_filter('cron_schedules', array($this, 'cron_add_5_minutes'));
    }

    public function AdminMailAddress() {
	$optionAdminemailset = get_option('_wswebinar_AdminEmailAddress');
	return $AdminMailAddress = (!empty($optionAdminemailset) ? $optionAdminemailset : get_bloginfo('admin_email') );
    }

    public function SendFromMailAddress() {
	$sender_addr = get_option('_wswebinar_email_senderAddress');
	if (empty($sender_addr)):
	    update_option('_wswebinar_email_senderAddress', get_bloginfo('admin_email'));
	endif;
    }

    private function GeneralEmailTemplateTop($title, $content) {
	$hdr_Img = get_option('_wswebinar_email_headerImg');
	$logoURI = (!empty($hdr_Img) ? $hdr_Img : '');
	$bse_clr = get_option('_wswebinar_email_baseCLR');
	$basecolor = (!empty($bse_clr) ? $bse_clr : '#fff');
	$em_back_clr = get_option('_wswebinar_email_bckCLR');
	$bodybgcolor = (!empty($em_back_clr) ? $em_back_clr : '#f2f2f2');
	$bdy_clr = get_option('_wswebinar_email_bodyBck');
	$emailbodybgcolor = (!empty($bdy_clr) ? $bdy_clr : '#fff');
	$bdy_text = get_option('_wswebinar_email_bodyTXT');
	$bodyTXTcolor = (!empty($bdy_text) ? $bdy_text : 'black');

	ob_start();

	WsWebinarTemplate_EmailHeader::get($logoURI, $title, $content, $basecolor, $bodybgcolor, $emailbodybgcolor, $bodyTXTcolor);

	$MailHTMLheadPart = ob_get_contents();
	ob_end_clean();
	return $MailHTMLheadPart;
    }

    private function GeneralEmailTemplateBottom() {
	ob_start();
//        $footer = (!null == get_option('_wswebinar_email_footerTxt') ? '<hr/> ' . get_option('_wswebinar_email_footerTxt') : '');
//        echo $footer;

	WsWebinarTemplate_EmailFooter::get();


	$MailHTMLfootPart = ob_get_contents();
	ob_end_clean();
	return $MailHTMLfootPart;
    }

    public function SendMailtoAdmin($inputName, $post_id, $inputEmail, $preview = FALSE) {
	if (get_option('_wswebinar_newregenable') == 'on' || $preview) {
	    
	    $wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	    $wp_time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
	
	    $wb_timezone=get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
        if(empty($wb_timezone))
        {
        $timezone_string = get_option('timezone_string');
        $wpoffset=get_option('gmt_offset');  
        $gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
        $wb_timezone = (!empty($timezone_string)) ? $timezone_string : 'UTC'.$gmt_offset.'';
        }
	    
        $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
        $getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
      
        if (WebinarSysteem::isRecurring($post_id)) {
		$attendee = WebinarSysteemAttendees::getAttendeeByEmail($inputEmail, $post_id);
		$time = $attendee->exact_time;
		$wb_time = date($wp_time_format, strtotime($time));
		$wb_date = date($wp_date_format, strtotime($time));
	    } else {
		$data_hour = get_post_meta($post_id, '_wswebinar_gener_hour', true);
		$data_min = get_post_meta($post_id, '_wswebinar_gener_min', true);
		$wb_time = date($wp_time_format, strtotime($data_hour . ':' . $data_min));

		$gener_date = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$wb_date = date($wp_date_format, strtotime($gener_date));
	    }
	    
	    
	    $receiver_name = $inputName." (". $inputEmail .")";
	    $wbnewregcontent = get_option('_wswebinar_newregcontent');
	    if (!empty($wbnewregcontent)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $receiver_name, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$meta = str_replace("\r", "<br />", apply_filters('the_content', $wbnewregcontent));
		$text = apply_filters('meta_content', $meta);
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$NewRegTemplate = $text;
	    } else {
		ob_start();
		WsWebinarTemplate_AdminDefault::get($post_id, $inputName, $inputEmail);
		$NewRegTemplate = ob_get_contents();
		ob_end_clean();
	    }
	    $title_newreg = get_option('_wswebinar_newregsubject');
	    if (!empty($title_newreg)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $receiver_name, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = $title_newreg;
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$EmailTitle = $text;
	    } else {
		$EmailTitle = __('New Registration', WebinarSysteem::$lang_slug);
	    }
	    
	    if($preview){
	        $email = $inputEmail;
	    } else {
	        $email = $this->AdminMailAddress();
	    }
	    
	    $MessagetoAttendee = $this->GeneralEmailTemplateTop($EmailTitle, $NewRegTemplate) . $this->GeneralEmailTemplateBottom();

	    $WebinarEmailHeader = array();
	    $WebinarEmailHeader[] = "MIME-Version: 1.0";
	    $WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	    $WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';
	    return wp_mail($email, $EmailTitle, $MessagetoAttendee, implode("\r\n", $WebinarEmailHeader));
	}
    }

    public function SendMailtoReader($inputName, $inputEmail, $post_id, $preview = FALSE) {
	if (get_option('_wswebinar_regconfirmenable') == 'on' || $preview) {
	    $wb_timezone=get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
	    $wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	    $wp_time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
        if(empty($wb_timezone))
        {
        $timezone_string = get_option('timezone_string');
        $wpoffset=get_option('gmt_offset');  
        $gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
        $wb_timezone = (!empty($timezone_string)) ? $timezone_string : 'UTC'.$gmt_offset.'';
        }
	    
        $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
        $getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
        
        if (WebinarSysteem::isRecurring($post_id)) {
		$attendee = WebinarSysteemAttendees::getAttendeeByEmail($inputEmail, $post_id);
		$time = $attendee->exact_time;
		$wb_time = date($wp_time_format, strtotime($time));
		$wb_date = date($wp_date_format, strtotime($time));
	    } else {
		$data_hour = get_post_meta($post_id, '_wswebinar_gener_hour', true);
		$data_min = get_post_meta($post_id, '_wswebinar_gener_min', true);
		$wb_time = date($wp_time_format, strtotime($data_hour . ':' . $data_min));

		$gener_date = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$wb_date = date($wp_date_format, strtotime($gener_date));
	    }
        
	    $wbregconfirmcontent = get_option('_wswebinar_regconfirmcontent');
	    if (!empty($wbregconfirmcontent)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$meta = str_replace("\r", "<br />", $wbregconfirmcontent);
		$text = apply_filters('meta_content', $meta);
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$RegConfirmTemplate = $text;
	    } else {
		ob_start();
		WsWebinarTemplate_Reader::get($inputName, $post_id);
		$RegConfirmTemplate = ob_get_contents();
		ob_end_clean();
	    }
	    $title_regconfirm = get_option('_wswebinar_regconfirmsubject');
	    if (!empty($title_regconfirm)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = $title_regconfirm;
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$EmailSubject = $text;
	    } else {
		$EmailSubject = __('You are registered for the webinar!', WebinarSysteem::$lang_slug);
	    }
	    
	    $is_webinar_profile_links = get_option('_wswebinar_subscription');
		if($is_webinar_profile_links == "on") {
			$unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
			$manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
			$RegConfirmTemplate .= '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
		}
            
	    $MessagetoAttendee = $this->GeneralEmailTemplateTop($EmailSubject, $RegConfirmTemplate) . $this->GeneralEmailTemplateBottom();

	    $WebinarEmailHeader = array();
	    $WebinarEmailHeader[] = "MIME-Version: 1.0";
	    $WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	    $WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';
	    return wp_mail($inputEmail, $EmailSubject, $MessagetoAttendee, implode("\r\n", $WebinarEmailHeader));
	}
    }

    public function SendMailtoReaderOnWCOrderComplete($inputName, $inputEmail, $post_id) {
	ob_start();
	WsWebinarTemplate_Reader::onOrderComplete($inputName, $inputEmail, $post_id);
	$NewReaderTemplate = ob_get_contents();
	ob_end_clean();
	$MessagetoAttendee = $this->GeneralEmailTemplateTop('', $NewReaderTemplate) . $this->GeneralEmailTemplateBottom();

	$WebinarEmailHeader = array();
	$WebinarEmailHeader[] = "MIME-Version: 1.0";
	$WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	$WebinarEmailHeader[] = "From: " . esc_attr(get_option('_wswebinar_email_sentFrom')) . " <" . get_option('_wswebinar_email_senderAddress') . '>';

	wp_mail($inputEmail, __('Your webinar link', WebinarSysteem::$lang_slug), $MessagetoAttendee, $WebinarEmailHeader);
    }

    public function SendQuestionToHost($inputName, $post_id, $inputEmail, $question) {
	$livepMailsDisabled = get_post_meta($post_id, '_wswebinar_livep_askq_send_email_yn', true) != "yes";
	$replaypMailsDisabled = get_post_meta($post_id, '_wswebinar_replayp_askq_send_email_yn', true) != "yes";

	if ($livepMailsDisabled && $replaypMailsDisabled)
	    return;

	$postStatus = get_post_meta($post_id, '_wswebinar_gener_webinar_status', true);

	$hostEmail = $postStatus == "liv" ? get_post_meta($post_id, '_wswebinar_livep_askq_send_email', true) : get_post_meta($post_id, '_wswebinar_replayp_askq_send_email', true);
	if (empty($hostEmail))
	    $hostEmail = $this->AdminMailAddress();

	ob_start();
	WsWebinarTemplate_AdminDefault::attendeeQuestion($post_id, $inputName, $inputEmail, $question);
	$NewReaderTemplate = ob_get_contents();
	ob_end_clean();
	$MessagetoAdmin = $this->GeneralEmailTemplateTop(__('New message', WebinarSysteem::$lang_slug), $NewReaderTemplate) . $this->GeneralEmailTemplateBottom();

	$WebinarEmailHeader = array();
	$WebinarEmailHeader[] = "MIME-Version: 1.0";
	$WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	$WebinarEmailHeader[] = "Reply-To: $inputEmail";
	$WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';
	return wp_mail($hostEmail, __('Question from', WebinarSysteem::$lang_slug) . ' ' . $inputName, $MessagetoAdmin, $WebinarEmailHeader);
    }

    public function SendMailtoAttendee24hr_Template($inputName, $inputEmail, $post_id, $preview = FALSE) {
	$wp_datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	$wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	$wp_time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
	if (get_option('_wswebinar_24hrb4enable') == 'on' || $preview) {
	    $wb_timezone=get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
        if(empty($wb_timezone))
        {
        $timezone_string = get_option('timezone_string');
        $wpoffset=get_option('gmt_offset');  
        $gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
        $wb_timezone = (!empty($timezone_string)) ? $timezone_string : 'UTC'.$gmt_offset.'';
        }
	    
	    if (WebinarSysteem::isRecurring($post_id)) {
		$attendee = WebinarSysteemAttendees::getAttendeeByEmail($inputEmail, $post_id);
		$time = $attendee->exact_time;
		$wb_time = date($wp_time_format, strtotime($time));
		$wb_date = date($wp_date_format, strtotime($time));
	    } else {
		$data_hour = get_post_meta($post_id, '_wswebinar_gener_hour', true);
		$data_min = get_post_meta($post_id, '_wswebinar_gener_min', true);
		$wb_time = date($wp_time_format, strtotime($data_hour . ':' . $data_min));

		$gener_date = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$wb_date = date($wp_date_format, strtotime($gener_date));
	    }
	    
	    $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
		$getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
	    
	    $wb24b4content = get_option('_wswebinar_24hrb4content');
	    if (!empty($wb24b4content)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = str_replace("\r", "<br />", $wb24b4content);
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$OneDayTemplate = $text;
	    } else {
		//Use Default template
		ob_start();
		WsWebinarTemplate_Attendee24hr::get($inputName, $inputEmail, $post_id);
		$OneDayTemplate = ob_get_contents();
		ob_end_clean();
	    }
	    $title_24hr = get_option('_wswebinar_24hrb4subject');
	    if (!empty($title_24hr)) {
		//User customized the template
		$date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
		$date = strtotime(get_post_meta($post_id, '_wswebinar_gener_date', true));
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = $title_24hr;
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$EmailTitle = $text;
	    } else {
		$EmailTitle = __('Reminder', WebinarSysteem::$lang_slug);
	    }
	    
	    $is_webinar_profile_links = get_option('_wswebinar_subscription');
		if($is_webinar_profile_links == "on") {
			$unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
	    	$manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
			$OneDayTemplate .= '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
		}

	    $MessagetoAttendee = $this->GeneralEmailTemplateTop($EmailTitle, $OneDayTemplate) . $this->GeneralEmailTemplateBottom();

	    $WebinarEmailHeader = array();
	    $WebinarEmailHeader[] = "MIME-Version: 1.0";
	    $WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	    $WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';

	    return wp_mail($inputEmail, $EmailTitle, $MessagetoAttendee, implode("\r\n", $WebinarEmailHeader));
	}
    }

    public function SendMailtoAttendee1hr_Template($inputName, $inputEmail, $post_id, $preview = FALSE) {
	$datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	$wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	$time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
	if (get_option('_wswebinar_1hrb4enable') == 'on' || $preview) {
       $wb_timezone=get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
        if(empty($wb_timezone))
        {
        $timezone_string = get_option('timezone_string');
        $wpoffset=get_option('gmt_offset');  
        $gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
        $wb_timezone = (!empty($timezone_string)) ? $timezone_string : 'UTC'.$gmt_offset.'';
        }
	    if (WebinarSysteem::isRecurring($post_id)) {
		$attendee = WebinarSysteemAttendees::getAttendeeByEmail($inputEmail, $post_id);
		$time = $attendee->exact_time;
		$wb_time = date($time_format, strtotime($time));
		$wb_date = date($wp_date_format, strtotime($time));
	    } else {
		$data_hour = get_post_meta($post_id, '_wswebinar_gener_hour', true);
		$data_min = get_post_meta($post_id, '_wswebinar_gener_min', true);
		$wb_time = date($time_format, strtotime($data_hour . ':' . $data_min));

		$gener_date = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$wb_date = date($wp_date_format, strtotime($gener_date));
	    }
	    
	    $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
		$getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
		
	    $wb1hrb4content = get_option('_wswebinar_1hrb4content');
	    if (!empty($wb1hrb4content)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$meta = str_replace("\r", "<br />", $wb1hrb4content);
		$text = apply_filters('meta_content', $meta);

		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$OneHourTemplate = $text;
	    } else {
		ob_start();
		WsWebinarTemplate_Attendee1hr::get($inputName, $inputEmail, $post_id);
		$OneHourTemplate = ob_get_contents();
		ob_end_clean();
	    }
	    $title_b41hr = get_option('_wswebinar_1hrb4subject');
	    if (!empty($title_b41hr)) {
		//User customized the template
		$date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
		$date = strtotime(get_post_meta($post_id, '_wswebinar_gener_date', true));
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = $title_b41hr;
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$EmailTitle = $text;
	    } else {
		$EmailTitle = __('We are live in one hour!', WebinarSysteem::$lang_slug);
	    }
	    
	    $is_webinar_profile_links = get_option('_wswebinar_subscription');
		if($is_webinar_profile_links == "on") {
			$unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
	    	$manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
			$OneHourTemplate .= '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
		}
		
	    $MessagetoAttendee = $this->GeneralEmailTemplateTop($EmailTitle, $OneHourTemplate) . $this->GeneralEmailTemplateBottom();

	    $WebinarEmailHeader = array();
	    $WebinarEmailHeader[] = "MIME-Version: 1.0";
	    $WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	    $WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';
	    return wp_mail($inputEmail, $EmailTitle, $MessagetoAttendee, implode("\r\n", $WebinarEmailHeader));
	}
    }

    public function SendMailtoAttendeeStarted_Template($inputName, $inputEmail, $post_id, $preview = FALSE) {
	$wp_datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	$wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	$wp_time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
	if (get_option('_wswebinar_wbnstartedenable') == 'on' || $preview) {
	    $wb_timezone=get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
        if(empty($wb_timezone))
        {
        $timezone_string = get_option('timezone_string');
        $wpoffset=get_option('gmt_offset');  
        $gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
        $wb_timezone = (!empty($timezone_string)) ? $timezone_string : 'UTC'.$gmt_offset.'';
        }
	    
	    if (WebinarSysteem::isRecurring($post_id)) {
		$attendee = WebinarSysteemAttendees::getAttendeeByEmail($inputEmail, $post_id);
		$time = $attendee->exact_time;
		$wb_time = date($wp_time_format, strtotime($time));
		$wb_date = date($wp_date_format, strtotime($time));
	    } else {
		$data_hour = get_post_meta($post_id, '_wswebinar_gener_hour', true);
		$data_min = get_post_meta($post_id, '_wswebinar_gener_min', true);
		$wb_time = date($wp_time_format, strtotime($data_hour . ':' . $data_min));

		$gener_date = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$wb_date = date($wp_date_format, strtotime($gener_date));
	    }
	    
	    $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
        $getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
	    
	    $wbstarted = get_option('_wswebinar_wbnstarted');
	    
	    if (!empty($wbstarted)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = str_replace("\r", "<br />", $wbstarted);
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$WebinarStartedTemplate = $text;
	    } else {
		ob_start();
		WsWebinarTemplate_AttendeeStarted::get($inputName, $post_id);
		$WebinarStartedTemplate = ob_get_contents();
		ob_end_clean();
	    }
	    $title_wbnstarted = get_option('_wswebinar_wbnstartedsubject');
	    if (!empty($title_wbnstarted)) {
		//User customized the template
		$date = strtotime(get_post_meta($post_id, '_wswebinar_gener_date', true));
		$date_formamt = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = $title_wbnstarted;
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$EmailTitle = $text;
	    } else {
		$EmailTitle = __('We are starting the webinar!', WebinarSysteem::$lang_slug);
	    }
	    
	    $is_webinar_profile_links = get_option('_wswebinar_subscription');
		if($is_webinar_profile_links == "on") {
			$unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
	    	$manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
			$WebinarStartedTemplate .= '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
		}
		
	    $MessagetoAttendee = $this->GeneralEmailTemplateTop($EmailTitle, $WebinarStartedTemplate) . $this->GeneralEmailTemplateBottom();

	    $WebinarEmailHeader = array();
	    $WebinarEmailHeader[] = "MIME-Version: 1.0";
	    $WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	    $WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';
	    return wp_mail($inputEmail, $EmailTitle, $MessagetoAttendee, $WebinarEmailHeader);
	}
    }

    public function SendMailtoAttendeeReplayLink_Template($inputName, $inputEmail, $post_id, $preview = FALSE) {
	if (get_option('_wswebinar_wbnreplayenable') == 'on' || $preview) {
	    $wp_datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	    $wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	    $wp_time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
	    $wb_timezone=get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
        if(empty($wb_timezone))
        {
        $timezone_string = get_option('timezone_string');
        $wpoffset=get_option('gmt_offset');  
        $gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
        $wb_timezone = (!empty($timezone_string)) ? $timezone_string : 'UTC'.$gmt_offset.'';
        }
        
        if (WebinarSysteem::isRecurring($post_id)) {
		$attendee = WebinarSysteemAttendees::getAttendeeByEmail($inputEmail, $post_id);
		$time = $attendee->exact_time;
		$wb_time = date($wp_time_format, strtotime($time));
		$wb_date = date($wp_date_format, strtotime($time));
	    } else {
		$data_hour = get_post_meta($post_id, '_wswebinar_gener_hour', true);
		$data_min = get_post_meta($post_id, '_wswebinar_gener_min', true);
		$wb_time = date($wp_time_format, strtotime($data_hour . ':' . $data_min));

		$gener_date = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$wb_date = date($wp_date_format, strtotime($gener_date));
	    }
        
        $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
	    $getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
		
	    $wbreplay = get_option('_wswebinar_wbnreplay');
	    if (!empty($wbreplay)) {
		//User customized the template
		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = str_replace("\r", "<br />", $wbreplay);
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$ReplayTemplate = $text;
	    } else {
		ob_start();
		WsWebinarTemplate_AttendeeStarted::get($inputName, $post_id);
		$ReplayTemplate = ob_get_contents();
		ob_end_clean();
	    }
	    $title_wbnreplay = get_option('_wswebinar_wbnreplaysubject');
	    if (!empty($title_wbnreplay)) {
		//User customized the template
		$date_ = get_post_meta($post_id, '_wswebinar_gener_date', true);
		$date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);

		$replaceThese = array('[receiver-name]' => $inputName, '[webinar-title]' => get_the_title($post_id), '[webinar-link]' => get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ), '[webinar-date]' => $wb_date, '[webinar-time]' => $wb_time, '[webinar-timezone]' => $wb_timezone );
		$text = $title_wbnreplay;
		foreach ($replaceThese as $what => $with):
		    $newText = str_replace($what, $with, $text);
		    $text = $newText;
		endforeach;
		$EmailTitle = $text;
	    } else {
		$EmailTitle = __('Webinar Replay Link', WebinarSysteem::$lang_slug);
	    }
	    
	    $is_webinar_profile_links = get_option('_wswebinar_subscription');
		if($is_webinar_profile_links == "on") {
			$unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
	    	$manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
			$ReplayTemplate .= '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
		}
		
	    $MessagetoAttendee = $this->GeneralEmailTemplateTop($EmailTitle, $ReplayTemplate) . $this->GeneralEmailTemplateBottom();
	    $WebinarEmailHeader = array();
	    $WebinarEmailHeader[] = "MIME-Version: 1.0";
	    $WebinarEmailHeader[] = "Content-type: text/html; charset=utf-8";
	    $WebinarEmailHeader[] = "From: " . get_option('_wswebinar_email_sentFrom') . " <" . get_option('_wswebinar_email_senderAddress') . '>';

	    return wp_mail($inputEmail, $EmailTitle, $MessagetoAttendee, $WebinarEmailHeader);
	}
    }

    public function wswebinarsendscheduledmails() {
	$loop = new WP_Query(array(
	    'post_type' => 'wswebinars',
	    'meta_key' => '_wswebinar_gener_webinar_status',
	    'meta_value' => 'clo',
	    'meta_compare' => '!='
	));
	if ($loop->have_posts()) :
	    while ($loop->have_posts()) :
		$loop->the_post();

		if (WebinarSysteem::isRecurring(get_the_ID())) {
		    $time_zone = get_post_meta(get_the_ID(), '_wswebinar_timezoneidentifier', true);
		    $gener_time_occur_saved = get_post_meta(get_the_ID(), '_wswebinar_gener_time_occur', true);
		    
		    if($gener_time_occur_saved == 'recur')
		    {
				$occcur_times = WebinarSysteem::getRecurringInstancesInTime(get_the_ID());	
			} else if($gener_time_occur_saved == 'jit')
			{
				$occcur_times = WebinarSysteem::getJITInstancesInTime(get_the_ID());
			}

		    foreach ($occcur_times as $time) {
			$time_ = $time['time'] + 0;
			$atts = WebinarSysteemAttendees::getAttendiesByOccurance(get_the_ID(), $time['day'], date('H:i:s', $time_));
			if (!empty($atts)) {
			    $timestamp = WebinarSysteem::getWebinarTime(get_the_ID(), $atts{0});

			    if ($this->checkBeforeOneHour(get_the_ID(), $timestamp) || ($this->checkBefore5mins(get_the_ID(), $timestamp)) || $this->checkBetween23and26Hours(get_the_ID(), $timestamp)) {
				foreach ($atts as $att) {
				    $this->triggerApplicableMailSender($att, $timestamp, get_the_ID(), TRUE);
				}
			    }
			}
		    }
		} else {
		    $wswebinarTime = WebinarSysteem::getWebinarTime(get_the_ID()); //Get webinar time
		    $regs = WebinarSysteemAttendees::getAttendies(get_the_ID());
		    foreach ($regs as $reg)
			$this->triggerApplicableMailSender($reg, $wswebinarTime, get_the_ID(), FALSE);
		}
	    endwhile;
	endif;
    }

    private function triggerApplicableMailSender($reg, $webtime, $webinar_id, $is_recurring) {
	/*
	 * Email Types
	 * 
	 * One Hour Email = 1;
	 * One Day Email = 2;
	 * Webinar Starting Email = 3;
	 */
	if (empty($reg->id))
	    return;

	if ($this->checkBeforeOneHour($webinar_id, $webtime) && !WebinarSysteemAttendees::checkRecurringNotificationSent($reg, 1, $is_recurring)) {
	    $sentmail1hr = $this->SendMailtoAttendee1hr_Template($reg->name, $reg->email, $webinar_id);
	    if ($sentmail1hr == true) {
		WebinarSysteemAttendees::markAttendeeNotificationSend($reg, 1, $is_recurring);
		//WebinarSysteemAttendees::modifyAttendee($reg->id, array('onehourmailsent' => '1'), array('%d'));
	    }
	} elseif ($this->checkBetween23and26Hours($webinar_id, $webtime) && !WebinarSysteemAttendees::checkRecurringNotificationSent($reg, 2, $is_recurring)) {
	    $sentmail1day = $this->SendMailtoAttendee24hr_Template($reg->name, $reg->email, $webinar_id);
	    if ($sentmail1day == true) {
		WebinarSysteemAttendees::markAttendeeNotificationSend($reg, 2, $is_recurring);
		//WebinarSysteemAttendees::modifyAttendee($reg->id, array('onedaymailsent' => '1'), array('%d'));
	    }
	} elseif ($this->checkBefore5mins($webinar_id, $webtime) && !WebinarSysteemAttendees::checkRecurringNotificationSent($reg, 3, $is_recurring)) {
	    $wbstartingmail = $this->SendMailtoAttendeeStarted_Template($reg->name, $reg->email, $webinar_id);
	    if ($wbstartingmail == true) {
		WebinarSysteemAttendees::markAttendeeNotificationSend($reg, 3, $is_recurring);
		//WebinarSysteemAttendees::modifyAttendee($reg->id, array('wbstartingmailsent' => '1'), array('%d'));
	    }
	}
    }

    private function checkBeforeOneHour($webinar_id, $time) {
	if (($time - 3900) < WebinarSysteem::populateDateTime($webinar_id) && WebinarSysteem::populateDateTime($webinar_id) < $time) {
	    return TRUE;
	}
	return FALSE;
    }

    private function checkBetween23and26Hours($webinar_id, $time) {
	if (($time - 60 * 60 * 23) > WebinarSysteem::populateDateTime($webinar_id) && WebinarSysteem::populateDateTime($webinar_id) > ($time - 60 * 60 * 26))
	    return TRUE;
	return FALSE;
    }

    private function checkBefore5mins($webinar_id, $time) {
	if ($time > WebinarSysteem::populateDateTime($webinar_id) && WebinarSysteem::populateDateTime($webinar_id) > ($time - 60 * 5))
	    return TRUE;
	return FALSE;
    }

// add custom interval
    public function cron_add_5_minutes($schedules) {
// Adds once every 5 minutes to the existing schedules.
	$schedules['every5minutes'] = array(
	    'interval' => 60*5,
	    'display' => __('Once 5 minutes'),
	);

	return $schedules;
    }

    public function registerMailSender() {
	if (!wp_next_scheduled('wswebinarsendscheduledmails')) {
	    wp_schedule_event(time(), 'every5minutes', 'wswebinarsendscheduledmails');
	}
    }

    public static function getTimeDateString($post_id, $attendee) {
	$wbnTimeZone = get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
	$timeFormat = get_option('time_format');
	$dateFormat = get_option('date_format');
	$time = WebinarSysteem::getWebinarTime($post_id, $attendee);
	$date = '';
	if (WebinarSysteem::isRecurring($post_id)) {
	    $time = WebinarSysteem::getWebinarTime($post_id, $attendee);
	    $date = date($dateFormat, $time);
	} else {
	    $date = get_post_meta($post_id, '_wswebinar_gener_date', true);
	}

	return array('time' => $time, 'date' => $date);
    }

}
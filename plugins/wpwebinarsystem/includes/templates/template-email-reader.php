<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template-email-reader
 *
 * @author Thambaru Wijesekara
 */
class WsWebinarTemplate_Reader {

    public static function get($inputName, $inputEmail, $post_id) {
        $links = '';
        
        $wp_datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	    $wp_date_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_FORMAT);
	    $wp_time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
        
        $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
        $getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
        
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
        
        $is_webinar_profile_links = get_option('_wswebinar_subscription');
        if($is_webinar_profile_links == "on") {
            $unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
            $manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
            $links = '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
        }
        ?>

        <p><?php _e('Hi', WebinarSysteem::$lang_slug) ?> <?php echo $inputName; ?>,</p>
        <p style="margin:25px 0px;"><?php _e('Thank you for your registration for the webinar. Below you will find the details of the webinar.', WebinarSysteem::$lang_slug) ?> </p>
        <p><?php _e('Webinar name:', WebinarSysteem::$lang_slug) ?> <?php echo get_the_title($post_id); ?></p>
        <p><?php _e('Date:', WebinarSysteem::$lang_slug) ?> <?php echo $wb_date; ?></p>
        <p><?php _e('Time:', WebinarSysteem::$lang_slug) ?> <?php echo $wb_time; ?></p>
        <p><?php _e('Timezone:', WebinarSysteem::$lang_slug) ?> <?php echo $wb_timezone; ?></p>
        <p><a style="background-color: green; border-radius: 3px; border: 1px solid transparent; padding: 3px 20px; text-decoration: none;  color:white;" href="<?php echo get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ); ?>"><?php _e('Go to Webinar', WebinarSysteem::$lang_slug) ?></a></p>
        <p><?php _e('See you then!', WebinarSysteem::$lang_slug) ?> </p> 
        <p><?php _e('Regards', WebinarSysteem::$lang_slug) ?>,</p>
        <p><i><?php echo get_bloginfo('name'); ?></i></p>
        <?php
        echo $links;
    }

    public static function onOrderComplete($inputName, $inputEmail, $post_id) {
        $links = '';
        $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
        $getRequest = WebinarSysteemWooCommerceIntegration::generateUniquePurchasedURL();
        
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
		
		$is_webinar_profile_links = get_option('_wswebinar_subscription');
        if($is_webinar_profile_links == "on") {
            $unsubscribe_url = WebinarSysteemUserPages::getUnsubscribeUrl($post_id, $inputEmail);
            $manage_url = WebinarSysteemUserPages::getManageUrl($inputEmail);
            $links = '<p style="text-align: center;"><a href="'.$unsubscribe_url.'" target="_blank">'.__('Unsubscribe',  WebinarSysteem::$lang_slug).'</a>|<a href="'.$manage_url.'" target="_blank">'.__('Manage Profile',  WebinarSysteem::$lang_slug).'</a></p>';
        }
	    }
	    
        ?>

        <p><?php _e('Hi', WebinarSysteem::$lang_slug) ?> <?php echo $inputName; ?>,</p>
        <p style="margin:25px 0px;"><?php _e('Thank you for purchasing a webinar ticket. Please click on the button below to login for the webinar when it starts.', WebinarSysteem::$lang_slug) ?> </p>
        <p><?php _e('Webinar name:', WebinarSysteem::$lang_slug) ?> <?php echo get_the_title($post_id); ?></p>
        <p><?php _e('Date:', WebinarSysteem::$lang_slug) ?> <?php echo $wb_date; ?></p>
        <p><?php _e('Time:', WebinarSysteem::$lang_slug) ?> <?php echo $wb_time; ?></p>
        <p><?php _e('Timezone:', WebinarSysteem::$lang_slug) ?> <?php echo $wb_timezone; ?></p>
         <p><a style="background-color: green; border-radius: 3px; border: 1px solid transparent; padding: 3px 20px; text-decoration: none;  color:white;" href="<?php echo get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ); ?>"><?php _e('Attend Webinar', WebinarSysteem::$lang_slug) ?></a></p>
        <p><small><?php _e('If you can\'t see the link, please visit:', WebinarSysteem::$lang_slug) ?> <a href="<?php echo get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ); ?>"><?php echo get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ); ?></a></small></p>
        <p><?php _e('See you then!', WebinarSysteem::$lang_slug) ?> </p> 
        <p><?php _e('Regards', WebinarSysteem::$lang_slug) ?>,</p>
        <p><i><?php echo get_bloginfo('name'); ?></i></p>
        <?php
        echo $links;
    }

}

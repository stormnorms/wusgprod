<?php

class WebinarSysteemAjax {

    private static function returnError($message = 'Unknown') {
        header('Content-Type:application/json');
        echo json_encode(array('status' => FALSE, 'message' => $message));
        wp_die();
    }

    private static function returnData($data) {
        header('Content-Type:application/json');
        echo json_encode(array('status' => TRUE, 'data' => $data));
        wp_die();
    }

    public static function raiseHand() {
        $webinar_id = $_POST['webinar_id'];
        $subscriber = WebinarSysteemAttendees::getAttendee($webinar_id);
        if (isset($subscriber->id) && $subscriber->id > 0) {
            if (WebinarSysteemAttendees::modifyAttendee($subscriber->id, array('high_five' => ($subscriber->high_five == 1 ? 0 : 1)), array('%s'))) {
                self::returnData(array('status' => TRUE));
            }
        }
        self::returnError();
    }

    public static function unraiseHands() {
        $webinar_id = $_POST['webinar_id'];
        $subscribers = WebinarSysteemAttendees::getAttendies($webinar_id);
        foreach ($subscribers as $subscriber) {
            if (isset($subscriber->id) && $subscriber->id > 0) {
                WebinarSysteemAttendees::modifyAttendee($subscriber->id, array('high_five' => 0));
            }
        }
    }

    public static function getOnlineCount($web_id = 0) {
        global $wpdb;
        $table = WSWEB_DB_TABLE_PREFIX . 'subscribers';

        $query = "SELECT id,name,high_five FROM $table WHERE webinar_id = $web_id AND last_seen BETWEEN '" . date('Y-m-d H:i:s', strtotime('-18 seconds')) . "' AND '" . date('Y-m-d H:i:s') . "'";
        $data = $wpdb->get_results($query);

        $handRaisedAttendees = array();
        if (!empty($data)) {
            foreach ($data as $dataSlice) {
                $RaiseStatus = $dataSlice->high_five;
                $raisehand = ( $RaiseStatus == 1 ? true : false);
                array_push($handRaisedAttendees, $raisehand);
            }
        }
        return array('count' => count($data), 'attendees' => $data, 'raisehandset' => $handRaisedAttendees);
    }

    public static function syncImportImgs() {
        $image_values = $_GET['img_values'];
        $image_names = $_GET['img_names'];
        $main_bucket = array();
        $new_images_path = array();
        $names_set = array();
        $count = 0;
        foreach ($image_values as $img_url) {
// Download to current server
            $status = self::is_url_exist($img_url);
            if ($status) {
                $new_imgname = basename($img_url);
                $upload_direc = wp_upload_dir();
                $directory = $upload_direc['path'] . "/$new_imgname";
                $path = $upload_direc['url'] . "/$new_imgname";
                if (!file_exists($directory)) {
                    try {
                        copy($img_url, $directory);
                        array_push($names_set, $image_names[$count]);
                        array_push($new_images_path, $path);
                        self::registerImage($new_imgname, $directory, $path);
                    } catch (Exception $exc) {
                        $count++;
                    }
                } else {
                    array_push($names_set, $image_names[$count]);
                    array_push($new_images_path, $path);
                }
            }
            $count++;
        }
        $main_bucket['names'] = $names_set;
        $main_bucket['values'] = $new_images_path;
        echo json_encode($main_bucket);
        wp_die();
    }

    public static function registerImage($file_name, $file_path, $file_url) {
        try {
            $wp_filetype = wp_check_filetype($file_path, null);
            $attachment = array(
                'guid' => $file_url,
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => $file_name,
                'post_status' => 'inherit',
                'post_date' => date('Y-m-d H:i:s')
            );
            $attachment_id = wp_insert_attachment($attachment, $file_path);
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public static function is_url_exist($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public static function getChats($webinar_id, $page_category) {
        global $wpdb;
        $webinar_systeem = new WebinarSysteem();
        $table_chats = $wpdb->prefix . $webinar_systeem->db_tablename_chats;
        $table_subsc = $wpdb->prefix . $webinar_systeem->db_tablename_subscribers;
        $table_quest = $wpdb->prefix . $webinar_systeem->db_tablename_questions;
        $chats = $wpdb->get_results("SELECT $table_chats.id,$table_chats.webinar_id,attendee_id,content,timestamp,name,admin,private FROM `$table_chats` LEFT OUTER JOIN $table_subsc w ON $table_chats.attendee_id=w.id where $table_chats.webinar_id=$webinar_id order by id ASC;");
        $questions = $wpdb->get_results("SELECT * FROM `$table_quest` where webinar_id='$webinar_id' order by id desc;");
        $show_chatbox = (get_post_meta($webinar_id, '_wswebinar_' . $page_category . 'show_chatbox', true) == 'yes' ? 'true' : 'false');
        $show_quesbox = (get_post_meta($webinar_id, '_wswebinar_' . $page_category . 'askq_yn', true) == 'yes' ? 'true' : 'false');
        $renderer = array('questions' => $questions, 'chats' => $chats, 'show_chatbox' => $show_chatbox, 'show_questionbox' => $show_quesbox);
        return $renderer;
    }

    public static function incentiveStatus($post_id, $webinar_status) {
        $webinar_status = (empty($webinar_status) ? 'replay' : $webinar_status);
        if ($webinar_status == 'live' || $webinar_status == 'liv' || $webinar_status == 'cou') {
            $incentiveStatus = get_post_meta($post_id, '_wswebinar_livep_incentive_yn', true);
            return ($incentiveStatus === 'yes') ? array('isShow' => true) : array('isShow' => false);
        } else if ($webinar_status == 'replay' || $webinar_status == 'rep' || $webinar_status == 'cou') {
            $incentiveStatus = get_post_meta($post_id, '_wswebinar_replayp_incentive_yn', true);
            return ($incentiveStatus === 'yes') ? array('isShow' => true) : array('isShow' => false);
        }
    }

    public static function updateIncentive() {
        $post_status = $_POST['status'];
        $post_status = (empty($post_status) ? 'replay' : $post_status);
        $post_id = $_POST['post_id'];
        $saved_value_live = get_post_meta($post_id, '_wswebinar_livep_incentive_yn', true);
        $value_live = ($saved_value_live == 'yes' ? '' : 'yes');
        $saved_value_rep = get_post_meta($post_id, '_wswebinar_replayp_incentive_yn', true);
        $value_replay = ($saved_value_rep == 'yes' ? '' : 'yes');
        if ($post_status == 'live' || $post_status == 'liv' || $post_status == 'cou') {
            update_post_meta($post_id, '_wswebinar_livep_incentive_yn', $value_live);
        } else if ($post_status == 'replay' || $post_status == 'rep' || $post_status == 'cou') {
            update_post_meta($post_id, '_wswebinar_replayp_incentive_yn', $value_replay);
        }
        wp_die();
    }

    public static function transferLivepData() {
        $webinar_id = $_POST['webinar_id'];
        $webinar_status = $_POST['webinar_st'];
        $page_category = $_POST['page_state'];
        $page_category = ($page_category == 'cou' ? 'replayp_' : $page_category);
        $remember_token = $_POST['user_code'];

        $return_array = array(
            'last_seen' => true,
            'webinar_id' => 'undefined',
            'online_attendees' => FALSE,
            'chats' => FALSE,
            'incentive_status' => FALSE,
            'CTA_status' => FALSE,
            'hostdesc_status' => FALSE,
            'actionbox_status' => FALSE,
            'user_session' => true,
            'attended' => FALSE
        );

        $attendee = WebinarSysteemAttendees::getAttendee($webinar_id);
        if (isset($attendee->id) && $attendee->id > 0) {
            if (WebinarSysteemAttendees::modifyAttendee($attendee->id, array('last_seen' => gmdate('Y-m-d H:i:s')), array('%s'))) {
                $return_array['last_seen'] = TRUE;
                $return_array['webinar_id'] = $webinar_id;
            }
        } else {
            $return_array['last_seen'] = FALSE;
            $return_array['webinar_id'] = $webinar_id;
        }

// Get online attendees
        $online_attendees = self::getOnlineCount($webinar_id);
        $return_array['online_attendees'] = $online_attendees;
// End of get online attendees
//Fetch chats.
        $chats = self::getChats($webinar_id, $page_category);
        $return_array['chats'] = $chats;

//Incentive status
        $incentive_status = self::incentiveStatus($webinar_id, $webinar_status);
        $return_array['incentive_status'] = $incentive_status;

//CTA Button Status
        $return_array['CTA_status'] = self::getCTAStatus($webinar_id, $page_category);
//End of CTA Button Status
//
// Host and description boxes status
        $return_array['hostdesc_status'] = self::getHostDescStatus($webinar_id, $page_category);
// end of Host and description boxes status
// Actionbox status
        $return_array['actionbox_status'] = self::getActionboxStatus($webinar_id, $page_category);
// end of actionbox status
        // Update Attended if user visited to live page.
        $attended = 'false';
        if ($page_category == 'livep_' || $page_category == 'replayp_') {
            WebinarSysteemAttendees::modifyAttendee($attendee->id, array('attended' => '1'), array('%d'));
            $attended = 'true';
        }
        $return_array['attended'] = $attended;
        
        $has_session = self::checkSession($remember_token);
        $return_array['user_session'] = $has_session;

        self::returnData($return_array);
    }

    public static function getCTAStatus($webinar_id, $page) {
        $isTimed = get_post_meta($webinar_id, '_wswebinar_' . $page . 'call_action', true) == 'aftertimer';
        if ($isTimed) {
            $plus_minutes = get_post_meta($webinar_id, '_wswebinar_' . $page . 'cta_show_after', true);
            $webinar_started = WebinarSysteem::getWebinarTime($webinar_id, WebinarSysteemAttendees::getAttendee($webinar_id));
            $add_time = strtotime('+ ' . $plus_minutes . ' minutes', $webinar_started);
            $cur_time = strtotime(WebinarSysteem::getTimezoneTime($webinar_id));
            return ($add_time < $cur_time ? TRUE : FALSE);
        } else {
            $CTA_status = get_post_meta($webinar_id, '_wswebinar_' . $page . 'manual_show_cta', true);
            return ($CTA_status == 'yes' ? TRUE : FALSE);
        }
    }

    public static function setCTAStatus() {
        ob_start();
        $webinar_id = $_POST['webinar_id'];
        $cta_status = $_POST['cta_status'];
        $page_state = $_POST['webinar_status'];

        if (!empty($webinar_id) && !empty($cta_status) && !empty($page_state)) {
            update_post_meta($webinar_id, '_wswebinar_' . $page_state . 'manual_show_cta', $cta_status);
            ob_end_clean();
            echo json_encode(array(
                'error' => false,
                'showStatus' => ($cta_status == 'yes' ? TRUE : FALSE)
            ));
        } else {
            echo json_encode(array(
                'error' => true,
                'showStatus' => "webinar ID : $webinar_id - CTA ststus : $cta_status - PAGE : $page_state",
            ));
        }
        wp_die();
    }

    public static function setHostUpdateBox() {
        $webinar_id = $_POST['webinar_id'];
        $page_state = $_POST['webinar_status'];
        $box_status = $_POST['box_status'];

        update_post_meta($webinar_id, '_wswebinar_' . $page_state . 'hostbox_yn', $box_status);
        update_post_meta($webinar_id, '_wswebinar_' . $page_state . 'webdes_yn', $box_status);
    }

    public static function getHostDescStatus($webinar_id, $page) {
        $hostbox = get_post_meta($webinar_id, '_wswebinar_' . $page . 'hostbox_yn', true);
        $descbox = get_post_meta($webinar_id, '_wswebinar_' . $page . 'webdes_yn', true);
        return ($hostbox == 'yes' | $descbox == 'yes' ? TRUE : FALSE);
    }

    public static function setActionbox() {
        $webinar_id = $_POST['webinar_id'];
        $page_state = $_POST['webinar_status'];
        $box_status = $_POST['box_status'];

        update_post_meta($webinar_id, '_wswebinar_' . $page_state . 'show_actionbox', $box_status);
    }

    public static function getActionboxStatus($webinar_id, $page) {
        $meta = get_post_meta($webinar_id, '_wswebinar_' . $page . 'show_actionbox', true);
        return ($meta == 'yes' ? TRUE : FALSE);
    }

    public function dismissAdminNotices() {
        if (!empty($_GET['wswebinar_ajax_dismiss']) | !empty($_GET['webinar_postnotf_dismiss'])) {
            $userInfo_ = wp_get_current_user();
            if (!empty($_GET['wswebinar_ajax_dismiss'])) {
                add_user_meta($userInfo_->ID, '_wswebinar_notdismiss', 'yes', TRUE);
            } else if (!empty($_GET['webinar_postnotf_dismiss'])) {
                add_user_meta($userInfo_->ID, '_wswebinar_postnotdismiss', 'yes', TRUE);
            }
            header('Content-Type: application/json');
            echo json_encode(TRUE);
            exit();
        } else {
            return;
        }
    }

    /*
     * Delete selected or all chats.
     */

    public function deleteChats() {
        $chat_ids = $_POST['messages'];
        foreach ($chat_ids as $id) {
            $this->deleteChatEntry($id);
        }
        wp_die();
    }

    public function deleteChatEntry($chat_id) {
        global $wpdb;
        $webinar_system = new WebinarSysteem();
        $table = $wpdb->prefix . $webinar_system->db_tablename_chats;
        $wpdb->delete($table, array('id' => $chat_id), '%d');
    }

    public function newAattendeeCSV() {
        $values = $_POST['file_values'];
        if (!empty($values)) {
            foreach ($values as $value) {
                $slice = explode(",", $value);
                if (!empty($slice[0]) && !empty($slice[1])) {
                    $att_name = str_replace("\\", '', $slice[0]);
                    $att_name = str_replace('"', '', $att_name);
                    $att_mail = $slice[1];
                    $webinar_id = $_POST['webinar_id'];
                    $send_email = $_POST['send_mails'];
                    $isRecurring = WebinarSysteem::isRecurring($webinar_id);
                    $timeslot = ($isRecurring ? $_POST['recurring_time'] : '0');
                    $webinar_systeem_class = new WebinarSysteem();
                    global $wpdb;
                    $table_name = $wpdb->prefix . '' . $webinar_systeem_class->db_tablename_subscribers;
                    if (!self::is_already_subscribed_webinar($webinar_id, $att_mail)) {
                    
                    //Get webinar timezone
					$time_zone = get_post_meta($webinar_id, '_wswebinar_timezoneidentifier', true);
					if (!empty($time_zone)) {
		    			$date = new DateTime("now", new DateTimeZone($time_zone));
		    			$current_date_time = $date->format('Y-m-d H:i:s');
					} else {
		    			$current_date_time = current_time("Y-m-d H:i:s");
					}
                        if (!$isRecurring) {
                            $data = array(
                                'name' => $att_name,
                                'email' => $att_mail,
                                'webinar_id' => $webinar_id,
                                'last_seen' => '0000-00-00 00:00:00',
                                'time' => $current_date_time,
                                'active' => '1');
                        } else {
                            $timeslot = str_replace(array( '(', ')' ), '', $timeslot);
                            $data = array(
                                'name' => $att_name,
                                'email' => $att_mail,
                                'webinar_id' => $webinar_id,
                                'last_seen' => '0000-00-00 00:00:00',
                                'exact_time' => date("Y-m-d H:i:s", strtotime($timeslot)),
                                'time' => $current_date_time,
                                'watch_time' => date("H:i:s", strtotime($timeslot)),
                                'watch_day' => strtolower(date("D", strtotime($timeslot))),
                                'active' => '1');
                        }
                        
                        $wpdb->insert($table_name, $data);
                        
                        if ($send_email == "true") {
                            $messages_ob = new WebinarSysteemMails();
                            $messages_ob->SendMailtoReader($att_name, $att_mail, $webinar_id);
                        }
                    }
                }
            }
        }
        echo json_encode(array(
            'error' => false,
            'data' => $values,
            'count' => $count
        ));
        wp_die();
    }

    public static function is_already_subscribed_webinar($webinar_id, $email) {
        global $wpdb;
        $webinar_systeem_class = new WebinarSysteem();
        $table_name = $wpdb->prefix . '' . $webinar_systeem_class->db_tablename_subscribers;
        $subs = $wpdb->get_row("SELECT * FROM $table_name WHERE email='$email' AND webinar_id='$webinar_id'");
        return !empty($subs);
    }

    public function deleteQuestions() {
        global $wpdb;
        $webinar_system = new WebinarSysteem();
        $table = $wpdb->prefix . $webinar_system->db_tablename_questions;
        $delete_type = $_POST['delete_type'];
        $que_ids = $_POST['question_ids'];
        $webinar_id = $_POST['webinar_id'];

        if ($delete_type == 'all') {
            $wpdb->delete($table, array('webinar_id' => $webinar_id), '%d');
        } else {
            foreach ($que_ids as $id) {
                $wpdb->delete($table, array('id' => $id), '%d');
            }
        }
        echo json_encode(array(
            'status' => 'true',
            'ids' => $que_ids,
            'type' => $delete_type
        ));
    }

    /*
     * Check the cookie value with the database.
     * @return true : If user has permission to surf on page.
     */

    public static function checkSession($random_var = null) {

        if ($random_var != null) {
            global $wpdb;
            $webinar_system = new WebinarSysteem();
            $table = $wpdb->prefix . $webinar_system->db_tablename_subscribers;
            $row = $wpdb->get_row("SELECT * FROM $table WHERE random_key='$random_var'");
            if (empty($row)) {
                $webinar_system->clearUserSession();
                return false;
            } else {
                return true;
            }
        }
    }
    
    /*
     * Get the recurring input times on the basis of day selected.
     * @return input times array.
     */
     public static function getInputTimes(){
	 	$url     = wp_get_referer();
		$post_id = url_to_postid( $url ); 
		$input_day = $_GET['input_day'];
		if(isset($_GET['wbnid'])){
			$post_id = $_GET['wbnid'];
		}
		
		$gener_time_occur_saved = get_post_meta($post_id, '_wswebinar_gener_time_occur', true);
		$timeabbr = get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
		$wpoffset = get_option('gmt_offset');
		$gmt_offset = WebinarSysteem::formatTimezone(( $wpoffset > 0) ? '+' . $wpoffset : $wpoffset);
		$timeZone = '(' . ( (!empty($timeabbr)) ? WebinarSysteem::getTimezoneAbbreviation($timeabbr) : 'UTC ' . $gmt_offset ) . ') ';
	
	    $time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
		$date_format = get_option('date_format');
		
		$metaval = get_post_meta($post_id, '_wswebinar_gener_timeslot_count', true);
		$timeslot_count = (empty($metaval) ? 100 : $metaval);
		
		if($gener_time_occur_saved == 'recur')
		{
		$recurr_instances = WebinarSysteem::getRecurringInstances($post_id);
		$gener_rec_times_saved = get_post_meta($post_id, '_wswebinar_gener_rec_times', true);
		
		$gener_rec_times_array = array();
		
		if (!empty($gener_rec_times_saved)) {
	    	$gener_rec_times_array = json_decode($gener_rec_times_saved, TRUE);
		}
		sort($gener_rec_times_array);
		$input_times = array();
		$input_times[ ] = array( 'label' => __('Select a time', WebinarSysteem::$lang_slug), 'value' => 'default' );
		
		$input_day = strtolower(substr($input_day, 0, 3));
		$day_string = "next $input_day";
		if (strtolower(date('D')) == $input_day)
		$day_string = "this $input_day";
		$input_day = strtotime($day_string);
		$input_date = date($date_format, strtotime('today ' . date("D", $input_day)));
		$input_date = str_replace(',','',$input_date);
		
        $showing_count = 1;
		$is_slot_available = FALSE;
		foreach ($gener_rec_times_array as $time) {
		if ($showing_count > $timeslot_count)
		continue;
			
		$slot_time_int = strtotime($input_date . ' ' . $time);
        $current_time = WebinarSysteem::populateDateTime($post_id);
		if ($current_time < $slot_time_int) {
			$is_slot_available = TRUE;
			$label = date($time_format, $slot_time_int) . " ". $timeZone;
			$value = date('H:i', $slot_time_int);
			$input_times[ ] = array( 'label' => $label, 'value' => $value );
			$showing_count++;
		} 
	}
         if(!$is_slot_available){
		$input_times[ ] = array( 'label' => __('No slots available', WebinarSysteem::$lang_slug), 'value' => 'no' );
	    }
		echo json_encode($input_times);
		wp_die();
		    
		} else if($gener_time_occur_saved == 'jit')
		{
		$justintime_instances = WebinarSysteem::getJustinTimeInstances($post_id);
		
		$input_times = array();
		$input_times[ ] = array( 'label' => __('Select a time', WebinarSysteem::$lang_slug), 'value' => 'default' );
		$input_day = strtolower(substr($input_day, 0, 3));
		$day_string = "next $input_day";
		if (strtolower(date('D')) == $input_day)
		$day_string = "this $input_day";
		$input_day = strtotime($day_string);
		$input_date = date($date_format, strtotime('today ' . date("D", $input_day)));
		$input_date = str_replace(',','',$input_date);
		$showing_count = 1;
		$is_slot_available = FALSE;

		foreach($justintime_instances['times'] as $time){
			if($showing_count > $timeslot_count)
			continue;
			
			$slot_time_int = strtotime($input_date . ' ' . $time);
			
			$current_time = WebinarSysteem::populateDateTime($post_id);
			
			if($current_time < $slot_time_int){
				
				$is_slot_available = TRUE;
				$label = date($time_format, $slot_time_int) . " ". $timeZone;
				$value = date('H:i', $slot_time_int);
				$input_times[ ] = array( 'label' => $label, 'value' => $value );
				$showing_count++;
			}
			
		}
		
		if(!$is_slot_available){
		$input_times[ ] = array( 'label' => __('No slots available', WebinarSysteem::$lang_slug), 'value' => 'no' );
	    }
	    
	    echo json_encode($input_times);
		wp_die();
		}
		
	 }

}

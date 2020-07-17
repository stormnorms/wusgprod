<?php

class WebinarSysteemAttendees {

    public static function wbn_attendees_list() {
	?>
	<div class="wrap wswebinarwrap">
	    <div class="wswebinarLogo">
		<img src="<?php echo plugins_url('images/WebinarSysteem-logo.png', __FILE__); ?>" />
	    </div>
	    <div style="clear: both"></div>
	    <h2><?php _e('WP WebinarSystem Attendees', WebinarSysteem::$lang_slug); ?></h2>
	    <p><?php _e('Select webinar to view attendees for active webinars', WebinarSysteem::$lang_slug); ?></p>


	    <div id="wpbody">
		<div class="tablenav top">
		    <form action="edit.php">
			<div class="actions alignleft ">
			    <input type="hidden" name="post_type" value="wswebinars">
			    <input type="hidden" name="page" value="wswbn-attendees">
			    <?php
			    $loop = new WP_Query(array('post_type' => 'wswebinars', 'posts_per_page' => -1));
			    $webinar_data = array();

			    if ($loop->have_posts()) :
				?><select onchange="jQuery(this).closest('form').trigger('submit');" name='id' class="selectwebinar"> <?php
				while ($loop->have_posts()) : $loop->the_post();

				    $webinar_id = get_the_ID();
				    
				    $gener_time_occur_saved = get_post_meta($webinar_id, '_wswebinar_gener_time_occur', true);
				    $timeslots = '';
				    if($gener_time_occur_saved == 'recur'){
						$timeslots = WebinarSysteem::getRecurringInstancesInTime($webinar_id);
					} else if($gener_time_occur_saved == 'jit') {
						$timeslots = WebinarSysteem::getJITInstancesInTime($webinar_id);
					}
					
				    array_push($webinar_data, array(
					'webinar_id' => $webinar_id,
					'isRecurring' => WebinarSysteem::isRecurring($webinar_id),
					'timeslots' => $timeslots,
				    ));
				    ?>
					<option value="<?php the_ID(); ?>" <?php
					$webinarID = (isset($_GET['id']) ? $_GET['id'] : get_the_ID() );
					echo (get_the_ID() == $webinarID ? 'selected' : null );
					?>><?php the_title(); ?></option>
						<?php
					    endwhile;
					    ?></select><?php
			    else:
				?>
	    		    <select class="selectwebinar"><option selected><?php _e('Please add webinars to listup.', WebinarSysteem::$lang_slug) ?></option></select><?php
			    endif;
			    ?>
			    <script>
				var WB_DATA = <?php echo json_encode($webinar_data); ?>
			    </script>
			</div>
			<?php
			$btnsdisabled = TRUE;
			if ($loop->have_posts()) :
			    $post_id = (isset($_GET["id"]) ? $_GET["id"] : get_the_ID());
			    $regs = self::getAttendies($post_id);
			    $btnsdisabled = ($regs == null ? true : false);
			endif;
			?>
			<div class="actions alignleft">
			    <input type='button' value='Remove Selected' <?php echo ($btnsdisabled ? 'disabled' : NULL); ?> class='removeAttendees button'>
			    <input type='button' value='Add New' class='addAttendee button'>
			</div>
			<div class="actions alignright">
			    <span class="attendees-top">
				<input postid="<?php echo $post_id; ?>" type="button" <?php echo ($btnsdisabled ? 'disabled' : NULL); ?> class="button exportbcc" value="Export BCC" />
				<input postid="<?php echo $post_id; ?>" type="button" <?php echo ($btnsdisabled ? 'disabled' : NULL); ?> class="button exportcsv" value="Export CSV" />
			    </span>   
			</div>
		</div>
		</form>
		<table class="wp-list-table widefat">
		    <thead>
			<tr scope="row">
			    <td scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1"></td>
			    <th scope="col" class="manage-column">#</th>
			    <th scope="col" class="manage-column"><?php _e('Name', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('E-Mail', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('Registered On', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('Registered For', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('Attended', WebinarSysteem::$lang_slug); ?></th>
			    <?php
			    $fields = json_decode(get_post_meta($post_id, '_wswebinar_regp_custom_field_json', true));
			    if (!empty($fields))
				foreach ($fields as $field) {
				    ?>
				    <th scope="col" class="manage-column"><?php echo $field->labelValue ?></th>
				<?php } ?>
			</tr>
		    </thead>
		    <tbody>
			<?php
			$datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
			if (!empty($regs)) :
			    $post_id = (isset($_GET["id"]) ? $_GET["id"] : get_the_ID());
			    $count = count($regs) + 1;
			    $metaCount = 0;
			    $isRecurring = WebinarSysteem::isRecurring($post_id);
			    $webinar_type = get_post_meta($post_id, '_wswebinar_gener_webinar_status', true);
			    if (is_array($regs)):
				$exportdisabled = false;
				foreach ($regs as $reg):
				    ?>
		    		<tr scope="row" id="attendee-row-<?php echo $reg->id; ?>" class="<?php echo ( --$count % 2 ? "alternate" : null) ?>">
		    		    <th class="check-column"><input type="checkbox" class="select-attendees" id="cb-select-<?php echo $reg->id; ?>" value="<?php echo $reg->id; ?>" name="attendeeid[]"></th>
		    		    <td><?php echo $count ?></td>
		    		    <td><?php echo $reg->name; ?></td>
		    		    <td><a href="mailto:<?php echo $reg->email; ?>" title="Mail to <?php echo $reg->email; ?>"><?php echo $reg->email; ?></a></td>
		    		    <td><?php echo date($datetime_format, strtotime($reg->time)) ?></td>
		    		    <td><?php echo date($datetime_format, ($isRecurring ? strtotime($reg->exact_time) : WebinarSysteem::getWebinarTime($post_id))); ?></td>
		    		    <td><?php echo ($reg->attended != '1' ? 'No' : 'Yes'); ?></td>
                                    
					<?php
                                        $fields = json_decode(get_post_meta($post_id, '_wswebinar_regp_custom_field_json', true));
                                        if (!empty($fields)){
                                            foreach ($fields as $field) { ?>
                                                <td><?php echo (WebinarSysteem::getFieldValue($reg, $field->id)); ?></td>
                                    <?php 
                                            }
                                        }
                                        ?>
		    		</tr>
				    <?php
				    $metaCount++;
				endforeach;
			    endif;
			else:
			    ?>
	    		<tr scope="row" class="alternate"> <td id="no-attendees" colspan="7"><?php _e('No Attendees :(', WebinarSysteem::$lang_slug) ?></td> </tr>
			<?php endif;
			?>
		    </tbody>
		    <tfoot><tr scope="row">
			    <td scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1"></td>
			    <th scope="col" class="manage-column">#</th>
			    <th scope="col" class="manage-column"><?php _e('Name', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('E-Mail', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('Registered On', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('Registered For', WebinarSysteem::$lang_slug); ?></th>
			    <th scope="col" class="manage-column"><?php _e('Attended', WebinarSysteem::$lang_slug); ?></th>
			    <?php
			    $fields = json_decode(get_post_meta($post_id, '_wswebinar_regp_custom_field_json', true));
			    if (!empty($fields))
				foreach ($fields as $field) {
				    ?>
				    <th scope="col" class="manage-column"><?php echo $field->labelValue ?></th>
				<?php } ?>
			</tr>
		    </tfoot>
		</table>
		<div class="attendees-bottom">
		    <input postid="<?php echo $post_id; ?>" type="button" <?php echo ($btnsdisabled ? 'disabled' : NULL); ?> class="button exportbcc" value="Export BCC">
		    <input postid="<?php echo $post_id; ?>" type="button" <?php echo ($btnsdisabled ? 'disabled' : NULL); ?> class="button exportcsv" value="Export CSV">
		</div>
	    </div>
	</div>

	<div id="hiddenContent" style="display:none;">
	    <h3>Add new attendee for your webinars <img class="new_at_loader" src="<?php echo plugins_url('includes/images/loading_small.GIF', WSWEB_FILE); ?>"></h3>
	    <div id="popupcontent">
		<form method="POST">

		    <div id="ws_popup_error">
			<p><?php echo _e("Please enter valid name and email to add new attendee.", WebinarSysteem::$lang_slug); ?></p>
		    </div>
		    <div id="single_attendee_data">
			<label for="newatt_name">Attendee Name : </label> <br>
			<input id="newatt_name" name="newatt_name" type="text" style="width: 100%;">

			<label for="newatt_email">Attendee Email : </label> <br>
			<input id="newatt_email" name="newatt_email" type="text" style="width: 100%;">
		    </div>
		    <?php if ($loop->have_posts()) : ?>
	    	    <label for="select-webinar" >Webinar :</label> <br>
	    	    <select id="select-webinar" name='webinar_id' style="width: 100%;margin: 0px 0px 10px 0px;"> <?php
			    while ($loop->have_posts()) : $loop->the_post();
				?>
				<option data-isrec="<?php echo (WebinarSysteem::isRecurring(get_the_ID()) ? 'true' : 'false'); ?>" value="<?php the_ID(); ?>" <?php
				$webinarID = (isset($_GET['id']) ? $_GET['id'] : get_the_ID() );
				echo (get_the_ID() == $webinarID ? 'selected' : null );
				?>><?php the_title(); ?></option>
					<?php
				    endwhile;
				    ?></select><?php
		    else:
			?>
	    	    <select class="selectwebinar"><option selected><?php _e('Please add webinars to listup.', WebinarSysteem::$lang_slug) ?></option></select><?php
		    endif;
		    ?>
		    <div id="ws_newatpop_rec_div" style="display: none;">        
			<label for="ws_newatpop_recurring_times" >Timeslot : </label><br>
			<select id="ws_newatpop_recurring_times" name="recurring_time" style="width: 100%">

			</select>
		    </div>

		    <label><?php echo _e('Send webinar confirmation e-mail to attendee?', WebinarSysteem::$lang_slug); ?></label><br>
		    <input id="new_at_sendconf" name="new_at_sendconf" data-switch="true" type="checkbox" data-on-text="Yes" data-off-text="No" value="yes" data-size="large" ><br>

		    <button type="submit" id="ws_save_new_attendee" class="button-primary pop-up-btn" style="margin-top: 5px;">Save Attendee</button>
		    <button type="submit" id="ws_save_new_attendees" class="button-primary pop-up-btn" style="margin-top: 5px;">Import Attendees</button>
		    <button type="button" id="import_csv_attendees" class="csv_import_btn" >Import CSV</button>
		    <label id="csv_file_show">No file selected</label>
		    <input type="file" name="attendee_csv_file" id="attendee_csv_file" style="display: none;">
		</form>
	    </div> 
	</div>
	<?php
    }

    static function getMetaIdByKey($post_id, $meta_key) {
	global $wpdb;
	$mid = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key));
	return $mid;
    }

    public static function getAttendies($webinar_id) {
	global $wpdb;
	$table = WSWEB_DB_TABLE_PREFIX . 'subscribers';
	$query = "SELECT * FROM $table WHERE webinar_id = $webinar_id";
	$query .= " ORDER BY id DESC";
	return $wpdb->get_results($query);
    }

    public static function getAttendiesByOccurance($webinar_id, $day, $time) {
	global $wpdb;
	$table = WSWEB_DB_TABLE_PREFIX . 'subscribers';
	$query = "SELECT * FROM $table WHERE webinar_id = $webinar_id AND watch_day = '$day' AND watch_time = '$time'";
	$query .= " ORDER BY id DESC";
	return $wpdb->get_results($query);
    }

    public static function getAttendee($webinar_id) {
	$attendee_localdata = WebinarSysteemAttendees::getAttendeeLocalData();
	if (!isset($attendee_localdata->email) || !isset($attendee_localdata->key))
	    return array();
	global $wpdb;
	$table = WSWEB_DB_TABLE_PREFIX . 'subscribers';
	$query = "SELECT * FROM $table WHERE email = '" . $attendee_localdata->email . "' AND webinar_id = $webinar_id";
	if (!empty($key))
	    $query .= " AND secretkey = '$key'";
	$query .= " LIMIT 1";
	return $wpdb->get_row($query);
    }

    public static function getAttendeeByEmail($attendee_email, $webinar_id) {
	global $wpdb;
	$table = WSWEB_DB_TABLE_PREFIX . 'subscribers';
	$query = "SELECT * FROM $table WHERE email = '" . $attendee_email . "' AND webinar_id = $webinar_id";
	if (!empty($key))
	    $query .= " AND secretkey = '$key'";
	$query .= " LIMIT 1";
	return $wpdb->get_row($query);
    }

    public static function getAttendeeByID($attendee_id) {
	global $wpdb;
	$table = WSWEB_DB_TABLE_PREFIX . 'subscribers';
	$query = "SELECT * FROM $table WHERE id = '" . $attendee_id . "' LIMIT 1 ";
	return $wpdb->get_row($query);
    }

    public static function saveAttendie($array, $format = array()) {
	global $wpdb;
	
	$attendee_data = self::getAttendeeByEmail($array['email'], $array['webinar_id']);
	
	if(!empty($attendee_data)){
		self::modifyAttendee($attendee_data->id, $array, $format);
	} else
	{
		$num = $wpdb->insert(WSWEB_DB_TABLE_PREFIX . "subscribers", $array, $format);	
	}
	
    }

    public static function getTimeDifferenceToWebinar($webinar_id) {
	$attendee = self::getAttendee($webinar_id);
	$web_time = WebinarSysteem::getWebinarTime($webinar_id, $attendee);
	return $web_time - WebinarSysteem::populateDateTime($webinar_id);
    }

    public static function saveNotificationSend($attendee_id, $type) {
	global $wpdb;
	$num = $wpdb->insert(WSWEB_DB_TABLE_PREFIX . "notifications", array('attendee_id' => $attendee_id, 'notification_type' => $type), array('%d', '%d'));
    }

    public static function getNumberOfSubscriptions($webinar_id) {
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(*) FROM " . WSWEB_DB_TABLE_PREFIX . "subscribers WHERE webinar_id = $webinar_id");
    }

    public static function checkRecurringNotificationSent($attendee, $type, $is_recurring) {
	/*
	 * Email Types
	 * 
	 * One Hour Email = 1;
	 * One Day Email = 2;
	 * Webinar Starting Email = 3;
	 */

	if ($is_recurring) {
	    global $wpdb;
	    $count = $wpdb->get_var("SELECT COUNT(*) FROM " . WSWEB_DB_TABLE_PREFIX . "notifications WHERE attendee_id = $attendee->id AND notification_type = $type AND DATE(sent_at) > (NOW() - INTERVAL 6 DAY) ");
	    if ($count > 0)
		return TRUE;
	    return FALSE;
	}else {
	    if ($type == 1) {
		if ($attendee->onehourmailsent == 1)
		    return TRUE;
		return FALSE;
	    }elseif ($type == 2) {
		if ($attendee->onedaymailsent == 1)
		    return TRUE;
		return FALSE;
	    }elseif ($type == 3) {
		if ($attendee->wbstartingmailsent == 1)
		    return TRUE;
		return FALSE;
	    }
	}
    }

    public static function markAttendeeNotificationSend($attendee, $type, $is_recurring) {
	/*
	 * Email Types
	 * 
	 * One Hour Email = 1;
	 * One Day Email = 2;
	 * Webinar Starting Email = 3;
	 */

	if ($is_recurring) {
	    self::saveNotificationSend($attendee->id, $type);
	} else {
	    if ($type == 1) {
		self::modifyAttendee($attendee->id, array('onehourmailsent' => '1'), array('%d'));
	    } elseif ($type == 2) {
		self::modifyAttendee($attendee->id, array('onedaymailsent' => '1'), array('%d'));
	    } elseif ($type == 3) {
		self::modifyAttendee($attendee->id, array('wbstartingmailsent' => '1'), array('%d'));
	    }
	}
    }

    /*
     * 
     * Create and let user to download attendee CSV of a requested webinar.
     * 
     */

    public static function createCsvFile() {
	if (!isset($_GET['wswebinar_createcsv']) || !isset($_GET['postid']))
	    return false;

	$postid = $_GET["postid"];
	$regs = WebinarSysteemAttendees::getAttendies($postid);

	$getTitle = get_the_title($postid);
	$posttitle = !empty($getTitle) ? $getTitle : 'Unknown';

	$csvTitle = 'webinarsysteem_subscriptions_' . self::adjustAndGetTitleForFileNames($posttitle) . '_' . time() . '.csv';

	$csvArray = array();

        $csvHeaders = array('Name', 'Email', 'Registered on', 'Registered for', 'Attended');
	$customHeaders = (array) json_decode(get_post_meta($postid, '_wswebinar_regp_custom_field_json', true));
        
	$csvCustomHeaders = [];
	foreach ($customHeaders as $customField){
            array_push($csvHeaders, $customField->labelValue);            
        }
	$csvArray[] = $csvHeaders;

	$datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	$isRecurring = WebinarSysteem::isRecurring($postid);
        
	foreach ($regs as $regw):
            $custom_field_values = array();
            $customValues = json_decode($regw->custom_fields);
            foreach ($customValues as $value) {
                array_push($custom_field_values, $value->value);
            }
	    $data = array(
		!empty($regw->name) ? $regw->name : '',
		!empty($regw->email) ? $regw->email : '',
		!empty($regw->time) ? date($datetime_format, strtotime($regw->time)) : '',
		!empty($regw->time) ? date($datetime_format, ($isRecurring ? strtotime($regw->exact_time) : WebinarSysteem::getWebinarTime($postid))) : '',
		$regw->attended == 1 ? 'Yes' : 'No'
	    );

	    $csvArray[] = array_merge($data, $custom_field_values);

        endforeach;
	self::convertToCsv($csvArray, $csvTitle, ',');
	exit();
    }

    private static function adjustAndGetTitleForFileNames($posttitle) {
	return preg_replace("/[\s_]/", "_", preg_replace("/[\s-]+/", " ", preg_replace("/[^a-z0-9_\s-]/", "", strtolower($posttitle))));
    }

    /*
     * 
     * Create and let user to download attendee BCC list of a requested webinar.
     * 
     */

    public static function createBccFile() {
	if (!isset($_GET['wswebinar_createbcc']) || !isset($_GET['postid']))
	    return false;

	$postid = $_GET["postid"];
	$regs = WebinarSysteemAttendees::getAttendies($postid);

	$getTitle = get_the_title($postid);
	$posttitle = !empty($getTitle) ? $getTitle : 'Unknown';

	$textTitle = 'webinarsysteem_bcclist_' . self::adjustAndGetTitleForFileNames($posttitle) . '_' . time() . '.txt';

	$datetime_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_DATE_TIME_FORMAT);
	$isRecurring = WebinarSysteem::isRecurring($postid);

	$bccArray = array();
	foreach ($regs as $regw):
	    $registered_on = date($datetime_format, strtotime($regw->time));
	    $registered_for = date($datetime_format, ($isRecurring ? strtotime($regw->exact_time) :
            WebinarSysteem::getWebinarTime($postid)));
	    $attended = $regw->attended == 1 ? 'Yes' : 'No';            
            $customFields = "";
            $attObj = json_decode($regw->custom_fields);
            foreach ($attObj as $data) {
                $fieldName = WebinarSysteem::getFieldName($regw->webinar_id, $data->id);
                $fieldValue= $data->value;
                $customFields = $customFields. ' <'.$fieldName.' : '.$fieldValue.'> ';
            }            
	    $bccArray[] = $regw->name . ' <' . $regw->email . '><' . $registered_on . '><' . $registered_for . '><' . $attended . '>'. $customFields;
	endforeach;
	header('Content-type: text/plain; charset=utf-8');
	header('Content-Disposition: attachement; filename="' . $textTitle . '";');
	echo implode(", ", $bccArray);
	exit();
    }

    /*
     * 
     * Convert array to CSV
     * 
     */

    private static function convertToCsv($input_array, $output_file_name, $delimiter) {
	$temp_memory = fopen('php://memory', 'w');
	foreach ($input_array as $line) {
            fputcsv($temp_memory, $line, $delimiter);
	}
	fseek($temp_memory, 0);
	header('Content-Type: application/csv');
	header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
	fpassthru($temp_memory);
    }

    public static function removeAttendee() {
	$retrn = array('error' => FALSE);
	if (!isset($_POST['attid']) || empty($_POST['attid'])) {
	    $retrn['error'] = TRUE;
	} else {
	    global $wpdb;
	    if (is_array($_POST['attid'])) {
		foreach ($_POST['attid'] as $attid) {
		    $process = $wpdb->delete(WSWEB_DB_TABLE_PREFIX . 'subscribers', array('id' => ((int) $attid)));
		}
	    } else {
		$process = $wpdb->delete(WSWEB_DB_TABLE_PREFIX . 'subscribers', array('id' => ((int) $_POST['attid'])));
	    }
	    if (!$process) {
		$retrn['error'] = TRUE;
	    }
	}
	echo json_encode($retrn);
	wp_die();
    }

    public static function removeSingleAttendee($row_id){
	global $wpdb;
	return $wpdb->delete(WSWEB_DB_TABLE_PREFIX . 'subscribers', array('id' => $row_id));
	}
	
    public static function modifyAttendee($row_id, $columns, $format = array('%d')) {
	global $wpdb;
	return $wpdb->update(WSWEB_DB_TABLE_PREFIX . 'subscribers', $columns, array('id' => $row_id), $format, array('%d'));
    }

    public static function getAttendeeLocalData() {
	$obj = new stdClass();
	if (isset($_COOKIE['_wswebinar_registered_email']))
	    $obj->email = $_COOKIE['_wswebinar_registered_email'];
	if (isset($_COOKIE['_wswebinar_registered_key']))
	    $obj->key = $_COOKIE['_wswebinar_registered_key'];
	return $obj;
    }

    public function saveNewAttendee() {
	if (!empty($_POST['newatt_name']) && !empty($_POST['newatt_email'])) {
	    $att_name = $_POST['newatt_name'];
	    $att_mail = $_POST['newatt_email'];
	    $webinar_id = $_POST['webinar_id'];
	    $send_email = (!empty($_POST['new_at_sendconf']) && $_POST['new_at_sendconf'] == 'yes');
	    $isRecurring = WebinarSysteem::isRecurring($webinar_id);
	    $timeslot = ($isRecurring ? $_POST['recurring_time'] : '0');
	    $format = array('%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d');
	    if($isRecurring)
	    $timeslot = str_replace(array( '(', ')' ), '', $timeslot);
	    $webinar_systeem_class = new WebinarSysteem();
	    global $wpdb;
	    $table_name = $wpdb->prefix . '' . $webinar_systeem_class->db_tablename_subscribers;
	    if (!WebinarSysteemAjax::is_already_subscribed_webinar($webinar_id, $att_mail)) {

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
		

		if ($send_email) {
// Send confirmation email
		    $messages_ob = new WebinarSysteemMails();
		    $messages_ob->SendMailtoReader($att_name, $att_mail, $webinar_id);
		}
		
	    }
	}
    }
    
    static function updateAttendees() {
        if( !wp_next_scheduled( 'attendeecronjob' ) ) {  
            wp_schedule_event( time(), 'every5minutes', 'attendeecronjob' );  
        }
	}
	

	static function getInactiveAttendees($webinar_id) {
		global $wpdb;
		$table = WSWEB_DB_TABLE_PREFIX . 'subscribers';
		$query = "SELECT * FROM $table WHERE webinar_id = $webinar_id and attended = 0";
		$query .= " ORDER BY id DESC";
		return $wpdb->get_results($query);
    }
	
    static function makeAttendeesInactive() {
        $loop = new WP_Query(array(
            'post_type' => 'wswebinars',
            'meta_key' => '_wswebinar_gener_webinar_status',
            'meta_value' => 'clo',
            'meta_compare' => '!='
	));

	if($loop->have_posts()) :
		while ($loop->have_posts()):
		$loop->the_post();
		$webinar_id = get_the_ID();
		if(WebinarSysteem::isRecurring($webinar_id)) {
			
			$inactiveAteendees = WebinarSysteemAttendees::getInactiveAttendees($webinar_id);

			if($inactiveAteendees) {
				$duration = WebinarSysteem::getWebinarDuration($webinar_id);
				foreach($inactiveAteendees as $attendee) {
					$time_now = WebinarSysteem::populateDateTime($webinar_id);
					$attendee_time = strtotime($attendee->exact_time);

					$total_time = $attendee_time+$duration;

					if($time_now > $attendee_time+$duration) {
						self::modifyAttendee($attendee->id, array('active' => '0'), array('%d'));
					}
				}
			}
			
		}
		endwhile;
	endif;
	
		
	}

}

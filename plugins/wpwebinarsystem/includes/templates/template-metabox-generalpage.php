<div class="wswebinarStatusPanel">
    <?php $saved_status = get_post_meta($post->ID, '_wswebinar_gener_webinar_status', true); ?>
    <ul>
        <li class="<?php echo $this->decideClassOfStatusButtons("cou", $saved_status); ?>" status="cou"><?php echo WebinarSysteem::isAutomated($post->ID) ? __('Automated', WebinarSysteem::$lang_slug) : __('Countdown', WebinarSysteem::$lang_slug) ?></li>
        <li class="<?php echo $this->decideClassOfStatusButtons("liv", $saved_status); ?>" status="liv"><?php _e('Live', WebinarSysteem::$lang_slug) ?></li>
        <li class="<?php echo $this->decideClassOfStatusButtons("rep", $saved_status); ?>" status="rep"><?php _e('Replay', WebinarSysteem::$lang_slug) ?></li>
        <li class="<?php echo $this->decideClassOfStatusButtons("clo", $saved_status); ?>" status="clo"><?php _e('Closed', WebinarSysteem::$lang_slug) ?></li>
    </ul>            
    <input type="hidden" data-style-collect="true" name="gener_webinar_status" id="gener_webinar_status" value="<?php echo (!empty($saved_status) ? $saved_status : 'cou'); ?>">
</div>

<div class="form-group">
    <?php $gener_air_type_saved = get_post_meta($post->ID, '_wswebinar_gener_air_type', true); ?>
    <label><?php _e('Webinar Type', WebinarSysteem::$lang_slug); ?></label>

    <label class="radio"><input data-style-collect="true" type="radio" name="gener_air_type" value="live" <?php echo self::checkCheckbox($gener_air_type_saved == 'live' || $gener_air_type_saved == ''); ?> >Live</label>
    <label class="radio" ><input data-style-collect="true" type="radio" name="gener_air_type" value="rec" <?php echo self::checkCheckbox($gener_air_type_saved == 'rec'); ?>>Automated</label>
    <div class="webinar_clear_fix"></div>
</div>

<div class="form-group show-for-recorded<?php echo ($gener_air_type_saved == 'rec') ? '' : ' hide-option'; ?>">
    <label><?php _e('Webinar Occurance', WebinarSysteem::$lang_slug); ?></label>
    <?php $gener_time_occur_saved = get_post_meta($post->ID, '_wswebinar_gener_time_occur', true); ?>
    <label class="radio"><input data-style-collect="true" type="radio" name="gener_time_occur" value="one" <?php echo self::checkCheckbox($gener_time_occur_saved == 'one' || $gener_time_occur_saved == ''); ?>>One time</label>
    <label class="radio"><input data-style-collect="true" type="radio" name="gener_time_occur" value="recur" <?php echo self::checkCheckbox($gener_time_occur_saved == 'recur'); ?>>Recurring</label>
    <label class="radio"><input data-style-collect="true" type="radio" name="gener_time_occur" value="jit" <?php echo self::checkCheckbox($gener_time_occur_saved == 'jit'); ?>>Just in time</label>
    <div class="webinar_clear_fix"></div>
</div>

<!--<div class="form-group  show-for-recurr<?php echo ($gener_air_type_saved == 'rec' && $gener_time_occur_saved == 'recur') ? '' : ' hide-option'; ?>">
    <div class="wsseparator"></div>
<?php $gener_onetimeregist = get_post_meta($post->ID, '_wswebinar_gener_onetimeregist', true); ?>
    <label><?php _e('One time registration', WebinarSysteem::$lang_slug); ?></label>
    <input type="checkbox" value="1" name="gener_onetimeregist" <?php echo $gener_onetimeregist == '1' ? 'checked="checked"' : '' ?>>
    <div class="webinar_clear_fix"></div>
</div>-->


<div class="form-group show-for-recurr<?php echo ($gener_air_type_saved == 'rec' && $gener_time_occur_saved == 'recur') ? '' : ' hide-option'; ?>">

    <label><?php _e('Recurring on every', WebinarSysteem::$lang_slug); ?></label>
    <?php
    $gener_rec_days_array = array();
    $gener_rec_days_saved = get_post_meta($post->ID, '_wswebinar_gener_rec_days', true);
    if (!empty($gener_rec_days_saved)) {
	$gener_rec_days_array = json_decode($gener_rec_days_saved, true);
    }

    function getDayRowForRecurDays($day, $full_day, $days_array) {
	echo "<td><label class='radio'><input data-style-collect='true' type='checkbox' class='rec-days-selector' value='$day' " . WebinarSysteemMetabox::checkCheckbox(in_array($day, $days_array)) . ">" . __($full_day, WebinarSysteem::$lang_slug) . "</label></td>";
    }
    ?>
    <table>
        <tr>
	    <?php getDayRowForRecurDays("mon", 'Monday', $gener_rec_days_array); ?>
	    <?php getDayRowForRecurDays('tue', 'Tuesday', $gener_rec_days_array); ?>
        </tr>
        <tr>
	    <?php getDayRowForRecurDays('wed', 'Wednesday', $gener_rec_days_array); ?>
	    <?php getDayRowForRecurDays('thu', 'Thursday', $gener_rec_days_array); ?>
        </tr>
        <tr>
	    <?php getDayRowForRecurDays('fri', 'Friday', $gener_rec_days_array); ?>
	    <?php getDayRowForRecurDays('sat', 'Saturday', $gener_rec_days_array); ?>
        </tr>
        <tr>
	    <?php getDayRowForRecurDays('sun', 'Sunday', $gener_rec_days_array); ?>
        </tr>
    </table>
    <input type="hidden" data-style-collect='true' name="gener_rec_days" id="gener_rec_days" value='<?php echo get_post_meta($post->ID, '_wswebinar_gener_rec_days', true); ?>'>

</div>

<div class="form-group show-for-recurr <?php echo ($gener_air_type_saved == 'rec' && $gener_time_occur_saved == 'recur') ? '' : ' hide-option'; ?>">
    <?php
    $gener_rec_times_saved = get_post_meta($post->ID, '_wswebinar_gener_rec_times', true);
    $gener_rec_times_array = array();
    if (!empty($gener_rec_times_saved)) {
	$gener_rec_times_array = json_decode($gener_rec_times_saved, TRUE);
    }
    ?>

    <div class="wsseparator"></div>
    <label><?php _e('Recurring Time(s)', WebinarSysteem::$lang_slug); ?></label>
    <table id="time-selector-wrapper">
        <tbody>
            <tr>
                <td><select data-selector-master="true" data-style-collect='true' name="recurring_times_sel" class="time-selector default-selector" data-saved="<?php echo!empty($gener_rec_times_array[0]) ? $gener_rec_times_array[0] : '' ?>"></select></td>
            </tr>
	    <?php for ($x = 1; $x < count($gener_rec_times_array); $x++): ?>
    	    <tr>
    		<td><select data-style-collect='true' name="recurring_times_sel" class="time-selector" data-saved="<?php echo!empty($gener_rec_times_array[$x]) ? $gener_rec_times_array[$x] : '' ?>"></select><a href="#" class="timeSelectorRemover"><?php _e('Remove'); ?></a></td>
    	    </tr>
	    <?php endfor; ?>
        </tbody>
        <tfoot>
            <tr><td><input type="submit" id="add-new-time-field" class="button button-primary" value="Add another time"></td></tr>
        </tfoot>
    </table>
    <input type="hidden" name="gener_rec_times" data-style-collect='true' id="gener_rec_times" value='<?php echo $gener_rec_times_saved; ?>'>
</div>

<div class="form-group show-for-jit<?php echo ($gener_air_type_saved == 'rec' && $gener_time_occur_saved == 'jit') ? '' : ' hide-option'; ?>">
	
	<label><?php _e('Only runs on', WebinarSysteem::$lang_slug); ?></label>
	<?php 
	$gener_jit_days_array = array();
	$gener_jit_days_saved = get_post_meta($post->ID, '_wswebinar_gener_jit_days', true);
	if (!empty($gener_jit_days_saved)) {
		$gener_jit_days_array = json_decode($gener_jit_days_saved, true);
	}
	
	function getDayRowForJitDays($day, $full_day, $days_array) {
	echo "<td><label class='radio'><input data-style-collect='true' type='checkbox' class='jit-days-selector' value='$day' " . WebinarSysteemMetabox::checkCheckbox(in_array($day, $days_array)) . ">" . __($full_day, WebinarSysteem::$lang_slug) . "</label></td>";
    }
	?>
	<table>
        <tr>
	    <?php getDayRowForJitDays("mon", 'Monday', $gener_jit_days_array); ?>
	    <?php getDayRowForJitDays('tue', 'Tuesday', $gener_jit_days_array); ?>
        </tr>
        <tr>
	    <?php getDayRowForJitDays('wed', 'Wednesday', $gener_jit_days_array); ?>
	    <?php getDayRowForJitDays('thu', 'Thursday', $gener_jit_days_array); ?>
        </tr>
        <tr>
	    <?php getDayRowForJitDays('fri', 'Friday', $gener_jit_days_array); ?>
	    <?php getDayRowForJitDays('sat', 'Saturday', $gener_jit_days_array); ?>
        </tr>
        <tr>
	    <?php getDayRowForJitDays('sun', 'Sunday', $gener_jit_days_array); ?>
        </tr>
    </table>
    <input type="hidden" data-style-collect='true' name="gener_jit_days" id="gener_jit_days" value='<?php echo get_post_meta($post->ID, '_wswebinar_gener_jit_days', true); ?>'>
</div>

<div class="form-group show-for-jit<?php echo ($gener_air_type_saved == 'rec' && $gener_time_occur_saved == 'jit') ? '' : ' hide-option'; ?>">

    <div class="wsseparator"></div>
    <label><?php _e('Start Webinar', WebinarSysteem::$lang_slug); ?></label>
    <select name="gener_jit_times">
	<?php
	$jittime = get_post_meta($post->ID, '_wswebinar_gener_jit_times', true);
	if(empty($jittime)){
	    $jittime = 15;
	}
	$getJitTimes = WebinarSysteemMetabox::getJITtimesArray($val='');
	foreach ($getJitTimes as $timeKey => $timeLabel) {
	    $selected = ($jittime == $timeKey) ? 'selected="selected"' : '';
	    echo '<option value="' . $timeKey . '" ' . $selected . '>' . $timeLabel . '</option>';
	}
	?>  
    </select>
  
</div>






<div class="form-field show-for-recurr show-for-jit <?php echo ($gener_air_type_saved == 'rec' && ($gener_time_occur_saved == 'recur' || $gener_time_occur_saved == 'jit')) ? '' : ' hide-option'; ?>">
    <label for="show_timeslots">Show Amount of Time Slots</label>
    <input type="text" data-style-collect="true" id="show_timeslots" name="gener_timeslot_count" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_gener_timeslot_count', true)); ?>">
    <p class="description " style="">Leave blank to show all recurring times</p>
    <div class="webinar_clear_fix"></div>

    <label for="show_offset">Day Offset</label>
    <input type="text" data-style-collect="true" id="show_timeslots" name="gener_offset_count" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_gener_offset_count', true)); ?>">
    <p class="description " style="">How many days will pass</p>
    <div class="webinar_clear_fix"></div>


</div>














<div class="wsseparator"></div>

<div class="form-field hide-for-recurr <?php echo ($gener_air_type_saved == 'rec' && ($gener_time_occur_saved == 'recur' || $gener_time_occur_saved == 'jit')) ? ' hide-option' : ''; ?>">
    <label for="gener_date"><?php _e('Webinar starts at', WebinarSysteem::$lang_slug); ?></label>
    <input type="text" name="gener_date" id="gener_date" placeholder="<?php _e('Date', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_post_meta($post->ID, '_wswebinar_gener_date', true); ?>">
    <div class="date_line_sep">@</div>
    <select class="alignleft" name="gener_hour">                
	<?php
	for ($i = 0; $i < 24; $i++):
	    ?>
    	<option <?php
	    $theHour = get_post_meta($post->ID, '_wswebinar_gener_hour', true);
	    echo!empty($theHour) && $theHour == $i ? 'selected="selected"' : ''
	    ?>><?php echo sprintf("%02s", $i) ?></option>
	    <?php endfor; ?>
    </select>
    <div class="date_line_sep">:</div>
    <select class="alignleft" name="gener_min">                
	<?php
	for ($j = 0; $j < 60; $j++):
	    ?>
	    <?php if ($j % 5 == 0): ?>
		<option <?php
		$theMin = get_post_meta($post->ID, '_wswebinar_gener_min', true);
		echo!empty($theMin) && $theMin == $j ? 'selected="selected"' : ''
		?>><?php echo sprintf("%02s", $j) ?></option>
		<?php endif; ?>
	    <?php endfor; ?>
    </select>

    <div class="webinar_clear_fix"></div>
</div>   
<div class="form-field">
    <label>Timezone annotation</label>
    <select name="timezoneidentifier">
	<option value="">Default WP</option>
	<?php
	$webinar_system = new WebinarSysteem();
	$timezoneidentifier = get_post_meta($post->ID, '_wswebinar_timezoneidentifier', true);
	$getTimezoneIdentifiers = $webinar_system->getTimezoneIdentifiers();
	foreach ($getTimezoneIdentifiers as $timeZoneKey => $timeZoneLabel) {
	    $selected = ($timezoneidentifier == $timeZoneKey) ? 'selected="selected"' : '';
	    echo '<option value="' . $timeZoneKey . '" ' . $selected . '>' . $timeZoneLabel . '</option>';
	}
	?>  
    </select>
</div>
<div class="form-field">
    <?php
    $_wswebinar_gener_duration = get_post_meta($post->ID, '_wswebinar_gener_duration', true);
    if (empty($_wswebinar_gener_duration))
	$_wswebinar_gener_duration = 3600;
    $_wswebinar_gener_duration = floatval($_wswebinar_gener_duration);
    ?>
    <label>Webinar Duration</label>
    <select name="gener_duration">
	<?php for ($rr = 600; $rr < 18000; $rr+=600): ?>
    	<option value="<?php echo $rr; ?>" <?php echo $rr == $_wswebinar_gener_duration ? 'selected="selected"' : '' ?> ><?php echo $rr > 3590 ? date("H", $rr) . 'h' : ''; ?> <?php echo date("i", $rr); ?>min</option>
	<?php endfor; ?>
    </select>
    <div class="webinar_clear_fix"></div>
</div>



<div class="form-group">
    <label for="gener_regdisabled_yn"><?php _e('Disable registration', WebinarSysteem::$lang_slug); ?></label>
    <?php $gener_regdisabled_yn_value = get_post_meta($post->ID, '_wswebinar_gener_regdisabled_yn', true); ?>
    <input data-style-collect="true" type="checkbox" data-switch="true" name="gener_regdisabled_yn" id="gener_regdisabled_yn" value="yes" <?php echo ($gener_regdisabled_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
    <div class="webinar_clear_fix"></div>
</div>

<script id="timeOptions">
<?php
for ($x = 0; $x < 24; $x++) {
    for ($y = 0; $y < 60; $y+=5) {
	$time = $x . ':' . $y;
	echo "<option value='" . date('H:i', strtotime($time)) . "'>" . date('H:i ', strtotime($time)) . "</option>";
    }
}
?>
</script>

<script id="timeoptionPlaceholder">
<?php echo '<option disabled="disabled" selected="selected">Select</option>'; ?>
</script>

<script id="rightnowOption">
    <?php echo "<option value='rightnow'>" . __('Right Now', WebinarSysteem::$lang_slug) . "</option>"; ?>
</script>

<script>
    jQuery(function () {
	jQuery("#gener_date").datepicker({
	    beforeShow: function () {
		jQuery(this).datepicker("widget").wrap("<div class='wswebinar-ui-theme'></div>");
	    },
	    dateFormat: "yy-mm-dd"
	});
    });
    /*
     * 
     * Add new time field.
     * 
     */

    var timeFieldLimit = 1;
    jQuery("#add-new-time-field").on('click', function (e) {
	if (timeFieldLimit > 4) {
	    jQuery(this).hide();
	    return false;
	}
	timeFieldLimit++;
	jQuery("#time-selector-wrapper").append(jQuery("<tr></tr>").html('<td><select class="time-selector">' + jQuery("#timeOptions").html() + '</select><a href="#" class="timeSelectorRemover"><?php _e('Remove'); ?></a></td>'));
	checkSelectedTimes();
	e.preventDefault();
    });

    jQuery(document).ready(function () {
	var defaultOptions = jQuery('#timeoptionPlaceholder').html() + " " + jQuery('#rightnowOption').html() + " " + jQuery("#timeOptions").html();
	var timeOptions = jQuery('#timeoptionPlaceholder').html() + " " + jQuery("#timeOptions").html();
	jQuery('.time-selector').each(function () {
	    if (jQuery(this).hasClass('default-selector')) {
		jQuery(this).html(defaultOptions);
	    } else {
		jQuery(this).html(timeOptions);
	    }
	});
	jQuery('.time-selector').each(function () {
	    var datasaved = jQuery(this).data('saved');
	    if (datasaved.length > 0) {
		jQuery(this).val(datasaved);
	    }
	});
    });

    function checkSelectedTimes() {
	var timeSelctions = [];
	jQuery('.time-selector').each(function () {
	    timeSelctions.push(jQuery(this).val());
	});
	jQuery('#gener_rec_times').val(JSON.stringify(timeSelctions));
    }


    jQuery(document).on('change', '.time-selector', function () {
	checkSelectedTimes();
    });
    /*
     * 
     * Remove time selector row
     * 
     */

    jQuery(document).on('click', '.timeSelectorRemover', function (e) {
	jQuery(this).parents("tr").remove();
	checkSelectedTimes();
	return e.preventDefault();
    });

    /*
     * 
     * Live / Recording switch
     * 
     */

    jQuery('input[name=gener_air_type]').on('change', function () {
	var air_type = jQuery('input[name=gener_air_type]:checked').val();
	if (air_type == 'live') {
	    jQuery('.show-for-recorded').slideUp();
	    showHideTimeSelectors();
	} else {
	    jQuery('.show-for-recorded').slideDown();
	    showHideTimeSelectors();
	}
    });

    /*
     * 
     * Occurance switch
     * 
     */

    jQuery('input[name=gener_time_occur]').on('change', function () {
	showHideTimeSelectors();
    });

    function showHideTimeSelectors() {
	var air_type = jQuery('input[name=gener_air_type]:checked').val();
	var accur_type = jQuery('input[name=gener_time_occur]:checked').val();
	if (air_type == 'live') {
	    jQuery('.show-for-recurr').slideUp();
	    jQuery('.hide-for-recurr').slideDown();
	    return;
	}
	if (accur_type == 'one') {
	    jQuery('.show-for-recurr').slideUp();
	    jQuery('.show-for-jit').slideUp();
	    jQuery('.hide-for-recurr').slideDown();
	} else if(accur_type == 'recur') {
        jQuery('.hide-for-recurr').slideUp();
        jQuery('.show-for-jit').slideUp();
        jQuery('.show-for-recurr').slideDown();
	} else if(accur_type == 'jit'){
		jQuery('.show-for-recurr').slideUp();
		jQuery('.hide-for-recurr').slideUp();
		jQuery('.show-for-jit').slideDown();
	    
	}
    }

    /*
     * 
     * Status buttons
     * 
     */

    jQuery(document).on('click', '.wswebinarStatusPanel li', function () {
	if (jQuery(this).hasClass('disabled'))
	    return;
	jQuery('.wswebinarStatusPanel li').removeClass('disabled active');
	jQuery(this).addClass('disabled active');
	jQuery('#gener_webinar_status').val(jQuery(this).attr('status'));
    });

    /*
     * 
     * Set days of week to hidden field
     * 
     */

    jQuery(document).on('change', '.rec-days-selector', function () {
	var gener_rec_days = [];
	jQuery('.rec-days-selector').each(function () {
	    if (jQuery(this).is(':checked')) {
		gener_rec_days.push(jQuery(this).val());
	    }
	});
	jQuery('#gener_rec_days').val(JSON.stringify(gener_rec_days));
    });
    
    jQuery(document).on('change', '.jit-days-selector', function () {
	var gener_jit_days = [];
	jQuery('.jit-days-selector').each(function () {
	    if (jQuery(this).is(':checked')) {
		gener_jit_days.push(jQuery(this).val());
	    }
	});
	jQuery('#gener_jit_days').val(JSON.stringify(gener_jit_days));
    });
</script>

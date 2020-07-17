<?php

$recurr_instances = WebinarSysteem::getRecurringInstances($post->ID);
$rightnow_option = array('has' => false, 'option_string' => '');

foreach ($recurr_instances['times'] as $time) {
    if ($time == 'rightnow') {
        $rightnow_option['has'] = TRUE;
        $rightnow_option['option_string'] = '<option value="rightnow">' . __("Right Now", WebinarSysteem::$lang_slug) . '</option>';
    }
}

$recurr_inst_day_ints = array();
$timeslot_count = get_post_meta($post->ID, '_wswebinar_gener_timeslot_count', true);

foreach ($recurr_instances['days'] as $day) {
    $day_string = "next $day";
    if (strtolower(date('D')) == $day)
        $day_string = "this $day";
    $recurr_inst_day_ints[] = strtotime($day_string);
}

?>
<div class="row">
    <div class="col-sm-12">
    <select class="form-control forminputs" name="inputday">
        <option disabled="disabled" selected="selected"><?php _e('Select a day', WebinarSysteem::$lang_slug); ?></option>
    <?php
    if ($rightnow_option['has']) {
        echo $rightnow_option['option_string'];
    }
    $date_format = get_option('date_format');
    $day_offset = get_post_meta($post->ID, '_wswebinar_gener_offset_count', true);
    sort($recurr_inst_day_ints);
    $metaval = '';
    $metaval = get_post_meta($post->ID, '_wswebinar_gener_timeslot_count', true);
    
    if(!empty($metaval)) {
        $gener_rec_times_saved = get_post_meta($post->ID, '_wswebinar_gener_rec_times', true);
        $gener_rec_times_array = array();
        $i = 0;
        
        if (!empty($gener_rec_times_saved)) {
            $gener_rec_times_array = json_decode($gener_rec_times_saved, TRUE);
        }
        
        $is_timeslot_available = false;
        
        foreach ($recurr_inst_day_ints as $day) {
            $i++;
            
            if ($i <= $day_offset)
                continue;
            
            if ($is_timeslot_available)
                break;
            
            $current_day = strtolower(current_time('D')) == strtolower(date("D", $day));
            $nxt_day = date($date_format, strtotime('today ' . date("D", $day)));
            $nxt_day = str_replace(',','',$nxt_day);
            $next_day = date_i18n( $date_format, strtotime('today ' . date("D", $day))); //To localize the date
            
            foreach ($gener_rec_times_array as $time) {
                $slot_time_int = strtotime($nxt_day . ' ' . $time); //Use php date function variable only
                $current_time = WebinarSysteem::populateDateTime($post->ID);

                if ($current_time < $slot_time_int) {
                    echo "<option class=" . ($current_day ? 'today' : 'not_today') . " value=" . WebinarSysteemMetabox::getStaticWeekDayArray(strtolower(date("D", $day))) . ">" . WebinarSysteemMetabox::getWeekDayArray(strtolower(date("D", $day))) . ' (' . $next_day . ')' . "</option>";
                    $is_timeslot_available = true;
                    break;
                }
            }
        }
    } else {
        $i = 0;
        foreach ($recurr_inst_day_ints as $day) {
            $i++;

            if ($i <= $day_offset)
                continue;

            $current_day = strtolower(current_time('D')) == strtolower(date("D", $day));
            //$next_day = date($date_format, strtotime('today ' . date("D", $day)));
            $next_day = date_i18n( $date_format, strtotime('today ' . date("D", $day)));
            echo "<option class=" . ($current_day ? 'today' : 'not_today') . " value=" . WebinarSysteemMetabox::getStaticWeekDayArray(strtolower(date("D", $day))) . ">" . WebinarSysteemMetabox::getWeekDayArray(strtolower(date("D", $day))) . ' (' . $next_day . ')' . "</option>";
        }
    }

    ?>
    </select>

    <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputday') { ?>
    <span class="error"><?php _e('Select a day to watch.', WebinarSysteem::$lang_slug) ?></span>
    <?php } ?>
    </div>


    <div class="col-sm-12" id="input-time">
    <select class="form-control forminputs" name="inputtime" id="inputtime">
        <option disabled="disabled" selected="selected"><?php _e('Select a time', WebinarSysteem::$lang_slug); ?></option>
        <option disabled="disabled"><?php _e('Select a day first', WebinarSysteem::$lang_slug); ?></option>
    </select>
    <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputtime') { ?>
    <span class="error"><?php _e('Select a time to watch.', WebinarSysteem::$lang_slug) ?></span>
    <?php } ?>
    </div>
</div>

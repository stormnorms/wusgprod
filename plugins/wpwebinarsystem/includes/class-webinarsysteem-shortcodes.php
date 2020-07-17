<?php

if (!defined('ASSFURL')) {
    define('ASSFURL', WP_PLUGIN_URL."/".dirname(plugin_basename( __FILE__)));
}

class WebinarSysteemShortCodes {

    function __construct() {
        add_shortcode('webinarsystem_registration', array($this, 'registration'));
        add_shortcode('webinarsystem_login', array($this, 'login'));
        add_filter('mce_buttons', array($this, 'register_tinymce_buttons'));
        add_filter('mce_external_plugins', array($this, 'register_tinymce_javascript'));
        add_action('admin_footer', array($this, 'shortcodeData'));
    }

    function registration($attributes) {
        wp_enqueue_script('wpws-external', ASSFURL.'/js/webinarsystem-external.js', array('jquery',),'',false);
        wp_localize_script('wpws-external', 'wpexternal', array(
            'available_timeslots' => __('Loading available timeslots...', WebinarSysteem::$lang_slug),
            'ajaxurl' => admin_url( 'admin-ajax.php')
        ));

        $attrs = shortcode_atts(array(
            'id' => "no_post_id",
            'url' => NULL,
            'button' => NULL
        ), $attributes);

        ob_start();

        //If posts exists
        if (get_post_status($attrs['id']) === FALSE):
            __('Error: ', WebinarSysteem::$lang_slug) . __('Invalid webinar id.', WebinarSysteem::$lang_slug);
            $content = ob_get_clean();
            return $content;
        endif;

        $meta_btn_txt = get_post_meta($attrs['id'], '_wswebinar_regp_ctatext', true);
        $registerButtonText = (!empty($attrs['button']) ? $attrs['button'] : (!empty($meta_btn_txt) ? $meta_btn_txt : 'Sign Up') );

        $postId = $attrs['id'];
        $url = $attrs['url'];
        //$registerButtonText = empty($attrs['button']) && empty($metaRegisterButtonText) ? 'Sign Up' : $attrs['button'];

        $wp_ticketId = get_post_meta($postId, '_wswebinar_ticket_id', true);
        global $woocommerce;
        $is_WCready = WebinarSysteemWooCommerceIntegration::isWCready();

        if ($wp_ticketId) {
            if ($is_WCready) {
                if(!empty($woocommerce->cart)) {
                    $cart_url = wc_get_cart_url();
                }

                if(!empty($wp_ticketId)) {
                    $product_url = get_permalink( $wp_ticketId );
                }

                //If a paid webinar
                if (get_post_meta($postId, '_wswebinar_ticket_wbnpaid_yn', true) == 'on') {
                    printf(__('You need to buy a ticket to register for this webinar. <a href="%s">Click here</a> to buy a ticket.', 
                    WebinarSysteem::$lang_slug), WebinarSysteem::isRecurring($postId)? $product_url : (add_query_arg(array('add-to-cart' => $wp_ticketId, 'quantity' => 1), $cart_url)));
                    $content = ob_get_clean();
                    return $content;
                }
            } else {
                printf(__('You need to buy a ticket to register for this webinar. Ticket sales is closed at this moment.'));
                $content = ob_get_clean();
                return $content;
            }
        }
        if (!empty($_GET['_wswebinarsystem_newly_registered' . $postId]) && $_GET['_wswebinarsystem_newly_registered' . $postId]) {
            ?>
            <span class="success"><?php printf(__('You\'ve successfully registered for %s', WebinarSysteem::$lang_slug), get_the_title($postId)) ?></span>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        if (!empty($_GET['_wswebinarsystem_already_registered' . $postId]) && $_GET['_wswebinarsystem_already_registered' . $postId]) {
            ?>
            <span class="success"><?php printf(__('You\'re already registered for %s', WebinarSysteem::$lang_slug), get_the_title($postId)) ?></span>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        $registration_disabled = get_post_meta($postId, '_wswebinar_gener_regdisabled_yn', true);

        if (!empty($registration_disabled)) {
            ?>
            <div class="text-center round-border-full signup">
                <h1><?php _e('Registration is closed for this webinar.', WebinarSysteem::$lang_slug) ?></h1>
            </div>
            <?php
            $content = ob_get_clean();
            return $content;
        }
        ?>

        <form method="POST">
            <input type="hidden" name="webinarRegForm" value="submit">
            <?php if (!empty($url)) { ?>
                <input type="hidden" name="redirectAfter" value="<?php echo $url ?>">
            <?php } ?>
            <input type="hidden" name="webinarTab" value="register">
            <input type="hidden" name="wbnid" value="<?php echo $postId ?>">
            <input class="form-control forminputs wswebinarsys-registration-name-input" name="inputname" placeholder="<?php _e('Your Name', WebinarSysteem::$lang_slug) ?>" type="text" value="<?php echo!empty($_REQUEST['inputname']) ? $_REQUEST['inputname'] : ''; ?>" />
            <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputname'): ?>
                <span class="error"><?php _e('Please enter your name.', WebinarSysteem::$lang_slug) ?></span>
            <?php endif; ?>
            <input class="form-control forminputs wswebinarsys-registration-email-input" name="inputemail" placeholder="<?php _e('Your Email Address', WebinarSysteem::$lang_slug) ?>" type="email" value="<?php echo!empty($_REQUEST['inputemail']) ? $_REQUEST['inputemail'] : ''; ?>" />
            <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputemail'): ?>
                <span class="error"><?php _e('Please enter your email.', WebinarSysteem::$lang_slug) ?></span>
            <?php endif; ?>
                <?php
                $fields = json_decode(get_post_meta($attributes['id'], '_wswebinar_regp_custom_field_json', true));
                if (!empty($fields))
                    foreach ($fields as $field) {
                        $customFieldIsText = $field->type != "checkbox";
                        ?>

                    <?php if($customFieldIsText){ ?>
                        <input id="ws-<?php echo $field->id ?>" name="ws-<?php echo $field->id ?>" type="<?php echo $field->type ?>" placeholder="<?php echo $field->labelValue ?>" class="form-control forminputs custom-reg-field">
                    <?php }else{ ?>
                        <label for="ws-<?php echo $field->id ?>" style="cursor: pointer; margin-bottom: 0px;">
                            <input id="ws-<?php echo $field->id ?>" name="ws-<?php echo $field->id ?>" type="<?php echo $field->type ?>" placeholder="<?php echo $field->labelValue ?>">
                            <?php echo $field->labelValue ?> </label>
                            <?php
                        }
                    }
                ?>

            <?php
            if (WebinarSysteem::isRecurring($postId)): 
            $gener_time_occur_saved = get_post_meta($postId, '_wswebinar_gener_time_occur', true);
            if ($gener_time_occur_saved == 'recur'){
            $recurr_instances = WebinarSysteem::getRecurringInstances($postId);
            $rightnow_option = array('has' => false, 'option_string' => '');
            foreach ($recurr_instances['times'] as $time) {
                if ($time == 'rightnow') {
                $rightnow_option['has'] = TRUE;
                $rightnow_option['option_string'] = '<option value="rightnow">' . __("Right Now", WebinarSysteem::$lang_slug) . '</option>';
                }
            }


            $recurr_inst_day_ints = array();

            $timeslot_count = get_post_meta($postId, '_wswebinar_gener_timeslot_count', true);
            foreach ($recurr_instances['days'] as $day) {
                $day_string = "next $day";
                if (strtolower(date('D')) == $day)
                $day_string = "this $day";
                $recurr_inst_day_ints[] = strtotime($day_string);
            }

            ?>
                <div class="row">
                <div class="col-sm-12">
                    <select class="form-control forminputs wswebinarsys-registration-day-select" name="inputday">
                    <option disabled="disabled" selected="selected"><?php _e('Select a day', WebinarSysteem::$lang_slug); ?></option>
                    <?php
                    if ($rightnow_option['has']) {
                    echo $rightnow_option['option_string'];
                    }
                    $date_format = get_option('date_format');
                    $day_offset = get_post_meta($postId, '_wswebinar_gener_offset_count', true);
                    sort($recurr_inst_day_ints);
                    $metaval = '';
                    $metaval = get_post_meta($postId, '_wswebinar_gener_timeslot_count', true);

                    if(!empty($metaval))
                    {
                        $gener_rec_times_saved = get_post_meta($postId, '_wswebinar_gener_rec_times', true);
                        $gener_rec_times_array = array();
                        
                        if (!empty($gener_rec_times_saved)) {
                        $gener_rec_times_array = json_decode($gener_rec_times_saved, TRUE);
                        }
                        
                        $i = 0;
                        $is_timeslot_available = false;
                        foreach ($recurr_inst_day_ints as $day) {
                        $i++;
                        if ($i <= $day_offset)
                        continue;
                        if($is_timeslot_available)
                        break;
                        $current_day = strtolower(current_time('D')) == strtolower(date("D", $day));
                        $nxt_day = date($date_format, strtotime('today ' . date("D", $day)));
                        $nxt_day = str_replace(',','',$nxt_day);
                        $next_day = date_i18n( $date_format, strtotime('today ' . date("D", $day)));
                        foreach ($gener_rec_times_array as $time) {
                        $slot_time_int = strtotime($nxt_day . ' ' . $time);
                        $current_time = WebinarSysteem::populateDateTime($postId);
                        if ($current_time < $slot_time_int) {
                        echo "<option class=" . ($current_day ? 'today' : 'not_today') . " value=" . WebinarSysteemMetabox::getStaticWeekDayArray(strtolower(date("D", $day))) . ">" . WebinarSysteemMetabox::getWeekDayArray(strtolower(date("D", $day))) . ' (' . $next_day . ')' . "</option>";
                        $is_timeslot_available = true;
                        break;
                    }
                    }
                }
            }
            else
            {
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
                <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputday'): ?>
                    <span class="error"><?php _e('Select a day to watch.', WebinarSysteem::$lang_slug) ?></span>
                <?php endif; ?>
                </div>
                <div class="col-sm-12" id="input-time">
                    <select class="form-control forminputs wswebinarsys-registration-time-select" name="inputtime" id="inputtime">
                        <option disabled="disabled" selected="selected"><?php _e('Select a time', WebinarSysteem::$lang_slug); ?></option>
                        <option disabled="disabled"><?php _e('Select a day first', WebinarSysteem::$lang_slug); ?></option>
                    </select>
                <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputtime'): ?>
                    <span class="error"><?php _e('Select a time to watch.', WebinarSysteem::$lang_slug) ?></span>
                <?php endif; ?>
                </div>
                </div>
            <?php }
            else if($gener_time_occur_saved == 'jit'){
            $justintime_instances = WebinarSysteem::getJustinTimeInstances($postId);
            $jit_inst_day_ints = array();
        
            foreach ($justintime_instances['days'] as $day) {
            $day_string = "next $day";
            if (strtolower(date('D')) == $day)
                $day_string = "this $day";
                $jit_inst_day_ints[] = strtotime($day_string);
            }
        ?>
            <div class = "row">
            <div class="col-sm-12">
                <select class="form-control forminputs wswebinarsys-registration-day-select" name="inputday">
                    <option disabled="disabled" selected="selected"><?php _e('Select a day', WebinarSysteem::$lang_slug); ?></option>
                    <?php 
                    $date_format = get_option('date_format');
                    $day_offset = get_post_meta($postId, '_wswebinar_gener_offset_count', true);
                    sort($jit_inst_day_ints);

                    $metaval = '';
                    $metaval = get_post_meta($postId, '_wswebinar_gener_timeslot_count', true);

                    if(!empty($metaval)) {
                        $is_timeslot_available = false;
                        $i=0;
                        foreach ($jit_inst_day_ints as $day) {
                        $i++;
                        
                        if($i <= $day_offset)
                        continue;
                        
                        if($is_timeslot_available)
                        break;
                        
                        $current_day = strtolower(current_time('D')) == strtolower(date("D", $day));
                        $nxt_day = date($date_format, strtotime('today ' . date("D", $day)));
                        $nxt_day = str_replace(',','',$nxt_day);
                        $next_day = date_i18n( $date_format, strtotime('today ' . date("D", $day))); //To localize the date
                        foreach($justintime_instances['times'] as $time){
                            $slot_time_int = strtotime($nxt_day . ' ' . $time); //Use php date function only
                            $current_time = WebinarSysteem::populateDateTime($postId);
                            if ($current_time < $slot_time_int) {
                        echo "<option class=" . ($current_day ? 'today' : 'not_today') . " value=" . WebinarSysteemMetabox::getStaticWeekDayArray(strtolower(date("D", $day))) . ">" . WebinarSysteemMetabox::getWeekDayArray(strtolower(date("D", $day))) . ' (' . $next_day . ')' . "</option>";

                        $is_timeslot_available = true;
                        break;
                        }
                        }
                        
                        }
                        
                    }
                    
                    else {
            $i = 0;
            foreach ($jit_inst_day_ints as $day) {
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
                
                <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputday'): ?>
                <span class="error"><?php _e('Select a day to watch.', WebinarSysteem::$lang_slug) ?></span>
                <?php endif; ?>
            </div>
            <div class="col-sm-12" id="input-time">
                <select class="form-control forminputs wswebinarsys-registration-time-select" name="inputtime" id="inputtime">
                    <option disabled="disabled" selected="selected"><?php _e('Select a time', WebinarSysteem::$lang_slug); ?></option>
                    <option disabled="disabled"><?php _e('Select a day first', WebinarSysteem::$lang_slug); ?></option>
                </select>
                <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputtime'): ?>
                    <span class="error"><?php _e('Select a time to watch.', WebinarSysteem::$lang_slug) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php     
            }
            endif; ?>
            <?php 
            $regp_gdpr_optin_yn_value = get_post_meta($postId, '_wswebinar_regp_gdpr_optin_yn', true);
            $showGDPROptin = ($regp_gdpr_optin_yn_value == "yes") ? true : false;
            $regp_gdpr_optin_text_value = get_post_meta($postId, '_wswebinar_regp_gdpr_optin_text', true);  ?>
            <div class="text-left regGdprOpted">
            <?php 
            if($showGDPROptin){ ?>
                    <input style="vertical-align: middle; position: relative; bottom: 1px;" type="checkbox" id="regGdprOpted" name="regp_gdpr_optin" required value="" />
                    <p style="vertical-align: middle; display: inline;"><?php echo $regp_gdpr_optin_text_value; ?></p>
            <?php
            }
            ?>
            </div>
            <button class="forminputs wswebinarsys-registration-submit-btn" type="submit"><?php echo $registerButtonText ?></button>
        </form>

        <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd'): ?>
            <span class="error"><?php _e('This email is not registered.', WebinarSysteem::$lang_slug) ?></span>
            <?php
        endif;
        $content = ob_get_clean();
        return $content;
    }

    function login($attributes) {
        $attrs = shortcode_atts(array(
            'id' => "no_post_id",
            'button' => NULL
            ), $attributes);

        ob_start();

        if (get_post_status($attrs['id']) === FALSE) {
            __('Error: ') . __('Invalid webinar id.', WebinarSysteem::$lang_slug);
            $content = ob_get_clean();
            return $content;
        }

        $webinar_id = $attrs['id'];
        $metaLoginButtonText = get_post_meta($webinar_id, '_wswebinar_regp_loginctatext', true);
        $postId = $attrs['id'];

        $loginButtonText = (!empty($attrs['button']) ? $attrs['button']
            : (!empty($metaLoginButtonText)
            ? $metaLoginButtonText : 'Login') );

        ?>
        <form method="POST">
            <input type="hidden" name="webinarRegForm" value="submit">
            <input type="hidden" name="webinarTab" value="login">
            <input type="hidden" name="wbnid" value="<?php echo $postId ?>">
            <input class="form-control forminputs wswebinarsys-login-email-input" name="inputemail" placeholder="<?php _e('Your Email Address', WebinarSysteem::$lang_slug) ?>" type="email" value="<?php echo!empty($_REQUEST['inputemail']) ? $_REQUEST['inputemail'] : ''; ?>" />
            <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputemail'): ?>
                <span class="error"><?php _e('Please enter your email.', WebinarSysteem::$lang_slug) ?></span>
            <?php endif; ?>
            <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd'): ?>
                <span class="error"><?php _e('Please register before login.', WebinarSysteem::$lang_slug) ?></span>
            <?php endif; ?>
            <button class="forminputs wswebinarsys-login-submit-btn" type="submit"><?php echo $loginButtonText ?></button>
        </form>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Add buttons to tinyMCE
     * 
     * @param array $buttons
     * @return array
     */
    function register_tinymce_buttons($buttons) {
        array_push($buttons, 'separator', 'login_register_shortcodes');
        return $buttons;
    }

    function register_tinymce_javascript($plugin_array) {
        $plugin_array['wpwebinarsystem'] = plugins_url('/js/tinymce-custom.js', __FILE__);
        return $plugin_array;
    }

    function shortcodeData() {
        global $post;

        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'category' => '',
            'category_name' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'include' => '',
            'exclude' => '',
            'meta_key' => '',
            'meta_value' => '',
            'post_type' => 'wswebinars',
            'post_mime_type' => '',
            'post_parent' => '',
            'author' => '',
            'author_name' => '',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $posts_array = get_posts($args);
	?>            

	<script type="text/javascript">
	    var wpwebinarsystem_shortcode_data = [[
			<?php foreach ($posts_array as $__p): ?>
		    {text: '<?php echo $__p->post_title; ?>', onclick: function () {
		    tinyMCE.activeEditor.insertContent('[webinarsystem_registration id="<?php echo $__p->ID; ?>" url="" button=""] ');
		    }},
			<?php endforeach; ?>
	    ],
		[
			<?php foreach ($posts_array as $__p): ?>
		    {text: '<?php echo $__p->post_title; ?>', onclick: function () {
		    tinyMCE.activeEditor.insertContent('[webinarsystem_login id="<?php echo $__p->ID; ?>" url="" button=""] ');
		    }},
			<?php endforeach; ?>
	    ]];
	</script>
	<?php
    }
}

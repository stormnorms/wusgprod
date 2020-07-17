<?php
global $post;
setup_postdata($post);
WebinarSysteem::setPostData($post->ID);
$status = isset($_GET['force_show']) ? $_GET['force_show'] : get_post_meta($post->ID, '_wswebinar_gener_webinar_status', true);
$data_title_clr = get_post_meta($post->ID, '_wswebinar_cntdwnp_title_clr', true);
$data_tagline_clr = get_post_meta($post->ID, '_wswebinar_cntdwnp_tagline_clr', true);
$data_desc_clr = get_post_meta($post->ID, '_wswebinar_cntdwnp_desc_clr', true);
$data_backg_clr = get_post_meta($post->ID, '_wswebinar_cntdwnp_bckg_clr', true);
$data_backg_img = get_post_meta($post->ID, '_wswebinar_cntdwnp_bckg_img', true);



$attendee = WebinarSysteemAttendees::getAttendee($post->ID);
$data_timer = WebinarSysteem::getWebinarTime($post->ID, $attendee);
$afterOneday = strtotime('+1 day', WebinarSysteem::populateDateTime($post->ID));

$data_show_countdown = get_post_meta($post->ID, '_wswebinar_cntdwnp_timershow_yn', true);
$data_hr = get_post_meta($post->ID, '_wswebinar_gener_hour', true);
$data_min = get_post_meta($post->ID, '_wswebinar_gener_min', true);

$dateFormat = get_option('date_format');
$timeFormat = get_option('time_format');

$data_time = get_post_meta($post->ID, '_wswebinar_gener_time', true);

$date_date = empty($data_timer) ? 'N/A' : date_i18n($dateFormat, $data_timer);
$wb_time = empty($data_timer) ? 'N/A' : date_i18n($timeFormat, $data_timer);

$cur_time = time();
$originalDate = new DateTime();
$originalDate->setTimestamp(strtotime($date_date == 'N/A' ? date_i18n($dateFormat, time()) : $date_date.' '.$wb_time));
$webinar_date =  $originalDate->format('Y-m-d H:i:s');

$timeabbr=get_post_meta($post->ID, '_wswebinar_timezoneidentifier', true);
$wpoffset=get_option('gmt_offset');
$gmt_offset= WebinarSysteem::formatTimezone( ( $wpoffset > 0) ? '+'.$wpoffset : $wpoffset );
$timeZone='('. ( (!empty($timeabbr)) ? WebinarSysteem::getTimezoneAbbreviation($timeabbr) : 'UTC '.$gmt_offset ) . ') ';
?>
<html>

    <head>
        <title><?php echo get_the_title(); ?></title>
        <meta property="og:title" content="<?php the_title(); ?>">
        <meta property="og:url" content="<?php echo get_permalink($post->ID); ?>">
        <meta property="og:description" content="<?php echo substr(wp_strip_all_tags(get_the_content(),true), 0, 500); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body.tmp-countdown{
                <?php echo (empty($data_backg_clr)) ? '' : 'background-color:' . $data_backg_clr . ';'; ?>
                <?php echo (empty($data_backg_img)) ? '' : 'background-image: url(' . $data_backg_img . '); background-size: cover;'; ?>                
            }
        </style>
        <script>
            
        </script>
        <?php wp_head(); ?>
    </head>
    <body class="tmp-countdown">
        <div class="container" style="margin-top: 40px;">

            <!--[if lt IE 9]>
                <div style='row'>
                    <div class="col-xs-6 col-xs-offset-2">
                        <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">
                          <img src="<?php echo plugins_url('./images/iecheck.jpg', __FILE__); ?>" border="0" height="42" width="820" alt="" />
                        </a>
                    </div>
                </div>
            <![endif]-->

            <div class="row">
                <div class="col-lg-12">
                    <?php if ($data_show_countdown == 'yes') { ?>
                        <h2 class="countdown" style="color:<?php echo $data_title_clr ?>;">

                            "<?php the_title(); ?>"
                            <span class="hideIfCountdownStop"><?php _e('will begin in', WebinarSysteem::$lang_slug) ?></span>
                            <span class="showIfCountdownStop"><?php _e('will begin shortly', WebinarSysteem::$lang_slug) ?></span>
                        </h2>
                    <?php } else { ?>
                        <h2 class="countdown" style="color:<?php echo $data_title_clr ?>;">
                            "<?php the_title(); ?>"
                            <?php _e('will start', WebinarSysteem::$lang_slug);
                            echo (!empty($date_date) ? '<br>' . __('on', WebinarSysteem::$lang_slug) . ' ' . $date_date . '  ' : null);
                            echo (!empty($data_min) || !empty($data_hr) ? __('at', WebinarSysteem::$lang_slug) . ' ' . $wb_time : NULL );
							echo ' '.$timeZone;
                            ?>
                        </h2>
                    <?php  ?>
                    <h3 class="text-center" id="countd_notice" style="display: block; color:<?php echo $data_tagline_clr ?>;">
                        <?php _e('Please come back at this time. Thank you for your patience', WebinarSysteem::$lang_slug) ?>
                    </h3>

                <?php } ?>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 text-center col-md-offset-2 flipclock-div">
                <div class="clock" style="margin:2em;"></div>
                <h3 class="refreshNotice" style="display: none; color:<?php echo empty($data_desc_clr)? '#AB27CC' : $data_desc_clr; ?>;"><?php _e('Just a second, we are starting the broadcast. This page will refresh automatically...', WebinarSysteem::$lang_slug) ?></h3>
                <div class="message"></div>
            </div>
            <div class="col-lg-2 col-md-1"></div>
        </div>
	</div>


        <script type="text/javascript">
            var theWebinarId = <?php echo $post->ID; ?>;
            var questionFormerror = '<?php _e('Something is wrong with your Add Questions form. Please re-check all fields are filled correctly', WebinarSysteem::$lang_slug) ?>';
            var questionWait = '<?php _e('Please wait..', WebinarSysteem::$lang_slug) ?>';
            var theWebinarstatus="<?php echo $status; ?>";
            var fetchValues = false;
            var transferValues = false;
            var pageCategory = "countd_";

            var clock;     
            var COUNTDOWN_DIFF;
            
            <?php if(!empty($data_timer)): ?>
            jQuery(document).ready(function () {
                var currentDate = new Date("<?php echo date("Y/m/d H:i:s", WebinarSysteem::populateDateTime($post->ID)) ?>");
                var futureDate = new Date("<?php echo date("Y/m/d H:i:s", $data_timer) ?>");

                if (currentDate > futureDate) {
                    countdownStopCallback();
                    return;
                }else{
                    jQuery('#countd_notice').fadeIn();
                    jQuery('.refreshNotice').fadeOut();
                }
                
                var diff = futureDate.getTime() / 1000 - currentDate.getTime() / 1000;
                
                <?php $locale = strtolower(get_locale());
                $locale = substr($locale, 0, 2); ?>
                clock = jQuery('.clock').FlipClock(diff, {
                    clockFace: 'DailyCounter',
                    countdown: true,
                    language: '<?php echo $locale; ?>',
                    callbacks: {stop: function () {
                            countdownStopCallback();
                        }}
                });
<?php if ($data_show_countdown !== 'yes'): ?>
                jQuery('.clock').hide();
<?php endif; ?>
        });
        function countdownStopCallback() {
            // Contdown timer stopped.
            jQuery('#countd_notice').fadeOut();
            jQuery('.refreshNotice').fadeIn();
                
            jQuery('.hideIfCountdownStop').hide();
            jQuery('.showIfCountdownStop').show();
            jQuery('.clock').fadeOut('slow');
            jQuery('.refreshNotice').fadeIn('slow');

            setInterval(function () {
                jQuery.ajax({
                    url: wpwebinarsystem.ajaxurl,
                    data: {action: 'checkWebinarStatus', post_id: <?php echo $post->ID; ?>},
                    dataType: 'json',
                    type: 'POST',
                }).done(function (response) {
                    if (response) {
                        location.reload();
                    }
                });
            }, 10000);
        }
        
        function showNotices(){
                var dt = new Date();
                var WEB_DATE = convertUTCDateToLocalDate(new Date('<?php echo $webinar_date; ?>'));
                
                var WEBINAR_DATE = new Date(WEB_DATE);
                var CURRENT_DATE = dt;
                
                if(WEBINAR_DATE < CURRENT_DATE){
                    jQuery('#countd_notice').fadeOut();
                    jQuery('.refreshNotice').fadeIn();
                }else{
                    jQuery('#countd_notice').fadeIn();
                    jQuery('.refreshNotice').fadeOut();
                }
        }
        
        function convertUTCDateToLocalDate(date) {
            var newDate = new Date(date.getTime()+date.getTimezoneOffset()*60*1000);
            var offset = date.getTimezoneOffset() / 60;
            var hours = date.getHours();
            newDate.setHours(hours - offset);
            return newDate;   
        }
            
        <?php endif; ?>
        
    </script>
    <?php wp_footer(); ?> 
</body>
</html>
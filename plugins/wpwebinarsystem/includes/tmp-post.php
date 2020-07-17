<?php
global $post;
setup_postdata($post);
$data_pagetitle_color = get_post_meta($post->ID, '_wswebinar_tnxp_pagetitle_clr', true);
$pagebkg = get_post_meta($post->ID, '_wswebinar_tnxp_bckg_clr', true);
$pagebkgimg = get_post_meta($post->ID, '_wswebinar_tnxp_bckg_img', true);
WebinarSysteem::setPostData($post->ID);
$dateFormat = get_option('date_format');
$timeFormat = get_option('time_format');
$data_imgvid_type = get_post_meta($post->ID, '_wswebinar_tnxp_vidurl_type', true);
$data_imgvid_url = get_post_meta($post->ID, '_wswebinar_tnxp_vidurl', true);
$data_defImgUrl = plugins_url('/images/macthumbsup.jpg', __FILE__);
$data_pagetitle = get_post_meta($post->ID, '_wswebinar_tnxp_pagetitle', true);

$the_tnxp_page_title_color = empty($data_pagetitle_color) ? '' : 'color:' . $data_pagetitle_color . ';';
$data_ticket_border_color = get_post_meta($post->ID, '_wswebinar_tnxp_tktbdr_clr', true);
$data_ticket_background_color = get_post_meta($post->ID, '_wswebinar_tnxp_tktbckg_clr', true);
$data_ticket_body_background_color = get_post_meta($post->ID, '_wswebinar_tnxp_tktbodybckg_clr', true);
$data_ticket_text_color = get_post_meta($post->ID, '_wswebinar_tnxp_tkttxt_clr', true);
$data_ticket_header_background_color = get_post_meta($post->ID, '_wswebinar_tnxp_tkthdrbckg_clr', true);
$data_ticket_header_text_color = get_post_meta($post->ID, '_wswebinar_tnxp_tkthdrtxt_clr', true);
$data_ticket_button_color = get_post_meta($post->ID, '_wswebinar_tnxp_tktbtn_clr', true);
$data_ticket_button_text_color = get_post_meta($post->ID, '_wswebinar_tnxp_tktbtntxt_clr', true);
$data_tnxp_link_above_clr = get_post_meta($post->ID, '_wswebinar_tnxp_link_above_clr', true);
$data_tnxp_link_below_clr = get_post_meta($post->ID, '_wswebinar_tnxp_link_below_clr', true);
$data_tnxp_socialsharing_border_clr = get_post_meta($post->ID, '_wswebinar_tnxp_socialsharing_border_clr', true);
$data_tnxp_socialsharing_bckg_clr = get_post_meta($post->ID, '_wswebinar_tnxp_socialsharing_bckg_clr', true);
$data_tnxp_calendar_border_clr = get_post_meta($post->ID, '_wswebinar_tnxp_calendar_border_clr', true);
$data_tnxp_calendar_bckg_clr = get_post_meta($post->ID, '_wswebinar_tnxp_calendar_bckg_clr', true);
$data_tnxp_calendartxt_clr = get_post_meta($post->ID, '_wswebinar_tnxp_calendartxt_clr', true) . ' !important';
$data_tnxp_calendarbtntxt_clr = ( (get_post_meta($post->ID, '_wswebinar_tnxp_calendarbtntxt_clr', true)) ? (get_post_meta($post->ID, '_wswebinar_tnxp_calendarbtntxt_clr', true)) : '#861e14' ) . ' !important';
$data_tnxp_calendarbtnbckg_clr = get_post_meta($post->ID, '_wswebinar_tnxp_calendarbtnbckg_clr', true) . ' !important';
$data_tnxp_calendarbtnborder_clr = get_post_meta($post->ID, '_wswebinar_tnxp_calendarbtnborder_clr', true) . ' !important';
$data_tnxp_autoplay = get_post_meta($post->ID, '_wswebinar_tnxp_video_auto_play_yn', true);
$data_tnxp_videocontrols = get_post_meta($post->ID, '_wswebinar_tnxp_video_controls_yn', true);
$data_tnxp_hideBigPlayButton = get_post_meta($post->ID, '_wswebinar_tnxp_bigplaybtn_yn', true);

$the_tnxp_ticket_border_color = empty($data_ticket_border_color) ? '#840000' : $data_ticket_border_color;
$the_tnxp_ticket_background_color = empty($data_ticket_background_color) ? '#fbd35d' : $data_ticket_background_color;
$the_tnxp_ticket_body_background_color = empty($data_ticket_body_background_color) ? '#fbd35d' : $data_ticket_body_background_color;
$the_tnxp_ticket_text_color = empty($data_ticket_text_color) ? '#840000' : $data_ticket_text_color;
$the_tnxp_ticket_header_background_color = empty($data_ticket_header_background_color) ? '#862a28' : $data_ticket_header_background_color;
$the_tnxp_ticket_header_text_color = empty($data_ticket_header_text_color) ? '#FFF' : $data_ticket_header_text_color;
$the_tnxp_ticket_button_color = empty($data_ticket_button_color) ? '' : 'background-color:' . $data_ticket_button_color . ' !important';
$the_tnxp_ticket_button_text_color = empty($data_ticket_button_text_color) ? '' : 'color:' . $data_ticket_button_text_color . ' !important';

$timeabbr = get_post_meta($post->ID, '_wswebinar_timezoneidentifier', true);
$wpoffset = get_option('gmt_offset');
$gmt_offset = WebinarSysteem::formatTimezone(( $wpoffset > 0) ? '+' . $wpoffset : $wpoffset );
$timeZone = '(' . ( (!empty($timeabbr)) ? WebinarSysteem::getTimezoneAbbreviation($timeabbr) : 'UTC ' . $gmt_offset ) . ') ';

$attendee = WebinarSysteemAttendees::getAttendee($post->ID);
$attend_time = WebinarSysteem::getWebinarTime($post->ID, $attendee);
$webinar_duration = WebinarSysteem::getWebinarDuration($post->ID);

$autoplay = empty($data_tnxp_autoplay) ? 0 : 1;
$controls = empty($data_tnxp_videocontrols) ? 0 : 1;
$hideBigPlayButton = $data_tnxp_hideBigPlayButton != "yes";
?>
<html>

    <head>   
        <meta property="og:title" content="<?php the_title(); ?>">
        <meta property="og:url" content="<?php echo get_permalink($post->ID); ?>">
        <meta property="og:description" content="<?php echo substr(wp_strip_all_tags(get_the_content(), true), 0, 500); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo get_the_title(); ?></title>

        <style>
            .center{margin : 0 auto; display :table !important;}
            .addthisevent_dropdown span{
		<?php if (!empty($data_tnxp_calendarbtntxt_clr)): ?> color:<?php echo $data_tnxp_calendarbtntxt_clr ?>; <?php endif; ?>
		<?php if (!empty($data_tnxp_calendarbtnbckg_clr)): ?> background-color:<?php echo $data_tnxp_calendarbtnbckg_clr ?>; <?php endif; ?>
		<?php if (!empty($data_tnxp_calendarbtnborder_clr)): ?> border-color:<?php echo $data_tnxp_calendarbtnborder_clr ?>; <?php endif; ?>
                border-bottom: 1px solid #000000;
                width: 100%;
            }
            .ateoutlook{margin-top: -6px;}
            .clipbaordfade{background: #000; height: 70px;}
            .copyToClip{cursor: pointer;}
            .zeroclipboard-is-hover{background-color: #E9D4D4;cursor: pointer;}
            .zeroclipboard-is-active{background: #C09DEC;}
	    <?php echo empty($pagebkg) ? '' : '.tmp-post{background-color:' . $pagebkg . ' !important;}' ?>
	    <?php echo empty($pagebkgimg) ? '' : '.tmp-post{background-image: url(' . $pagebkgimg . ');}' ?>
        </style>
	<?php wp_head(); ?>
    </head>
    <body class="tmp-post">

        <div id="fb-root"></div>
        <script>(function (d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id))
		    return;
		js = d.createElement(s);
		js.id = id;
		js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.3&appId=101090596631952";
		fjs.parentNode.insertBefore(js, fjs);
	    }(document, 'script', 'facebook-jssdk'));
        </script>
        <script type="text/javascript">(function () {
        // Add to calendar script
        if (window.addtocalendar)if(typeof window.addtocalendar.start == "function")return;
        if (window.ifaddtocalendar == undefined) { window.ifaddtocalendar = 1;
        var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
        s.type = 'text/javascript';s.charset = 'UTF-8';s.async = true;
        s.src = ('https:' == window.location.protocol ? 'https' : 'http')+'://addtocalendar.com/atc/1.5/atc.min.js';
        var h = d[g]('body')[0];h.appendChild(s); }})();
        </script>
        <script>
	    !function (d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
		if (!d.getElementById(id)) {
		    js = d.createElement(s);
		    js.id = id;
		    js.src = p + '://platform.twitter.com/widgets.js';
		    fjs.parentNode.insertBefore(js, fjs);
		}
	    }(document, 'script', 'twitter-wjs');
        </script>

        <div class="container">
            <!--[if lt IE 9]>
                <div style='row'>
                    <div class="col-xs-6 col-xs-offset-2">
                        <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">
                          <img src="<?php echo plugins_url('./images/iecheck.jpg', __FILE__); ?>" border="0" height="42" width="820" alt="" />
                        </a>
                    </div>
                </div>
            <![endif]-->
            <div class="row" style="margin-bottom: 30px;" id="webinar_title">
                <div class="col-xs-12 col-lg-12 col-sm-12">
                    <div> <h1 class="text-center" style="font-weight: 800; <?php echo $the_tnxp_page_title_color; ?>">
			    <?php echo esc_attr($data_pagetitle); ?></h1> </div> 
                </div>
            </div>
            <div class="row" style="padding: 6px;">
                <div class="tmp-post-container">
                    <div id="leftcol" class="col-lg-7 col-sm-6 col-md-7 col-xs-12">
                        <div class="row">
                            <div id="embed">
				<?php if (empty($data_imgvid_url)) { ?>
    				<img src="<?php echo $data_defImgUrl; ?>" width="100%" height="315">
				    <?php
				} else {
				    switch ($data_imgvid_type):
					case 'image':
					    echo '<img src="' . $data_imgvid_url . '" width="100%" height="315">';
					    break;
					case 'youtube':
					    $link = $data_imgvid_url;
					    $youtubeid = WebinarSysteem::getYoutubeIdFromUrl($link);
					    WebinarSysteemVideoSources::getSourceCode('youtube', $youtubeid, $controls, $autoplay, $hideBigPlayButton, $fullscreen = 0);
					    break;
					case 'vimeo':
					    echo '<iframe src="https://player.vimeo.com/video/' . $data_imgvid_url . '?autoplay=' . $autoplay . '" width="100%" height="563" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
					    break;
					case 'file':
					    WebinarSysteemVideoSources::getSourceCode('mp4', $data_imgvid_url, $controls, $autoplay, $hideBigPlayButton, $fullscreen = 0);
					    break;
				    endswitch;
				}
				?>
                            </div>
                            <div id="webinar-link" style="padding:20px;">
                                <h4 style="color:<?php echo $data_tnxp_link_above_clr ?>;"><?php _e('Here is the webinar URL...', WebinarSysteem::$lang_slug) ?></h4>

                                <div class="input-group">
                                    <input type="text" class="form-control" id="theWebinarUrl" value="<?php echo get_permalink($post->ID); ?>"/>
                                    <span class="input-group-btn">
                                        <button style="top:0px; padding: 9px 15px;" data-clipboard-text="<?php echo get_permalink($post->ID); ?>" class="btn btn-default " id="copyToClip"><span class="glyphicon glyphicon-link"></button>
                                    </span>
                                </div>
                                <h5 style="color:<?php echo $data_tnxp_link_below_clr ?>;"><?php _e('Save and bookmark this URL so you can get access to the webinar...', WebinarSysteem::$lang_slug) ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-sm-6 col-md-5 col-xs-12">
                        <div class="tickets" style="border: 2px <?php echo $the_tnxp_ticket_border_color ?> dashed; background-color: <?php echo $the_tnxp_ticket_background_color ?>;">
                            <div class="ticket-top" style="color:<?php echo $the_tnxp_ticket_header_text_color ?>;  background-color: <?php echo $the_tnxp_ticket_header_background_color ?>;">

                                <div style="font-size: 40pt;margin: 5px 10px;" class="pull-left glyphicon glyphicon-film"></div>
                                <div class="pull-left">
                                    <h4><?php _e('Your Webinar Ticket', WebinarSysteem::$lang_slug) ?></h4>
                                    <h6><?php _e('The Webinar Event Information...', WebinarSysteem::$lang_slug) ?></h6>
                                </div> <br/>

                            </div>

                            <div class="ticket-bottom" style="background-color: #fbd35d;">
                                <div class="ticket-bottom" style="background-color: <?php echo $the_tnxp_ticket_body_background_color ?>;">
                                    <div style="color: <?php echo $the_tnxp_ticket_text_color ?>; padding: 25px;">
					<?php
					$dateTimeIsSet = empty($attend_time);
					?>
                        <div id="ticket-webinar-title" class="ticket-info"><span class="glyphicon glyphicon-facetime-video"></span>&nbsp;&nbsp;<span class="tick-left"><?php _e('Webinar', WebinarSysteem::$lang_slug) ?></span><span class="tick-right"><?php echo get_the_title(); ?></span></div>
                        <div id="ticket-webinar-host" class="ticket-info"><span class="glyphicon glyphicon-bullhorn"></span>&nbsp;&nbsp;<span class="tick-left"><?php _e('Host', WebinarSysteem::$lang_slug) ?></span><span class="tick-right"><?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_hostmetabox_hostname', true)); ?></span></div>
					<?php if (!$dateTimeIsSet): ?>
    					<div id="ticket-webinar-date" class="ticket-info"><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;<span class="tick-left"><?php _e('Date', WebinarSysteem::$lang_slug) ?></span><span class="tick-right"><?php echo date_i18n($dateFormat, $attend_time); ?></span></div>
    					<div id="ticket-webinar-time" class="ticket-info"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;<span class="tick-left"><?php _e('Time', WebinarSysteem::$lang_slug) ?></span><span class="tick-right"><?php echo date_i18n($timeFormat, $attend_time) . ' ' . $timeZone; ?></span></div>
					<?php endif; ?>
                                    </div>
				    <?php
				    $wbstatus = get_post_meta($post->ID, '_wswebinar_gener_webinar_status', true);
				    $hide = TRUE;
				    if ($wbstatus == 'liv' || $wbstatus == 'cou' || $wbstatus == 'rep')
					$hide = FALSE;
				    ?>
                                    <div style="padding:10px;"><a id="view-webinar-button" href="<?php echo get_permalink(); ?>" style="width:100%; <?php echo $the_tnxp_ticket_button_color ?>; <?php echo $the_tnxp_ticket_button_text_color ?>; " class="btn <?php echo ($hide ? 'hidden' : '') ?>">
					    <?php
					    $join_now_btn = (isset($pagedata['rightnowuser']) ? $pagedata['rightnowuser'] : false);
					    //_e('Join webinar now', WebinarSysteem::$lang_slug);
					    switch ($wbstatus):
						case 'liv':
						    _e('Join Webinar in Progress...', WebinarSysteem::$lang_slug);
						    $join_now_btn = FALSE;
						    break;
						case 'cou':
						    _e(($join_now_btn ? 'Join Webinar in Progress...' : 'Go to Webinar'), WebinarSysteem::$lang_slug);
						    $join_now_btn = FALSE;
						    break;
						case 'rep':
						    _e('View Webinar Replay', WebinarSysteem::$lang_slug);
						    $join_now_btn = FALSE;
						    break;
					    endswitch;
					    ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="webinar-socialsharing" class="tnxp-box" style="background-color: <?php echo $data_tnxp_socialsharing_bckg_clr ?>;border-color: <?php echo $data_tnxp_socialsharing_border_clr ?>;">
                            <div class="social-buttons">
                                <a href="#" onClick="window.open('http://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink($post->ID); ?>', 'webinar-fb', 'width=500,height=500')"><img src="<?php echo plugins_url('./images/ui/fb.png', __FILE__); ?>"/></a>
                                <span>
                                    <a href="https://twitter.com/intent/tweet?text=<?php echo get_the_title(); ?>&url=<?php echo get_permalink($post->ID); ?>" target="_blank"><img src="<?php echo plugins_url('./images/ui/tw.png', __FILE__); ?>"/></a>
                                </span>
                                <span>
                                    <a href="#" onClick="window.open('https://plus.google.com/share?url=<?php echo get_permalink($post->ID); ?>', 'webinar-gplus', 'width=500,height=500')"><img src="<?php echo plugins_url('./images/ui/gp.png', __FILE__); ?>"/></a>
                                </span>
                                <span>
                                    <a href="#" onClick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink($post->ID)); ?>&title=<?php echo urlencode(get_the_title()); ?>&summary=<?php echo urlencode(wp_strip_all_tags(get_the_content(), true)); ?>', 'webinar-linkedin', 'width=500,height=500')"><img src="<?php echo plugins_url('./images/ui/li.png', __FILE__); ?>"/></a>
                                </span>
                            </div>
                        </div>

                        <div id="webinar-calender" class="tnxp-box" style="color:<?php echo $data_tnxp_calendartxt_clr ?>;  position: relative; overflow: visible;  background-color: <?php echo $data_tnxp_calendar_bckg_clr ?>;border-color: <?php echo $data_tnxp_calendar_border_clr ?>;">
                            <div style="font-size: 40pt;margin: 5px 10px;" class="pull-left glyphicon glyphicon-calendar"></div>
                            <div class="pull-left">
                                <h4><?php _e('Add To Your Calendar', WebinarSysteem::$lang_slug) ?></h4>
                                <h6><?php _e('Remind Yourself Of The Event', WebinarSysteem::$lang_slug) ?></h6>
                            </div><br/>
                            <div style="border-top: 2px dotted #e7e4e0; margin-top: 43px;"></div>
                            <div style="width:100%;">
                                
                            <!--Add-to-calendar html-->
                            <?php
                                $title = urldecode(get_the_title());
                                $title = '&quot;' . $title . '&quot;';
                                $titleWithQ = '' . get_the_title() . '';
                                $dauration_secs  = get_post_meta($post->ID, '_wswebinar_gener_duration', true);
                                $start_at = $attend_time;
                                $end_at = strtotime("+$dauration_secs seconds", $start_at);
                                $timezone = get_post_meta($post->ID, '_wswebinar_timezoneidentifier', true);
                                
                                $gmt_offset = (strpos($gmt_offset, '.') ? $gmt_offset : $gmt_offset.':00');
                                $gmt_offset = str_replace('.', ':', $gmt_offset);
                                $gen_str = WebinarSysteem::get_timezone_str_by_utc_offset($gmt_offset);
                            ?>
                            <style>
                                .atcb-link{ display : inline; color:<?php echo $data_tnxp_calendarbtntxt_clr ?>; font-size: 12px !important; margin-left: 15px !important; }
                            </style>
                            <span class="addtocalendar atc-style-blue btn btm-btn glyphicon" style="color:<?php echo $data_tnxp_calendarbtntxt_clr ?>;background-color:<?php echo $data_tnxp_calendarbtnbckg_clr ?>;border-color:<?php echo $data_tnxp_calendarbtnborder_clr ?>;">
                                <span class="glyphicon-plus-sign" style="position: absolute; padding-top: 3px;"></span>
                                <var class="atc_event">
                                    <var class="atc_date_start"><?php echo gmdate('d-m-Y H:i:s', $start_at); ?></var>
                                    <var class="atc_date_end"><?php echo gmdate('d-m-Y H:i:s', $end_at); ?></var>
                                    <var class="atc_timezone"><?php echo ($timezone == null || strlen($timezone) <= 1 ? (empty($gen_str) ? "UTC" : $gen_str ) : $timezone); ?></var>
                                    <var class="atc_title"><?php echo $titleWithQ; ?></var>
                                    <var class="atc_description"><?php echo wp_strip_all_tags(get_the_content(), true); ?></var>
                                    <var class="atc_location"><?php echo get_permalink($post->ID); ?></var>
                                    <var class="atc_organizer"><?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_hostmetabox_hostname', true)); ?></var>
                                    <var class="atc_organizer_email"><?php echo get_option('admin_email'); ?></var>
                                </var>
                            </span>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
		var client = new ZeroClipboard(document.getElementById("copyToClip"));

		client.on("ready", function (readyEvent) {
		    client.on("aftercopy", function (event) {
			jQuery("#theWebinarUrl").animate("clipbaordfade", 1000);
		    });
		});

		jQuery(function () {
		    jQuery(document).on('click', 'input[type=text]', function () {
			this.select();
		    });
		});


		addthisevent.settings({
		    mouse: false,
		    css: false,
		    outlook: {show: true, text: "Outlook Calendar"},
		    ical: {show: true, text: "iCal Calendar"}
		});


            </script>
	    <?php wp_footer(); ?> 
    </body>
</html>

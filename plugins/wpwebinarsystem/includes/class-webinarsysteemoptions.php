<?php
/*
 * 
 * Webinar General Settings page.
 * 
 */

class WebinarSysteemOptions extends WebinarSysteem {

    private $localkey_statuss;

    function __construct($localkey_status) {
	$this->localkey_statuss = $localkey_status;
	parent::setAttributes();
    }

    public static function wbn_network_setting_save() {
	if (!isset($_POST['wpwbn-network-save']))
	    return;

	$lkey = $_POST['wbn_network_lkey'];
	update_option('_wswebinar_licensekey_network', $lkey);
    }

    public static function wbn_non_mpmu_license_save() {
	if (!isset($_POST['_wswebinar_licensekey']))
	    return;

	update_option('_wswebinar_licensekey', $_POST['_wswebinar_licensekey']);
    }

    public function wbn_network_menu() {
	?>
	<div class="wrap wswebinarwrap">
	    <div class="wswebinarLogo">
		<img src="<?php echo plugins_url('images/WebinarSysteem-logo.png', __FILE__); ?>" />
	    </div>
	    <div style="clear: both"></div>
	    <h2 class=""><?php _e('WP WebinarSystem Options', WebinarSysteem::$lang_slug); ?></h2>
	    <form  method="post">
		<input type="hidden" name="wpwbn-network-save" value="true">

		<?php
		include'templates/template-options-license.php';
		echo submit_button();
		?>
	    </form>
	</div>
	<?php
    }

    public function wbn_gengeral_settings() {

	if (isset($_GET['reset']) && !isset($_GET['settings-updated'])):
	    $setting = isset($_GET['setting']) ? $_GET['setting'] : NULL;
	    WebinarSysteemOptions::DoResetDefaults($setting);
	endif;

	$license = get_option('_wswebinar_licensekey');
	$status = get_option('edd_sample_license_status');
	?>



	<div class="wrap wswebinarwrap">
	    <div class="wswebinarLogo">
		<img src="<?php echo plugins_url('images/WebinarSysteem-logo.png', __FILE__); ?>" />
	    </div>
	    <div style="clear: both"></div>
	    <h2 class=""><?php _e('WP WebinarSystem Options', WebinarSysteem::$lang_slug); ?></h2>
	    <form action="options.php" method="post">                    
		<h2 class="nav-tab-wrapper">
		    <?php if (!is_multisite()) { ?>
	    	    <a class="nav-tab nav-tab-active" href="#tabs-1"><?php _e('General', WebinarSysteem::$lang_slug); ?></a>
		    <?php } ?>
		    <a class="nav-tab<?php if (is_multisite()) { ?> nav-tab-active<?php } ?>" href="#tabs-2"><?php _e('Emails', WebinarSysteem::$lang_slug); ?></a>         
		    <a class="nav-tab" href="#tabs-3"><?php _e('Mailinglist providers', WebinarSysteem::$lang_slug); ?></a>
		    <a class="nav-tab" href="#tabs-4"><?php _e('Advanced', WebinarSysteem::$lang_slug); ?></a>
		    <a class="nav-tab" href="#tabs-6"><?php _e('Integrations', WebinarSysteem::$lang_slug); ?></a>
		    <a class="nav-tab" href="#tabs-7"><?php _e('Styles', WebinarSysteem::$lang_slug); ?></a>
		    <a class="nav-tab" href="#tabs-5"><?php _e('System Status', WebinarSysteem::$lang_slug); ?></a>
		</h2>
		<?php settings_fields('wswebinar_options'); ?>
		<?php do_settings_sections('wswebinar_options'); ?>
		<?php if (!is_multisite()) { ?>
	    	<div id="tabs-1" class="nav-tab-content">
	    	    <h3><?php _e('General Options', WebinarSysteem::$lang_slug); ?></h3>
			<?php
			include'templates/template-options-license.php';
			submit_button();
			?>

	    	</div>
		<?php } ?>
		<div id="tabs-2" class="<?php if (!is_multisite()) { ?>ws-hide-nav-tabs <?php } ?>nav-tab-content">
		    <div class="wswebinarCustomTab">
			<div class="tabHeaders-container">
			    <ul class="tabHeaders">
				<li><a href="#customTab1" class="customTabActive"><?php _e('Email options', WebinarSysteem::$lang_slug); ?></a></li> |
				<li><a href="#customTab2"><?php _e('New Registration', WebinarSysteem::$lang_slug); ?></a></li> |
				<li><a href="#customTab7"><?php _e('Registration Confirmation', WebinarSysteem::$lang_slug); ?></a></li> |
				<li><a href="#customTab3"><?php _e('Reminder day before Webinar', WebinarSysteem::$lang_slug); ?></a></li> |
				<li><a href="#customTab4"><?php _e('Reminder one hour before Webinar', WebinarSysteem::$lang_slug); ?></a></li> |
				<li><a href="#customTab5"><?php _e('Reminder Webinar Starting', WebinarSysteem::$lang_slug); ?></a></li> |
				<li><a href="#customTab6"><?php _e('Webinar replay', WebinarSysteem::$lang_slug); ?></a></li>
			    </ul>
			</div>
			<div class="email-customTab" id="customTab1">

			    <h3><?php _e('Email Sender Options', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">                                    
				<tr>
				    <th><label for="SentFrom"><?php _e('"From" Name', WebinarSysteem::$lang_slug); ?></label></th><td><input id="SentFrom" name="_wswebinar_email_sentFrom" class="regular-text" type="text" placeholder="<?php _e('Sender name', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_email_sentFrom'); ?>"/></td>
				</tr>
				<tr>
				    <th><label for="SenderEmailAddress"><?php _e('"From" Email Address', WebinarSysteem::$lang_slug); ?></label></th><td><input id="SenderEmailAddress" class="regular-text" name="_wswebinar_email_senderAddress" type="email" placeholder="<?php _e('Sender email', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_email_senderAddress'); ?>"/></td>
				</tr>
			    </table>

			    <h3><?php _e('Email Template', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">
				<tr>
				    <th>
					<label for="HeaderImg"><?php _e('Header Image', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input type="text" name="_wswebinar_email_headerImg" id="email_headerImg" class="regular-text" value="<?php echo get_option('_wswebinar_email_headerImg') ?>">
					<button class="button wswebinar_uploader" resultId="email_headerImg" uploader_title="Header Image for Mails"><?php _e('Upload', WebinarSysteem::$lang_slug); ?></button>
					<div class="webinar_clear_fix"></div>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="EmailFooterTxt"><?php _e('Email Footer Text', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<textarea id="EmailFooterTxt" class="large-text" name="_wswebinar_email_footerTxt" placeholder="<?php _e('Footer Text', WebinarSysteem::$lang_slug) ?>"><?php echo get_option('_wswebinar_email_footerTxt'); ?></textarea>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="BaseCLR"><?php _e('Base color', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="BaseCLR" name="_wswebinar_email_baseCLR" class="color-field" type="text" value="<?php echo get_option('_wswebinar_email_baseCLR'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="BckCLR"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="BckCLR" name="_wswebinar_email_bckCLR" class="color-field" type="text" value="<?php echo get_option('_wswebinar_email_bckCLR'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="EmailBodyBck"><?php _e('Email Body Background color', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="EmailBodyBck" name="_wswebinar_email_bodyBck" type="text" class="color-field" value="<?php echo get_option('_wswebinar_email_bodyBck'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="EMailBodyTXT"><?php _e('Email Body Text color', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="EMailBodyTXT" name="_wswebinar_email_bodyTXT" type="text" class="color-field" value="<?php echo get_option('_wswebinar_email_bodyTXT'); ?>"/>
				    </td>
				</tr>
                <tr>
					<th>
					<label for="_wswebinar_subscription"><?php _e('Activate profile and unsubscribe link', WebinarSysteem::$lang_slug); ?></label>
					</th>
					<td>
					<input class="wswebinar_subscription" data-switch="true" type="checkbox" name="_wswebinar_subscription" <?php echo (get_option('_wswebinar_subscription') == 'on' ? 'checked' : ''); ?>/>
					</td>
				</tr>
				<tr id="wswebinar_unsubscribe" style="<?php echo (get_option('_wswebinar_subscription') == 'on' ? '' : 'display: none;'); ?>">
					<?php 
					$pages = WebinarSysteemUserPages::getAllPages();
					$unsubscribe = get_option('_wswebinar_unsubscribe');
					?>
					<th>
					<label for="_wswebinar_unsubscribe"><?php _e('Choose Unsusbscribe Page', WebinarSysteem::$lang_slug); ?></label>
					</th>
					<td>
					<select name="_wswebinar_unsubscribe" id="_wswebinar_unsubscribe">
						<?php foreach($pages as $page) {
						$selected = ($page['id'] == $unsubscribe) ? 'selected="selected"' : '';
						?>
						<option  value="<?php echo $page['id']; ?>" <?php echo $selected; ?> ><?php echo $page['title']; ?></option>
						<?php } ?>
						<option></option>
					</select>
					<p class="description"><?php _e('WP WebinarSystem plugin uses it\'s own generated page by default. If you prefer an own custom page, then please select this on the dropdown menu + add shortcode [wpws_unsubscribe] to your page.', WebinarSysteem::$lang_slug); ?></p>
					</td>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
			</div>

			<div class="email-customTab ws-hide-nav-tabs" id="customTab2">
			    <h3><?php _e('New Registration Options', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">    
			    <tr>
			    	<th>
			    	<label for="newreg-enable"><?php _e('Enable this reminder?', WebinarSysteem::$lang_slug) ?></label>
			    	</th>
			    	<td>
					<input id="newreg-enable" data-switch="true" type="checkbox" name="_wswebinar_newregenable" <?php echo (get_option('_wswebinar_newregenable') == 'on' ? 'checked' : ''); ?>/>
				    </td>
			    </tr>
			    <tr>
				    <th>
					<label for="newregsubject"><?php _e('Subject', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="newregsubject" class="regular-text" name="_wswebinar_newregsubject"  placeholder="<?php _e('Email Head', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_newregsubject'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label><?php _e('Available Shortcodes:', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					[receiver-name], [webinar-title], [webinar-link], [webinar-date], [webinar-time], [webinar-timezone]
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="newreg"><?php _e('Content', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<?php
					$meta = get_option('_wswebinar_newregcontent');
					if(empty($meta)){
						$template = WebinarSysteem::getDefaultMailTemplates();
						update_option(WebinarSysteem::$lang_slug . '_newregcontent', $template['newreg']);				
						$meta = get_option('_wswebinar_newregcontent');		
					}
					
					$content = apply_filters('meta_content', $meta);
					wp_editor($content, '_wswebinar_newregcontent');
					?>
				    </td>
				</tr>                               
				<tr>
				    <th><label for="AdminEmailAddress"><?php _e('Admin Email Address', WebinarSysteem::$lang_slug); ?></label></th><td><input id="AdminEmailAddress" class="regular-text" name="_wswebinar_AdminEmailAddress" type="email" placeholder="<?php _e('Admin email', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_AdminEmailAddress'); ?>"/></td>
				</tr>
                <tr>
				    <th>&nbsp;</th>
				    <?php
				    $posts = query_posts(array('post_type' => 'wswebinars'));
				    if (!empty($posts)):
					?>
	    			    <td>
	    				<input type="email" class="regular-text preview-email-textbox" placeholder="<?php _e('Your email address', WebinarSysteem::$lang_slug) ?>" data-mail-type="_wswebinar_newreg"/>
	    				<input type="button" value="<?php _e('Send Preview', WebinarSysteem::$lang_slug) ?>" id="submit" class="button button-primary" data-mail-type="_wswebinar_newreg">
	    				<p>Please use a different email address than your website admin email address</p>
	    			    </td>
				    <?php else: ?>
	    			    <td><?php _e('Add a Webinar to send a preview email.', WebinarSysteem::$lang_slug); ?></td>
				    <?php endif; ?>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes&setting=newreg'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
			</div>
			
			<div class="email-customTab ws-hide-nav-tabs" id="customTab7">
            	<h3><?php _e('Registration Confirmation Email ', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">
				<tr>
				    <th>
					<label for="regconfirm-enable"><?php _e('Enable this reminder?', WebinarSysteem::$lang_slug) ?></label>
				    </th>
				    <td>
					<input id="regconfirm-enable" data-switch="true" type="checkbox" name="_wswebinar_regconfirmenable" <?php echo (get_option('_wswebinar_regconfirmenable') == 'on' ? 'checked' : ''); ?>/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="regconfirmsubject"><?php _e('Subject', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="regconfirmsubject" class="regular-text" name="_wswebinar_regconfirmsubject"  placeholder="<?php _e('Email Head', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_regconfirmsubject'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label><?php _e('Available Shortcodes:', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					[receiver-name], [webinar-title], [webinar-link], [webinar-date], [webinar-time], [webinar-timezone]
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="regconfirm"><?php _e('Content', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<?php
					$meta = get_option('_wswebinar_regconfirmcontent');
					if(empty($meta)){
						$template = WebinarSysteem::getDefaultMailTemplates();
						update_option(WebinarSysteem::$lang_slug . '_regconfirmcontent', $template['regconfirm']);				
						$meta = get_option('_wswebinar_regconfirmcontent');		
					}
					$content = apply_filters('meta_content', $meta);
					wp_editor($content, '_wswebinar_regconfirmcontent');
					?>
				    </td>
				</tr>
				<tr>
				    <th>&nbsp;</th>
				    <?php
				    $posts = query_posts(array('post_type' => 'wswebinars'));
				    if (!empty($posts)):
					?>
	    			    <td>
	    				<input type="email" class="regular-text preview-email-textbox" placeholder="<?php _e('Your email address', WebinarSysteem::$lang_slug) ?>" data-mail-type="_wswebinar_regconfirm"/>
	    				<input type="button" value="<?php _e('Send Preview', WebinarSysteem::$lang_slug) ?>" id="submit" class="button button-primary" data-mail-type="_wswebinar_regconfirm">
	    				<p>Please use a different email address than your website admin email address</p>
	    			    </td>
				    <?php else: ?>
	    			    <td><?php _e('Add a Webinar to send a preview email.', WebinarSysteem::$lang_slug); ?></td>
				    <?php endif; ?>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes&setting=regconfirm'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
            </div>
            
			<div class="email-customTab ws-hide-nav-tabs" id="customTab3">
			    <h3><?php _e('Reminder day before Webinar ', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">
				<tr>
				    <th>
					<label for="24hrb4-enable"><?php _e('Enable this reminder?', WebinarSysteem::$lang_slug) ?></label>
				    </th>
				    <td>
					<input id="24hrb4-enable" data-switch="true" type="checkbox" name="_wswebinar_24hrb4enable" <?php echo (get_option('_wswebinar_24hrb4enable') == 'on' ? 'checked' : ''); ?>/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="24hrb4subject"><?php _e('Subject', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="24hrb4subject" class="regular-text" name="_wswebinar_24hrb4subject"  placeholder="<?php _e('Email Head', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_24hrb4subject'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label><?php _e('Available Shortcodes:', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					[receiver-name], [webinar-title], [webinar-link], [webinar-date], [webinar-time], [webinar-timezone]
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="24hrb4"><?php _e('Content', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<?php
					$meta = get_option('_wswebinar_24hrb4content');
					$content = apply_filters('meta_content', $meta);
					wp_editor($content, '_wswebinar_24hrb4content');
					?>
				    </td>
				</tr>
				<tr>
				    <th>&nbsp;</th>
				    <?php
				    $posts = query_posts(array('post_type' => 'wswebinars'));
				    if (!empty($posts)):
					?>
	    			    <td>
	    				<input type="email" class="regular-text preview-email-textbox" placeholder="<?php _e('Your email address', WebinarSysteem::$lang_slug) ?>" data-mail-type="_wswebinar_24hrb4"/>
	    				<input type="button" value="<?php _e('Send Preview', WebinarSysteem::$lang_slug) ?>" id="submit" class="button button-primary" data-mail-type="_wswebinar_24hrb4">
	    				<p>Please use a different email address than your website admin email address</p>
	    			    </td>
				    <?php else: ?>
	    			    <td><?php _e('Add a Webinar to send a preview email.', WebinarSysteem::$lang_slug); ?></td>
				    <?php endif; ?>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes&setting=24hr'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
			</div>
			<div class="email-customTab  ws-hide-nav-tabs" id="customTab4">
			    <h3><?php _e('Reminder one hour before Webinar ', WebinarSysteem::$lang_slug); ?></h3>

			    <table class="form-table">
				<tr>
				    <th>
					<label for="1hrb4-enable"><?php _e('Enable this reminder?', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="1hrb4-enable" data-switch="true" type="checkbox" name="_wswebinar_1hrb4enable" <?php echo (get_option('_wswebinar_1hrb4enable') == 'on' ? 'checked' : ''); ?>/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="1hrb4subject"><?php _e('Subject', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="1hrb4subject" class="regular-text" name="_wswebinar_1hrb4subject"  placeholder="<?php _e('Email Head', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_1hrb4subject'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label><?php _e('Available Shortcodes:', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					[receiver-name], [webinar-title], [webinar-link], [webinar-date], [webinar-time], [webinar-timezone]
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="1hrb4"><?php _e('Content', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<?php
					$meta = get_option('_wswebinar_1hrb4content');
					$content = apply_filters('meta_content', $meta);
					wp_editor($content, '_wswebinar_1hrb4content');
					?>
				    </td>
				</tr>
				<tr>
				    <th>&nbsp;</th>
				    <?php if (!empty($posts)): ?>
	    			    <td>
	    				<input class="regular-text preview-email-textbox" placeholder="<?php _e('Your email address', WebinarSysteem::$lang_slug) ?>" data-mail-type="_wswebinar_1hrb4" type="email" />
	    				<input type="button" value="<?php _e('Send Preview', WebinarSysteem::$lang_slug) ?>" id="submit" class="button button-primary" data-mail-type="_wswebinar_1hrb4">
	    				<p>Please use a different email address than your website admin email address</p>
	    			    </td>
				    <?php else: ?>
	    			    <td><?php _e('Add a Webinar to send a preview email.', WebinarSysteem::$lang_slug); ?></td>
				    <?php endif; ?>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes&setting=1hr'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
			</div>
			<div class="email-customTab  ws-hide-nav-tabs" id="customTab5">
			    <h3><?php _e('Reminder when Webinar starts', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">  
				<tr>
				    <th>
					<label for="wbnstartedenable"><?php _e('Enable this reminder?', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="wbnstartedenable" data-switch="true" name="_wswebinar_wbnstartedenable" type="checkbox" <?php echo (get_option('_wswebinar_wbnstartedenable') == 'on' ? 'checked' : ''); ?>/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="wbnstartedsubject"><?php _e('Subject', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="wbnstartedsubject" class="regular-text" name="_wswebinar_wbnstartedsubject" placeholder="<?php _e('Email Head', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_wbnstartedsubject'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label><?php _e('Available Shortcodes:', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					[receiver-name], [webinar-title], [webinar-link], [webinar-date], [webinar-time], [webinar-timezone]
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="24hrb4"><?php _e('Content', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<?php
					$meta = get_option('_wswebinar_wbnstarted');
					$content = apply_filters('meta_content', $meta);
					wp_editor($content, '_wswebinar_wbnstarted');
					?>
				    </td>
				</tr>
				<tr>
				    <th>&nbsp;</th>
				    <?php if (!empty($posts)): ?>
	    			    <td>
	    				<input class="regular-text preview-email-textbox" placeholder="<?php _e('Your email address', WebinarSysteem::$lang_slug) ?>" data-mail-type="_wswebinar_wbnstarted" type="email" />
	    				<input type="button" value="<?php _e('Send Preview', WebinarSysteem::$lang_slug) ?>" id="submit" class="button button-primary" data-mail-type="_wswebinar_wbnstarted">
	    				<p>Please use a different email address than your website admin email address</p>
	    			    </td>
				    <?php else: ?>
	    			    <td><?php _e('Add a Webinar to send a preview email.', WebinarSysteem::$lang_slug); ?></td>
				    <?php endif; ?>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes&setting=started'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
			</div>
			<div class="email-customTab  ws-hide-nav-tabs" id="customTab6">
			    <h3><?php _e('Send Webinar Replay Link', WebinarSysteem::$lang_slug); ?></h3>
			    <table class="form-table">
				<tr>
				    <th>
					<label for="wbnreplay-enable"><?php _e('Enable this reminder?', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="wbnreplay-enable" data-switch="true" type="checkbox" name="_wswebinar_wbnreplayenable" <?php echo (get_option('_wswebinar_wbnreplayenable') == 'on' ? 'checked' : ''); ?>/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="wbnreplaysubject"><?php _e('Subject', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<input id="wbnreplaysubject" class="regular-text" name="_wswebinar_wbnreplaysubject" placeholder="<?php _e('Email Head', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_wbnreplaysubject'); ?>"/>
				    </td>
				</tr>
				<tr>
				    <th>
					<label><?php _e('Available Shortcodes:', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					[receiver-name], [webinar-title], [webinar-link], [webinar-date], [webinar-time], [webinar-timezone]
				    </td>
				</tr>
				<tr>
				    <th>
					<label for="24hrb4"><?php _e('Content', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <td>
					<?php
					$meta = get_option('_wswebinar_wbnreplay');
					$content = apply_filters('meta_content', $meta);
					wp_editor($content, '_wswebinar_wbnreplay');
					?>
				    </td>
				</tr>
				<tr>
				    <th>&nbsp;</th>
				    <?php if (!empty($posts)): ?>
	    			    <td>
	    				<input class="regular-text preview-email-textbox" placeholder="<?php _e('Your email address', WebinarSysteem::$lang_slug) ?>" data-mail-type="_wswebinar_wbnreplay" type="email" />
	    				<input type="button" value="<?php _e('Send Preview', WebinarSysteem::$lang_slug) ?>" id="submit" class="button button-primary" data-mail-type="_wswebinar_wbnreplay">
	    				<p>Please use a different email address than your website admin email address</p>
	    			    </td>
				    <?php else: ?>
	    			    <td><?php _e('Add a Webinar to send a preview email.', WebinarSysteem::$lang_slug); ?></td>
				    <?php endif; ?>
				</tr>
			    </table>
			    <div>
				<div class="optionpage-buttons-email">
				    <a class="button optionspage-buttons-email-reset" href="<?php echo admin_url('edit.php?post_type=wswebinars&page=wswbn-options&reset=yes&setting=replay'); ?>"><?php _e('Reset Default Template', WebinarSysteem::$lang_slug); ?></a>
				</div>
				<div class="optionpage-buttons-email">
				    <?php submit_button(); ?>
				</div>
			    </div>
			</div>
		    </div>
		</div>  

		<div id="tabs-3"  class="ws-hide-nav-tabs nav-tab-content">

		    <?php settings_fields('wswebinar_options'); ?>
		    <?php do_settings_sections('wswebinar_options'); ?>
		    <div class="customTab">
			<h3><?php _e('Mailinglist providers', WebinarSysteem::$lang_slug); ?></h3>                        

			<table>
			    <tbody>
			     <tr class="_wswebinar_drip_setting_section">
			    	<th>
			    	<label for="drip_api_key"><?php _e('Drip API Key', WebinarSysteem::$lang_slug); ?></label>
			    	</th>
			    	<th>
			    	:
			    	</th>
			    	<td>
			    	<input id="drip_api_key" class="regular-text" type="text" name="_wswebinar_dripapikey" placeholder="<?php 
			    	_e('The API key you have received from Drip',WebinarSysteem::$lang_slug);?>" value="<?php echo get_option('_wswebinar_dripapikey'); ?>">	
			    	</td>
			    	<td>
			    	<img id="webinar_drip_loader" style="display: none;" src="<?php echo plugins_url('images/loading_small.GIF', __FILE__); ?>">
					<img id="webinar_drip_correct" style="display: none;" src="<?php echo plugins_url('images/email_verf_correct.PNG', __FILE__); ?>">
					<img id="webinar_drip_incorrect" style="display: none;" src="<?php echo plugins_url('images/email_verf_error.PNG', __FILE__); ?>">
					<button class="button drip_apichecker" type="button" id="webinar_drip_check">Check</button>	
			    	</td>
			    	<td id="webinar_drip_invalid_api"></td>
			    </tr>
				<tr class="_wswebinar_mailchimp_setting_section">
				    <th>
					<label for="mailchimp_api_key"><?php _e('MailChimp API Key', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <th>
					:
				    </th>
				    <td>
					<input id="mailchimp_api_key" class="regular-text" type="text" name="_wswebinar_mailchimpapikey" placeholder="<?php _e('The API key you have received from MailChimp', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_mailchimpapikey'); ?>">
				    </td>

				</tr>
				<tr class="_wswebinar_enormail_setting_section"> 
				    <th>
					<label for="enormail_api_key"><?php _e('Enormail API Key', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <th>
					:
				    </th>
				    <td>
					<input id="enormail_api_key" class="regular-text" type="text" name="_wswebinar_enormailapikey" placeholder="<?php _e('The API key you have received from Enormail', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_enormailapikey'); ?>">
				    </td>
				    <td>
					<img id="webinar_enormail_loader" style="display: none;" src="<?php echo plugins_url('images/loading_small.GIF', __FILE__); ?>">
					<img id="webinar_enormail_correct" style="display: none;" src="<?php echo plugins_url('images/email_verf_correct.PNG', __FILE__); ?>">
					<img id="webinar_enormail_error" style="display: none;" src="<?php echo plugins_url('images/email_verf_error.PNG', __FILE__); ?>">
					<button class="button" type="button" id="webinar_enormail_check">Check</button>
				    </td>
				    <td id="webinar_enormail_user_name"></td>
				</tr>
				<tr class="_wswebinar_aweber_setting_section">
				    <th>
					<label for="aweber_con">Connect with Aweber </label>
				    </th>
				    <td>
					:
				    </td>
				    <td>
					<button class="button connect-aweber" data-tab="<?php echo (!empty($_GET['tab']) ? 'true' : 'false'); ?>" type="button"><?php echo (WebinarsysteemMailingListIntegrations::aWeber_Connected() ? _e('Reconfigure', WebinarSysteem::$lang_slug) : _e('Configure', WebinarSysteem::$lang_slug)) ?></button>
					<?php if (WebinarsysteemMailingListIntegrations::aWeber_Connected()) { ?>
	    				<span style="  margin-top: 5px; display: inline-block;">or</span>
	    				<button class="button wswebinar-revoke-aweber" type="button">Revoke</button>
					<?php } ?>
				    </td>
				</tr>
				<tr class="_wswebinar_getresponse_setting_section">
				    <th>
					<label for="getresponse_api_key"><?php _e('Getresponse API Key', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <th>
					:
				    </th>
				    <td>
					<input id="getresponse_api_key" class="regular-text getresponse-apikey" type="text" name="_wswebinar_getresponseapikey" placeholder="<?php _e('The API key you have received from Getresponse', WebinarSysteem::$lang_slug) ?>" value="<?php echo get_option('_wswebinar_getresponseapikey'); ?>">
				    </td>
				    <td>
					<img class="webinar_getresponse_loader" style="display: none;" src="<?php echo plugins_url('images/loading_small.GIF', __FILE__); ?>">
					<img class="webinar_getresponse_correct" style="display: none;" src="<?php echo plugins_url('images/email_verf_correct.PNG', __FILE__); ?>">
					<img class="webinar_getresponse_error" style="display: none;" src="<?php echo plugins_url('images/email_verf_error.PNG', __FILE__); ?>">
					<button class="button getresponse-apichecker" type="button"  id="webinar_getresponse_check">Check</button>
				    </td>
				</tr>
				<tr class="_wswebinar_activecampaign_setting_section">
				    <th>
					<label for="activecampaign-api-key"><?php _e('ActiveCampaign API Key', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <th>
					:
				    </th>
				    <td>
					<?php
					$_wswebinar_activecampaignapikey = get_option('_wswebinar_activecampaignapikey');
					$_wswebinar_activecampaignurl = get_option('_wswebinar_activecampaignurl');
					?>
					<input id="activecampaign-api-key" class="regular-text" type="text" name="_wswebinar_activecampaignapikey" placeholder="<?php _e('The API key you have received from ActiveCampaign', WebinarSysteem::$lang_slug) ?>" value="<?php echo $_wswebinar_activecampaignapikey; ?>">
				    </td>
				</tr>
				<tr class="_wswebinar_activecampaign_setting_section" <?php echo empty($_wswebinar_activecampaignapikey) ? 'style="display:none;"' : '' ?>>
				    <th>
					<label for="activecampaign-api-url"><?php _e('ActiveCampaign API URL', WebinarSysteem::$lang_slug); ?></label>
				    </th>
				    <th>
					:
				    </th>
				    <td>
					<input id="activecampaign-api-url" class="regular-text" type="text" name="_wswebinar_activecampaignurl" placeholder="<?php _e('eg: ', WebinarSysteem::$lang_slug) ?> companyname.api-us1.com" value="<?php echo $_wswebinar_activecampaignurl; ?>">
				    </td>
				    <td>
					<img id="webinar-mailing-list-activecampaign-loading" style="display: none;" src="<?php echo plugins_url('images/loading_small.GIF', __FILE__); ?>">
					<img id="webinar-mailing-list-activecampaign-correct" style="display: none;" src="<?php echo plugins_url('images/email_verf_correct.PNG', __FILE__); ?>">
					<img id="webinar-mailing-list-activecampaign-incorrect" style="display: none;" src="<?php echo plugins_url('images/email_verf_error.PNG', __FILE__); ?>">
					<span id="webinar-mailing-list-activecampaign-status"></span>
					<button class="button webinar-mailing-list-api-check" type="button" data-name="activecampaign" data-action="checkActiveCampaign_apicredentials" data-key="<?php echo $_wswebinar_activecampaignapikey ?>" data-url="<?php echo $_wswebinar_activecampaignurl ?>">Check</button>
				    </td>
				</tr>
			    </tbody>
			</table>
			<?php submit_button(); ?>
		    </div>
		</div>


		<div id="tabs-4"  class="ws-hide-nav-tabs nav-tab-content">
		    <h3><?php _e('Advanced', WebinarSysteem::$lang_slug); ?></h3>
		    <?php
		    global $wp_roles;
		    $roles = $wp_roles->get_names();
		    ?>
		    <table class="wp-list-table widefat">
			<thead>
			    <tr scope="row">
				<th scope="col" class="manage-column"><?php _e('Roles & Permissions', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $role) { ?>
	    			<th scope="col" class="manage-column"><?php _e($role, WebinarSysteem::$lang_slug); ?></th>
				<?php } ?>
			    </tr>
			</thead>
			<tbody>
			    <tr>
				<th><?php _e('Create Webinars', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $roleSlug => $roleName) { ?>
	    			<td>
	    			    <input type="hidden" name="_wswebinar_createwebinars_<?php echo $roleSlug ?>" value="off" />
	    			    <input type="checkbox" name="_wswebinar_createwebinars_<?php echo $roleSlug ?>" 
					<?php
					$option1 = get_option('_wswebinar_createwebinars_' . $roleSlug);
					$status1 = (empty($option1) || (isset($option1) && $option1=="off")) ? '' : 'checked';
					echo $roleSlug == 'administrator' ? 'checked disabled' : $status1
					?>/>
	    			</td>
				<?php } ?>
			    </tr>
			    <tr>
				<th><?php _e('Manage Subscribers', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $roleSlug => $roleName) { ?>
	    			<td>
	    			    <input type="hidden" name="_wswebinar_managesubscribers_<?php echo $roleSlug ?>" value="off" />
	    			    <input type="checkbox" name="_wswebinar_managesubscribers_<?php echo $roleSlug ?>" 
					<?php
					$option2 = get_option('_wswebinar_managesubscribers_' . $roleSlug);
					$status2 = (empty($option2) || (isset($option2) && $option2=="off")) ? '' : 'checked';
					echo $roleSlug == 'administrator' ? 'checked disabled' : $status2
					?>/>
	    			</td>
				<?php } ?>
			    </tr>
			    <tr>
				<th><?php _e('Access Control Bar', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $roleSlug => $roleName) { ?>
	    			<td>
	    			    <input type="hidden" name="_wswebinar_accesscontrolbar_<?php echo $roleSlug ?>" value="off" />
	    			    <input type="checkbox" name="_wswebinar_accesscontrolbar_<?php echo $roleSlug ?>" 
					<?php
					$option3 = get_option('_wswebinar_accesscontrolbar_' . $roleSlug);
					$status3 = (empty($option3) || (isset($option3) && $option3=="off")) ? '' : 'checked';
					echo $roleSlug == 'administrator' ? 'checked disabled' : $status3
					?>/>
	    			</td>
				<?php } ?>
			    </tr>
			    <tr>
				<th><?php _e('Manage Questions', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $roleSlug => $roleName) { ?>
	    			<td>
	    			    <input type="hidden" name="_wswebinar_managequestions_<?php echo $roleSlug ?>" value="off" />
	    			    <input type="checkbox" name="_wswebinar_managequestions_<?php echo $roleSlug ?>" 
					<?php
					$option4 = get_option('_wswebinar_managequestions_' . $roleSlug);
					$status4 = (empty($option4) || (isset($option4) && $option4=="off")) ? '' : 'checked';
					echo $roleSlug == 'administrator' ? 'checked disabled' : $status4
					?>/>
	    			</td>
				<?php } ?>
			    </tr>
			    <tr>
			    <th><?php _e('Manage Chatlogs', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $roleSlug => $roleName) { ?>
	    			<td>
	    			    <input type="hidden" name="_wswebinar_managechatlogs_<?php echo $roleSlug ?>" value="off" />
	    			    <input type="checkbox" name="_wswebinar_managechatlogs_<?php echo $roleSlug ?>" 
					<?php
					$option5 = get_option('_wswebinar_managechatlogs_' . $roleSlug);
					$status5 = (empty($option5) || (isset($option5) && $option5=="off")) ? '' : 'checked';
					echo $roleSlug == 'administrator' ? 'checked disabled' : $status5
					?>/>
	    			</td>
				<?php } ?>
			    </tr>
			    <tr>
				    <th><?php _e('Change WebinarSystem Settings', WebinarSysteem::$lang_slug); ?></th>
				    <?php foreach ($roles as $roleSlug => $roleName) { ?>
		    			<td>
		    			    <input type="hidden" name="_wswebinar_webinarsettings_<?php echo $roleSlug ?>" value="off" />
		    			    <input type="checkbox" name="_wswebinar_webinarsettings_<?php echo $roleSlug ?>" 
						<?php
						$option6 = get_option('_wswebinar_webinarsettings_' . $roleSlug);
						$status6 = (empty($option6) || (isset($option6) && $option6=="off")) ? '' : 'checked';
						echo $roleSlug == 'administrator' ? 'checked disabled' : $status6
						?>/>
		    			</td>
					<?php } ?>
				</tr>
			</tbody>
			<tfoot>
			    <tr scope="row">
				<th scope="col" class="manage-column"><?php _e('Roles & Permissions', WebinarSysteem::$lang_slug); ?></th>
				<?php foreach ($roles as $role) { ?>
	    			<th scope="col" class="manage-column"><?php _e($role, WebinarSysteem::$lang_slug); ?></th>
				<?php } ?>
			    </tr>
			</tfoot>
		    </table>
		    <?php submit_button(); ?>
		</div>
		<div id="tabs-7"  class="ws-hide-nav-tabs nav-tab-content">
		    <h3><?php _e('Styling Settings', WebinarSysteem::$lang_slug); ?></h3>
		    <table class="form-table">
			<tbody>
			    <tr>
				<th>
				    <label for="_wswebinar_enable_theme_styles"><?php _e('Use Theme Styles', WebinarSysteem::$lang_slug); ?></label>
				</th>
				<td>
				    <input type="checkbox" id="_wswebinar_enable_theme_styles" name="_wswebinar_enable_theme_styles" <?php echo in_array(get_option('_wswebinar_enable_theme_styles'), array('on', NULL, '')) ? 'checked' : ''; ?> data-switch="true">
				</td>
			    </tr>
			</tbody>
		    </table>
		    <?php submit_button(); ?>
		</div>
		<!--
		System Status tab
		-->
		<div id="tabs-5"  class="ws-hide-nav-tabs nav-tab-content">
		    <div class="customTab">
			<div class="webinar_systeem_sys_report_box">
			    <p class="webinar_systeem_sys_report_box_para"><?php _e('Please copy and paste this information in your ticket when contacting support:', WebinarSysteem::$lang_slug); ?> </p>
			    <div id="debug-report" >
				<textarea readonly="readonly" style="display: none;" class="webinar_systeem_sys_report_textarea" ></textarea>
				<button type="button" class="button-primary webinar_systeem_sys_report_copy_btn" style="display: none;" href="#" ><?php _e('Copy for Support', WebinarSysteem::$lang_slug); ?></button>
				<span style="display: none;" class="webinar_systeem_sys_report_copy_status" ><strong><?php _e('Copied!', WebinarSysteem::$lang_slug); ?></strong></span>
			    </div>
			    <button class="button-primary webinar_debug_report" type="button"><?php _e('Get System Report', WebinarSysteem::$lang_slug); ?></button>

			</div>
		    </div>

		    <div class="customTab">
			<h3><?php _e('WordPress Environment', WebinarSysteem::$lang_slug); ?></h3>                        
			<table class="form-table">
			    <tr data-info='WordPress Environment'>
				<th data-value="Home URL" >
				    <?php _e('Home URL', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo get_home_url(); ?>
				</td>
			    </tr>
			    <tr data-info='WordPress Environment'>
				<th data-value="Site URL" >
				    <?php _e('Site URL', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo get_site_url(); ?>
				</td>
			    </tr>
			    <tr data-info='WordPress Environment'>
				<th data-value="WP Version">
				    <?php _e('WP Version', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php bloginfo('version'); ?>
				</td>
			    </tr>
			    <tr data-info='WordPress Environment'>
				<th data-value="WP Multisite"><?php _e('WP Multisite', WebinarSysteem::$lang_slug); ?></th>
				<td><?php echo (is_multisite() ? 'Enabled' : 'Disabled'); ?> </td>
			    </tr>
			    <tr data-info='WordPress Environment'>
				<th data-value="WP Debug mode"><?php _e('WP Debug mode', WebinarSysteem::$lang_slug); ?></th>
				<td><?php
				    if (defined('WP_DEBUG') && WP_DEBUG)
					echo 'Enabled';
				    else
					echo 'Disabled';
				    ?></td>
			    </tr>
			    <tr data-info="WordPress Environment">
				<th data-value="WP Cron status"><?php _e('WP Cron status', WebinarSysteem::$lang_slug); ?></th>
				<td>
				    <?php
				    if (!defined('DISABLE_WP_CRON')) {
					echo 'Enabled';
				    } else {
					echo 'Disabled';
				    }
				    ?>
				</td>
			    </tr>
			    <tr data-info='WordPress Environment'>
				<th data-value="Language"><?php _e('Language', WebinarSysteem::$lang_slug); ?></th>
				<td><?php echo get_locale() ?></td>
			    </tr>

			</table>
		    </div>

		    <div class="customTab">
			<h3><?php _e('Plugin Environment', WebinarSysteem::$lang_slug); ?></h3>
			<table class="form-table">
			    <tr data-info='Plugin Environment'>
				<th data-value="Webinar System Version">
				    <?php _e('Webinar System Version', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo WebinarSysteem::plugin_info('Version'); ?>
				</td>
			    </tr>
			</table>
		    </div>

		    <div class="customTab">
			<h3><?php _e('Server Environment', WebinarSysteem::$lang_slug); ?></h3>                        
			<table class="form-table">
			    <tr data-info='Server Environment'>
				<th data-value="Server Info">
				    <?php _e('Server Info', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="PHP Version">
				    <?php _e('PHP Version', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php
				    // Check if phpversion function exists
				    if (function_exists('phpversion')) {
					$php_version = phpversion();
					echo esc_html($php_version);
				    } else {
					_e("Couldn't determine PHP version because phpversion() doesn't exist.", WebinarSysteem::$lang_slug);
				    }
				    ?>
				</td>
			    </tr>

			    <tr data-info='Server Environment'>
				<th data-value="PHP Post Max Size">
				    <?php _e('PHP Post Max Size', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo ini_get('post_max_size'); ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="PHP Time Limit">
				    <?php _e('PHP Time Limit', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo ini_get('max_execution_time') . ' Seconds'; ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="PHP Max Input Vars">
				    <?php _e('PHP Max Input Vars', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo ini_get('max_input_vars'); ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="SUHOSIN Installed">
				    <?php _e('SUHOSIN Installed', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo extension_loaded('suhosin') ? 'Installed' : 'Not Installed'; ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="MySQL Version">
				    <?php _e('MySQL Version', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php
				    global $wpdb;
				    echo $wpdb->db_version();
				    ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="Max Upload Size">
				    <?php _e('Max Upload Size', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php echo size_format(wp_max_upload_size()); ?>
				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="Default Timezone">
				    <?php _e('Default Timezone', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php
				    $default_timezone = date_default_timezone_get();
				    echo $default_timezone . '' . WebinarSysteem::getTimezone();
				    ?>
				</td>
			    </tr>

			    <?php
			    $posting = array();
			    ?>
			    <tr data-info='Server Environment'>
				<th data-value="PHP Memory Limit">
				    <?php _e('PHP Memory Limit', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php
				    $memory_limit = 0;
				    $ini_memory = ini_get('memory_limit');
				    if (!empty($ini_memory)) {
					echo ($ini_memory);
				    } else {
					echo 'Not available';
				    }
				    ?>
				</td>
			    </tr>
			    <?php
			    // FSOCK
			    $posting['fsockopen_curl']['name'] = 'FsockOpen Curl';
			    if (function_exists('fsockopen') || function_exists('curl_init')) {
				$posting['fsockopen_curl']['success'] = true;
			    } else {
				$posting['fsockopen_curl']['success'] = false;
			    }

			    // SOAP
			    $posting['soap_client']['name'] = 'Soap Client';

			    if (class_exists('SoapClient')) {
				$posting['soap_client']['success'] = true;
			    } else {
				$posting['soap_client']['success'] = false;
			    }

			    // DOMDocument
			    $posting['dom_document']['name'] = 'DOM Document';

			    if (class_exists('DOMDocument')) {
				$posting['dom_document']['success'] = true;
			    } else {
				$posting['dom_document']['success'] = false;
			    }

			    $posting['gzip']['name'] = 'GZip';
			    if (is_callable('gzopen')) {
				$posting['gzip']['success'] = true;
			    } else {
				$posting['gzip']['success'] = false;
			    }

			    foreach ($posting as $post) {
				$mark = !empty($post['success']) ? 'Enabled' : 'Disabled';
				?>
	    		    <tr data-info='Server Environment'>
	    			<th data-value="<?php echo esc_html($post['name']); ?>" ><?php echo esc_html($post['name']); ?></th>
	    			<td>
					<?php _e($mark, WebinarSysteem::$lang_slug); ?>
	    			</td>
	    		    </tr>
			    <?php } ?>
			    <tr data-info='Server Environment'>
				<th data-value="Server Time">
				    <?php _e('Server Time', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php
				    $timezone_time = 'UTC';
				    $ini_timezone_time = ini_get('date.timezone');
				    $php_timezone_time = date_default_timezone_get();
				    if (!empty($ini_timezone_time)) {
					$timezone_time = $timezone_time;
				    } else if (!empty($php_timezone_time)) {
					$timezone_time = $php_timezone_time;
				    }
				    $date = new DateTime();
				    $date->setTimezone(new DateTimeZone($timezone_time));
				    echo date('Y-m-d D H:i:s a', $date->getTimestamp());
				    ?>

				</td>
			    </tr>
			    <tr data-info='Server Environment'>
				<th data-value="Server Timezone">
				    <?php
				    $timezone = 'Not available';
				    $ini_timezone = ini_get('date.timezone');
				    $php_timezone = date_default_timezone_get();
				    if (!empty($ini_timezone)) {
					$timezone = $ini_timezone;
				    } else if (!empty($php_timezone)) {
					$timezone = $php_timezone;
				    }
				    ?>
				    <?php _e('Server Timezone', WebinarSysteem::$lang_slug); ?>
				</th>
				<td>
				    <?php
				    $time_zone = date_default_timezone_get();
				    echo $time_zone;
				    //echo $timezone;
				    ?>
				</td>
			    </tr>
			</table>
		    </div>

		    <div class="customTab">
			<h3><?php _e('Server Locale', WebinarSysteem::$lang_slug); ?></h3>                        
			<table class="form-table">

			    <?php
			    $locale = localeconv();
			    foreach ($locale as $key => $val) {
				if (in_array($key, array('decimal_point', 'mon_decimal_point', 'thousands_sep', 'mon_thousands_sep'))) {
				    echo '<tr data-info="Server Locale" ><th data-value=' . $key . ' >' . $key . '</th><td>' . ( $val ? $val : __('N/A', WebinarSysteem::$lang_slug) ) . '</td></tr>';
				}
			    }
			    ?>

			</table>
		    </div>
		    <?php
		    $active_plugins = (array) get_option('active_plugins', array());
		    if (is_multisite()) {
			$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
		    }
		    ?>
		    <div class="customTab">
			<h3><?php _e('Active Plugins (' . count($active_plugins) . ')', WebinarSysteem::$lang_slug); ?></h3>
			<table class="form-table">
			    <?php
			    foreach ($active_plugins as $key => $plugin) {
				$plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);

				if (empty($plugin_data['Name'])) {
				    $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $key);
				}

				$version_string = '';
				$network_string = '';

				if (!empty($plugin_data['Name'])) {
				    $plugin_name = esc_html($plugin_data['Name']);
				    ?>
				    <tr data-info="Active Plugins" data-has-a="true">
					<th data-value="<?php echo $plugin_name; ?>"><?php echo $plugin_name; ?></th>
					<td><?php echo _e('by ', WebinarSysteem::$lang_slug) . $plugin_data['Author'] . ' &ndash; [Version : ' . esc_html($plugin_data['Version']) . ']' . $version_string . $network_string; ?></td>
				    </tr>
				    <?php
				}
			    }
			    ?>
			</table>
		    </div>

		    <div class="customTab">
			<h3><?php _e('Theme', WebinarSysteem::$lang_slug); ?></h3>   
			<?php $active_theme = wp_get_theme(); ?>
			<table class="form-table">
			    <tr data-info="Theme">
				<th data-value="Name"><?php _e('Name', WebinarSysteem::$lang_slug); ?>:</th>
				<td><?php echo $active_theme->Name; ?></td>
			    </tr>
			    <tr data-info="Theme">
				<th data-value="Version"><?php _e('Version', WebinarSysteem::$lang_slug); ?>:</th>
				<td><?php echo $active_theme->Version; ?></td>
			    </tr>
			    <tr data-info="Theme">
				<th data-value="Author URL"><?php _e('Author URL', WebinarSysteem::$lang_slug); ?>:</th>
				<td><?php echo $active_theme->{'Author URI'}; ?></td>
			    </tr>

			</table>
		    </div>

		</div>
		<!-- End of system status tab -->

		<div id="tabs-6" class="ws-hide-nav-tabs nav-tab-content">
		    <h3><?php _e('Integrations', WebinarSysteem::$lang_slug); ?></h3>
		    <table class="form-table">
			<tr>
			    <th><label for="_wswebinar_enable_woocommerce_integration">Enable WooCommerce Integration</label></th>
			    <td>
				<input type="checkbox" id="_wswebinar_enable_woocommerce_integration" name="_wswebinar_enable_woocommerce_integration" <?php echo get_option('_wswebinar_enable_woocommerce_integration') == 'on' ? 'checked' : ''; ?> data-switch="true">
			    </td>
			</tr>
		    </table>
		    <?php submit_button(); ?>
		</div>

	    </form>
	</div>
	<!--jQuery stuff to handle above tabs-->
	<script>
	    var wpws_ajaxurl = "<?php echo home_url(); ?>/wp-admin/admin-ajax.php";
	    jQuery(function () {

		jQuery(document).on('click', '.nav-tab-wrapper .nav-tab', function (e) {
		    e.preventDefault();
		    var href = jQuery(this).attr('href');
		    jQuery('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
		    jQuery('.nav-tab-content').hide();
		    jQuery(this).addClass('nav-tab-active');
		    jQuery(href).show();
		});

		jQuery(document).on('click', '.wswebinarCustomTab ul li a', function (e) {
		    e.preventDefault();
		    var href = jQuery(this).attr('href');
		    jQuery('.wswebinarCustomTab ul li a').removeClass('customTabActive');
		    jQuery('.email-customTab').hide();
		    jQuery(this).addClass('customTabActive');
		    jQuery(href).show();
		});
	    });

	    /*
	     * Check enormail API key
	     */


	</script>
	<?php
    }

    public static function DoResetDefaults($setting = NULL) {
	$template = WebinarSysteem::getDefaultMailTemplates();
	if ($setting == NULL) {
	    update_option('_wswebinar_email_headerImg', plugins_url('images/WebinarSysteem-logo.png', __FILE__));
	    update_option('_wswebinar_email_baseCLR', '#fff');
	    update_option('_wswebinar_email_bckCLR', '#f2f2f2');
	    update_option('_wswebinar_email_bodyBck', '#fff');
	    update_option('_wswebinar_email_bodyTXT', 'black');
	    update_option('_wswebinar_email_footerTxt', '');
	} elseif ($setting == "newreg") {
	    update_option(WebinarSysteem::$lang_slug . '_newregcontent', $template['newreg']);
	} elseif ($setting == "regconfirm") {
	    update_option(WebinarSysteem::$lang_slug . '_regconfirmcontent', $template['regconfirm']);
	} elseif ($setting == "1hr") {
	    update_option(WebinarSysteem::$lang_slug . '_1hrb4content', $template['1hr']);
	} elseif ($setting == "24hr") {
	    update_option(WebinarSysteem::$lang_slug . '_24hrb4content', $template['24hr']);
	} elseif ($setting == "started") {
	    update_option(WebinarSysteem::$lang_slug . '_wbnstarted', $template['started']);
	} elseif ($setting == "replay") {
	    update_option(WebinarSysteem::$lang_slug . '_wbnreplay', $template['replay']);
	}
	
	/* Set manage questions to author */
	$is_managequestions_author = get_option('_wswebinar_managequestions_author');
		
	if(!isset($is_managequestions_author) || (isset($is_managequestions_author) && $is_managequestions_author !== "off"))
		update_option('_wswebinar_managequestions_author', 'on');
		
    }

}

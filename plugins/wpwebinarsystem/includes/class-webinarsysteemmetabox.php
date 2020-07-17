<?php

class WebinarSysteemMetabox {

    private $_FILE_, $post_slug;

    public function __construct($file, $post_slug) {
        $this->_FILE_ = $file;
        $this->post_slug = $post_slug;
        add_action('add_meta_boxes', array($this, 'addWebinarMetaBox'));
        add_action('add_meta_boxes', array($this, 'addWebinarMetaBoxHost'));
        add_action('save_post', array($this, 'saveMetaBoxData'));
    }

    /*
     * 
     * Load the Webinar option metaboxes.
     * 
     */

    function addWebinarMetaBox() {
	    add_meta_box('webinarMetaBox', __('Webinar Settings', WebinarSysteem::$lang_slug), array($this, 'webinarMetaBoxContent'), $this->post_slug, 'normal');
    }

    function addWebinarMetaBoxHost() {
        add_meta_box('webinarMetaBoxHost', __('Host Names', WebinarSysteem::$lang_slug), array($this, 'webinarMetaBoxHostContent'), $this->post_slug, 'side', 'default');
        add_meta_box('webinarMetaBoxUrl', __('Your Webinar URL', WebinarSysteem::$lang_slug), array($this, 'webinarMetaBoxUrlContent'), $this->post_slug, 'normal', 'high');
    }

    public function webinarMetaBoxUrlContent() {
	?>
	<div class="form-field">
	    <input type="text" id="wswebinar_url" value="" name="wswebinar_url" readonly="readonly">
	    <div style="top:0px;" data-clipboard-text="" class="input-group-addon glyphicon glyphicon-link" id="copyTo" ></div>
	</div>

	<script>
	    function loadUrlFromPreviewAnchor() {
		var u = jQuery('#sample-permalink a').attr('href');
		jQuery('#wswebinar_url').val(u);
	    }
	    jQuery(document).ready(function () {
		setTimeout(function () {
		    var weburl = jQuery('#wswebinar_url');
		    if (weburl.val().length === 0) {
			loadUrlFromPreviewAnchor();
		    }
		    var copyurl = weburl.val();
		    jQuery("#copyTo").attr("data-clipboard-text", copyurl);
		}, 2000);



	    });

	    jQuery(document).ready(function () {
		ZeroClipboard.config({
		    forceHandCursor: true
		});

		var client = new ZeroClipboard(jQuery("#copyTo"));

		client.on("error", function (e) {
		});
		client.on("ready", function (e) {
		    client.on("aftercopy", function (e) {
		    });
		});
	    });

	</script> 
	<?php
	if (is_rtl()) {
	    ?>
	    <style>
	        .ui-tabs-panel{ 
	    	padding-right: 200px !important;
	        }
	        #webinarMetaBox .ui-tabs-vertical .ui-tabs-nav li a{    padding-right: 15px;}
	        #webinarMetaBox .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active a{padding-right: 15px;}
	        #webinarMetaBox #gener_date{    margin-left: 60px;}
	        #webinarMetaBox .ui-tabs-vertical .ui-tabs-panel{ padding-left: 0px !important;}
	        #webinarMetaBox .form-field input{ float: right; }
	        #webinarMetaBox .wswebinar_uploader{ float: right; }
	        #webinarMetaBox .form-field label, #webinarMetaBox .form-group label{ float: right;}
	        #webinarMetaBox .date_line_sep{ float: right; }
	        #webinarMetaBox .description{margin-right: 5px; }
	    </style>
	    <?php
	}
    }

    /*
     * 
     * Webinar Meta box content loader
     * 
     */

    function webinarMetaBoxContent($post) {
	    wp_nonce_field('webinarmetabox', 'webinarmetabox_nonce');
	?>

	<div id="tabs">
	    <ul>
		<li><a href="#tabs-6"><i class="wbn-icon wbnicon-cog"></i>&nbsp; &nbsp;<?php _e('General Options', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-1"><i class="wbn-icon wbnicon-registration"></i>&nbsp; &nbsp;<?php _e('Registration Page', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-2"><i class="wbn-icon wbnicon-thumbs-up"></i>&nbsp; &nbsp;<?php _e('Thank You Page', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-3"><i class="wbn-icon wbnicon-sort-by-order"></i>&nbsp; &nbsp;<?php _e('Countdown Page', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-4"><i class="wbn-icon wbnicon-live"></i>&nbsp; &nbsp;<?php _e('Live Page', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-5"><i class="wbn-icon wbnicon-facetime-video"></i>&nbsp; &nbsp;<?php _e('Replay Page', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-9"><i class="wbn-icon wbnicon-ticket"></i>&nbsp; &nbsp;<?php _e('Ticket', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-7"><i class="wbn-icon wbnicon-envelope"></i>&nbsp; &nbsp;<?php _e('Mailinglist', WebinarSysteem::$lang_slug); ?></a></li>
		<li><a href="#tabs-10"><i class="wbn-icon glyphicon-lock"></i>&nbsp; &nbsp;<?php _e('Access', WebinarSysteem::$lang_slug); ?></a></li>
		<li class="final-tab"><a href="#tabs-8"><i class="wbn-icon wbnicon-list"></i>&nbsp; &nbsp;<?php _e('Import / Export', WebinarSysteem::$lang_slug); ?></a></li>
	    </ul>            

	    <div id="tabs-1">
		<div class="panelContent">
		    <?php $this->metaBoxTab_registerPage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>
	    <div id="tabs-2">
		<div class="panelContent">
		    <?php $this->metaBoxTab_thankyouPage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>
	    <div id="tabs-3" class="panelContent">
		<div class="panelContent">
		    <?php $this->metaBoxTab_countdownPage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>
	    <div id="tabs-4" class="panelContent">
		<div class="panelContent">
		    <?php $this->metaBoxTab_livePage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>
	    <div id="tabs-5" class="final-tab panelContent">
		<div class="panelContent">
		    <?php $this->metaBoxTab_replayPage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>
	    <div id="tabs-6">
		<div class="panelContent">
		    <?php $this->metaBoxTab_generalPage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>

	    </div>
	    <div id="tabs-7">
		<div class="panelContent">
		    <?php $this->metaBoxTab_mailingListPage($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>

	    <div id="tabs-8">
		<div class="panelContent">
		    <?php $this->metaBoxTab_importExport($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>

	    <div id="tabs-9">
		<div class="panelContent">
		    <?php $this->metaBoxTab_ticket($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>

	    <div id="tabs-10">
		<div class="panelContent">
		    <?php $this->metaBox_AccessTab($post); ?>
		    <div class="webinar_clear_fix"></div>
		</div>
	    </div>

	    <div class="webinar_clear_fix"></div>
	</div>
	<div class="webinar_clear_fix"></div>
	<script type="text/javascript">
	    jQuery('[id$="p-accordian"]').accordion({heightStyle: "content"});
	</script>
	<?php
    }

    private function decideClassOfStatusButtons($this_status, $saved_status) {
	if (empty($saved_status)) {
	    if ($this_status == 'cou')
		return 'active disabled';
	    return '';
	}

	if ($saved_status == $this_status)
	    return 'active disabled';
	return '';
    }

    /*
     * 
     * Metabox General Options Page tab content
     * 
     */

    private function metaBoxTab_generalPage($post) {
	    include 'templates/template-metabox-generalpage.php';
    }

    private function metaBoxTab_mailingListPage($post) {
	$mailchimpKey = get_option('_wswebinar_mailchimpapikey');
	$mailchimp_error = get_option('_wswebinar_mailchimp_api_key_error');
	$list = array();
	if (!empty($mailchimpKey) && !$mailchimp_error) {
	    $Mailchimp = new Mailchimp($mailchimpKey);
	    $Mailchimp_Lists = new Mailchimp_Lists($Mailchimp);
	    $list = $Mailchimp_Lists->getList(array(), 0, 25, 'created', 'DESC');
	}

	$mailchimp_list_saved = get_post_meta($post->ID, '_wswebinar_mailchimp_list', true);
	$mail_provider = get_post_meta($post->ID, '_wswebinar_default_mail_provider', true);

	$apiKey = get_option('_wswebinar_getresponseapikey');
	if (!empty($apiKey)) {
	    $getResponseActive = json_decode(WebinarsysteemMailingListIntegrations::checkGetresponse_apikey($apiKey));
	}

	$enormail_api_key = get_option('_wswebinar_enormailapikey');
	$valid_enormail_key = WebinarsysteemMailingListIntegrations::isValidEnormailKey($enormail_api_key);
	
	$drip_api_key = get_option('_wswebinar_dripapikey');
	$valid_drip_key = WebinarsysteemMailingListIntegrations::isValidDripKey($drip_api_key);
	?>

	<div class="form-field">
	    <label for="_wswebinar_mailinglist_provider_selector" ><?php _e('Default mail provider', WebinarSysteem::$lang_slug); ?> </label>
	    <select class="regular-text" id="_wswebinar_mailinglist_provider_selector" name="default_mail_provider">
		<option value="none" <?php echo ($mail_provider == 'none') ? 'selected' : ''; ?>>None</option>
		<option value="mailchimp" <?php echo ($mail_provider == 'mailchimp') ? 'selected' : ''; ?> <?php echo!empty($list) ? '' : 'disabled'; ?>>MailChimp</option>                    
		<option value="mailpoet" <?php echo ($mail_provider == 'mailpoet') ? 'selected' : ''; ?> <?php echo class_exists('WYSIJA') ? '' : 'disabled'; ?>>Mailpoet</option>     
		<option value="mailpoet3" <?php echo ($mail_provider == 'mailpoet3') ? 'selected' : ''; ?> <?php echo class_exists('\MailPoet\API\API') ? '' : 'disabled'; ?>>Mailpoet3</option>     
		<option value="aweber" <?php echo ($mail_provider == 'aweber') ? 'selected' : ''; ?> <?php echo (WebinarsysteemMailingListIntegrations::aWeber_Connected()) ? '' : 'disabled'; ?>>AWeber</option>
		<option value="enormail" <?php echo ($mail_provider == 'enormail') ? 'selected' : ''; ?> <?php echo class_exists('EM_Lists') && !empty($enormail_api_key) && $valid_enormail_key ? '' : 'disabled'; ?>>Enormail</option>
		<option value="drip" <?php echo ($mail_provider == 'drip') ? 'selected' : ''; ?> <?php echo !empty($drip_api_key) && $valid_drip_key ? '' : 'disabled'; ?>>Drip</option>
		<option value="getresponse" <?php echo ($mail_provider == 'getresponse') ? 'selected' : ''; ?> <?php echo!empty($apiKey) && !$getResponseActive->error ? '' : 'disabled="disabled"'; ?>>GetResponse</option>
		<option value="activecampaign" <?php echo ($mail_provider == 'activecampaign' && WebinarsysteemMailingListIntegrations::isReadyActiveCampaign()) ? 'selected' : ''; ?> <?php echo WebinarsysteemMailingListIntegrations::isReadyActiveCampaign() ? '' : 'disabled'; ?>>ActiveCampaign</option>
	    </select>
	</div>

	<div class="form-group mailing-provider-mailchimp mailing-provider-options" id="_wswebinar_mailchimp_form_group" <?php echo ($mail_provider == 'mailchimp') ? '' : 'style="display: none;"'; ?>>
	    <!--For select default mailingList provider--> 
	    <label for="_wswebinar_mailchimp_list"><?php _e('Mailchimp list', WebinarSysteem::$lang_slug) ?></label>
	    <?php
	    if (!empty($list['data'])) {
		?>
	        <select id="_wswebinar_mailchimp_list" name="mailchimp_list">
		    <?php
		    foreach ($list['data'] as $item) {
			?>
			<option <?php
			if ($mailchimp_list_saved == $item['id']) {
			    echo 'selected';
			}
			?> value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
			    <?php
			}
			?>
	        </select>
	    <?php } else { ?>
	        <p><?php _e('You\'ve no mailing list(s) on MailChimp.', WebinarSysteem::$lang_slug) ?></p>
	    <?php } ?>
	</div>


	<div class="form-group mailing-provider-mailpoet mailing-provider-options" id="_wswebinar_mailpoet_form_group" <?php echo ($mail_provider == 'mailpoet') ? '' : 'style="display: none;"'; ?>>
	    <?php
	    $saved_list = get_post_meta($post->ID, '_wswebinar_mailpoet_list', true);
	    $mailpoet_list_array = array();
	    if (class_exists('WYSIJA')) {
		$model_list = WYSIJA::get('list', 'model');
		$mailpoet_lists = $model_list->get(array('name', 'list_id'), array('is_enabled' => 1));
		$t = 1;
		foreach ($mailpoet_lists as $mailpoet_list) {
		    $mailpoet_list_array[$mailpoet_list['list_id']] = $mailpoet_list['name'];
		    $t++;
		}
	    }
	    ?>

	    <label for="_wswebinar_mailpoet_list"><?php _e('Mailpoet list', WebinarSysteem::$lang_slug) ?></label>
	    <select id="_wswebinar_mailpoet_list" name="mailpoet_list">
		<option value="" disabled="disabled" selected="selected"><?php _e('Select a list..', WebinarSysteem::$lang_slug) ?></option>
		<?php
		if (is_array($mailpoet_list_array) && count($mailpoet_list_array) > 0) {
		    foreach ($mailpoet_list_array as $ke => $val) {
			echo '<option value="' . $ke . '" ' . (($saved_list == $ke) ? "selected" : "") . '>' . $val . '</option>';
		    }
		}
		?>
	    </select>
	</div>
	
	<div class="form-group mailing-provider-mailpoet3 mailing-provider-options" id="_wswebinar_mailpoet3_form_group" <?php echo ($mail_provider == 'mailpoet3') ? '' : 'style="display: none;"'; ?>>
	    <?php
	    $saved_list = get_post_meta($post->ID, '_wswebinar_mailpoet3_list', true);

	    $mailpoet3_list_array = array();
	    if (class_exists('\MailPoet\API\API')) {
		$mailpoet3_lists = \MailPoet\API\API::MP('v1')->getLists();
		$t = 1;
		foreach ($mailpoet3_lists as $mailpoet3_list) {
		    $mailpoet3_list_array[$mailpoet3_list['id']] = $mailpoet3_list['name'];
		    $t++;
		}
	    }
	    ?>

	    <label for="_wswebinar_mailpoet3_list"><?php _e('Mailpoet3 list', WebinarSysteem::$lang_slug) ?></label>
	    <select id="_wswebinar_mailpoet3_list" name="mailpoet3_list">
		<option value="" disabled="disabled" selected="selected"><?php _e('Select a list..', WebinarSysteem::$lang_slug) ?></option>
		<?php
		if (is_array($mailpoet3_list_array) && count($mailpoet3_list_array) > 0) {
		    foreach ($mailpoet3_list_array as $ke => $val) {
			echo '<option value="' . $ke . '" ' . (($saved_list == $ke) ? "selected" : "") . '>' . $val . '</option>';
		    }
		}
		?>
	    </select>
	</div>

	<div class="form-group mailing-provider-enormail mailing-provider-options" id="_wswebinar_enormail_form_group" <?php
	if ($mail_provider != 'enormail') {
	    echo 'style="display: none;"';
	}
	?>>
	    <label for="_wswebinar_enormail_list"><?php _e('Enormail list', WebinarSysteem::$lang_slug) ?></label>
	    <?php
	    $key = get_option('_wswebinar_enormailapikey');
	    $enormail_error = get_option('_wswebinar_enormail_api_key_error');
	    if (!empty($key) && !$enormail_error) {
		$lists = new EM_Lists(new Em_Rest($key));
		$set = $lists->get();
		$decoded_set = json_decode($set);
		?>

	        <select id="_wswebinar_enormail_list" name="enormail_list">
	    	<option value="" disabled="disabled" selected="selected"><?php _e('Select a list..', WebinarSysteem::$lang_slug) ?></option>
		    <?php
		    $saved_enormail_list = get_post_meta($post->ID, '_wswebinar_enormail_list', true);
		    if (count($decoded_set) > 0) {
			foreach ($decoded_set as $list) {
			    echo '<option value="' . $list->listid . '" ' . (($saved_enormail_list == $list->listid) ? "selected" : "") . '>' . $list->title . '</option>';
			}
		    } else {
			echo '<p>' . _e('You\'ve no mailing list(s) on Enormail.', WebinarSysteem::$lang_slug) . '</p>';
		    }
		}
		?>
	    </select>
	</div>
	<div class="form-group mailing-provider-drip mailing-provider-options" id="_wswebinar_drip_form_group" <?php
	if ($mail_provider != 'drip') {
		echo 'style="display: none;"';	
	}
	?>>
	    <table>
	    <tr>
	    <td>
		<label for="_wswebinar_drip_accounts"><?php _e('Drip Accounts', WebinarSysteem::$lang_slug) ?></label>
		</td>
		<?php
		$key = get_option('_wswebinar_dripapikey');
		$drip_error = get_option('_wswebinar_drip_api_key_error');
		if (!empty($key) && !$drip_error) {
		$accounts = WebinarsysteemMailingListIntegrations::get_drip_account_lists($key);
		?>
		<td>
			<select id="_wswebinar_drip_accounts" name="drip_accounts">
				<option value="" disabled="disabled" selected="selected"><?php _e('Select an account..', WebinarSysteem::$lang_slug) ?></option>
				<?php 
				$saved_drip_accounts = get_post_meta($post->ID, '_wswebinar_drip_accounts', true);

				if(count($accounts) > 0) {
					foreach ($accounts as $list) {
						if($list['value'] == '' || $list['label'] == ''){
							
						}
						else {
						echo '<option value="' .$list['value']. '" '. (($saved_drip_accounts == $list['value']) ? "selected" : "") . '>' .$list['label'] . '</option>';	
						}			
					}
				}
				else {
					echo '<p>' . _e('You\'ve no account(s) on Drip.', WebinarSysteem::$lang_slug) . '</p>';
				}
			}
				?>
			</select>
			</td>
			<td>
			  <img id="webinar_drip_campaign_loader" style="display:none;" src="<?php echo plugins_url('images/loading_small.GIF', __FILE__); ?>">  
			</td>
			</tr>
			</table>
			<div style="margin-top:10px">
        <label for="_wswebinar_drip_campaigns"><?php _e('Drip Campaigns', WebinarSysteem::$lang_slug) ?></label>
		<?php $account = get_post_meta($post->ID, '_wswebinar_drip_accounts', true);
		if(!empty($account)){
			$campaigns = WebinarsysteemMailingListIntegrations::getDripCampaignList($account);
		
		?>
			<select id="_wswebinar_drip_campaigns" name="drip_campaigns">
			<?php if($campaigns) { ?>
			<option value="" disabled="disabled" selected="selected"><?php _e('Select a campaign..', WebinarSysteem::$lang_slug) ?></option>
				<?php 
				$saved_drip_campaigns = get_post_meta($post->ID, '_wswebinar_drip_campaigns', true);		
				if(count($campaigns) > 0) {
					foreach ($campaigns as $list) {
						if($list['value'] == '' || $list['label'] == ''){
							
						}
						else {
						echo '<option value="' .$list['value']. '" '. (($saved_drip_campaigns == $list['value']) ? "selected" : "") . '>' .$list['label'] . '</option>';	
						}			
					}
				echo '<option value="no"' .(($saved_drip_campaigns == "no") ? "selected" : "") . '>Do not add subscriber to campaign</option>';
				}
				
				else {
					echo '<p>' . _e('You\'ve no campaigns(s) under Drip Account.', WebinarSysteem::$lang_slug) . '</p>';
				}
				}
				
				 ?>
			</select>
			<?php }
			else
			{ ?>
			<select id="_wswebinar_drip_campaigns" name="drip_campaigns"></select>
			<?php }?>
		</div>
	</div>
	<div class="form-group mailing-provider-aweber mailing-provider-options" id="_wswebinar_aweber_form_group" <?php echo ($mail_provider == 'aweber') ? '' : 'style="display: none;"'; ?>>
	    <?php
	    $saved_list = get_post_meta($post->ID, '_wswebinar_aweber_list', true);
	    if (WebinarsysteemMailingListIntegrations::aWeber_Connected()) {
		$aweber = new WSAWeberAPI(WebinarsysteemMailingListIntegrations::$consumerKey, WebinarsysteemMailingListIntegrations::$consumerSecret);
		$account = $aweber->getAccount(get_option('_wswebinar_aweber_accessToken'), get_option('_wswebinar_aweber_accessTokenSecret'));
		//$account_id = $account->id;
	    } else {
		$account = array();
	    }
	    ?>
	    <label for="_wswebinar_aweber_list"><?php _e('AWeber List', WebinarSysteem::$lang_slug) ?></label>
	    <select id="_wswebinar_aweber_list" name="aweber_list">
		<option value="" disabled="disabled" selected="selected"><?php _e('Select a list..', WebinarSysteem::$lang_slug) ?></option>
		<?php
		if (count($account) > 0) {
		    foreach ($account->lists as $list) {
			echo '<option value="' . $list->id . '" ' . (($saved_list == $list->id) ? "selected" : "") . '>' . $list->name . '</option>';
		    }
		}
		?>
	    </select>
	    <br>
	    <?php $privent_info = get_post_meta($post->ID, '_wswebinar_aweber_privent_info_emails', true); ?>
	    <div style="margin-top: 10px;">
		<label style="margin-top: -5px;" for="aweber_privent_info_emails"><?php _e('Prevent info emails', WebinarSysteem::$lang_slug) ?></label>
		<input class="inline" type="checkbox" name="aweber_privent_info_emails" id="aweber_privent_info_emails" value="yes" <?php echo ($privent_info == "yes" ) ? 'checked="checked"' : ''; ?> >
		<p class="inline"> <?php _e('Prevent users from registering when they use an info email addresses', WebinarSysteem::$lang_slug); ?></p>
	    </div>
	</div>

	<?php
	$getesponse_api_key = get_option('_wswebinar_getresponseapikey');
	if (!empty($getesponse_api_key)):
	    ?>
	    <div class="form-group mailing-provider-getresponse mailing-provider-options getresponse" id="_wswebinar_getresponse_form_group" <?php
	    if ($mail_provider != 'getresponse') {
		echo 'style="display: none;"';
	    } else {
		echo 'style="display: block;"';
	    }
	    ?>>
	        <label for="_wswebinar_getresponse_list"><?php _e('GetResponse list', WebinarSysteem::$lang_slug) ?></label>
	        <select id="_wswebinar_getresponse_list" name="getresponse_list">
		    <?php
		    $campaign_id = get_post_meta($post->ID, '_wswebinar_getresponse_list', TRUE);
		    if ($getResponseActive->error == false) {
			$getResPonceLists = WebinarsysteemMailingListIntegrations::getResponseLists($apiKey);
			foreach ($getResPonceLists as $key => $value) {
			    ?>
		    	<option value="<?php echo $key ?> " <?php echo ($campaign_id == $key ? 'selected="selected"' : ''); ?> ><?php echo $value->name; ?></option>
			    <?php
			}
		    } else {
			?>
			<option value="0" disabled="disabled">No campaigns.</option>
		    <?php } ?>
	        </select>
	    </div>


	<?php endif; ?>

	<div class="form-group mailing-provider-activecampaign mailing-provider-options" id="_wswebinar_activecampaign_form_group" <?php echo ($mail_provider == 'activecampaign' && WebinarsysteemMailingListIntegrations::isReadyActiveCampaign()) ? '' : 'style="display: none;"'; ?>>
	    <!--For select default mailingList provider--> 
	    <label for="_wswebinar_activecampaign_list"><?php echo sprintf(__('%s list', WebinarSysteem::$lang_slug), "ActiveCampaign") ?></label>
	    <?php
	    $activecampaign_list_saved = get_post_meta($post->ID, '_wswebinar_activecampaign_list', true);
	    $activeCampaignList = WebinarsysteemMailingListIntegrations::getActiveCampaignListList();
	    if (!empty($activeCampaignList) && is_array($activeCampaignList)) {
		?>
	        <select id="_wswebinar_activecampaign_list" name="activecampaign_list">
	    	<option value="" disabled selected><?php _e('Select a list..', WebinarSysteem::$lang_slug) ?></option>
		    <?php
		    foreach ($activeCampaignList as $item) {
			?>
			<option <?php
			if ($activecampaign_list_saved == $item->id) {
			    echo 'selected';
			}
			?> value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
			    <?php
			}
			?>
	        </select>
	    <?php } else { ?>
	        <p><?php echo sprintf(__("You've no mailing list(s) on %s.", WebinarSysteem::$lang_slug), 'ActiveCampaign') ?></p>
	    <?php } ?>
	</div>

	<?php
    }

    /*
     * 
     * Metabox register Page tab content
     * 
     */

    private function metaBoxTab_registerPage($post) {
        $nonce = wp_create_nonce('post_preview_' . $post->ID);
        include 'templates/template-metabox-registerpage.php';
    }

    /*
     * 
     * Metabox thank you page tab content
     * 
     */

    private function metaBoxTab_thankyouPage($post) {
	    include('templates/template-metabox-thankyoupage.php');
    }

    /*
     * 
     * Metabox countdown page tab content
     * 
     */

    private function metaBoxTab_countdownPage($post) {
	    include('templates/template-metabox-countdownpage.php');
    }

    /*
     * 
     * Metabox live page tab content
     * 
     */

    private function metaBoxTab_livePage($post) {
	    include('templates/template-metabox-livepage.php');
    }

    /*
     * 
     * Metabox replay page tab content
     * 
     */

    private function metaBoxTab_replayPage($post) {
	    include('templates/template-metabox-replaypage.php');
    }

    /*
     * 
     * Metabox ticket tab content
     * 
     */

    private function metaBoxTab_ticket($post) {
	    include('templates/template-metabox-ticket.php');
    }

    /*
     * 
     * Import and Export
     * 
     */

    public function metaBoxTab_importExport($post) {
	?>
	<table style="margin: 0 auto;">
	    <tr>
	    <p><?php _e('Click Export to download backup of current webinar settings. Upload your already exported file to Import section to apply settings from other webinar.', WebinarSysteem::$lang_slug); ?></p>
	</tr>
	<tr>
	    <td>
		<div id="webinar_imp_exp_tile_left">
		    <button type="button" class="webinar_imp_exp_action" id="webinar_imp_exp_action_export"><?php _e('Export', WebinarSysteem::$lang_slug); ?></button>
		</div>
	    </td>
	    <td>
		<div id="webinar_imp_exp_tile_right">
		    <div id="webinar_import_loader" style="display: none;">
			<img class="webinar_center_loader" src="<?php echo plugins_url('images/loding_large64x64.GIF', __FILE__); ?>">
			<span class="webinar_center_loader"><?php _e('Working on it...', WebinarSysteem::$lang_slug); ?></span>
		    </div>
		    <button type="button" class="webinar_imp_exp_action" id="webinar_imp_exp_action_import" ><?php _e('Import', WebinarSysteem::$lang_slug) ?></button>
		    <input type="file" class="hidden" id="webinar_imp_exp_action_import_hidden" data-saveas="<?php echo (get_post_status($post->ID) == 'auto-draft' | get_post_status($post->ID) == 'draft' ? 'draft' : 'publish'); ?>">
		</div>
	    </td>
	</tr>
	</table>

	<?php
    }

    /*
     * Metabox - Access tab content
     */

    private function metaBox_AccessTab($post) {
	    include('templates/template-access-tab.php');
    }

    /*
     * 
     * Save metabox options data
     * 
     */

    function saveMetaBoxData($post_id) {
	if (!isset($_POST['webinarmetabox_nonce'])) {
	    return;
	}

	if (!wp_verify_nonce($_POST['webinarmetabox_nonce'], 'webinarmetabox')) {
	    return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	    return;
	}

// Check the user's permissions.
	if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

	    if (!current_user_can('edit_page', $post_id)) {
		return;
	    }
	} else {

	    if (!current_user_can('edit_post', $post_id)) {
		return;
	    }
	}

	/* OK, it's safe for us to save the data now. */

// Make sure that it is set.

	/* if (!isset($_POST['myplugin_new_field'])) {
	  return;
	  } */

	$datestring = '';
	if (!empty($_POST['gener_date'])) {
	    $datestring .= $_POST['gener_date'];
	    $datestring .= ' ' . $_POST['gener_hour'] . $_POST['gener_min'];
	}
	$_POST['gener_time'] = strtotime($datestring);

	$_POST['regp_show_content_setion'] = ((isset($_POST['regp_show_content_setion']) && $_POST['regp_show_content_setion'] == 'yes') ? 'yes' : 'no');
	$_POST['regp_show_description'] = (isset($_POST['regp_show_description']) ? 'yes' : 'no');
	$_POST['regp_bigplaybtn_yn'] = (isset($_POST['regp_bigplaybtn_yn']) ? 'yes' : 'no');
	$_POST['tnxp_bigplaybtn_yn'] = (isset($_POST['tnxp_bigplaybtn_yn']) ? 'yes' : 'no');
	$_POST['livep_bigplaybtn_yn'] = (isset($_POST['livep_bigplaybtn_yn']) ? 'yes' : 'no');
	$_POST['replayp_bigplaybtn_yn'] = (isset($_POST['replayp_bigplaybtn_yn']) ? 'yes' : 'no');
	$_POST['livep_simulate_video_yn'] = (isset($_POST['livep_simulate_video_yn']) ? 'yes' : 'no');
	$_POST['replayp_simulate_video_yn'] = (isset($_POST['replayp_simulate_video_yn']) ? 'yes' : 'no');
	$_POST['regp_gdpr_optin_yn'] = (isset($_POST['regp_gdpr_optin_yn']) ? 'yes' : 'no');
	$_POST['regp_wc_gdpr_optin_yn'] = (isset($_POST['regp_wc_gdpr_optin_yn']) ? 'yes' : 'no');
	

	$field_array = array(
	    array('sanitize' => true, 'slug' => 'gener_min'),
	    array('sanitize' => true, 'slug' => 'gener_hour'),
	    array('sanitize' => true, 'slug' => 'gener_date'),
	    array('sanitize' => true, 'slug' => 'gener_time'),
	    array('sanitize' => true, 'slug' => 'gener_webinar_status'),
	    array('sanitize' => true, 'slug' => 'gener_regdisabled_yn'),
	    array('sanitize' => true, 'slug' => 'gener_air_type'),
	    array('sanitize' => true, 'slug' => 'gener_time_occur'),
	    array('sanitize' => true, 'slug' => 'gener_rec_days'),
	    array('sanitize' => true, 'slug' => 'gener_rec_times'),
	    array('sanitize' => true, 'slug' => 'gener_jit_days'),
	    array('sanitize' => true, 'slug' => 'gener_jit_times'),
	    array('sanitize' => true, 'slug' => 'gener_duration'),
	    array('sanitize' => true, 'slug' => 'gener_timeslot_count'),
	    array('sanitize' => true, 'slug' => 'gener_onetimeregist'),
	    array('sanitize' => true, 'slug' => 'gener_offset_count'),
	    array('sanitize' => true, 'slug' => 'livep_title_show_yn'),
	    array('sanitize' => true, 'slug' => 'livep_askq_yn'),
	    array('sanitize' => true, 'slug' => 'livep_askq_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_askq_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_askq_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_askq_send_email_yn'),
	    array('sanitize' => true, 'slug' => 'livep_askq_send_email'),
	    array('sanitize' => true, 'slug' => 'livep_webdes_yn'),
	    array('sanitize' => true, 'slug' => 'livep_hostbox_yn'),
	    array('sanitize' => true, 'slug' => 'livep_leftbox_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_leftbox_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_hostbox_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_hostbox_title_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_hostbox_content_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_descbox_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_descbox_title_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_descbox_content_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_title_clr'),
	    array('sanitize' => true, 'slug' => 'livep_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_bckg_img'),
	    array('sanitize' => true, 'slug' => 'livep_vidurl'),
	    array('sanitize' => true, 'slug' => 'livep_vidurl_type'),
	    array('sanitize' => true, 'slug' => 'livep_video_auto_play_yn'),
	    array('sanitize' => true, 'slug' => 'livep_video_controls_yn'),
	    array('sanitize' => true, 'slug' => 'livep_bigplaybtn_yn', 'def' => 'yes'),
	    array('sanitize' => true, 'slug' => 'livep_simulate_video_yn', 'def' => 'no'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_yn'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_title'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_title_clr'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_title_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_incentive_content_clr'),
	    array('sanitize' => true, 'slug' => 'livep_show_actionbox'),
	    array('sanitize' => true, 'slug' => 'livep_action_raise_hand_clr'),
	    array('sanitize' => true, 'slug' => 'livep_action_raise_hand_hover_clr'),
	    array('sanitize' => true, 'slug' => 'livep_action_raise_hand_act_clr'),
	    array('sanitize' => true, 'slug' => 'livep_button_bg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_buttonhover_bg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_button_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_buttonhover_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_button_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_buttonhover_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_button_radius'),
	    array('sanitize' => true, 'slug' => 'livep_action_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_action_box_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_call_action'),
	    array('sanitize' => true, 'slug' => 'livep_call_action_ctatype'),
	    array('sanitize' => true, 'slug' => 'livep_cta_show_after'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_hover_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_txt_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_txt'),
	    array('sanitize' => true, 'slug' => 'livep_ctatxt_txt'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_url'),
	    array('sanitize' => true, 'slug' => 'livep_manual_show_cta'),
	    array('sanitize' => true, 'slug' => 'livep_show_chatbox'),
	    array('sanitize' => true, 'slug' => 'livep_show_chatbox_timestmp'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_quebox_title', 'def' => 'Question Box'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_quebox_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_quebox_bkg_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_quebox_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_chat_title', 'def' => 'Chatbox'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_chat_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_chat_bkg_text_clr'),
	    array('sanitize' => true, 'slug' => 'livep_chtb_chat_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_hover_txt_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctabtn_brdr_radius'),
	    array('sanitize' => true, 'slug' => 'livep_ctatxt_fld_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctatxt_fld_border_clr'),
	    array('sanitize' => true, 'slug' => 'livep_ctatxt_fld_content_clr'),
	    array('sanitize' => true, 'slug' => 'livep_bgclr_chatbtn'),
	    array('sanitize' => true, 'slug' => 'livep_txtclr_chatbtn'),
	    array('sanitize' => true, 'slug' => 'regp_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'regp_bckg_img'),
	    array('sanitize' => true, 'slug' => 'regp_vidurl'),
	    array('sanitize' => true, 'slug' => 'regp_vidurl_type'),
	    array('sanitize' => true, 'slug' => 'regp_video_auto_play_yn'),
	    array('sanitize' => true, 'slug' => 'regp_video_controls_yn'),
	    array('sanitize' => true, 'slug' => 'regp_bigplaybtn_yn', 'def' => 'yes'),
	    array('sanitize' => true, 'slug' => 'regp_regformtitle'),
	    array('sanitize' => true, 'slug' => 'regp_regformtxt'),
	    array('sanitize' => true, 'slug' => 'regp_ctatext'),
	    array('sanitize' => true, 'slug' => 'regp_regformfont_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regformbckg_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regformborder_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regformbtn_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regformbtnborder_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regformbtntxt_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regtitle_clr'),
	    array('sanitize' => true, 'slug' => 'regp_regmeta_clr'),
	    array('sanitize' => true, 'slug' => 'regp_wbndesc_clr'),
	    array('sanitize' => true, 'slug' => 'regp_wbndescbck_clr'),
	    array('sanitize' => true, 'slug' => 'regp_wbndescborder_clr'),
	    array('sanitize' => true, 'slug' => 'regp_loginformtitle'),
	    array('sanitize' => true, 'slug' => 'regp_loginformtxt'),
	    array('sanitize' => true, 'slug' => 'regp_loginformbtn_clr'),
	    array('sanitize' => true, 'slug' => 'regp_loginformbtnborder_clr'),
	    array('sanitize' => true, 'slug' => 'regp_loginformbtntxt_clr'),
	    array('sanitize' => true, 'slug' => 'regp_loginctatext'),
	    array('sanitize' => true, 'slug' => 'regp_tabbg_clr'),
	    array('sanitize' => true, 'slug' => 'regp_tabtext_clr'),
	    array('sanitize' => true, 'slug' => 'regp_tabone_text'),
	    array('sanitize' => true, 'slug' => 'regp_tabtwo_text'),
	    array('sanitize' => false, 'slug' => 'regp_custom_field_json'),
	    array('sanitize' => true, 'slug' => 'regp_show_content_setion', 'def' => 'yes'),
	    array('sanitize' => true, 'slug' => 'regp_show_description', 'def' => 'yes'),
	    array('sanitize' => true, 'slug' => 'regp_position', 'def' => 'Right'),
	    array('sanitize' => true, 'slug' => 'regp_hide_regtab', 'def' => 'no'),
	    array('sanitize' => true, 'slug' => 'regp_hide_logintab', 'def' => 'no'),
        array('sanitize' => true, 'slug' => 'regp_gdpr_optin_yn', 'def' => 'no'),
        array('sanitize' => true, 'slug' => 'regp_wc_gdpr_optin_yn', 'def' => 'no'),
        array('sanitize' => true, 'slug' => 'regp_gdpr_optin_text', 'def' => 'I agree my personal information will be stored on your website for the use of this webinar and to send me notifications about the event'),
	    array('sanitize' => true, 'slug' => 'tnxp_vidurl'),
	    array('sanitize' => true, 'slug' => 'tnxp_vidurl_type'),
	    array('sanitize' => true, 'slug' => 'tnxp_video_auto_play_yn'),
	    array('sanitize' => true, 'slug' => 'tnxp_video_controls_yn'),
	    array('sanitize' => true, 'slug' => 'tnxp_bigplaybtn_yn', 'def' => 'yes'),
	    array('sanitize' => true, 'slug' => 'tnxp_pagetitle'),
	    array('sanitize' => true, 'slug' => 'tnxp_pagetitle_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_bckg_img'),
	    array('sanitize' => true, 'slug' => 'tnxp_tktbckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tktbdr_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tkttxt_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tktbodybckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tkthdrbckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tkthdrtxt_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tktbtn_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_tktbtntxt_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_link_above_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_link_below_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_socialsharing_border_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_socialsharing_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_calendar_border_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_calendar_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_calendartxt_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_calendarbtntxt_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_calendarbtnbckg_clr'),
	    array('sanitize' => true, 'slug' => 'tnxp_calendarbtnborder_clr'),
	    array('sanitize' => true, 'slug' => 'cntdwnp_title_clr'),
	    array('sanitize' => true, 'slug' => 'cntdwnp_tagline_clr'),
	    array('sanitize' => true, 'slug' => 'cntdwnp_desc_clr'),
	    array('sanitize' => true, 'slug' => 'cntdwnp_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'cntdwnp_bckg_img'),
	    array('sanitize' => true, 'slug' => 'cntdwnp_timershow_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_title_show_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_title_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_bckg_img'),
	    array('sanitize' => true, 'slug' => 'replayp_askq_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_askq_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_askq_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_askq_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_askq_send_email_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_askq_send_email'),
	    array('sanitize' => true, 'slug' => 'replayp_webdes_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_hostbox_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_vidurl'),
	    array('sanitize' => true, 'slug' => 'replayp_vidurl_type'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_leftbox_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_hostbox_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_hostbox_title_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_hostbox_content_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_descbox_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_descbox_title_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_descbox_content_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_title_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_bckg_img'),
	    array('sanitize' => true, 'slug' => 'replayp_vidurl'),
	    array('sanitize' => true, 'slug' => 'replayp_vidurl_type'),
	    array('sanitize' => true, 'slug' => 'replayp_video_auto_play_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_video_controls_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_bigplaybtn_yn', 'def' => 'yes'),
	    array('sanitize' => true, 'slug' => 'replayp_simulate_video_yn', 'def' => 'no'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_yn'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_title'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_title_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_title_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_button_bg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_buttonhover_bg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_button_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_buttonhover_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_button_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_buttonhover_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_button_radius'),
	    array('sanitize' => true, 'slug' => 'replayp_show_chatbox'),
	    array('sanitize' => true, 'slug' => 'replayp_show_chatbox_timestmp'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_quebox_title', 'def' => 'Question Box'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_quebox_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_quebox_bkg_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_quebox_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_chat_title', 'def' => 'Chatbox'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_chat_title_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_chat_bkg_text_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_chtb_chat_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_action_raise_hand_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_action_raise_hand_hover_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_action_raise_hand_act_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_incentive_content_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_call_action'),
	    array('sanitize' => true, 'slug' => 'replayp_call_action_ctatype'),
	    array('sanitize' => true, 'slug' => 'replayp_cta_show_after'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_hover_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_txt_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_txt'),
	    array('sanitize' => true, 'slug' => 'replayp_ctatxt_txt'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_url'),
	    array('sanitize' => true, 'slug' => 'replayp_manual_show_cta'),
	    array('sanitize' => true, 'slug' => 'replayp_action_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_action_box_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_show_actionbox'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_hover_txt_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctabtn_brdr_radius'),
	    array('sanitize' => true, 'slug' => 'replayp_bgclr_chatbtn'),
	    array('sanitize' => true, 'slug' => 'replayp_txtclr_chatbtn'),
	    array('sanitize' => true, 'slug' => 'replayp_ctatxt_fld_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctatxt_fld_border_clr'),
	    array('sanitize' => true, 'slug' => 'replayp_ctatxt_fld_content_clr'),
	    array('sanitize' => true, 'slug' => 'hostmetabox_hostname'),
	    array('sanitize' => true, 'slug' => 'mailchimp_list'),
	    array('sanitize' => true, 'slug' => 'mailchimp_enabled', 'def' => 'false'),
	    array('sanitize' => true, 'slug' => 'default_mail_provider', 'def' => '0'),
	    array('sanitize' => true, 'slug' => 'mailpoet_list', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'mailpoet3_list', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'enormail_list', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'drip_accounts', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'drip_campaigns', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'aweber_list', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'aweber_privent_info_emails', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'getresponse_list', 'def' => ''),
	    array('sanitize' => true, 'slug' => 'activecampaign_list'),
	    array('sanitize' => true, 'slug' => 'ticket_wbnpaid_yn'),
	    array('sanitize' => true, 'slug' => 'ticket_title'),
	    array('sanitize' => true, 'slug' => 'ticket_description'),
	    array('sanitize' => true, 'slug' => 'ticket_price'),
	    array('sanitize' => true, 'slug' => 'ticketp_buyformtitle'),
	    array('sanitize' => true, 'slug' => 'ticketp_buyformtxt'),
	    array('sanitize' => true, 'slug' => 'ticketp_bckg_clr'),
	    array('sanitize' => true, 'slug' => 'ticketp_border_clr'),
	    array('sanitize' => true, 'slug' => 'ticketp_font_clr'),
	    array('sanitize' => true, 'slug' => 'timezoneidentifier'),
	    array('sanitize' => true, 'slug' => 'accesstab_parent'),
	    array('sanitize' => true, 'slug' => 'selected_user_role'),
	    array('sanitize' => true, 'slug' => 'filter_user_ids'),
	    array('sanitize' => true, 'slug' => 'selected_member_level'),
	    array('sanitize' => true, 'slug' => 'ws_actab_redirect_page'),
	    array('sanitize' => true, 'slug' => 'livep_fullscreen_control'),
	    array('sanitize' => true, 'slug' => 'replayp_fullscreen_control'),
	);

	foreach ($field_array as $field) {

	    $slug = $field['slug'];
        $dataToSave = '';

	    if (isset($_POST[$slug])) {
		    $dataToSave = $_POST[$slug];
	    } elseif (isset($field['def'])) {
		    $dataToSave = $field['def'];
	    }

	    if ($field['sanitize']) {
            $dataToSave = sanitize_text_field($dataToSave);
        }

	    update_post_meta($post_id, '_wswebinar_' . $slug, $dataToSave);
	}

	wpautop(stripslashes(update_post_meta($post_id, '_wswebinar_livep_incentive_content', @$_POST['livep_incentive_content'])));
	wpautop(stripslashes(update_post_meta($post_id, '_wswebinar_replayp_incentive_content', @$_POST['replayp_incentive_content'])));
	wpautop(stripslashes(update_post_meta($post_id, '_wswebinar_ticket_description', @$_POST['ticket_description'])));
	wpautop(stripslashes(update_post_meta($post_id, '_wswebinar_livep_ctatxt_txt', @$_POST['livep_ctatxt_txt'])));
	wpautop(stripslashes(update_post_meta($post_id, '_wswebinar_replayp_ctatxt_txt', @$_POST['replayp_ctatxt_txt'])));

    $regs = WebinarSysteemAttendees::getAttendies($post_id);

    if (get_post_meta($post_id, '_wswebinar_gener_webinar_status', true) == 'rep') {
	    foreach ($regs as $reg):
		if ($reg->replaymailsent == 0) {
		    $sendreplaymail = new WebinarSysteemMails;
		    $wbreplaymail = $sendreplaymail->SendMailtoAttendeeReplayLink_Template($reg->name, $reg->email, $post_id);
		    if ($wbreplaymail == true):
			WebinarSysteemAttendees::modifyAttendee($reg->id, array('replaymailsent' => '1'), array('%d'));
		    endif;
		}
	    endforeach;
	}
	//Create WooCommerce Product
	if (WebinarSysteemWooCommerceIntegration::isReady() && get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == "on")
	    WebinarSysteemWooCommerceIntegration::createOrUpdateTicket($post_id);
    }

    /*
     * 
     * Hostname meta box
     *  
     */

    function webinarMetaBoxHostContent($post) {
        wp_nonce_field('webinarmetaboxhost', 'webinarmetaboxhost_nonce');
        ?>
        <div class="form-field">
            <label for="hostmetabox_hostname"><?php _e('Webinar will be presented by:', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="hostmetabox_hostname" id="hostmetabox_hostname" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_hostmetabox_hostname', true)); ?>">
            <p class="description"><?php _e('Separate each by commas', WebinarSysteem::$lang_slug); ?></p>
            <div class="webinar_clear_fix"></div>
        </div>
        <?php
    }

    /*
     * 
     * Content Styling for Replay page
     * 
     */

    static function _page_styling($post, $live = TRUE) {
        $page = $live ? 'livep' : 'replayp';
        ?>
        <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Host & Description Box', WebinarSysteem::$lang_slug) ?></h3>
        <div class="ws-accordian-section">

            <div class="form-field">
            <label for="<?php echo $page ?>_leftbox_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_leftbox_bckg_clr" class="color-field" id="<?php echo $page ?>_leftbox_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_leftbox_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="wsseparator"></div>

            <div class="form-group">
            <label for="<?php echo $page ?>_hostbox_yn"><?php _e('Show Host Box', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_hostbox_yn_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_hostbox_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="<?php echo $page ?>_hostbox_yn" id="<?php echo $page ?>_hostbox_yn" value="yes" <?php echo ($livep_hostbox_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_hostbox_title_bckg_clr"><?php _e('Host Title Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_hostbox_title_bckg_clr" class="color-field" id="<?php echo $page ?>_hostbox_title_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_hostbox_title_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_hostbox_title_text_clr"><?php _e('Host Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_hostbox_title_text_clr" class="color-field" id="<?php echo $page ?>_hostbox_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_hostbox_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_hostbox_content_text_clr"><?php _e('Host Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_hostbox_content_text_clr" class="color-field" id="<?php echo $page ?>_hostbox_content_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_hostbox_content_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="wsseparator"></div>

            <div class="form-group">
            <label for="<?php echo $page ?>_webdes_yn"><?php _e('Show Description Box', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_webdes_yn_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_webdes_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="<?php echo $page ?>_webdes_yn" id="<?php echo $page ?>_webdes_yn" value="yes" <?php echo ($livep_webdes_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_descbox_title_bckg_clr"><?php _e('Description Title Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_descbox_title_bckg_clr" class="color-field" id="<?php echo $page ?>_descbox_title_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_descbox_title_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_descbox_title_text_clr"><?php _e('Description Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_descbox_title_text_clr" class="color-field" id="<?php echo $page ?>_descbox_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_descbox_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_descbox_content_text_clr"><?php _e('Description Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_descbox_content_text_clr" class="color-field" id="<?php echo $page ?>_descbox_content_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_descbox_content_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>


        </div>

        <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Question Box', WebinarSysteem::$lang_slug) ?></h3>
        <div class="ws-accordian-section">

            <div class="form-group">
            <label for="<?php echo $page ?>_askq_yn"><?php _e('Show Question box', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_askq_yn_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_askq_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="<?php echo $page ?>_askq_yn" id="<?php echo $page ?>_askq_yn" value="yes" <?php echo ($livep_askq_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
            <label for="replayp_askq_send_email_yn"><?php _e('Send an email on every question', WebinarSysteem::$lang_slug); ?></label>
            <?php $replayp_askq_send_email_yn_value = get_post_meta($post->ID, '_wswebinar_replayp_askq_send_email_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="replayp_askq_send_email_yn" id="replayp_askq_send_email_yn" value="yes" <?php echo ($replayp_askq_send_email_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group <?php echo $replayp_askq_send_email_yn_value != "yes" ? 'hidden' : '' ?>">
            <label for="replayp_askq_send_email"><?php _e('If then email to', WebinarSysteem::$lang_slug); ?></label>
            <input type="email" name="replayp_askq_send_email" size="20" placeholder="you@example.com" id="replayp_askq_send_email" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_askq_send_email', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_askq_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input  data-style-collect="true" type="text" name="<?php echo $page ?>_askq_bckg_clr" class="color-field" id="<?php echo $page ?>_askq_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_askq_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_askq_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_askq_border_clr" class="color-field" id="<?php echo $page ?>_askq_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_askq_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_askq_title_text_clr"><?php _e('Title Text Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_askq_title_text_clr" class="color-field" id="<?php echo $page ?>_askq_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_askq_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_button_radius"><?php _e('Border Radius', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_button_radius" size="20" placeholder="5px" id="<?php echo $page ?>_button_radius" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_button_radius', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_button_bg_clr"><?php _e('Button Background Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_button_bg_clr" class="color-field" id="<?php echo $page ?>_button_bg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_button_bg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_buttonhover_bg_clr"><?php _e('Button Hover Background Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_buttonhover_bg_clr" class="color-field" id="<?php echo $page ?>_buttonhover_bg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_buttonhover_bg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_button_border_clr"><?php _e('Button Border Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_button_border_clr" class="color-field" id="<?php echo $page ?>_button_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_button_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_buttonhover_border_clr"><?php _e('Button Hover Border Color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_buttonhover_border_clr" class="color-field" id="<?php echo $page ?>_buttonhover_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_buttonhover_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_button_text_clr"><?php _e('Button Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_button_text_clr" class="color-field" id="<?php echo $page ?>_button_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_button_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_buttonhover_text_clr"><?php _e('Button Hover Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_buttonhover_text_clr" class="color-field" id="<?php echo $page ?>_buttonhover_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_buttonhover_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Live chatbox', WebinarSysteem::$lang_slug) ?></h3>
        <div class="ws-accordian-section">

            <div class="form-group">
            <label for="<?php echo $page ?>_show_chatbox"><?php _e('Show Chatbox', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_chtb_yn_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_show_chatbox', true); ?>
            <input data-switch="true" class="wsweb_listen_fortabs" data-style-collect="true" type="checkbox" name="<?php echo $page ?>_show_chatbox" id="<?php echo $page ?>_chtb_yn" value="yes" <?php echo ($livep_chtb_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_bckg_clr" class="color-field" id="<?php echo $page ?>_chtb_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_border_clr" class="color-field" id="<?php echo $page ?>_chtb_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_title_text_clr"><?php _e('Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_title_text_clr" class="color-field" id="<?php echo $page ?>_chtb_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-group">
            <label for="<?php echo $page ?>_show_chatbox_timestmp"><?php _e('Show Timestamps', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_chtb_yn_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_show_chatbox_timestmp', true); ?>
            <input data-switch="true" data-style-collect="true" type="checkbox" name="<?php echo $page ?>_show_chatbox_timestmp" id="<?php echo $page ?>_chtb_yn" value="yes" <?php echo ($livep_chtb_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-group">
            <label for="replayp_bgclr_chatbtn"><?php _e('Background Color of Button', WebinarSysteem::$lang_slug); ?></label>
            <?php $replayp_chtbtn_clr = get_post_meta($post->ID, '_wswebinar_replayp_bgclr_chatbtn', true); ?>
            <input data-switch="true" class="color-field wp-color-picker" data-style-collect="true" type="text" name="replayp_bgclr_chatbtn" id="replayp_bgclr_chatbtn" value="<?php echo ($replayp_chtbtn_clr) ?>" >
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-group">
            <label for="replayp_txtclr_chatbtn"><?php _e('Text Color of Button', WebinarSysteem::$lang_slug); ?></label>
            <?php $replayp_txtbtn_clr = get_post_meta($post->ID, '_wswebinar_replayp_txtclr_chatbtn', true); ?>
            <input data-switch="true" class="color-field wp-color-picker" data-style-collect="true" type="text" name="replayp_txtclr_chatbtn" id="replayp_txtclr_chatbtn" value="<?php echo ($replayp_txtbtn_clr) ?>" >
            <div class="webinar_clear_fix"></div>
            </div>

        </div>

        <h3 style="display: none;" class="ws-accordian-title wsweb_livep_tablayout"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Tab Layout', WebinarSysteem::$lang_slug) ?></h3>
        <div style="display: none;" class="ws-accordian-section ">
            <span><b><?php _e('Questionbox tab', WebinarSysteem::$lang_slug); ?></b></span><br>
            <div class="wsseparator webinar-seperator-livep"></div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_quebox_title"><?php _e('Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_quebox_title" id="<?php echo $page ?>_chtb_quebox_title" value="<?php
            $val = esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_quebox_title', true));
            echo (!empty($val) ? $val : 'Question Box');
            ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_quebox_title_text_clr"><?php _e('Tab Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_quebox_title_text_clr" class="color-field" id="<?php echo $page ?><?php echo $page ?>_chtb_quebox_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_quebox_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_quebox_bkg_text_clr"><?php _e('Tab Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_quebox_bkg_text_clr" class="color-field" id="<?php echo $page ?>_chtb_quebox_bkg_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_quebox_bkg_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_quebox_border_clr"><?php _e('Tab Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_quebox_border_clr" class="color-field" id="<?php echo $page ?>_chtb_quebox_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_quebox_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <span><b><?php _e('Chatbox tab', WebinarSysteem::$lang_slug); ?></b></span><br>
            <div class="wsseparator webinar-seperator-livep"></div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_chat_title"><?php _e('Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_chat_title" id="<?php echo $page ?>_chtb_chat_title" value="<?php
            $val = esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_chat_title', true));
            echo (!empty($val) ? $val : 'Chatbox');
            ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_chat_title_text_clr"><?php _e('Tab Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_chat_title_text_clr" class="color-field" id="livep_chtb_chat_title_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_chat_title_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_chat_bkg_text_clr"><?php _e('Tab Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_chat_bkg_text_clr" class="color-field" id="<?php echo $page ?>_chtb_chat_bkg_text_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_chat_bkg_text_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_chtb_chat_border_clr"><?php _e('Tab Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_chtb_chat_border_clr" class="color-field" id="<?php echo $page ?>_chtb_chat_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_chtb_chat_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Incentive Box', WebinarSysteem::$lang_slug) ?></h3>
        <div class="ws-accordian-section">

            <div class="form-group">
            <label for="<?php echo $page ?>_incentive_yn"><?php _e('Show Incentive Box', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_incentive_yn_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_yn', true); ?>
            <input data-style-collect="true" type="checkbox" data-switch="true" name="<?php echo $page ?>_incentive_yn" id="<?php echo $page ?>_incentive_yn" value="yes" <?php echo ($livep_incentive_yn_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_incentive_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_incentive_bckg_clr" class="color-field" id="<?php echo $page ?>_incentive_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>


            <div class="form-field">
            <label for="<?php echo $page ?>_incentive_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_incentive_border_clr" class="color-field" id="<?php echo $page ?>_incentive_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_incentive_title"><?php _e('Incentive Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_incentive_title" id="<?php echo $page ?>_incentive_title" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_title', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_incentive_title_clr"><?php _e('Title Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_incentive_title_clr" class="color-field" id="<?php echo $page ?>_incentive_title_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_title_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_incentive_title_bckg_clr"><?php _e('Title Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_incentive_title_bckg_clr" class="color-field" id="<?php echo $page ?>_incentive_title_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_title_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_incentive_content_clr"><?php _e('Content text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_incentive_content_clr" class="color-field" id="<?php echo $page ?>_incentive_content_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_content_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-group">
            <label for="<?php echo $page ?>_incentive_content"><?php _e('Incentive box content', WebinarSysteem::$lang_slug); ?></label>
            <?php
            $meta = get_post_meta($post->ID, '_wswebinar_' . $page . '_incentive_content', true);
            $content = apply_filters('meta_content', $meta);
            wp_editor($content, $page . '_incentive_content');
            ?>
            <div class="webinar_clear_fix"></div>
            </div>

        </div>
        <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Action Box', WebinarSysteem::$lang_slug) ?></h3>

        <div class="ws-accordian-section">
            <div class="form-group">
            <?php $page = 'replayp'; ?>
            <label for="<?php echo $page ?>_show_actionbox"><?php _e('Show Action Box', WebinarSysteem::$lang_slug); ?></label>
            <?php $livep_actionbox_show_value = get_post_meta($post->ID, '_wswebinar_' . $page . '_show_actionbox', true); ?>
            <input type="checkbox" data-style-collect="true" data-switch="true" name="<?php echo $page ?>_show_actionbox" id="<?php echo $page ?>_show_actionbox" value="yes" <?php echo ($livep_actionbox_show_value == "yes" ) ? 'checked="checked"' : ''; ?> >
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="<?php echo $page ?>_action_raise_hand_clr"><?php _e('Hand button color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_action_raise_hand_clr" class="color-field" id="<?php echo $page ?>_action_raise_hand_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_action_raise_hand_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_action_raise_hand_hover_clr"><?php _e('Hand button hover color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_action_raise_hand_hover_clr" class="color-field" id="<?php echo $page ?>_action_raise_hand_hover_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_action_raise_hand_hover_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="<?php echo $page ?>_action_raise_hand_act_clr"><?php _e('Hand button Active color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="<?php echo $page ?>_action_raise_hand_act_clr" class="color-field" id="<?php echo $page ?>_action_raise_hand_act_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_' . $page . '_action_raise_hand_act_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>

            <div class="form-field">
            <label for="replayp_action_bckg_clr"><?php _e('Background Color ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="replayp_action_bckg_clr" class="color-field" id="replayp_action_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_action_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
            <div class="form-field">
            <label for="replayp_action_box_border_clr"><?php _e('Border Color  ', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="replayp_action_box_border_clr" class="color-field" id="replayp_action_box_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_replayp_action_box_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
            </div>
        </div>

        <?php
    }

    function previewButton($post, $page = 'register') {
        ?>
        <a target="wp-preview-<?php echo $post->ID; ?>" class="preview button wswebinar_button" href="<?php echo add_query_arg(array('force_show' => $page), get_post_permalink($post->ID)); ?>"><?php _e('Preview Page', WebinarSysteem::$lang_slug) ?></a>
        <span class="description"><?php _e('Only use this generated link for test purposes', WebinarSysteem::$lang_slug); ?></span>
        <?php
    }

    public static function checkCheckbox($state = FALSE) {
        if ($state)
            return 'checked="checked"';
        return '';
    }
    
    public static function getJITtimesArray($req = ''){
		$arr = array(
            '5' => __('every 5 minutes',WebinarSysteem::$lang_slug),
            '10' => __('every 10 minutes',WebinarSysteem::$lang_slug),
            '15' => __('every 15 minutes',WebinarSysteem::$lang_slug),
            '20' => __('every 20 minutes',WebinarSysteem::$lang_slug),
            '30' => __('every 30 minutes',WebinarSysteem::$lang_slug),
            '40' => __('every 40 minutes',WebinarSysteem::$lang_slug),
            '45' => __('every 45 minutes',WebinarSysteem::$lang_slug),
            '50' => __('every 50 minutes',WebinarSysteem::$lang_slug),
            '60' => __('every 60 minutes',WebinarSysteem::$lang_slug)
		);

        if (empty($req))
            return $arr;

	    return $arr[$req];
	}

    public static function getWeekDayArray($req = '') {
        $arr = array(
            'mon' => __('Monday', WebinarSysteem::$lang_slug),
            'tue' => __('Tuesday', WebinarSysteem::$lang_slug),
            'wed' => __('Wednesday', WebinarSysteem::$lang_slug),
            'thu' => __('Thursday', WebinarSysteem::$lang_slug),
            'fri' => __('Friday', WebinarSysteem::$lang_slug),
            'sat' => __('Saturday', WebinarSysteem::$lang_slug),
            'sun' => __('Sunday', WebinarSysteem::$lang_slug),
        );
        if (empty($req))
            return $arr;
        return $arr[$req];
    }
    
    public static function getStaticWeekDayArray($req = '') {
        $arr = array(
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday',
        );
        if (empty($req))
            return $arr;
        return $arr[$req];
    }

    function getSortDate($a, $b) {
	    return strtotime($a["date"]) - strtotime($b["date"]);
    }
}

<?php

class WebinarsysteemMailingListIntegrations{

    public static $consumerKey = "Akz3wAZDmnUvC4U8Bo1y4Fo1"; # For Aweber developer account.
    public static $consumerSecret = "agCxf2Av1IfEla2mPhJaN3UIHeppGUMxv3jU8Rjw";  # For Aweber developer account.
    
    /**
     * MailChimp API Key validation
     * 
     * @param string $api_key API Key
     */
    public static function isValidMailingChimpAPIKEY($api_key) {
        if (!empty($api_key)) {
            try {
                $Mailchimp = new Mailchimp($api_key);
                $Mailchimp_Lists = new Mailchimp_Lists($Mailchimp);
                $Mailchimp_Lists->getList(array(), 0, 25, 'created', 'DESC');
            } catch (Exception $exc) {
                $exception = TRUE;
            }
            if (isset($exception)) {
                //Set an option
                update_option('_wswebinar_mailchimp_api_key_error', TRUE);
            } else {
                //API key is corrects
                update_option('_wswebinar_mailchimp_api_key_error', FALSE);
            }
        }
        if (empty($api_key)) {
            update_option('_wswebinar_mailchimp_api_key_error', FALSE);
        }
        return TRUE;
    }

    /**
     * Enormail API Key validation
     * 
     * @param string $key API Key
     * @return boolean
     */
    public static function isValidEnormailKey($key) {
        $update_option_api_key_error = true;
        $valid_key = false;
        if (!empty($key)) {
            $acccount = new EM_Account(new Em_Rest($key));
            $api_check = $acccount->info($key);
            $decoded_apicheck = json_decode($api_check);

            if (isset($decoded_apicheck->error) && $decoded_apicheck->error) {
                $update_option_api_key_error = true;
                $valid_key = false;
            } else {
                $update_option_api_key_error = false;
                $valid_key = true;
            }
        } else {
            $update_option_api_key_error = false;
            $valid_key = false;
        }
        update_option('_wswebinar_enormail_api_key_error', $update_option_api_key_error);
        return $valid_key;
    }
    /**
	* Drip API Key validation
	* 
	* @param string $key API key
	* @return boolean
	*/
	public static function isValidDripKey($key) {
		$update_option_api_key_error = true;
		$valid_key = false;
		$_drip_api = new WP_GetDrip_API(empty( $key ) ? null : $key);
		if (!empty($key)){
			$valid = $_drip_api->validate_drip_token( $key );
			if(!$valid) {
				$update_option_api_key_error = true;
				$valid_key = false;
			} else {
				$update_option_api_key_error = false;
				$valid_key = true;
			}
		} else {
			$update_option_api_key_error = false;
			$valid_key = false;
		}
		update_option('_wswebinar_drip_api_key_error', $update_option_api_key_error);
		return $valid_key;
	}

    /**
	* Checks on init drip api key. if not valid,
	* Show admin notice
	* 
	*/
	public static function invalid_drip_key() {
		$key = get_option('_wswebinar_dripapikey');
		self::isValidDripKey($key);
		if (get_option('_wswebinar_drip_api_key_error')) {
			?>
			<div class="error">
				<p><?php echo sprintf(__('Invalid Drip API key. To fix, please visit <a href="%s">WebinarSystem Settings</a>.', WebinarSysteem::$lang_slug), "edit.php?post_type=wswebinars&page=wswbn-options"); ?></p>
			</div>
			<?php
		}
	}
    /*
     * Checks on init enormail api key. if not valid,
     * Show admin notice.
     */
    public static function invalid_enormail_key() {
        $key = get_option('_wswebinar_enormailapikey');
        self::isValidEnormailKey($key);
        if (get_option('_wswebinar_enormail_api_key_error')) {
            ?>
            <div class="error">
                <p><?php echo sprintf(__('Invalid Enormail API key. To fix, please visit <a href="%s">WebinarSystem Settings</a>.', WebinarSysteem::$lang_slug), "edit.php?post_type=wswebinars&page=wswbn-options"); ?></p>
            </div><?php
        }
    }
    /**
     * aWeber API Key validation
     * 
     * @return boolean
     */
    public static function aWeber_Connected() {
        $has_tokens = false;
        $can_communicate = false;
        $token_secret = get_option('_wswebinar_aweber_accessTokenSecret');
        $token_secret_token = get_option('_wswebinar_aweber_accessToken');
        $has_tokens = (!empty($token_secret) & !empty($token_secret_token) ? TRUE : FALSE);

        if ($has_tokens) {
            $aweber = new WSAWeberAPI(self::$consumerKey, self::$consumerSecret);
            try {
                $account = $aweber->getAccount(get_option('_wswebinar_aweber_accessToken'), get_option('_wswebinar_aweber_accessTokenSecret'));
            } catch (Exception $ex) {
                update_option(WebinarSysteem::$lang_slug . '_aweber_key_revoked', true);
                self::revokeAweberConfig();
                return false;
            }

            $account_id = $account->id;
            $can_communicate = (!empty($account_id) ? true : false);
        }
        return ($has_tokens && $can_communicate ? TRUE : FALSE);
    }

    /**
     * Checks if ActiveCampaign API Key and URL is valid.
     * 
     * @param string $key
     * @param string $url
     * @return boolean
     */
    public static function isReadyActiveCampaign($key = NULL, $url = NULL) {
        $key = $key ? $key : get_option('_wswebinar_activecampaignapikey');
        $url = $url ? $url : get_option('_wswebinar_activecampaignurl');

        if (!$key && !$url)
            return FALSE;

        $ac = new ActiveCampaign($url, $key);
        if ((int) $ac->credentials_test())
            return TRUE;

        return FALSE;
    }

    /**
     * Shows an admin notice if ActiveCampaign credentials are invalid.
     * 
     * @return string
     */
    static function showActiveCampaignNotice() {
        $key = get_option('_wswebinar_activecampaignapikey');
        $url = get_option('_wswebinar_activecampaignurl');

        if (!$key && !$url)
            return;

        if (self::isReadyActiveCampaign())
            return;
        ?>
        <div class="error">
            <p><?php echo sprintf(__('Invalid %s API KEY and URL.', WebinarSysteem::$lang_slug), "ActiveCampaign") . ' ' . sprintf(__('Please visit <a href="%s">WebinarSystem Settings</a> to fix.', WebinarSysteem::$lang_slug), admin_url("edit.php?post_type=wswebinars&page=wswbn-options")); ?></p>
        </div>
        <?php
    }

    static function ajaxIsValidActiveCampaignCredentials() {
        $data = $_REQUEST['data'];
        if (!is_array($data) || empty($data['key']) || empty($data['url'])) {
            $output = array(
                'error' => true,
                'content' => __('Please add both API KEY and URL', WebinarSysteem::$lang_slug)
            );
        } else {
            $ready = self::isReadyActiveCampaign($data['key'], $data['url']);
            $output = array(
                'error' => !$ready,
                'content' => $ready ? '' : __('Invalid API credentials.', WebinarSysteem::$lang_slug)
            );
        }
        echo json_encode($output);
        wp_die();
    }

    static function addSubscriberActiveCampaign($post_id, $name, $email) {
        WebinarSysteemSubscribe::subscribeActiveCampaign($post_id, $name, $email);
    }

    /**
     * Get ActiveCampaign list of Lists. API: list/list
     * 
     * @return boolean|array
     */
    static function getActiveCampaignListList() {
        $key = get_option('_wswebinar_activecampaignapikey');
        $url = get_option('_wswebinar_activecampaignurl');
        $list = array();
        
        if (!$key || !$url)
            return FALSE;
        
        $ac = new ActiveCampaign($url, $key);
        $result = $ac->api("list/list", array('ids' => 'all'));
        
        if (!$result->result_code) //Exit if response failed.
            return FALSE;
        
        foreach ($result as $a => $b)
            if (is_object($b))
                array_push($list, $b);
            
        return $list;
    }

    /**
     * Check Enomail API key.
     * 
     * @return boolean.
     */
    public static function checkEnomailAPIkey() {
        $key = $_GET['key'];
        $lists = new EM_Account(new Em_Rest($key));
        $set = $lists->info(get_option('_wswebinar_enormailapikey'));

        $decoded_set = json_decode($set);
        if (isset($decoded_set->error)) {
            $return = array('error' => TRUE, 'content' => __('Invalid api key!', WebinarSysteem::$lang_slug));
            echo json_encode($return);
        } else {
            $return = array('error' => FALSE, 'content' => $decoded_set->firstname . ' ' . $decoded_set->lastname);
            echo json_encode($return);
        }
        wp_die();
    }
    /**
	* Check Drip API key
	* 
	* @return
	*/
    public static function checkDripAPIkey() {
		$api_token = get_option('_wswebinar_dripapikey');
		$_drip_api = new WP_GetDrip_API(empty( $api_token ) ? null : $api_token);
		                                    
		$key = $_GET['key'];

		$valid = $_drip_api->validate_drip_token( $key );
		
		if(!$valid) {
			$return = array('error' => TRUE);
			echo json_encode($return);
		} else {
			$return = array('error' => FALSE);
			echo json_encode($return);
		} 
		wp_die();                                    
	}
	/**
	* Get Drip Campaigns
	* 
	* @return Campaign List
	*/
	public static function getDripCampaigns(){
		
		$account_id = $_GET['account_id'];	

		$account_campaigns = array(
			array(
				'label' => '',
				'value' => ''
			)
		);
		$api_key = get_option('_wswebinar_dripapikey');
			if(!empty($account_id)){
						$_drip_api = new WP_GetDrip_API($api_key);
		$_drip_api->set_drip_api_token($api_key);
		$campaigns = $_drip_api->list_campaigns($account_id);
		if( ! empty( $campaigns )) {
			if ( 1 < $campaigns[ 'meta' ][ 'total_pages' ] ) {

					$all_campaigns = $campaigns[ 'campaigns' ];

					while ( $campaigns[ 'meta' ][ 'page' ] < $campaigns[ 'meta' ][ 'total_pages' ] ) {

						$campaigns = $_drip_api->list_campaigns( $account_id, $campaigns[ 'meta' ][ 'page' ] + 1 );

						if ( ! empty( $campaigns ) ) {

							$all_campaigns = array_merge( $all_campaigns, $campaigns[ 'campaigns' ] );

						}

					}
		}
		else
		{
			$all_campaigns = $campaigns[ 'campaigns' ];
		}
		foreach ( $all_campaigns as $campaign ) {

					$account_campaigns[ ] = array( 'label' => $campaign[ 'name' ], 'value' => $campaign[ 'id' ] );

				}
			}
	}
	
	echo json_encode($account_campaigns);
	wp_die();		

	}
    public static function getDripCampaignList($account_id){
		
	$account_campaigns = array(
			array(
				'label' => '',
				'value' => ''
			)
		);
		$api_key = get_option('_wswebinar_dripapikey');
			if(!empty($account_id)){
						$_drip_api = new WP_GetDrip_API($api_key);
		$_drip_api->set_drip_api_token($api_key);
		$campaigns = $_drip_api->list_campaigns($account_id);
		if( ! empty( $campaigns )) {
			if ( 1 < $campaigns[ 'meta' ][ 'total_pages' ] ) {

					$all_campaigns = $campaigns[ 'campaigns' ];

					while ( $campaigns[ 'meta' ][ 'page' ] < $campaigns[ 'meta' ][ 'total_pages' ] ) {

						$campaigns = $_drip_api->list_campaigns( $account_id, $campaigns[ 'meta' ][ 'page' ] + 1 );

						if ( ! empty( $campaigns ) ) {

							$all_campaigns = array_merge( $all_campaigns, $campaigns[ 'campaigns' ] );

						}

					}
		}
		else
		{
			$all_campaigns = $campaigns[ 'campaigns' ];
		}
		foreach ( $all_campaigns as $campaign ) {

					$account_campaigns[ ] = array( 'label' => $campaign[ 'name' ], 'value' => $campaign[ 'id' ] );

				}
			}
	}
	
	return $account_campaigns;		

	}
	/**
	* Get Drip Account Choices
	* 
	* @return
	*/
	public static function get_drip_account_lists($key) {
		$account_choices = array(
		array(
			'label' => '',
			'value' => ''
		)
		);
		
		$_drip_api = new WP_GetDrip_API($key);
		$_drip_api->set_drip_api_token($key);
			
		$accounts = $_drip_api->list_accounts();
		
		if( !empty($accounts)) {
			foreach ($accounts['accounts'] as $account){
				$account_choices[] = array('label' => $account['name'], 'value' => $account['id'] );
			}
		}

		return $account_choices;
	}
    
    /*
     * Check mailchim API key, if is invalid, show an admin notice.
     */
    public static function invalid_mailchimp_key() {
        self::isValidMailingChimpAPIKEY(get_option('_wswebinar_mailchimpapikey'));
        if (get_option('_wswebinar_mailchimp_api_key_error')) {
            ?>
            <div class="error">
                <p><?php echo sprintf(__('Invalid MailChimp API key. To fix, please visit <a href="%s">WebinarSystem Settings</a>.', WebinarSysteem::$lang_slug), "edit.php?post_type=wswebinars&page=wswbn-options"); ?></p>
            </div><?php
        }
    }
    /*
     * Connect with Aweber Mailing API
     * Set cookies and update options.
     */
    public static function aweber_connect() {
        if (isset($_GET['wswebinar_aweber_connect'])) {
            $aweber = new WSAWeberAPI(self::$consumerKey, self::$consumerSecret);
            $_wswebinar_aweber_accessToken = get_option('_wswebinar_aweber_accessToken');
            if (empty($_wswebinar_aweber_accessToken)) {
                $webinar_aweber_access_token = get_option('_wswebinar_aweber_accessToken');
                if (empty($webinar_aweber_access_token)) {
                    $auth_token = @$_GET['oauth_token'];
                    if (empty($auth_token)) {
                        $callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
                        update_option(WebinarSysteem::$lang_slug . '_aweber_request_token_secret', $requestTokenSecret);
                        setcookie('webinar_aweberrtkns', $requestTokenSecret);
                        header("Location: {$aweber->getAuthorizeUrl()}");
                        exit();
                    }

                    $aweber->user->tokenSecret = $_COOKIE['webinar_aweberrtkns'];
                    $aweber->user->requestToken = $_GET['oauth_token'];
                    $aweber->user->verifier = $_GET['oauth_verifier'];
                    list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();

                    update_option(WebinarSysteem::$lang_slug . '_aweber_accessTokenSecret', $accessTokenSecret);
                    update_option(WebinarSysteem::$lang_slug . '_aweber_accessToken', $accessToken);
                    update_option(WebinarSysteem::$lang_slug . '_aweber_key_success', 1);
                    $home_url = home_url();
                    header('Location: ' . "$home_url/wp-admin/edit.php?post_type=wswebinars&page=wswbn-options&tab=mp");
                    self::check_aweber_connected();
                    exit();
                }
            }
        }
    }
    
    
    /*
     * Checks Aweber API is connected to the App.
     * Update the option the status for getting every function.
     */
    public static function check_aweber_connected() {
        $showed = get_option('_wswebinar_aweber_key_success');
        if ($showed < 3) {
            if (WebinarsysteemMailingListIntegrations::aWeber_Connected()) {
                ?>
                <div class="updated">
                    <p><?php echo sprintf(__('Now you completed the aWeber authentication. For changes Go to <a href="%s">WebinarSysteem Settings</a>.', WebinarSysteem::$lang_slug), "edit.php?post_type=wswebinars&page=wswbn-options"); ?></p>
                </div>
                <?php
                $update_show = $showed + 1;
                update_option(WebinarSysteem::$lang_slug . '_aweber_key_success', $update_show);
            }
        }
    }
    
    public static function check_aweber_disconnected() {
        $showed = get_option('_wswebinar_aweber_key_revoked');
        if ($showed == 1) {
            ?>
            <div class="error">
                <p><?php echo sprintf(__('Unexpectedly aWeber has been disconnected from the server. You are no longer subscribed to aWeber mailinglist. For Changes go to <a href="%s">WebinarSysteem Settings</a>.', WebinarSysteem::$lang_slug), "edit.php?post_type=wswebinars&page=wswbn-options"); ?></p>
            </div>
            <?php
            update_option(WebinarSysteem::$lang_slug . '_aweber_key_revoked', false);
        }
    }
    
    /*
     * Get Response Integration functions.
     * echo if called by ajax.
     * @return if called by not ajax.
     */

    public static function checkGetresponse_apikey($key = null) {
        $ifDie = $key;
        $key = ($key == null ? $_GET['getresponse_apikey'] : $key);
        if (!empty($key)) {
            $api = new GetResponse($key);
            $ping = $api->ping();
            if ($ping) {
                if ($ifDie == null) {
                    echo json_encode(array(
                        'error' => false
                    ));
                } else {
                    return json_encode(array(
                        'error' => false
                    ));
                }
            } else {
                if ($ifDie == null) {
                    echo json_encode(array(
                        'error' => true
                    ));
                } else {
                    return json_encode(array(
                        'error' => true
                    ));
                }
            }
        }
        if ($ifDie == null) {
            wp_die();
        }
    }
    
    /*
     * Checks if API key is error for getResponce in init.
     * Show admin notice for error if API key is invalid.
     */
    static function checkGetresponseAPIKeyShowError() {
        $APIKEY = get_option('_wswebinar_getresponseapikey');
        if (!empty($APIKEY)) {
            $checkAccount = json_decode(self::checkGetresponse_apikey($APIKEY));
            if ($checkAccount->error) {
                ?>
                <div class="error">
                    <p><?php echo sprintf(__('Invalid Getresponse API key. To fix, please visit <a href="%s">WebinarSystem Settings</a>.', WebinarSysteem::$lang_slug), "edit.php?post_type=wswebinars&page=wswbn-options"); ?></p>
                </div>
                <?php
            }
        }
    }
    
    /*
     * Remoke the Aweber API configuration from the App.
     */
    
    public static function revokeAweberConfig() {
        unset($_COOKIE['webinar_aweberrtkns']);
        update_option(WebinarSysteem::$lang_slug . '_aweber_accessTokenSecret', '');
        update_option(WebinarSysteem::$lang_slug . '_aweber_accessToken', '');
        update_option(WebinarSysteem::$lang_slug . '_aweber_key_success', 1);
        return true;
    }
    
    /*
     * Gets getResponse mailing lists.
     * @return GetResponse Lists.
     */
    public static function getResponseLists($APIKEY) {
        $api = new GetResponse($APIKEY);
        $lists = $api->getCampaigns();
        return $lists;
    }
}

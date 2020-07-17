<?php

class WebinarSysteemSubscribe
{
    /*
     *
     * Mailpoet external subscription process.
     *
     */

    public static function subscribeMailpoet($post_id, $email, $user)
    {
        $list_id = get_post_meta($post_id, '_wswebinar_mailpoet_list', true);
        if (!class_exists('WYSIJA') || empty($list_id)) {
            return;
        }

        $exploded_name = self::explodeName($user);
        $list = array('list_ids' => array($list_id));
        $user_data = array('email' => $email, 'firstname' => $exploded_name['fname'], 'lastname' => $exploded_name['lname']);
        $data_subscriber = array('user' => $user_data, 'user_list' => $list);
        $helper_user = WYSIJA::get('user', 'helper');
        $helper_user->no_confirmation_email = true;
        $added_user_id = $helper_user->addSubscriber($data_subscriber);
        $helper_user->confirm_user($added_user_id);
        $helper_user->subscribe($added_user_id, true, false, array($list_id));
    }

    /*
     *
     * Mailpoet 3 external subscription process.
     *
     */

    public static function subscribeMailpoet3($post_id, $email, $user)
    {
        $list_id = get_post_meta($post_id, '_wswebinar_mailpoet3_list', true);

        if (!class_exists('\MailPoet\API\API') || empty($list_id)) {
            return;
        }

        $exploded_name = self::explodeName($user);
        $list = array($list_id);
        $user_data = array('email' => $email, 'first_name' => $exploded_name['fname'], 'last_name' => $exploded_name['lname']);

        $options = array(
            'send_confirmation_email' => false, // default: true
            'schedule_welcome_email' => false, // default: true
        );

        try {
            \MailPoet\API\API::MP('v1')->addSubscriber($user_data, $list, $options);
        } catch (Exception $exception) {
            try {
                $subscriber = \MailPoet\API\API::MP('v1')->subscribeToLists($email, $list);
            } catch (Exception $exception) {
            }
        }
    }

    /*
     *
     * Split First Name and Last Name from single name sting
     *
     */

    public static function explodeName($user)
    {
        $fname = "";
        $lname = "";
        $nameAr = explode(" ", $user);
        $getvar = 0;

        if (count($nameAr) > 1) {
            $fname = $nameAr[$getvar];
            $getvar++;
            for ($var = $getvar; $var < count($nameAr); $var++) {
                $lname .= ' ' . $nameAr[$var];
            }
        } else {
            $fname = $user;
        }

        return array('fname' => $fname, 'lname' => $lname);
    }

    public static function subscribeEnormail($post_id, $user, $email)
    {
        $provider = get_post_meta($post_id, '_wswebinar_default_mail_provider', true);
        if ($provider == 'enormail') {
            $api_key = get_option('_wswebinar_enormailapikey');
            $enormail_list = get_post_meta($post_id, '_wswebinar_enormail_list', true);

            if (!empty($enormail_list) && class_exists('EM_Contacts') && (!get_option('_wswebinar_enormail_api_key_error'))) {
                $contact = new EM_Contacts(new Em_Rest($api_key));
                $expolde_name = self::explodeName($user);
                $fname = $expolde_name['fname'];
                $lname = $expolde_name['lname'];
                $subscriber = $contact->add($enormail_list, $fname, $email, array('lastname' => $lname));
                $subscriber_status = json_decode($subscriber);

                if ($subscriber_status->status == 'error') {
                    // Error in subscription
                    return array('state' => false, 'reason' => 'Provided user information not valid');
                } else {
                    // Subscription Completed
                    return array('state' => true, 'name' => $subscriber_status->name, 'lname' => $subscriber_status->lastname, 'subs_at' => $subscriber_status->subscribed_at);
                }
            } else {
                return array('state' => false, 'reason' => 'Class EM_Contacts does not exits or Invalid API Key or Enormail List not selected');
            }
        }
    }

    public static function get_create_or_update_drip_api_params($name, $email, $campaign_id)
    {
        $custom_field = array();
        $custom_field['name'] = $name;
        $drip_api_params['email'] = $email;
        if (!empty($campaign_id) && $campaign_id != "no") {
            $drip_api_params['double_optin'] = 0;
        }
        $drip_api_params['custom_fields'] = $custom_field;
        return array('subscribers' => array($drip_api_params));
    }

    public static function subscribeDripMail($post_id, $user, $email)
    {
        $provider = get_post_meta($post_id, '_wswebinar_default_mail_provider', true);

        if ($provider == 'drip') {
            $api_key = get_option('_wswebinar_dripapikey');
            $drip_account = get_post_meta($post_id, '_wswebinar_drip_accounts');
            $drip_campaign = get_post_meta($post_id, '_wswebinar_drip_campaigns');
            $account_id = (string) $drip_account[0];
            $campaign_id = (string) $drip_campaign[0];

            $drip_api_params = self::get_create_or_update_drip_api_params($user, $email, $campaign_id);

            $_drip_api = new WP_GetDrip_API(empty($api_key) ? null : $api_key);

            if (!empty($campaign_id) && !empty($account_id) && $campaign_id != "no") {
                $result = $_drip_api->subscribe_to_campaign($account_id, $campaign_id, $drip_api_params);
            } else {
                $result = $_drip_api->create_or_update_subscriber($account_id, $drip_api_params);
            }
        }
    }

    public static function subscribeAweberMail($post_id, $user, $email)
    {
        $default_mail_provider = get_post_meta($post_id, '_wswebinar_default_mail_provider', true);
        $aweber_list = get_post_meta($post_id, '_wswebinar_aweber_list', true);
        if (!empty($aweber_list) & WebinarsysteemMailingListIntegrations::aWeber_Connected() & $default_mail_provider == 'aweber') {
            try {
                $aweber = new WSAWeberAPI(WebinarsysteemMailingListIntegrations::$consumerKey, WebinarsysteemMailingListIntegrations::$consumerSecret);
                $token_secret = get_option('_wswebinar_aweber_accessTokenSecret');
                $token_secret_token = get_option('_wswebinar_aweber_accessToken');
                $account = $aweber->getAccount($token_secret_token, $token_secret);

                $list_id = $aweber_list;
                $listURL = "/accounts/$account->id/lists/$list_id";
                $list = $account->loadFromUrl($listURL);

                $params = array(
                    'email' => $email,
                    'name' => $user,
                );
                $subscribers = $list->subscribers;
                $new_subscriber = $subscribers->create($params);
            } catch (Exception $exc) {
                //print $exc->message;
            }
        }
    }

    public static function saveGetresponseSubscriber($post_id, $user, $email)
    {
        $api_key = get_option('_wswebinar_getresponseapikey');
        $campaign_id = get_post_meta($post_id, '_wswebinar_getresponse_list', true);
        $mail_provider = get_post_meta($post_id, '_wswebinar_default_mail_provider', true);
        if (!empty($api_key) && $mail_provider == 'getresponse' && !empty($campaign_id)) {
            $api = new GetResponse($api_key);
            $expolde_name = self::explodeName($user);
            $fname = $expolde_name['fname'];
            $lname = $expolde_name['lname'];
            $addContact = $api->addContact($campaign_id, $fname . ' ' . $lname, $email, 'insert');
        }
    }

    /**
     * Subscribes a user to ActiveCampaign List.
     *
     * @param int $post_id
     * @param string $name
     * @param string $email
     * @return boolean
     */
    public static function subscribeActiveCampaign($post_id, $name, $email)
    {
        $mail_provider = get_post_meta($post_id, '_wswebinar_default_mail_provider', true);

        if (!WebinarsysteemMailingListIntegrations::isReadyActiveCampaign() || $mail_provider != 'activecampaign') {
            return;
        }

        $list_id = get_post_meta($post_id, '_wswebinar_activecampaign_list', true);
        $api_key = get_option('_wswebinar_activecampaignapikey');
        $url = get_option('_wswebinar_activecampaignurl');
        $expolde_name = self::explodeName($name);
        $fname = $expolde_name['fname'];
        $lname = $expolde_name['lname'];

        $ac = new ActiveCampaign($url, $api_key);
        $subscriber = array(
            "email" => $email,
            "first_name" => $fname,
            "last_name" => $lname,
            "p[$list_id]" => $list_id,
            "status[$list_id]" => 1, // "Active" status
        );
        $contact_sync = $ac->api("contact/sync", $subscriber);
        return $contact_sync->success;
    }

}

<?php

/*
 * 
 * Importing class files
 * 
 */

require 'class-webinarsysteem.php';
require 'class-webinarsysteem-helper-functions.php';
require 'class-webinarsysteem-dbmigrations.php';
require 'class-webinarsysteemoptions.php';
require 'class-webinarsysteemmetabox.php';
require 'class-webinarsysteemhosts.php';
require 'class-webinarsysteemmessages.php';
require 'class-webinarsysteemattendees.php';
require 'class-webinarsysteemviews.php';
require 'class-webinarsysteem-questions.php';
require 'class-webinarsysteemupdates.php';
require 'class-webinarsysteem-ajaxpreviewemails.php';
require 'class-webinarsysteem-subscribe.php';
require 'class-webinarsysteem-ajax.php';
require 'class-webinarsysteem-woocommerce-integration.php';
require 'class-webinarsysteem-mailinglist-integrations.php';
require 'class-webinarsysteem-promotional-notices.php';
require 'class-webinarsysteem-shortcodes.php';
require 'class-webinarsysteem-chatlogs.php';
require 'class-woocommerce-custom-webinar-product.php';
require 'class-webinarsysteem-userpages.php';
require 'class-webinarsysteem-requirements.php';


/*
 * Importing Widget Classes
 */

require 'widgets/class-webinarsysteem-upcoming-widget.php';
require 'widgets/class-webinarsysteem-past-widget.php';

/*
 * 
 * Importing template files
 * 
 */

require 'templates/template-email-header.php';
require 'templates/template-email-footer.php';
require 'templates/template-email-reader.php';
require 'templates/template-email-attendee24hr.php';
require 'templates/template-email-attendee1hr.php';
require 'templates/template-email-attendee-wbstarted.php';
require 'templates/template-email-attendee-replay.php';
require 'templates/template-email-admin-email.php';
require 'templates/template-video-source.php';

/*
 * 
 * Importing library files
 * 
 */

require 'libs/aweber_api/aweber_api.php';

if (!class_exists('EM_Account')) {
    require_once 'libs/enormail/rest.php';
    require_once 'libs/enormail/base.php';
    require_once 'libs/enormail/lists.php';
    require_once 'libs/enormail/account.php';
    require_once 'libs/enormail/contacts.php';
}

if (!class_exists('GetResponse')) {
    require_once ('libs/Getresponse/GetResponseAPI.class.php');
    require_once ('libs/Getresponse/jsonRPCClient.php');
}

if (!class_exists('Mailchimp'))
    require_once 'libs/Mailchimp.php';

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (is_plugin_active('mailpoet/mailpoet.php') && !class_exists('MailPoet\API\API'))
    require_once plugin_dir_path(__FILE__) . '../../mailpoet/mailpoet.php';

if (!class_exists('ActiveCampaign'))
    require_once 'libs/activecampaign/ActiveCampaign.class.php';

if (!class_exists('WP_GetDrip_API')) {
    require_once 'libs/Drip/class-wp-getdrip-api.php';
}


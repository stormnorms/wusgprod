<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template-email-attendee-replay
 *
 * @author Thambaru Wijesekara
 */
class WsWebinarTemplate_AttendeeReplay {

    public static function get($inputName, $post_id) {

        $paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';
        $getRequest = WebinarSysteemWooCommerceIntegration::generateUniqueMailURL();
        ?>

        <p><?php _e('Hi', WebinarSysteem::$lang_slug) ?> <?php echo $inputName ?>,</p>
        <p style="margin:25px 0px;"><?php _e('Make sure to join the webinar via this link:', WebinarSysteem::$lang_slug) ?></p>
        <p><a style="background-color: green; border-radius: 3px; border: 1px solid transparent; padding: 3px 20px; text-decoration: none;  color:white;" href="<?php echo get_permalink($post_id, false) . ($paidWebinar ? $getRequest : '' ); ?>"><?php _e('Go to Webinar', WebinarSysteem::$lang_slug) ?></a></p>
        <p><?php _e('See you later!', WebinarSysteem::$lang_slug) ?></p>
        <p><?php _e('Regards', WebinarSysteem::$lang_slug) ?>,</p>
        <p><i><?php get_bloginfo('name'); ?></i></p>
        <?php
    }

}

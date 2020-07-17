<?php
if (!WebinarSysteemWooCommerceIntegration::isReady())
    return printf(__('This section is to issue tickets for webinars. Enable WooCommerce Integration from <a href="%s" target="_blank">WebinarSystem Settings</a> to use this feature.', WebinarSysteem::$lang_slug), admin_url('edit.php?post_type=wswebinars&page=wswbn-options'));

if (!WebinarSysteemWooCommerceIntegration::isWCready())
    return printf(__('Please install/activate WooCommerce first to integrate with WebinarSystem. You can find WooCommerce <a href="%s" class="thickbox" aria-label="Download WooCommerce for WebinarSystem" data-title="WooCommerce">here</a>.', WebinarSysteem::$lang_slug), admin_url("plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true"));

$ticketId = get_post_meta($post->ID, '_wswebinar_ticket_id', true);
$ticketExists = get_post_status($ticketId) != FALSE;
$webinarMetaBoxTicketPrice = get_post_meta($post->ID, '_wswebinar_ticket_price', true);
?>
<div id="ticketp-accordian" class="ws-accordian">
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Ticket details', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-field">
            <label for="ticket_wbnpaid_yn"><?php _e('Paid webinar?', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" data-switch="true" type="checkbox" name="ticket_wbnpaid_yn" id="ticket_wbnpaid_yn" <?php echo (get_post_meta($post->ID, '_wswebinar_ticket_wbnpaid_yn', true) == "on" ) ? 'checked' : ''; ?> data-on-text="Yes" data-off-text="No">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="ticketp_buytitle"><?php _e('Buying Form Title', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="ticketp_buyformtitle" id="ticketp_buyformtitle" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_ticketp_buyformtitle', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="ticketp_buyformtxt"><?php _e('Buying Form Description', WebinarSysteem::$lang_slug); ?></label>
            <textarea data-style-collect="true" name="ticketp_buyformtxt" id="ticketp_buyformtxt"><?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_ticketp_buyformtxt', true)); ?></textarea>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="ticket_price"><?php _e('Price', WebinarSysteem::$lang_slug); ?>*</label>
            <input type="text" name="ticket_price" id="ticket_regular_price" value="<?php
            if ($ticketExists) {
                echo get_post_meta($ticketId, '_price', true);
            } elseif (empty($webinarMetaBoxTicketPrice)) {
                echo 0;
            } else {
                echo esc_attr($webinarMetaBoxTicketPrice);
            }
            ?>">
            <p class="description"><?php _e('Required', WebinarSysteem::$lang_slug); ?></p>
            <div class="webinar_clear_fix"></div>
        </div>

        <div class="form-field">
            <label for="ticket_title"><?php _e('Ticket Title', WebinarSysteem::$lang_slug); ?></label>
            <input type="text" name="ticket_title" id="ticket_title" value="<?php echo $ticketExists ? get_post($ticketId)->post_title : esc_attr(get_post_meta($post->ID, '_wswebinar_ticket_title', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="ticket_description"><?php _e('Ticket Description', WebinarSysteem::$lang_slug); ?></label>
            <?php
            $meta = $ticketExists ? get_post($ticketId)->post_content : get_post_meta($post->ID, '_wswebinar_ticket_description', true);
            $content = apply_filters('meta_content', $meta);
            wp_editor($content, 'ticket_description');
            ?>
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
    <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Style', WebinarSysteem::$lang_slug) ?></h3>
    <div class="ws-accordian-section">
        <div class="form-field">
            <label for="ticketp_bckg_clr"><?php _e('Background color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="ticketp_bckg_clr" class="color-field" id="ticketp_bckg_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_ticketp_bckg_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="ticketp_border_clr"><?php _e('Border color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="ticketp_border_clr" class="color-field" id="ticketp_border_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_ticketp_border_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
        <div class="form-field">
            <label for="ticketp_formfont_clr"><?php _e('Text color', WebinarSysteem::$lang_slug); ?></label>
            <input data-style-collect="true" type="text" name="ticketp_font_clr" class="color-field" id="ticketp_formfont_clr" value="<?php echo esc_attr(get_post_meta($post->ID, '_wswebinar_ticketp_font_clr', true)); ?>">
            <div class="webinar_clear_fix"></div>
        </div>
    </div>
</div>

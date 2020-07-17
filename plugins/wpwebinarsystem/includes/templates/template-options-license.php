<input type="hidden" name="wpws_edd_license_activate" value="true">
<?php wp_nonce_field('wsweb_license_nonce', 'wsweb_active'); ?>
<table class="form-table">
    <?php
    $blogs = WebinarSystemUpdate::get_site_list();
    $current_site = WebinarSystemUpdate::get_current_site();
    if (is_multisite() && count($blogs) > 0) {
	?>
        <tr>
    	<th>
    	    <label><?php _e('Activate for', WebinarSysteem::$lang_slug) ?></label>
    	</th>
    	<td>
    	    <select name="wpws_edd_license_url">
    		<option value="<?php echo $current_site->domain ?>"><?php printf(__('This domain (%s)', WebinarSysteem::$lang_slug), $current_site->domain) ?></option>
		    <?php foreach ($blogs as $blog): if ($blog['domain'] == $current_site->domain) continue; ?>
			<option value="<?php echo $blog['domain'] ?>"><?php echo $blog['domain'] ?></option>
		    <?php endforeach; ?>
    	    </select>
    	    <p class="description"><?php _e('If you have an Agency license, it will be applied for all websites.', WebinarSysteem::$lang_slug) ?></p>
    	</td>
        </tr>
    <?php } ?>
    <tr>
        <th><label><?php _e('License Key', WebinarSysteem::$lang_slug) ?></label></th>
        <td>
            <input type="text" class="regular-text" name="<?php if (is_multisite()) { ?>wbn_network_lkey<?php } else { ?>_wswebinar_licensekey<?php } ?>" value="<?php echo WebinarSystemUpdate::get_license(); ?>">

            <a href="<?php echo $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') !== false ? '&' : '?') ?>ws_webinar_flush_license_data=y" class="button"><?php _e('Flush data (Recheck)', WebinarSysteem::$lang_slug); ?></a>
	    <?php
	    if (WebinarSystemUpdate::is_license_active())
		echo '<p class="description">' . __('Your copy of WebinarSysteem is Active.', parent::$lang_slug) . '</p>';
	    else
		echo '<p class="description">' . __('Your copy of WebinarSysteem is not activated. Plugin will not work as expected. Please activate your copy.', parent::$lang_slug) . '</p>';
	    ?>
        </td>
    </tr>
    <?php if (is_multisite()) { ?>
        <tr>
    	<th><?php _e('Activated sites', WebinarSysteem::$lang_slug) ?></th>
    	<td>
    	    <table>
		    <?php
		    $i = 0;
		    foreach ($blogs as $blog):
			$licensekey = get_blog_option($blog['blog_id'], '_wswebinar_licensekey');
			if (empty($licensekey))
			    continue;
			echo "<tr><td><a href='//{$blog['domain']}' target='_blank'>{$blog['domain']}</a></td></tr>";
			$i++;
		    endforeach;
		    ?>
    	    </table>
		<?php
		if ($i < 1)
		    echo '<p class="description">No sites activated.</p>';
		?>
    	</td>
        </tr>
    <?php } ?>
    <?php if (WebinarSystemUpdate::is_license_active()) { ?>
        <tr>
    	<th>
    	    <label><?php _e('Deactivate license', WebinarSysteem::$lang_slug) ?></label>
    	</th>
    	<td>
    	    <a href="<?php echo $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') !== false ? '&' : '?') ?>edd_license_deactivate=y" class="button btn-secondary">Deactivate</a>
    	</td>
        </tr>
    <?php } ?>
</table>
<?php $visible_for = get_post_meta($post->ID,'_wswebinar_accesstab_parent',true); ?>
<div class="form-field-2">
    <label for="_wswebinar_mailinglist_provider_selector" ><?php _e('Make Webinar only accessible for : ', WebinarSysteem::$lang_slug); ?> </label>
    <select class="regular-text" id="_wswebinar_accesstab_parent" name="accesstab_parent">
        <option <?php echo ($visible_for == 'everyone' ? 'selected' : ''); ?> value="everyone">Everyone (Default)</option>
        <option <?php echo ($visible_for == 'user_roles' ? 'selected' : ''); ?> value="user_roles">User Roles</option>
        <option <?php echo ($visible_for == 'member_levels' ? 'selected' : ''); ?> value="member_levels">Member levels</option>
        <option <?php echo ($visible_for == 'user_ids' ? 'selected' : ''); ?> value="user_ids">User ID's</option>
    </select>
</div>
<?php
    global $wp_roles;
    $roles = $wp_roles->get_names();
    $users = get_users(array('order' => 'ASC'));
?>

<div id="_wswebinar_accesstab_show_child">
    
    <div id="everyone" style="display: <?php echo ($visible_for == 'everyone' ? 'block;' : 'none;'); ?>">
        <p>This webinar will show for everyone.</p>
    </div>
    
    <div id="user_roles" class="hide-on-create" style="display: <?php echo ($visible_for == 'user_roles' ? 'block;' : 'none;'); ?>">
        <div id="acctab-accordian" class="ws-accordian">
            <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Only for user roles', WebinarSysteem::$lang_slug) ?></h3>
            <div class="ws-accordian-section">
                    <?php 
                        $selected_vals_str = get_post_meta($post->ID, '_wswebinar_selected_user_role',true);
                        $slected_vals = explode(',', $selected_vals_str);
                        foreach ($roles as $roleSlug => $roleName) {
                    ?>
                        <br><label for="ac_role_<?php echo $roleSlug; ?>">
                            <input class="ws_acc_selrole" id="ac_role_<?php echo $roleSlug; ?>" type="checkbox" name="selected_rols" <?php echo (in_array($roleSlug, $slected_vals) ? 'checked' : '');?> value="<?php echo $roleSlug; ?>">
                        <?php echo $roleName; ?>
                        </label>
                            <?php
                        }
                    ?>
                    <div class="webinar_clear_fix"></div>
                    <input type="hidden" name="selected_user_role" value="<?php echo $selected_vals_str; ?>">
            </div>
        </div>
    </div>
    
    <div id="member_levels" class="hide-on-create" style="display: <?php echo ($visible_for == 'member_levels' ? 'block;' : 'none;'); ?>">
        <div id="acctab-accordian-memlevels" class="ws-accordian">
            <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Member Levels', WebinarSysteem::$lang_slug) ?></h3>
            <div class="ws-accordian-section">
                <div class="form-field">
                    <?php if(function_exists('wc_memberships')){ ?>
                    <label for="member_level"><?php _e('Member Level', WebinarSysteem::$lang_slug); ?></label>
                    <select data-style-collect="true" id="member_level" name="selected_member_level" id="member_level" >
                        <option><?php echo _e("Select",  WebinarSysteem::$lang_slug); ?></option>
                    <?php 
                    $saved_membership = get_post_meta($post->ID, '_wswebinar_selected_member_level',true);
                    $membership_plans_wp = wc_memberships_get_membership_plans();
                    foreach($membership_plans_wp as $membership){ ?>
                        <option <?php echo ($membership->id == $saved_membership ? 'selected' : ''); ?> value="<?php echo $membership->id; ?>" ><?php echo $membership->name; ?></option>
                    <?php } ?>
                    </select>
                    <div class="webinar_clear_fix"></div>
                    <?php }else{ ?>
                    <p><?php echo _e("Please install and configure the 'WooCommerce Memberships' plugin to use this functionality.",WebinarSysteem::$lang_slug); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    
    <div id="user_ids" class="hide-on-create" style="display: <?php echo ($visible_for == 'user_ids' ? 'block;' : 'none;'); ?>">
        <div id="acctab-accordian-userids" class="ws-accordian">
            <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('User Id\'s', WebinarSysteem::$lang_slug) ?></h3>
            <div class="ws-accordian-section">
                <table class="user-roles-table">
                    <thead>
                        <tr>
                        <th>User ID</th>
                        <th>Role</th>
                        <th>User Name</th>
                        <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                            <tr>
                                <td><?php echo $user->ID; ?></td>
                                <td><?php 
                                foreach($user->roles as $role){
                                    echo $role.' ';
                                }
                                ?></td>
                                <td><?php echo $user->display_name; ?></td>
                                <td><?php echo $user->user_email; ?></td>
                            </tr>                   
                        <?php } ?>
                    </tbody>
                </table>
                <div class="form-field">
                    <?php $user_ids = get_post_meta($post->ID, '_wswebinar_filter_user_ids',true); ?>
                    <label for="filter_user_ids"><?php _e('User Id\'s', WebinarSysteem::$lang_slug); ?></label>
                    <input value="<?php echo (empty($user_ids) ? '' : $user_ids); ?>" type="text" data-style-collect="true" id="filter_user_ids" name="filter_user_ids">
                    <p class="description"><?php _e('Enter user ID. (You can add multiple user ID\'s. ID separated by comma).', WebinarSysteem::$lang_slug) ?></p>
                    <div class="webinar_clear_fix"></div>
                </div>
                
            </div>
        </div>
    </div>
    
  <div id="redirect_action_accordian" class="hide-on-create" style="display: <?php echo ($visible_for == 'everyone' ? 'none;' : 'block;'); ?>">
      <div id="acctab-accordian" class="ws-accordian">
          <h3 class="ws-accordian-title"><i class="wbn-icon wbnicon-play ws-accordian-icon"></i> <?php _e('Redirect', WebinarSysteem::$lang_slug) ?></h3>
          <div class="ws-accordian-section">    
              Redirect visitors without access to page.
              <div class="ui-widget form-field" id="selector-widget">
                  <?php $selected_page = get_post_meta($post->ID, '_wswebinar_ws_actab_redirect_page', true); ?>
                  <label for="pages">Select Post: </label>
                  <select id="pages" class="chosen-select slected-page" name="ws_actab_redirect_page" data-placeholder="Choose a Post...">
                      <?php
                      $pages = get_pages();
                      foreach ($pages as $page) {
                          echo '<option '.($selected_page == $page->ID ? 'selected' : '').' value="'.$page->ID.'">'.$page->post_title . '</option>';
                      }
                      ?>
                  </select>
                  <div class="webinar_clear_fix"></div>
              </div>
          </div>
      </div>
  </div>
</div>

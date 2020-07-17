<?php
/*
 * @package WebinarSysteem
 */

class WebinarSysteem {

    public $_FILE_, $post_slug, $localkey_status, $plugin_version, $db_tablename_questions, $db_tablename_chats, $db_tablename_subscribers, $db_version;
    public static $lang_slug;

    /*
     * 
     * Don't edit, remove or comment anything in this file if you are not sure what you are doing. 
     * It will cause to break the plugin or even the Wordpress website.
     * 
     */

    public function __construct($theFile = null, $version = null) {

	$this->_FILE_ = $theFile;
	$this->plugin_version = $version;
	$this->setAttributes($theFile);
	//$this->webinarSysteemVerify();	

	add_action('init', array($this, 'registerWebinars'));
	add_action('init', array('WebinarSysteemUserPages', 'init'));
	register_activation_hook($this->_FILE_, array($this, 'install'));
	add_action( 'wp_before_admin_bar_render', array($this, 'wpws_admin_bar_render') );
	add_filter("manage_{$this->post_slug}_posts_columns", array($this, 'webinarBrowseColumns'));
	add_action("manage_{$this->post_slug}_posts_custom_column", array($this, 'webinarBrowseCustomColumns'), 10, 2);
	add_action('init', array($this, 'webinarSysteemVerify'));

	add_action('init', array('WebinarSysteemAttendees', 'createCsvFile'));
	add_action('init', array('WebinarSysteemAttendees', 'createBccFile'));
	add_action('init', array($this, 'init_thickbox'));
	add_action('admin_init', array($this, 'flushLocalKeyData'));
	add_action('init', array($this, 'databaseMigrations'));
	add_action('init', array($this, 'flushLocalKeyData'));
	add_action('init', array('WebinarsysteemMailingListIntegrations', 'aweber_connect'));
	add_action('init', array(new WebinarSysteemAjax(), 'dismissAdminNotices'));

	add_action('admin_menu', array($this, 'webinar_menu'));
	//add_action('network_admin_menu', array($this, 'network_menu'));
	add_action("template_include", array($this, 'myThemeRedirect'));
	add_action("template_include", array($this, 'registrationFormSubmit'));
	//add_action("init", array($this, 'logoutWebinars'));
	add_action('admin_enqueue_scripts', array($this, 'load_plugin_scripts'));

	add_action('wp_enqueue_scripts', array($this, 'load_front_end_scripts'), 1000);
	add_action('wp_enqueue_scripts', array($this, 'load_page_scripts'), 1100);
	add_action('wp_enqueue_scripts', array($this, 'deregisterCurrentThemeScripts'), 5000);

	add_action('admin_init', array($this, 'registerOptions'));
	add_action('admin_init', array($this, 'setPermissions'));
	add_action('admin_init', array($this, 'resetInvalidKeyProperty'));
	add_action('admin_init', array(new WebinarSysteemAttendees(), 'saveNewAttendee'));
	//add_action('admin_init', array('WebinarSysteemOptions', 'wbn_network_setting_save'));
	add_action('wp_footer', array($this, 'liveControlBar'));

	add_action('wp_ajax_saveQuestionAjax', array($this, 'saveQuestionAjax'));
	add_action('wp_ajax_nopriv_saveQuestionAjax', array($this, 'saveQuestionAjax'));
	add_action('wp_ajax_sendChat', array($this, 'sendChat'));
	add_action('wp_ajax_nopriv_sendChat', array($this, 'sendChat'));
	add_action('wp_ajax_setEnabledChats', array($this, 'setEnabledChats'));
	add_action('wp_ajax_nopriv_setEnabledChats', array($this, 'setEnabledChats'));
	add_action('wp_ajax_setEnabledQuestions', array($this, 'setEnabledQuestions'));
	add_action('wp_ajax_nopriv_setEnabledQuestions', array($this, 'setEnabledQuestions'));
	add_action('wp_ajax_retrieveQuestions', array(new WebinarSysteemQuestions(), 'retrieveQuestions'));
	add_action('wp_ajax_quickchangestatus', array($this, 'quickchangestatus'));
	add_action('wp_ajax_previewemails', array('WebinarSysteemPreviewMails', 'previewMails'));
	add_action('wp_ajax_remove_attendee', array('WebinarSysteemAttendees', 'removeAttendee'));
	add_action('wp_ajax_checkWebinarStatus', array($this, 'ajaxCheckIfWebinarStatusLive'));
	add_action('wp_ajax_nopriv_checkWebinarStatus', array($this, 'ajaxCheckIfWebinarStatusLive'));
	add_action('wp_ajax_transferLivepData', array('WebinarSysteemAjax', 'transferLivepData'));
	add_action('wp_ajax_nopriv_transferLivepData', array('WebinarSysteemAjax', 'transferLivepData'));
	add_action('wp_ajax_nopriv_raiseHand', array('WebinarSysteemAjax', 'raiseHand'));
	add_action('wp_ajax_raiseHand', array('WebinarSysteemAjax', 'raiseHand'));
	add_action('wp_ajax_unraiseHands', array('WebinarSysteemAjax', 'unraiseHands'));
	add_action('wp_ajax_nopriv_unraiseHands', array('WebinarSysteemAjax', 'unraiseHands'));
	add_action('wp_ajax_syncImportImgs', array('WebinarSysteemAjax', 'syncImportImgs'));
	add_action('wp_ajax_checkEnomailAPIkey', array('WebinarsysteemMailingListIntegrations', 'checkEnomailAPIkey'));
	add_action('wp_ajax_checkDripAPIkey', array('WebinarsysteemMailingListIntegrations', 'checkDripAPIkey'));
	add_action('wp_ajax_getDripCampaigns', array('WebinarsysteemMailingListIntegrations', 'getDripCampaigns'));
	add_action('wp_ajax_nopriv_getDripCampaigns', array('WebinarsysteemMailingListIntegrations', 'getDripCampaigns'));
	add_action('wp_ajax_revokeAweberConfig', array('WebinarsysteemMailingListIntegrations', 'revokeAweberConfig'));
	add_action('wp_ajax_checkGetresponse_apikey', array('WebinarsysteemMailingListIntegrations', 'checkGetresponse_apikey'));
	add_action('wp_ajax_updateIncentive', array('WebinarSysteemAjax', 'updateIncentive'));
	add_action('wp_ajax_showCTA', array('WebinarSysteemAjax', 'setCTAStatus'));
	add_action('wp_ajax_nopriv_showCTA', array('WebinarSysteemAjax', 'setCTAStatus'));
	add_action('wp_ajax_hostdescBoxes', array('WebinarSysteemAjax', 'setHostUpdateBox'));
	add_action('wp_ajax_nopriv_hostdescBoxes', array('WebinarSysteemAjax', 'setHostUpdateBox'));
	add_action('wp_ajax_actionBoxStatus', array('WebinarSysteemAjax', 'setActionbox'));
	add_action('wp_ajax_nopriv_actionBoxStatus', array('WebinarSysteemAjax', 'setActionbox'));
	add_action('wp_ajax_deleteChats', array(new WebinarSysteemAjax(), 'deleteChats'));
	add_action('wp_ajax_deleteQuestions', array(new WebinarSysteemAjax(), 'deleteQuestions'));
	add_action('wp_ajax_checkActiveCampaign_apicredentials', array('WebinarsysteemMailingListIntegrations', 'ajaxIsValidActiveCampaignCredentials'));
	
	add_action('wp_ajax_getInputTimes', array('WebinarSysteemAjax', 'getInputTimes'));
	add_action('wp_ajax_nopriv_getInputTimes', array('WebinarSysteemAjax', 'getInputTimes'));

	add_action('wp_ajax_newAattendeeCSV', array(new WebinarSysteemAjax(), 'newAattendeeCSV'));

	add_action('admin_head', array($this, 'webinarsysteem_ajaxurl'));
	add_action('wp_head', array($this, 'webinarsysteem_ajaxurl'));
	add_filter('meta_content', 'wptexturize');
	add_filter('meta_content', 'convert_smilies');
	add_filter('meta_content', 'convert_chars');
	add_filter('meta_content', 'wpautop');
	add_filter('meta_content', 'shortcode_unautop');
	add_filter('meta_content', 'prepend_attachment');

	add_action('after_setup_theme', array($this, 'load_languages'));
	add_action('admin_init', array($this, 'addDeleteWebinarHook'));
	add_action('admin_init', array($this, 'assignAdminNotices'));

	new WebinarSysteemMetabox($this->_FILE_, $this->post_slug);

	add_action('admin_action_wswebinar_duplicate_post_as_draft', array($this, 'wswebinar_duplicate_post_as_draft'));
	add_filter('post_row_actions', array($this, 'postRow'), 10, 2);

	new WebinarSysteemMails;
	register_activation_hook($this->_FILE_, array($this, 'setDefaultMailTemplates'));

	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'invalid_mailchimp_key'));
	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'invalid_enormail_key'));
	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'invalid_drip_key'));
	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'check_aweber_disconnected'));
	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'checkGetresponseAPIKeyShowError'));
	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'check_aweber_connected'));
	add_action('admin_notices', array('WebinarsysteemMailingListIntegrations', 'showActiveCampaignNotice'));
	add_action('admin_notices', array('WebinarSysteemWooCommerceIntegration', 'noWCNotice'));
	add_action('admin_notices', array($this, 'postNotices'));
	add_action('admin_notices', array($this, 'check_mysql_and_php_version'));
	
	add_action('admin_init', array($this, 'wpws_plugin_notice_ignore'));

	add_action('admin_bar_init', array($this, 'my_admin_bar_init'));
	
	register_activation_hook($this->_FILE_, array($this, 'createRoles'));
	register_deactivation_hook($this->_FILE_, array($this, 'purgeRoles'));

	if (self::isWebinarPage())
	    add_action('admin_footer_text', array('WebinarSysteemPromotionalNotices', 'footerRating'));

	//WooCommerce Integration
	if (WebinarSysteemWooCommerceIntegration::isReady()) {
		// add_action('woocommerce_order_status_completed', array('WebinarSysteemWooCommerceIntegration', 'on_woo_order_status_completed'));
		add_action('woocommerce_order_status_changed', array('WebinarSysteemWooCommerceIntegration', 'on_woo_commerce_order_status_changed'), 99, 3);
	    add_action('woocommerce_product_options_general_product_data', array('WebinarSysteemWooCommerceIntegration', 'addWebinarIDField'));
	    add_action('woocommerce_thankyou', array('WebinarSysteemWooCommerceIntegration', 'joinTheWebinarOrderConfirmation'));
	    add_action('woocommerce_before_my_account', array('WebinarSysteemWooCommerceIntegration', 'showTicketListOnDashboard'));
	    add_filter('product_type_selector', array('WebinarSysteemWooCommerceIntegration', 'add_simple_webinar_product'));
	    add_action('woocommerce_before_add_to_cart_button', array('WebinarSysteemWooCommerceIntegration', 'add_webinar_recurring_fields'));
	    add_filter('woocommerce_add_to_cart_validation', array('WebinarSysteemWooCommerceIntegration', 'webinar_fields_validation'), 10, 3);
	    add_action('woocommerce_add_cart_item_data', array('WebinarSysteemWooCommerceIntegration', 'save_webinar_fields'), 10, 2);
	    add_action('woocommerce_new_order_item', array('WebinarSysteemWooCommerceIntegration', 'custom_order_meta_handler'), 10, 3);
	    add_action('woocommerce_email_before_order_table', array('WebinarSysteemWooCommerceIntegration', 'hide_custom_fields_in_order_email'), 10, 2);
	    add_action('woocommerce_after_order_notes', array('WebinarSysteemWooCommerceIntegration', 'customise_checkout_field'));
	    add_action('woocommerce_checkout_process', array('WebinarSysteemWooCommerceIntegration', 'customise_checkout_field_process'));
	}


	add_action('admin_init', array($this, 'run_updates'), 0);
	add_action('admin_init', array($this, 'activate_license'));
	add_action('admin_init', array($this, 'deactivate_license'));

	add_action('widgets_init', create_function('', 'return register_widget("WebinarSysteemUpcomingWebinars");'));
	add_action('widgets_init', create_function('', 'return register_widget("WebinarSysteemPastWebinars");'));

	if (!empty($_GET['webinar_login_redirect']))
	    add_filter('login_message', array($this, 'my_login_logo_url_title'));

	add_filter('option_active_plugins', array($this, 'webinar_exclude_plugins'));
	add_filter( 'option_page_capability_wswebinar_options', array( $this, 'wswebinar_options_page_capability'));

	new WebinarSysteemShortCodes;
	/*
	 *
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'add_dashboard_setup' action below.
	 */

	add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
	
	/* Webinar User Profile Hooks */
	add_action('template_redirect', array('WebinarSysteemUserPages', 'userProfile'));
	add_filter('the_content', array('WebinarSysteemUserPages', 'setPageContent'),10,1);
	add_action('wp_ajax_unSubscribeAttendee', array('WebinarSysteemUserPages', 'unSubscribeAttendee'));
	add_action('wp_ajax_nopriv_unSubscribeAttendee', array('WebinarSysteemUserPages', 'unSubscribeAttendee'));
	
	/* Adjust attendees to inactive state for those who didn't attend the webinar after it is finished for Recurring and JIT webianr type*/
	add_action('wp', array('WebinarSysteemAttendees', 'updateAttendees'));
	add_action('attendeecronjob', array('WebinarSysteemAttendees','makeAttendeesInactive'));
    }
    
    

    /*
     *
     * Function to register a Widget with Wordpress
     *
     */

    public function add_dashboard_widget() {
	add_meta_box('idx_dashboard_widget', 'WP WebinarSystem', array($this, 'compile_dashboard_widget'), 'dashboard', 'normal', 'low');
    }

    /*
     *
     * Function to output the contents of our Dashboard Widget.
     *
     */

    public function compile_dashboard_widget() {
	echo $this->dashboard_widget_html();
    }

    public function dashboard_widget_html() {
	$count = 1;
	$output = '';
	$results = $this->getWpWebinarSystemData();

	if ($results) {
	    $output .= '<div class="table-responsive ws-dashboard-widget">';
	    $output .= '<table class="table text-center">';
	    $output .= '<thead>';
	    $output .= '<tr>';
	    $output .= '<th>#</th>';
	    $output .= '<th>' . __('Webinar Title', self::$lang_slug) . '</th>';
	    $output .= '<th>' . __('Views', self::$lang_slug) . '</th>';
	    $output .= '<th>' . __('Registrations', self::$lang_slug) . '</th>';
	    $output .= '<th>' . __('Questions', self::$lang_slug) . '</th>';
	    $output .= '</tr>';
	    $output .= '</thead>';
	    $output .= '<tbody>';

	    foreach ($results as $index => $post) {
		$post_id = $post['ID'];

		$views = get_post_meta($post_id, '_wswebinar_views', true);

		$subs = WebinarSysteemAttendees::getNumberOfSubscriptions($post_id);

		$questions = new WebinarSysteemQuestions;
		$questionsData = $questions->getQuestionsFromDb($post_id);
		$noOfQuestions = $questionsData['num_of_rows'];

		$output .= '<tr>';
		$output .= '<td>';
		$output .= $count++;
		$output .= '</td>';
		$output .= '<td>';
		$output .= '<a href="post.php?post=' . $post_id . '&action=edit">' . $post['post_title'] . "</a>";
		$output .= '</td>';
		$output .= '<td>';
		$output .= empty($views) ? '-' : (int) $views;
		$output .= '</td>';
		$output .= '<td>';
		$output .= empty($subs) ? '-' : '<a href="edit.php?post_type=wswebinars&page=wswbn-attendees&id=' . $post_id . '">' . $subs . "</a>";
		$output .= '</td>';
		$output .= '<td>';
		$output .= empty($noOfQuestions) ? '-' : '<a href="edit.php?post_type=wswebinars&page=wswbn-questions&webinar_id=' . $post_id . '">'
			. $noOfQuestions . "</a>";
		$output .= '</td>';
		$output .= '</tr>';
	    }
	    $output .= '</tbody>';
	    $output .= '</table>';
	    $output .= '</div>';
	} else {
	    $output .= '<p>No Webinars Created! <a class="create-webinar-button" href="post-new.php?post_type=wswebinars" role="button">'
		    . __('Create Now', self::$lang_slug) . '</a>';
	}



	return $output;
    }

    /*
     *
     * Function o fetch the 5 latest Webinars
     *
     */

    public function getWpWebinarSystemData() {
	global $wpdb;
	$custom_post_type = 'wswebinars';

	$webinarSstemResults = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status 			= 'publish' ORDER BY post_date DESC LIMIT 5", $custom_post_type), ARRAY_A);

	return $webinarSstemResults;
    }

    public function init_thickbox() {
	add_thickbox();
    }

    //oauth_token
    public function quickchangestatus() {

	$webinar_id = (int) $_POST['webinar_id'];
	$stat = $_POST['status'];
	if (empty($stat))
	    die();
	update_post_meta($webinar_id, '_wswebinar_gener_webinar_status', $stat);
	echo json_encode(array('status' => TRUE, 'updated' => $stat));
	die();
    }

    /*
     * 
     * Define ajax url for ajax requests.
     * 
     */

    public function webinarsysteem_ajaxurl() {
	?>
	<script type="text/javascript">
	    var wpws_ajaxurl = '<?php echo plugin_dir_url(__FILE__) . 'core-requesthandler.php'; ?>';
	</script>
	<?php
    }

    public function resetInvalidKeyProperty() {
	if (!isset($_GET['settings-updated']) || !$_GET['settings-updated'] || empty($_GET['page']) || $_GET['page'] !== 'wswbn-options' || $_GET['post_type'] !== $this->post_slug) {
	    return;
	}
	update_option('_wswebinar_invalid_key', '0');
    }

    /*
     * 
     * Check webinar status via AJAX
     * 
     */

    public function ajaxCheckIfWebinarStatusLive() {

        if (empty($_POST['post_id'])) {
            echo json_encode(FALSE);
            die();
        }

        $post_id = $_POST['post_id'];

        $is_recur = WebinarSysteem::isRecurring($post_id);
        $_wswebinar_gener_duration = self::getWebinarDuration($post_id);

        $attendee = WebinarSysteemAttendees::getAttendee($post_id);
        $webiner_t = WebinarSysteem::getWebinarTime($post_id, $attendee);

        if ($is_recur) {
            if ($webiner_t <= WebinarSysteem::populateDateTime($post_id) && WebinarSysteem::populateDateTime($post_id) <= ($webiner_t + $_wswebinar_gener_duration)) {
                echo json_encode(TRUE);
            } else {
                echo json_encode(FALSE);
            }
            die();
        }

        if (self::webinarAirType($post_id) == 'rec' && ($webiner_t <= WebinarSysteem::populateDateTime($post_id) && WebinarSysteem::populateDateTime($post_id) <= ($webiner_t + $_wswebinar_gener_duration))) {
            echo json_encode(TRUE);
            die();
        }

        $wbstatus = get_post_meta($post_id, '_wswebinar_gener_webinar_status', true);

        if ($wbstatus == 'liv') {
            echo json_encode(TRUE);
        } else {
            echo json_encode(FALSE);
        }

        wp_die();
    }

    /*
     * 
     * Run migrations
     * 
     */

    public function databaseMigrations() {
        $db = new WebinarsysteemDbMigrations();
        $db->runMigrations();
    }

    /*
     * 
     * Load language files
     * 
     */

    public function load_languages() {
        load_plugin_textdomain(self::$lang_slug, false, dirname(plugin_basename($this->_FILE_)) . '/localization/');
    }

    /*
     * 
     * Adds webinarDelete function to the delete_post hook if current use have rights.
     * 
     */

    public function addDeleteWebinarHook() {
        if (current_user_can('delete_posts'))
            add_action('delete_post', array($this, 'webinarDelete'), 10);
    }

    /*
     * 
     * Deleting questions that belongs to the deleted webinar.
     * 
     */

    public function webinarDelete($pid) {
        if (get_post_type($pid) !== $this->post_slug)
            return;

        global $wpdb;
        $tabl = $wpdb->prefix . $this->db_tablename_questions;
        if ($wpdb->get_var($wpdb->prepare('SELECT webinar_id FROM ' . $tabl . ' WHERE webinar_id = %d', $pid))) {
            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $tabl . ' WHERE webinar_id = %d', $pid));
        }

        return true;
    }

    /*
     * 
     * Control the ajax request of adding question
     * 
     */

    public function saveQuestionAjax() {
        header("content-type: application/javascript; charset=utf-8");
        global $wpdb;
        $table_name = $wpdb->prefix . $this->db_tablename_questions;
        $timestamp = $this->populateDateTime($_GET['webinar_id']);
        $num = $wpdb->insert(
            $table_name, array(
            'name' => $_GET['name'],
            'email' => $_GET['email'],
            'question' => $_GET['question'],
            'time' => date('y-m-d h:i-s', $timestamp),
            'webinar_id' => $_GET['webinar_id'],
            )
        );

        if ($num == 1) {
            $mail = new WebinarSysteemMails();
            $mail->SendQuestionToHost($_GET['name'], $_GET['webinar_id'], $_GET['email'], $_GET['question']);
            echo $_GET['callback'] . '(' . json_encode(array('status' => TRUE, 'question' => str_replace("\\", '', $_GET['question']), 'time' => date("Y-m-d H:i A", WebinarSysteem::populateDateTime($_GET['webinar_id'])))) . ')';
        } else {
            echo htmlspecialchars($_GET['callback']) . '(' . json_encode(array('status' => FALSE)) . ')';
        }
        die();
    }

    public function flushLocalKeyData() {
        if (empty($_GET['ws_webinar_flush_license_data']) || $_GET['ws_webinar_flush_license_data'] !== 'y') {
            return;
        }

        if (is_multisite()) {
            delete_option('_wswebinar_licensekey_network');
            delete_option('edd_sample_license_status');
            $blogs = WebinarSystemUpdate::get_site_list();

            foreach ($blogs as $blog) {
                delete_blog_option($blog['blog_id'], '_wswebinar_localkey');
                delete_blog_option($blog['blog_id'], '_wswebinar_licensekey');
                update_blog_option($blog['blog_id'], '_wswebinar_invalid_key', '0');
            }
        } else {
            delete_option('_wswebinar_localkey');
            delete_option('edd_sample_license_status');
            delete_option('_wswebinar_licensekey');
            delete_option('edd_sample_license_activations_left');
        }

        wp_redirect(remove_query_arg('ws_webinar_flush_license_data'));
        exit;
    }

    /*
     * 
     * Load admin scripts
     * 
     */

    public function load_plugin_scripts() {

        wp_enqueue_style('webinar-admin', plugin_dir_url($this->_FILE_) . 'includes/css/webinar-admin.css');
        wp_enqueue_style('webinar-admin-fonts', plugin_dir_url($this->_FILE_) . 'includes/css/fonts.css');

        $post_type = get_post_type(get_the_ID());

        if (!self::isWebinarPage() && $post_type != 'wswebinars') {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core', false, array('jquery'));
        wp_enqueue_script('jquery-ui-tabs', false, array('jquery'));
        wp_enqueue_script('jquery-ui-datepicker', false, array('jquery'));
        wp_enqueue_script('jquery-ui-accordion', false, array('jquery'));
        wp_enqueue_script('jquery-ui-sortable', false, array('jquery'));
        wp_enqueue_script('wp-color-picker', false, array('jquery'));
        //wp_enqueue_script('wp-color-picker-alpha', plugin_dir_url($this->_FILE_) . 'includes/js/wp-color-picker-alpha.js');
        wp_enqueue_script('bootstrap-switch-script', plugin_dir_url($this->_FILE_) . 'includes/js/bootstrap-switch.min.js');
        wp_enqueue_script('webinar-systeem', plugin_dir_url($this->_FILE_) . 'includes/js/webinar-systeem.js', array('jquery', 'jquery-ui-core', 'jquery-ui-accordion'));
        wp_enqueue_script('webinar-systeem-custom-fields', plugin_dir_url($this->_FILE_) . 'includes/js/webinar-systeem-custom-fields.js', array('jquery', 'jquery-ui-core', 'jquery-ui-accordion'));
        wp_localize_script('webinar-systeem', 'wpwebinarsystem', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
        wp_enqueue_script('ZeroClipboard_script', plugin_dir_url($this->_FILE_) . 'includes/js/ZeroClipboard.min.js');
        wp_enqueue_script('wp-chosen', plugin_dir_url($this->_FILE_) . 'includes/js/chosen.jquery.min.js');

        wp_enqueue_style('wp-color-picker');
        wp_style_add_data('webinar-admin', 'rtl', 'replace');
        wp_enqueue_style('wswebinar-jquery-ui', plugin_dir_url($this->_FILE_) . 'includes/css/jquery-ui.theme.min.css');
        wp_enqueue_style('wswebinar-jquery-ui-structure', plugin_dir_url($this->_FILE_) . 'includes/css/jquery-ui.structure.min.css');

        $screen = get_current_screen();
        if ($screen->post_type == 'wswebinars' || wp_get_theme()->get('Name') != 'Divi') {
            wp_enqueue_style('webinar-admin-icons', plugin_dir_url($this->_FILE_) . 'includes/css/icons.css');
        };

        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
        wp_enqueue_style('bootstrap-switch-style', plugin_dir_url($this->_FILE_) . 'includes/css/bootstrap-switch.min.css');
        wp_enqueue_style('wp-chosen-style', plugin_dir_url($this->_FILE_) . 'includes/css/chosen.min.css');
        wp_enqueue_media();

        wp_localize_script('webinar-systeem', 'wpwsL10n', array(
            'automated' => __('Automated', self::$lang_slug),
            'countdown' => __('Countdown', self::$lang_slug),
        ));
    }
    
    public function load_page_scripts() {
        $post_types = get_post_type(get_the_ID());

		if ($post_types == 'wpws_page') {
			wp_enqueue_script('wpws-jquery-ui-core', false, array('jquery'));
			wp_enqueue_script('wpws-overview', plugin_dir_url($this->_FILE_) . 'includes/js/wpws-overview.js', array('jquery'));
			wp_localize_script('wpws-overview', 'wpws', array('ajaxurl' => admin_url('admin-ajax.php')));
		}
	}


    public function load_front_end_scripts() {
        $post_type = get_post_type(get_the_ID());

        if ($post_type != 'wswebinars' || !is_single())
            return;

        wp_enqueue_script('wpws-jquery-ui-core', false, array('jquery'));
        wp_enqueue_script('wpws-zero-clipboard', plugin_dir_url($this->_FILE_) . 'includes/js/ZeroClipboard.min.js', array('jquery', 'jquery-ui-core', 'wpws-bootstrap-switch-script'));
        wp_enqueue_script('wpws-bootstrap-script', plugin_dir_url($this->_FILE_) . 'includes/js/bootstrap.min.js');
        wp_enqueue_script('wpws-bootstrap-switch-script', plugin_dir_url($this->_FILE_) . 'includes/js/bootstrap-switch.min.js');
        wp_enqueue_script('wpws-add-event', plugin_dir_url($this->_FILE_) . 'includes/js/addEvent.js', array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('wpws-wpwebinarsystem-helper', plugin_dir_url($this->_FILE_) . 'includes/js/helper-functions.js', array('jquery',));
        wp_enqueue_script('wpws-wpwebinarsystem', plugin_dir_url($this->_FILE_) . 'includes/js/front-end.js', array('jquery',));
        wp_enqueue_script('wpws-wpwebinarsystem-front', plugin_dir_url($this->_FILE_) . 'includes/js/int-controllers.js', array('jquery'));
        wp_localize_script('wpws-wpwebinarsystem-front', 'wpwebinarsystem', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
        wp_enqueue_script('wpws-flipclock', plugin_dir_url($this->_FILE_) . 'includes/js/flipclock.min.js', array('jquery'));
        wp_enqueue_script('wpws-google-platform', '//apis.google.com/js/platform.js', array('jquery'));
        wp_enqueue_script('wpws-videojs', plugin_dir_url($this->_FILE_) . 'includes/libs/videojs/videojs.js');
        wp_enqueue_script('wpws-videojs-ie', plugin_dir_url($this->_FILE_) . 'includes/libs/videojs/videojs-ie8.min.js');
        wp_enqueue_script('wpws-videojs-youtube', plugin_dir_url($this->_FILE_) . 'includes/libs/videojs/videojs-youtube.min.js');
        wp_enqueue_script('wpws-wpwsmediaelement-js', plugin_dir_url($this->_FILE_) . 'includes/libs/mediaelement/mediaelement-and-player.min.js', array('jquery'));
        wp_localize_script('wpws-wpwebinarsystem-front', 'wpws', array('available_timeslots' => __('Loading available timeslots...', self::$lang_slug)));

        wp_enqueue_style('wpws-bootstrap', plugin_dir_url($this->_FILE_) . 'includes/css/bootstrap.min.css');
        wp_enqueue_style('wpws-bootstrap-switch-style', plugin_dir_url($this->_FILE_) . 'includes/css/bootstrap-switch.min.css');
        wp_enqueue_style('wpws-font-awesome', plugin_dir_url($this->_FILE_) . 'includes/css/font-awesome.min.css');
        wp_enqueue_style('wpws-webinar', plugin_dir_url($this->_FILE_) . 'includes/css/webinar.css');
        wp_enqueue_style('wpws-flipclock', plugin_dir_url($this->_FILE_) . 'includes/css/flipclock.css');
        wp_enqueue_style('wpws-ubuntu-font', '//fonts.googleapis.com/css?family=Ubuntu:300,400,500');
        wp_enqueue_style('wpws-webinar-admin-fonts', plugin_dir_url($this->_FILE_) . 'includes/css/fonts.css');
        wp_enqueue_style('wpws-webinar-admin-icons', plugin_dir_url($this->_FILE_) . 'includes/css/icons.css');
        wp_enqueue_style('wpws-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
        wp_enqueue_style('wpws-videojs-css', plugin_dir_url($this->_FILE_) . 'includes/libs/videojs/videojs.css');
        wp_enqueue_style('wpws-wpwsmediaelement', plugin_dir_url($this->_FILE_) . 'includes/libs/mediaelement/mediaelementplayer.css');
        wp_enqueue_style('wpws-mediaelement-skin', plugin_dir_url($this->_FILE_) . 'includes/libs/mediaelement/mejs-skins.css');
        wp_enqueue_style('wswebinar-calendar', plugin_dir_url($this->_FILE_) . 'includes/css/atc-style-blue.css');

        wp_enqueue_media();
    }

    public function deregisterCurrentThemeScripts() {
        global $post, $wp_styles;
        if (empty($post) || in_array(get_option('_wswebinar_enable_theme_styles'), array('on', NULL, ''))) {
            return;
        }

        $post_type = get_post_type($post->ID);

        if ($post_type != $this->post_slug) {
            return;
        }

        $temp_dir = get_template_directory_uri();

        foreach ($wp_styles->registered as $handle => $data) {
            if ($this->startsWith($data->src, $temp_dir)) {
                wp_deregister_style($handle);
                wp_dequeue_style($handle);
            }
        }
    }

    private function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /*
     * 
     * Register options needed for the options page.
     * 
     */

    public function registerOptions() {
        register_setting('wswebinar_options', '_wswebinar_licensekey', array($this, 'sanitize_license'));
        register_setting('wswebinar_options', '_wswebinar_licensekey_network', array($this, 'sanitize_license'));
        register_setting('wswebinar_options', '_wswebinar_enable_woocommerce_integration');
        register_setting('wswebinar_options', '_wswebinar_enable_theme_styles', array($this, 'sanitize_use_theme_style_default_values'));
        register_setting('wswebinar_options', '_wswebinar_email_sentFrom');
        register_setting('wswebinar_options', '_wswebinar_email_senderAddress');
        register_setting('wswebinar_options', '_wswebinar_email_headerImg');
        register_setting('wswebinar_options', '_wswebinar_email_footerTxt');
        register_setting('wswebinar_options', '_wswebinar_email_baseCLR');
        register_setting('wswebinar_options', '_wswebinar_email_bckCLR');
        register_setting('wswebinar_options', '_wswebinar_email_bodyBck');
        register_setting('wswebinar_options', '_wswebinar_email_bodyTXT');
        register_setting('wswebinar_options', '_wswebinar_AdminEmailAddress');
        register_setting('wswebinar_options', '_wswebinar_email_templatereset');
        register_setting('wswebinar_options', '_wswebinar_newregcontent');
        register_setting('wswebinar_options', '_wswebinar_regconfirmcontent');
        register_setting('wswebinar_options', '_wswebinar_24hrb4content');
        register_setting('wswebinar_options', '_wswebinar_1hrb4content');
        register_setting('wswebinar_options', '_wswebinar_wbnstarted');
        register_setting('wswebinar_options', '_wswebinar_wbnreplay');
        register_setting('wswebinar_options', '_wswebinar_newregsubject');
        register_setting('wswebinar_options', '_wswebinar_regconfirmsubject');
        register_setting('wswebinar_options', '_wswebinar_24hrb4subject');
        register_setting('wswebinar_options', '_wswebinar_1hrb4subject');
        register_setting('wswebinar_options', '_wswebinar_wbnstartedsubject');
        register_setting('wswebinar_options', '_wswebinar_wbnreplaysubject');
        register_setting('wswebinar_options', '_wswebinar_newregenable');
        register_setting('wswebinar_options', '_wswebinar_regconfirmenable');
        register_setting('wswebinar_options', '_wswebinar_1hrb4enable');
        register_setting('wswebinar_options', '_wswebinar_24hrb4enable');
        register_setting('wswebinar_options', '_wswebinar_wbnstartedenable');
        register_setting('wswebinar_options', '_wswebinar_wbnreplayenable');
        register_setting('wswebinar_options', '_wswebinar_mailchimpapikey');
        register_setting('wswebinar_options', '_wswebinar_enormailapikey');
        register_setting('wswebinar_options', '_wswebinar_dripapikey');
        register_setting('wswebinar_options', '_wswebinar_getresponseapikey');
        register_setting('wswebinar_options', '_wswebinar_activecampaignapikey');
        register_setting('wswebinar_options', '_wswebinar_activecampaignurl');
        register_setting('wswebinar_options', '_wswebinar_subscription');
        register_setting('wswebinar_options', '_wswebinar_unsubscribe');

        $this->registerPermissionSettings();
    }

    /*
     * 
     * Add the WebinarSysteem admin menus.
     * 
     */

    function webinar_menu() {
        add_menu_page(__('WP WebinarSystem', self::$lang_slug), __('WebinarSystem', self::$lang_slug), '_wswebinar_createwebinars', 'edit.php?post_type=' . $this->post_slug, '', 'none', 59);

        if (current_user_can('_wswebinar_createwebinars'))
            add_submenu_page('edit.php?post_type=' . $this->post_slug, __('Webinars', self::$lang_slug), __('Webinars', self::$lang_slug), 'manage_options', 'edit.php?post_type=' . $this->post_slug);

        add_submenu_page('edit.php?post_type=' . $this->post_slug, __('New webinar', self::$lang_slug), __('New webinar', self::$lang_slug), '_wswebinar_createwebinars', 'post-new.php?post_type=' . $this->post_slug);
        add_submenu_page('edit.php?post_type=' . $this->post_slug, __('Attendee Lists', self::$lang_slug), __('Attendee Lists', self::$lang_slug), '_wswebinar_managesubscribers', 'wswbn-attendees', array('WebinarSysteemAttendees', 'wbn_attendees_list'));
        add_submenu_page('edit.php?post_type=' . $this->post_slug, __('Private Questions', self::$lang_slug), __('Private Questions', self::$lang_slug), '_wswebinar_managequestions', 'wswbn-questions', array(new WebinarSysteemQuestions(), 'showPage'));

        add_submenu_page('edit.php?post_type=' . $this->post_slug, __('Chatlogs', self::$lang_slug), __('Chatlogs', self::$lang_slug), '_wswebinar_managechatlogs', 'wswbn-chatlogs', array(new WebinarSysteemChatlogs(), 'showPage'));

        $options = new WebinarSysteemOptions($this->localkey_status);
        add_submenu_page('edit.php?post_type=' . $this->post_slug, __('Settings', self::$lang_slug), __('Settings', self::$lang_slug), '_wswebinar_webinarsettings', 'wswbn-options', array($options, 'wbn_gengeral_settings'));

        self::remove_admin_menu_links();
    }

    /*
     * 
     * Network menu
     * 
     */

    public function network_menu() {
        $options = new WebinarSysteemOptions($this->localkey_status);
        add_menu_page(__('WP WebinarSystem', self::$lang_slug), __('WebinarSystem', self::$lang_slug), 'manage_netwrok', 'wswbn-netwoptions', '', 'none', 59);
        add_submenu_page('wswbn-netwoptions', __('Settings', self::$lang_slug), __('Settings', self::$lang_slug), 'manage_netwrok', 'wswbn-netwoptions', array($options, 'wbn_network_menu'));
    }

    /*
     * 
     * Set required class variables.
     * 
     */

    protected function setAttributes($file = NULL) {
        global $wpdb;
        if (!empty($file)) {
            define('WSWEB_FILE', $this->_FILE_);
            define('WSWEB_OPTION_PREFIX', '_wswebnar_');
            define('WSWEB_DB_TABLE_PREFIX', $wpdb->prefix . 'wswebinars_');
            define('WSWEB_STORE_URL', 'https://wpwebinarsystem.com');
            define('WSWEB_ITEM_NAME', 'WP WebinarSystem Pro');
        }



        $this->post_slug = 'wswebinars';
        $this->db_tablename_questions = 'wswebinars_questions';
        $this->db_tablename_chats = 'wswebinars_chats';
        $this->db_tablename_subscribers = 'wswebinars_subscribers';
        $this->db_version = '1.0';
        self::$lang_slug = '_wswebinar';
    }

    /*
     * 
     * Add post row links to Webinars
     * 
     */

    public function postRow($actions, $post) {
        if ($post->post_type == $this->post_slug) {
            $new = Array();
            foreach ($actions as $key => $val) {
                if ($key == 'view') {
                    /* $new['settings'] = "<a href='#'>Settings</a>"; */
                    $questions = new WebinarSysteemQuestions;
                    $new['questions'] = '<a href="edit.php?post_type=wswebinars&page=wswbn-questions&webinar_id=' . $post->ID . '">' . __('Questions', self::$lang_slug) . '</a>';
                    $attendees = new WebinarSysteemAttendees;
                    $new['attendees'] = '<a href="edit.php?post_type=wswebinars&page=wswbn-attendees&id=' . $post->ID . '">' . __('Attendees', self::$lang_slug) . '</a>';
                    $chatlog = new WebinarSysteemChatlogs;
                    $new['chatlog'] = '<a href="edit.php?post_type=wswebinars&page=wswbn-chatlogs&webinar_id=' . $post->ID . '">' . __('Chatlog', self::$lang_slug) . '</a>';
                    /* $new['registrations'] = "<a href='#'>" . __('Registrations', self::$lang_slug) . "</a>";
                    $new['statistics'] = "<a href='#'>" . __('Statistics', self::$lang_slug) . "</a>";
                    $new['preview'] = "<a href='#'>" . __('Preview', self::$lang_slug) . "</a>"; */
                    if (current_user_can('_wswebinar_createwebinars'))
                    $new['duplicate'] = '<a href="admin.php?action=wswebinar_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="' . __('Duplicate this Webinar') . '" rel="permalink">' . __('Duplicate', self::$lang_slug) . '</a>';
                }
                $new[$key] = $val;
            }
            return $new;
        }
        return $actions;
    }

    /*
     * 
     * Adds columns to the Webinar browse page
     * 
     */

    public function webinarBrowseColumns($columns) {
        $new = array();
        foreach ($columns as $key => $title) {
            if ($key == 'date') {
                $new['wswebinar_views'] = __('Views', self::$lang_slug);
                $new['wswebinar_registrations'] = __('Registrations', self::$lang_slug);
                $new['wswebinar_questions'] = __('Questions', self::$lang_slug);
                $new['wswebinar_status'] = __('Status', self::$lang_slug);
            }
            $new[$key] = $title;
        }
        return $new;
    }

    /*
     * 
     * Assign contents to the Webinar custom columns
     * 
     */

    public function webinarBrowseCustomColumns($column, $post_id) {
        switch ($column) {
            case 'wswebinar_views' :
                $views = get_post_meta($post_id, '_wswebinar_views', true);
                echo empty($views) ? '-' : (int) $views;
                break;

            case 'wswebinar_registrations' :
                $subs = WebinarSysteemAttendees::getNumberOfSubscriptions($post_id);
                echo empty($subs) ? '-' : '<a href="edit.php?post_type=wswebinars&page=wswbn-attendees&id=' . $post_id . '">' . $subs . "</a>";
                break;

            case 'wswebinar_questions' :
                $questions = new WebinarSysteemQuestions;
                $subsData = $questions->getQuestionsFromDb($post_id);
                $subs = $subsData['num_of_rows'];
                echo empty($subs) ? '-' : '<a href="edit.php?post_type=wswebinars&page=wswbn-questions&webinar_id=' . $post_id . '">' . $subs . "</a>";
                break;

            case 'wswebinar_status':
                if (self::isAutomated($post_id)) {
                    $isClosed = self::checkIfWebinarIsClosed($post_id);
                    if($isClosed) {?>
                        <span><?php _e('Closed', WebinarSysteem::$lang_slug) ?></span>
                    <?php } else { ?>
                    <span style="color:red"><?php _e('Automated', WebinarSysteem::$lang_slug) ?></span><?php
                    }
                    break;
                }
                $saved_status = get_post_meta($post_id, '_wswebinar_gener_webinar_status', true);
                ?>
                <select class="quickstatusupdater" webinar="<?php echo $post_id; ?>">
                    <option value="cou" <?php echo $saved_status == 'cou' ? 'selected' : ''; ?>>Countdown</option>
                    <option value="liv" <?php echo $saved_status == 'liv' ? 'selected' : ''; ?>>Live</option>
                    <option value="rep" <?php echo $saved_status == 'rep' ? 'selected' : ''; ?>>Replay</option>
                    <option value="clo" <?php echo $saved_status == 'clo' ? 'selected' : ''; ?>>Closed</option>
                </select>
                <span class="wswaiticon" id="waitingIcon_<?php echo $post_id; ?>"><img src="<?php echo plugin_dir_url($this->_FILE_); ?>includes/images/wait.GIF"></span>
                <span id="checkIcon_<?php echo $post_id; ?>" class="webi-class-check"></span>
                <?php
                break;
        }
    }

    /*
     * 
     * Register Webinar type
     * 
     */

    public function registerWebinars() {
        register_post_type($this->post_slug, array(
            'labels' => array(
            'name' => __('Webinars', self::$lang_slug),
            'singular_name' => __('Webinar', self::$lang_slug),
            'name_admin_bar' => __('Webinar', self::$lang_slug),
            'add_new' => __('Add New Webinar', self::$lang_slug),
            'add_new_item' => __('Add New Webinar', self::$lang_slug),
            'new_item' => __('New Webinar', self::$lang_slug),
            'edit_item' => __('Edit Webinar', self::$lang_slug),
            'view_item' => __('View Webinar', self::$lang_slug),
            ),
            'public' => true,
            'has_archive' => FALSE,
            'show_in_menu' => false,
            'rewrite' => array('slug' => 'webinars', 'with_front' => false),
            'show_in_admin_bar' => true,
            'supports' => array('title', 'editor'),
            'capibility_type' => array('wswebinar','wswebinars'),
            'capabilities' => array(
                'read_post' => 'read_wswebinar',
                'edit_post' => 'edit_wswebinar',
                'delete_post' => 'delete_wswebinar',
                'publish_posts' => 'publish_wswebinars',
                'edit_posts' => 'edit_wswebinars',
                'edit_others_posts' => 'edit_others_wswebinars',
                'read_private_posts' => 'read_private_wswebinars',
                'delete_posts' => 'delete_wswebinars',
            ),
            )
        );
    }
    
    private function createUnsubscribePage() {
		$pages = get_posts(array(
			'name' => 'webinar-unsubscribe',
      		'orderby' => 'date',
      		'order' => 'DESC',
      		'post_type' => 'wpws_page',
    	));
    	$page = null;
    	if(!empty($pages)){
			$page = array_shift($pages);
			if(empty($page->post_content)){
				$page = null;
			}
		}
		
		if($page === null) {
			$wpws_page_id = WebinarSysteemUserPages::createUnSubscribePage();
		} else {
			$wpws_page_id = (int)$page->ID;
		}
		$subscription = get_option('_wswebinar_unsubscribe');
		if(!isset($subscription) || empty($subscription)) {
			update_option('_wswebinar_unsubscribe', $wpws_page_id);
		}

	}

	private function createWebinarOverviewPage() {
		$pages = get_posts(array(
			'name' => 'webinar-overview',
      		'orderby' => 'date',
      		'order' => 'DESC',
      		'post_type' => 'wpws_page',
    	));
    	$page = null;
    	if(!empty($pages)){
			$page = array_shift($pages);
			if(empty($page->post_content)){
				$page = null;
			}
		}
		
		if($page === null) {
			$wpws_page_id = WebinarSysteemUserPages::createWebinarOverviewPage();
			
		} else {
			$wpws_page_id = (int)$page->ID;
		}
		$wpws_overview = get_option('_wswebinar_overview');
		if(!isset($wpws_overview) || empty($wpws_overview)) {
			update_option('_wswebinar_overview', $wpws_page_id);
		}
	}

    /*
     * 
     * Plugin installation hook function.
     * 
     */

    public function install() {
    
	$this->registerWebinars();
	$this->createUnsubscribePage();
	$this->createWebinarOverviewPage();
	flush_rewrite_rules();

	WebinarSysteemOptions::DoResetDefaults();
    }
    

    /*
     * 
     * Run the database migrations.
     * 
     */

    private function runDatabaseMigrations() {
	$curr_db_version = get_option('_wswebnar_db_version', 'no');

	if ($curr_db_version == $this->db_version)
	    return;

	global $wpdb;
	$table_name = $wpdb->prefix . $this->db_tablename_questions;

	$charset_collate = '';

	if (!empty($wpdb->charset)) {
	    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}

	if (!empty($wpdb->collate)) {
	    $charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		email text NOT NULL,
		question text NOT NULL,
		webinar_id int(11) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

	add_option('_wswebnar_db_version', $this->db_version);
    }

    /*
     * 
     * Saving registration form data.
     * 
     */

    public static function saveRegFormData($post_id, $inputName, $inputEmail, $inputDay = NULL, $inputTime = NULL, $inputTab = 'register', $cameFromShortcode = 'no') {
	$gener_air_type_saved = get_post_meta($post_id, '_wswebinar_gener_air_type', true);
	$gener_time_occur_saved = get_post_meta($post_id, '_wswebinar_gener_time_occur', true);
	$wp_paidWebinar = get_post_meta($post_id, '_wswebinar_ticket_wbnpaid_yn', true) == 'on';

	/* Get webinar timezone */
	$time_zone = get_post_meta($post_id, '_wswebinar_timezoneidentifier', true);
	if (!empty($time_zone)) {
	    $date = new DateTime("now", new DateTimeZone($time_zone));
	    $current_date_time = $date->format('Y-m-d H:i:s');
	} else {
	    $current_date_time = current_time("Y-m-d H:i:s");
	}

	$error_check_rightnow = false;
	if (empty($inputDay) && empty($inputTime)) {
	    $error_check_rightnow = TRUE;
	} elseif ($inputDay == 'rightnow' && empty($inputTime)) {
	    $error_check_rightnow = FALSE;
	} elseif (empty($inputDay) || empty($inputTime)) {
	    $error_check_rightnow = TRUE;
	}
	if (empty($inputName) || empty($inputEmail) || (($gener_air_type_saved == 'rec' && ( $gener_time_occur_saved == 'recur'  || $gener_time_occur_saved == 'jit')) && $error_check_rightnow)) {
	    $errorUrl = '';
	    if (($gener_air_type_saved == 'rec' && ( $gener_time_occur_saved == 'recur'  || $gener_time_occur_saved == 'jit')) && (empty($inputDay) || empty($inputTime) )) {
		$errorUrl = empty($inputDay) ? 'inputday' : 'inputtime';
	    } else if (empty($inputName) || empty($inputEmail)) {
		$errorUrl = empty($inputName) ? 'inputname' : 'inputemail';
	    }

	    if ($inputTab == 'login') {
		$errorUrl = 'notregisterd';
	    }

	    $emailUrl = empty($inputEmail) ? "" : urlencode($inputEmail);
	    $nameUrl = empty($inputName) ? "" : urlencode($inputName);
	    $redirectUrl = preg_replace('/\?.*/', '', $_SERVER['HTTP_REFERER']);
	    wp_redirect($redirectUrl . '?error=' . $errorUrl . '&inputemail=' . $emailUrl . '&inputname=' . $nameUrl);
	    exit();
	}

	$rand = rand(888888, 889888);
	$data = Array();
	$data['name'] = trim($inputName);
	$data['email'] = trim($inputEmail);
	$data['time'] = $current_date_time;

	$cur_timestamp = WebinarSysteem::populateDateTime($post_id);
	$attendee = WebinarSysteemAttendees::getAttendee($post_id);
	$isRecurring = WebinarSysteem::isRecurring($post_id);
	$webinar_times = WebinarSysteem::getWebinarTime($post_id, $attendee);
	if ($isRecurring && $inputDay == 'rightnow') {
	    $exact_time = $cur_timestamp;
	} else if ($isRecurring && $inputDay != 'rightnow') {
	    $exact_time = strtotime("$inputDay $inputTime");
	} else {
	    $exact_time = strtotime($current_date_time);
	}
	$data['exact_time'] = date('Y-m-d H:i:s', $exact_time);
	$data['secretkey'] = $rand;
	$data['webinar_id'] = $post_id;
	$data['active'] = 1;
	$data['random_key'] = self::RandomString(20);


	$data['watch_day'] = ($inputDay == 'rightnow' ? strtolower(date('D', WebinarSysteem::populateDateTime($post_id))) : strtolower(substr($inputDay, 0, 3)));

	$data['watch_time'] = ($inputDay == 'rightnow' ? date('H:i', WebinarSysteem::populateDateTime($post_id)) : $inputTime);

	$data['custom_fields'] = self::prepareRegFormCustomFields($post_id);

	WebinarSysteemAttendees::saveAttendie($data, array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s'));
	self::setUserSession($rand, $data['email'], $data['random_key'], $cameFromShortcode);
	self::subscribeMailChimp($post_id, $data['name'], $data['email']);
	WebinarSysteemSubscribe::subscribeMailpoet($post_id, $data['email'], $data['name']);
	WebinarSysteemSubscribe::subscribeMailpoet3($post_id, $data['email'], $data['name']);
	WebinarSysteemSubscribe::subscribeEnormail($post_id, $data['name'], $data['email']);
	WebinarSysteemSubscribe::saveGetresponseSubscriber($post_id, $data['name'], $data['email']);
	WebinarSysteemSubscribe::subscribeAweberMail($post_id, $data['name'], $data['email']);
	WebinarSysteemSubscribe::subscribeActiveCampaign($post_id, $data['name'], $data['email']);
	WebinarSysteemSubscribe::subscribeDripMail($post_id, $data['name'], $data['email']);

	$ws_webinar_sendmail = new WebinarSysteemMails;
	$ws_webinar_sendmail->SendMailtoAdmin($inputName, $post_id, $inputEmail);
	if(!$wp_paidWebinar){
	$ws_webinar_sendmail->SendMailtoReader($inputName, $inputEmail, $post_id);	
	}
	return array('success' => true, 'inputDay' => $inputDay);
    }

    public static function subscribeMailChimp($post_id, $user, $email) {
	$mail_provider = get_post_meta($post_id, '_wswebinar_default_mail_provider', true);
	$mail_list = get_post_meta($post_id, '_wswebinar_mailchimp_list', true);
	$api_key = get_option('_wswebinar_mailchimpapikey');
	$expolde_name = WebinarSysteemSubscribe::explodeName($user);
	$fname = $expolde_name['fname'];
	$lname = $expolde_name['lname'];
	if ($mail_provider == 'mailchimp' && !empty($api_key) && !empty($mail_list)) {
	    $Mailchimp = new Mailchimp($api_key);
	    $Mailchimp_Lists = new Mailchimp_Lists($Mailchimp);

	    $subscriber = $Mailchimp_Lists->subscribe($mail_list, array('email' => htmlentities($email)), array('FNAME' => htmlentities($fname), 'LNAME' => htmlentities($lname)));
	    if ($subscriber['euid'] !== '') {
		return array('state' => TRUE, 'euid' => $subscriber['euid'], 'leid' => $subscriber['leid']);
	    } else {
		return array('state' => FALSE, 'reason' => $subscriber);
	    }
	} else {
	    return array('state' => FALSE, 'reason' => 'API key or Group ID seems to be invalid');
	}
    }
    
    /*Adjust the registration shortcode cookie to no to stop redirecting to Thank you page more than once*/
    
    private static function updateShortcodeCookie(){
    	
    	$camefromShortcode = 'no';
		unset($_COOKIE['_wswebinar_camefromshortcode']);
		setcookie('_wswebinar_camefromshortcode', '', time() - 3600, '/');
		$_COOKIE['_wswebinar_camefromshortcode'] = $camefromShortcode;
		setcookie('_wswebinar_camefromshortcode', $camefromShortcode, time() + 60 * 60 * 24 * 30, '/');
		return TRUE;
	}

    private static function setUserSession($rand, $email, $random_key = 0, $cameFromShortcode='no') {
	unset($_COOKIE['_wswebinar_registered']);
	unset($_COOKIE['_wswebinar_registered_key']);
	unset($_COOKIE['_wswebinar_registered_email']);
	unset($_COOKIE['_wswebinar_camefromshortcode']);

	if ($random_key) {
	    unset($_COOKIE['_wswebinar_regrandom_key']);
	    setcookie('_wswebinar_regrandom_key', '', time() - 3600, '/');
	    $_COOKIE['_wswebinar_regrandom_key'] = $random_key;
	    setcookie('_wswebinar_regrandom_key', $random_key, time() + 60 * 60 * 24 * 30, '/');
	}
    
    setcookie('_wswebinar_camefromshortcode', '', time() - 3600, '/');
	setcookie('_wswebinar_registered', '', time() - 3600, '/');
	setcookie('_wswebinar_registered_email', '', time() - 3600, '/');
	setcookie('_wswebinar_registered_key', '', time() - 3600, '/');

	setcookie('_wswebinar_registered', 'yes', time() + 60 * 60 * 24 * 30, '/');
	setcookie('_wswebinar_registered_email', $email, time() + 60 * 60 * 24 * 30, '/');
	setcookie('_wswebinar_registered_key', $rand, time() + 60 * 60 * 24 * 30, '/');
	setcookie('_wswebinar_camefromshortcode', $cameFromShortcode, time() + 60 * 60 * 24 * 30, '/');

	$_COOKIE['_wswebinar_registered'] = 'yes';
	$_COOKIE['_wswebinar_registered_key'] = $rand;
	$_COOKIE['_wswebinar_registered_email'] = $email;
	$_COOKIE['_wswebinar_camefromshortcode'] = $cameFromShortcode;
	
    }

    /*
     * Clears user session cookies.
     * @return void.
     */

    public function clearUserSession() {
	
	unset($_COOKIE['_wswebinar_registered']);
	unset($_COOKIE['_wswebinar_registered_key']);
	unset($_COOKIE['_wswebinar_registered_email']);
	unset($_COOKIE['_wswebinar_regrandom_key']);
	unset($_COOKIE['_wswebinar_camefromshortcode']);
    
    }

    /*
     * 
     * Redirect the template url to Webinar custom template.
     * 
     */

    public function myThemeRedirect($original_template, $force_execute = FALSE, $post_id = NULL) {
	global $wp;

	if (!$force_execute)
	    if (!isset($wp->query_vars["post_type"]) || $wp->query_vars["post_type"] !== $this->post_slug)
		return $original_template;

	global $post;
	@$postId = empty($post_id) ? $post->ID : $post_id;

	if (empty($postId)) {
	    wp_die(__('Please save your webinar first before previewing.', self::$lang_slug));
	}

	$canAccess = self::can_access_webinar($postId);

	self::redirectAccessPages($postId, $canAccess);

	$already_regd = false;
	$new_registd = false;
	$return_template = '';
	$plugindir = dirname($this->_FILE_);
	$webinar_status = $this->checkWebinarStatusForNow($postId);

	$registration_filename = $plugindir . '/' . 'includes/tmp-registration.php';
	$thankyou_filename = $plugindir . '/' . 'includes/tmp-post.php';
	$live_filename = $plugindir . '/' . 'includes/tmp-live.php';
	$countd_filename = $plugindir . '/' . 'includes/tmp-countdown.php';
	$replay_filename = $plugindir . '/' . 'includes/tmp-live.php';
	$closed_filename = $plugindir . '/' . 'includes/tmp-closed.php';

	//Saving registration form data.
	extract($this->registrationFormSubmit($original_template, $forceRun = TRUE));

	if(!isset($_POST['webinarRegForm'])){
	$reg_for_webinar = $this->isRegforWebinar($postId);
	if (!$reg_for_webinar) {
	    $return_template = $registration_filename;
	    $already_regd = false;
	}
	}

	if ($new_registd) {
	    $return_template = $thankyou_filename;
	}

	$is_recur = WebinarSysteem::isRecurring($postId);
	$_wswebinar_gener_duration = self::getWebinarDuration($postId);
	$webinar_air_type = self::webinarAirType($postId);

	$attendee = WebinarSysteemAttendees::getAttendee($postId);
	$time_st = WebinarSysteem::getWebinarTime($postId, $attendee);
	$webiner_t = $time_st;

	if ($already_regd && $webinar_status == 'cou'):
	    $return_template = $countd_filename;
	elseif ($already_regd && $webinar_status == 'liv'):
	    $return_template = $live_filename;
	elseif ($already_regd && $webinar_status == 'clo'):
	    $return_template = $closed_filename;
	elseif ($already_regd && $webinar_status == 'rep'):
	    $return_template = $replay_filename;
	endif;

	$one_time_register = get_post_meta($postId, '_wswebinar_gener_onetimeregist', true);
	if ($already_regd && $is_recur && $attendee->active == '1') {
	    if ($one_time_register !== '1') {
		if ($webiner_t <= WebinarSysteem::populateDateTime($postId) && WebinarSysteem::populateDateTime($postId) <= ($webiner_t + $_wswebinar_gener_duration)) {
		    $return_template = $live_filename;
		} elseif (WebinarSysteem::populateDateTime($postId) <= $webiner_t) {
		    $return_template = $countd_filename;
		} else {
		    WebinarSysteemAttendees::modifyAttendee($attendee->id, array('active' => '0'), array('%d'));
		    $return_template = $closed_filename;
		}
	    } elseif ($webiner_t <= WebinarSysteem::populateDateTime($postId) && WebinarSysteem::populateDateTime($postId) <= ($webiner_t + $_wswebinar_gener_duration)) {
		$return_template = $live_filename;
	    } else {
		$return_template = $countd_filename;
	    }
	} else if ($already_regd && !$is_recur && $attendee->active == '1' && $webinar_air_type == 'rec') {
	    if ($webiner_t <= WebinarSysteem::populateDateTime($postId) && WebinarSysteem::populateDateTime($postId) <= ($webiner_t + $_wswebinar_gener_duration)) {
		$return_template = $live_filename;
	    } else if(WebinarSysteem::populateDateTime($postId) > ($webiner_t + $_wswebinar_gener_duration)){
	    $return_template = $closed_filename;
	    }
	}

	if (!$already_regd && !$new_registd) {
	    $return_template = $registration_filename;
	}



	if (isset($attendee->active) && intval($attendee->active) !== 1) {
	    $return_template = $registration_filename;
	}
	/*
	 * Overwrite if force show available.
	 */
	$isAdmin = current_user_can('manage_options');
	$can_create_webinars = current_user_can('_wswebinar_createwebinars');
	$forceShow = @$_GET['force_show'];

	if(!empty($forceShow)){
		if ( ($isAdmin || $can_create_webinars)){
		switch ($_GET['force_show']) {
		case 'live':
		    $return_template = $live_filename;
		    break;
		case 'register':
		    $return_template = $registration_filename;
		    break;
		case 'thankyou':
		    $return_template = $thankyou_filename;
		    break;
		case 'countdown':
		    $return_template = $countd_filename;
		    break;
		case 'closed':
		    $return_template = $closed_filename;
		    break;
		case 'replay':
		    $return_template = $replay_filename;
		    break;
		default:
		    break;
	    }	
	} else
	{
		$url = get_permalink($postId);
		return wp_redirect($url);
	
		exit;
	}
	
	} 


	$pagedata['rightnowuser'] = ($right_now_registered ? TRUE : FALSE);

	$this->doThemeRedirect($return_template, $pagedata);
    }

    /*
     * 
     * Remove admin bar in Webinar pages
     * 
     */

    public function my_admin_bar_init() {
	$post_types = get_post_type(get_the_ID());
	if ($post_types == 'wswebinars' && is_single()) {
	    remove_action('wp_footer', 'wp_admin_bar_render', 1000);
	    remove_action('wp_head', '_admin_bar_bump_cb');
	    remove_action('wp_head', 'skt_itconsultant_custom_head_codes');
	}
    }

    public function admin_notices() {
	if (!empty($this->localkey_status) && $this->localkey_status == 499)
	    return;

	echo '<div class="updated wswebinar_adnotice">';
	switch ($this->localkey_status) {
	    case 499:
		echo '<p>' . __('Your license for WebinarSystem is seems to be Invalid. Please contact support desk!', self::$lang_slug) . '</p>';
		break;
	    case 299:
		echo '<p>' . __('Your WebinarSystem license key has suspended! Please contact the support.', self::$lang_slug) . '</p>';
		break;
	    case 399:
		echo '<p>' . __('Your WebinarSystem license key has expired! Please renew it again before using the plugin.', self::$lang_slug) . '</p>';
		break;
	    default:
		echo '<p>' . sprintf(__('Please %senter your license key%s to activate WebinarSystem plugin!', self::$lang_slug), '<a href="' . admin_url('edit.php?post_type=wswebinars&page=wswbn-options') . '">', '</a>') . '</p>';
		break;
	}
	?>

	</div>
	<?php
    }

    private function doThemeRedirect($url, $pagedata) {
	global $post, $wp_query;
	$pagedata['url'] = $url;
	if (have_posts()) {
	    include($url);
	    die();
	} else {
	    $wp_query->is_404 = true;
	}
    }

    public function assignAdminNotices() {
	$userInfo = wp_get_current_user();
	if (get_user_meta($userInfo->ID, '_wswebinar_notdismiss', TRUE) == 'yes')
	    return;
    }

    /*
     * 
     * Check WebinarSysteem License Key 
     * 
     */

    function webinarSysteemVerify() {
	$exp_date = get_option('edd_sample_license_exp_date');

	if (!empty($exp_date))
	    if ($exp_date > time())
		return;


	global $wp_version;

	$license = trim(WebinarSystemUpdate::get_license());

	$api_params = array(
	    'edd_action' => 'check_license',
	    'license' => $license,
	    'item_name' => urlencode(WSWEB_ITEM_NAME),
	    'url' => home_url()
	);

// Call the custom API.
	$response = wp_remote_post(WSWEB_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

	if (is_wp_error($response))
	    return false;

	$license_data = json_decode(wp_remote_retrieve_body($response));

	if (empty($license_data->success) || $license_data->success != true)
	    return;

	if (isset($license_data->license) && $license_data->license == 'valid') {
	    $this->localkey_status = 599;
// this license is still valid
	} else {
	    $this->localkey_status = 498;
//add_action('admin_init', array($this, 'assignAdminNotices'));
// this license is no longer valid
	}

	update_option('edd_sample_license_exp_date', strtotime($license_data->expires));
	update_option('edd_sample_license_status', $license_data->license);
    }

    /*
     * 
     * Set the Webinar views data
     * 
     */

    public static function setPostData($post_id) {
	$current = get_post_meta($post_id, '_wswebinar_views', true);
	if (empty($current))
	    $current = 0;
	$new = 1 + (int) $current;
	update_post_meta($post_id, '_wswebinar_views', $new);
    }

    private function checkUserForSavedSessions() {
	if (isset($_COOKIE['_wswebinar_registered']) && isset($_COOKIE['_wswebinar_registered_key']) && isset($_COOKIE['_wswebinar_regrandom_key'])) {
	    $int = (int) $_COOKIE['_wswebinar_registered_key'];
	    if ($int < 889888 && $int > 888888)
		return TRUE;
	}
	return FALSE;
    }

    private function checkWebinarStatusForNow($post_id) {
	$getStatus = get_post_meta($post_id, '_wswebinar_gener_webinar_status', true);
	if (empty($getStatus))
	    $getStatus = 'cou';
	return $getStatus;
    }
    
    /**
	* 
	* Check if webinar is automated
	* 
	* @return bool
	*/
    public static function isAutomated($webinar_id) {
	$air_type = self::webinarAirType($webinar_id);
	$gener_time_occur_saved = get_post_meta($webinar_id, '_wswebinar_gener_time_occur', true);
	
	if (!empty($gener_time_occur_saved) && $air_type == 'rec')
	{
		return TRUE;
	}
	return FALSE;
	}
	
	/**
	* Check if webinar is closed
	* @param undefined $webinar_id
	* 
	* @return bool
	*/

	public static function checkIfWebinarIsClosed($webinar_id) {
		$is_recur = WebinarSysteem::isRecurring($webinar_id);
		$_wswebinar_gener_duration = self::getWebinarDuration($webinar_id);
		$webinar_air_type = self::webinarAirType($webinar_id);
		
		if (!$is_recur && $webinar_air_type == 'rec') {
		$time_st = WebinarSysteemMails::getWebinarTime($webinar_id, $attendee=NULL);
		$webiner_t = $time_st;
	    if(WebinarSysteem::populateDateTime($webinar_id) > ($webiner_t + $_wswebinar_gener_duration)){
			return TRUE;
	    }
	    }
	return FALSE;
	}

    private function getWebinarStatusText($webinar_id) {

	if (WebinarSysteem::isAutomated($webinar_id))
	    return __('Automated', WebinarSysteem::$lang_slug);

	$stat = $this->checkWebinarStatusForNow($webinar_id);
	$string = '';
	switch ($stat) {
	    case 'cou':
		$string = 'Countdown';
		break;
	    case 'liv':
		$string = 'Live';
		break;
	    case 'rep':
		$string = 'Replay';
		break;
	    case 'clo':
		$string = 'Closed';
		break;
	    default:
		break;
	}
	return $string;
    }

    private function checkUserAlreadyRegisteredForWebinar($post_id, $email) {
	// todo Write a mysql check without looping whole attendies.
	$email = trim($email);
	global $wpdb;
	$table = $wpdb->prefix . $this->db_tablename_subscribers;
	$row = $wpdb->get_row("SELECT * FROM $table WHERE email='$email' AND webinar_id='$post_id' AND active=1");
	if (!empty($row)) {
	    // Email is registered.
	    $rand_nbr = (isset($_COOKIE['_wswebinar_regrandom_key']) ? $_COOKIE['_wswebinar_regrandom_key'] : 0);
	    $this_browser = $this->isThisBrowser($rand_nbr, $post_id);
	    // Update random key to this browser.
	    if (!$this_browser) {
		$key = $this->RandomString(20);
		$wpdb->update($table, array('random_key' => $key), array('id' => $row->id), array('%s'), array('%d'));
		$rand = rand(888888, 889888);
		$this->setUserSession($rand, $email, $key, $cameFromShortcode='no');
	    }
	    return true;
	}
	return false;
    }

    /*
     * Duplicate Webinar
     */

    public function wswebinar_duplicate_post_as_draft() {
	global $wpdb;
	if (!( isset($_GET['post']) || isset($_POST['post']) || ( isset($_REQUEST['action']) && 'wswebinar_duplicate_post_as_draft' == $_REQUEST['action'] ) )) {
	    wp_die('No Webinar to duplicate has been supplied!');
	}

	/*
	 * get the original post id
	 */
	$post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	/*
	 * and all the original post data then
	 */
	$post = get_post($post_id);

	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;

	/*
	 * if post data exists, create the post duplicate
	 */
	if (isset($post) && $post != null) {

	    /*
	     * new post data array
	     */
	    $args = array(
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_author' => $new_post_author,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'post_name' => $post->post_name,
		'post_parent' => $post->post_parent,
		'post_password' => $post->post_password,
		'post_status' => 'draft',
		'post_title' => 'Copy of ' . $post->post_title,
		'post_type' => $post->post_type,
		'to_ping' => $post->to_ping,
		'menu_order' => $post->menu_order
	    );

	    /*
	     * insert the post by wp_insert_post() function
	     */
	    $new_post_id = wp_insert_post($args);

	    /*
	     * get all current post terms ad set them to the new post draft
	     */
	    $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
	    foreach ($taxonomies as $taxonomy) {
		$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
		wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
	    }

	    /*
	     * duplicate all post meta
	     */
	    $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
	    if (count($post_meta_infos) != 0) {
		$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		foreach ($post_meta_infos as $meta_info) {
		    $meta_key = $meta_info->meta_key;
		    $meta_value = addslashes($meta_info->meta_value);
		    $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
		}
		$sql_query .= implode(" UNION ALL ", $sql_query_sel);
		$wpdb->query($sql_query);
		delete_post_meta($new_post_id, '_wswebinar_views');
	    }

	    /*
	     * finally, redirect to the edit post screen for the new draft
	     */
	    wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
	    exit;
	} else {
	    wp_die('Webinar creation failed, could not find original Webinar: ' . $post_id);
	}
    }

    public function webinarSysteemRunUpdates() {
	new WebinarSysteemUpdates($this->plugin_version, $this->localkey_status, plugin_basename($this->_FILE_));
    }

    public static function getDefaultMailTemplates() {
    $_wswebinar_wbnnewreg_template = __('Howdy,', WebinarSysteem::$lang_slug) . "\r\n\r\n" . __('[receiver-name] just signed up for your webinar', WebinarSysteem::$lang_slug) . " <i>[webinar-title]</i>\r\n\r\n" . __('Regards', WebinarSysteem::$lang_slug) . ",\r\n<i>" . get_bloginfo('name') ."</i>";
    
    $_wswebinar_wbnregconfirm_template = __('Hi', WebinarSysteem::$lang_slug) . " [receiver-name],\r\n\r\n" . __('Thank you for your registration for the webinar. Below you will find the details of the webinar.', WebinarSysteem::$lang_slug) . "\r\n\r\n" . __('<b>Webinar name:</b>', WebinarSysteem::$lang_slug) . " [webinar-title]\r\n\r\n" . __('<b>Date:</b>', WebinarSysteem::$lang_slug) . " [webinar-date]\r\n\r\n" . __('<b>Time:</b>', WebinarSysteem::$lang_slug) . " [webinar-time]\r\n\r\n" .__('<b>Timezone:</b>', WebinarSysteem::$lang_slug)." [webinar-timezone]\r\n\r\n".'<a style="background-color: green; border-radius: 3px; border: 1px solid transparent; padding: 3px 20px; text-decoration: none;  color:white;" href="[webinar-link]">' .__('Go to Webinar', WebinarSysteem::$lang_slug) . '</a>'."\r\n\r\n" . __('See you then!', WebinarSysteem::$lang_slug) . "\r\n\r\n" . __('Regards', WebinarSysteem::$lang_slug) . ",\r\n<i>" . get_bloginfo('name') . "</i>";
    
	$_wswebinar_wbn1hr_template = __('Hi', WebinarSysteem::$lang_slug) . " [receiver-name]\r\n" . __('The webinar you signed up for starts in one hour. Below you will find the link to attend the webinar.', WebinarSysteem::$lang_slug) . "\r\n" . __('Webinar name:', WebinarSysteem::$lang_slug) . " [webinar-title]\r\n" . __('Date:', WebinarSysteem::$lang_slug) . " [webinar-date]\r\n" . __('Time:', WebinarSysteem::$lang_slug) . " [webinar-time]\r\n" .__('Timezone:', WebinarSysteem::$lang_slug)." [webinar-timezone]\r\n[webinar-link]\r\n" . __('See you then!', WebinarSysteem::$lang_slug) . "\r\n" . __('Regards', WebinarSysteem::$lang_slug) . ",\r\n" . get_bloginfo('name');

	$_wswebinar_wbn24hr_template = __('Hi', WebinarSysteem::$lang_slug) . " [receiver-name]\r\n" . __('This is a reminder for your upcoming webinar tomorrow. Below you will find the details of the webinar.', WebinarSysteem::$lang_slug) . "\r\n" . __('Webinar name:', WebinarSysteem::$lang_slug) . " [webinar-title]\r\n" . __('Date:', WebinarSysteem::$lang_slug) . " [webinar-date]\r\n" . __('Time:', WebinarSysteem::$lang_slug) . " [webinar-time]\r\n" . __('Timezone:', WebinarSysteem::$lang_slug) . " [webinar-timezone]\r\n[webinar-link]\r\n" . __('See you then!', WebinarSysteem::$lang_slug) . "\r\n" . __('Regards', WebinarSysteem::$lang_slug) . ",\r\n" . get_bloginfo('name');


	$_wswebinar_wbnstarted_template = __('We are starting the webinar [receiver-name]! Click on the link below to join us.', WebinarSysteem::$lang_slug) . "\r\n[webinar-link]\r\n\r\n" . __('See you later!', WebinarSysteem::$lang_slug) . "\r\n" . __('Regards', WebinarSysteem::$lang_slug) . ",\r\n" . get_bloginfo('name');

	$_wswebinar_wbnreplay_template = __('Hi', WebinarSysteem::$lang_slug) . " [receiver-name]\r\n\r\n" . __('Make sure to join the webinar via this link:', WebinarSysteem::$lang_slug) . " [webinar-link]\r\n\r\n" . __('See you later!', WebinarSysteem::$lang_slug) . "\r\n" . __('Regards', WebinarSysteem::$lang_slug) . ",\r\n" . get_bloginfo('name');

	return array('newreg' => $_wswebinar_wbnnewreg_template, 'regconfirm' => $_wswebinar_wbnregconfirm_template, '1hr' => $_wswebinar_wbn1hr_template, '24hr' => $_wswebinar_wbn24hr_template, 'started' => $_wswebinar_wbnstarted_template, 'replay' => $_wswebinar_wbnreplay_template);
    }

    public function setDefaultMailTemplates() {
	$template = self::getDefaultMailTemplates();
	
	$_newregcontent     = get_option('_wswebinar_newregcontent');
	$_regconfirmcontent = get_option('_wswebinar_regconfirmcontent');
	$_1hrb4content      = get_option('_wswebinar_1hrb4content');
	$_24hrb4content     = get_option('_wswebinar_24hrb4content');
	$_webstartedcontent = get_option('_wswebinar_wbnstarted');
	$_webreplaycontent  = get_option('_wswebinar_wbnreplay');
	$_emailsentfrom     = get_option('_wswebinar_email_sentFrom');
	$_emailaddress      = get_option('_wswebinar__email_senderAddress');
	
	if(empty($_newregcontent)){
	    update_option(self::$lang_slug . '_newregcontent', $template['newreg']);
	}
	
	if(empty($_regconfirmcontent)){
	    update_option(self::$lang_slug . '_regconfirmcontent', $template['regconfirm']);
	}
	
	if(empty($_1hrb4content)){
		update_option(self::$lang_slug . '_1hrb4content', $template['1hr']);
	}
	
	if(empty($_24hrb4content)){
		update_option(self::$lang_slug . '_24hrb4content', $template['24hr']);
	}
	
	if(empty($_webstartedcontent)){
		update_option(self::$lang_slug . '_wbnstarted', $template['started']);
	}
	
	if(empty($_webreplaycontent)){
		update_option(self::$lang_slug . '_wbnreplay', $template['replay']);
	}

	$name = get_bloginfo('name');
	$admin_email = get_option('admin_email');
	if(empty($_emailsentfrom)){
		update_option(self::$lang_slug . '_email_sentFrom', $name);
	}
	
	if(empty($_emailsentfrom)){
		update_option(self::$lang_slug . '_email_senderAddress', $admin_email);
	}
    }

    public static function getYoutubeIdFromUrl($link) {
	preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $link, $matches);
	if (!empty($matches[0]))
	    return $matches[0];
	return false;
    }

    public static function webinarAirType($webinar_id) {
	$gener_air_type_saved = get_post_meta($webinar_id, '_wswebinar_gener_air_type', true);
	if (empty($gener_air_type_saved))
	    $gener_air_type_saved = 'live';
	return $gener_air_type_saved;
    }

    public static function isRecurring($webinar_id) {
	$air_type = self::webinarAirType($webinar_id);
	$gener_time_occur_saved = get_post_meta($webinar_id, '_wswebinar_gener_time_occur', true);
	if (!empty($gener_time_occur_saved) && $air_type == 'rec' && ( $gener_time_occur_saved == 'recur'  || $gener_time_occur_saved == 'jit'))
	    return TRUE;
	return FALSE;
    }

    public static function getRecurringInstances($webinar_id) {
	$gener_rec_days_array = array();
	$gener_rec_days_saved = get_post_meta($webinar_id, '_wswebinar_gener_rec_days', true);
	if (!empty($gener_rec_days_saved)) {
	    $gener_rec_days_array = json_decode($gener_rec_days_saved, true);
	}

	$gener_rec_times_saved = get_post_meta($webinar_id, '_wswebinar_gener_rec_times', true);
	$gener_rec_times_array = array();
	if (!empty($gener_rec_times_saved)) {
	    $gener_rec_times_array = json_decode($gener_rec_times_saved, TRUE);
	}

	$timestamp_collection = array();
	$date = date('Y-m-d', time());
	foreach ($gener_rec_times_array as $time) {
	    if ($time != 'rightnow') {
		$timestamp = strtotime($date . ' ' . $time);
		array_push($timestamp_collection, $timestamp);
	    } else {
		// Equal to 'rightnow';
		array_push($timestamp_collection, $time);
	    }
	}
	sort($timestamp_collection);
	return array('days' => $gener_rec_days_array, 'times' => $timestamp_collection);
    }
    
    public static function getJustinTimeInstances($webinar_id){
	$gener_jit_days_array = array();
	$gener_jit_days_saved = get_post_meta($webinar_id, '_wswebinar_gener_jit_days', true);
	if (!empty($gener_jit_days_saved)) {
	    $gener_jit_days_array = json_decode($gener_jit_days_saved, true);
	}
	
	$gener_jit_times_saved = get_post_meta($webinar_id, '_wswebinar_gener_jit_times', true);
	$gener_jit_times_array = array();
	$gener_jit_time = filter_var($gener_jit_times_saved, FILTER_SANITIZE_NUMBER_INT);

	for ($x = 0; $x < 24; $x++) {
    for ($y = 0; $y < 60; $y+=$gener_jit_time) {
	$time = $x . ':' . $y;
    array_push($gener_jit_times_array, $time);
    }
	}

	return array('days' => $gener_jit_days_array, 'times' => $gener_jit_times_array);
	}

    /*
     * Return the plugin information
     */

    public static function plugin_info($needs = false) {
	$plugin_info = get_plugin_data(WSWEB_FILE);
	return ($needs == false ? $plugin_info : $plugin_info[$needs]);
    }

    /*
     * 
     * Return recurring time integers
     * 
     */

    public static function getRecurringInstancesInTime($webinar_id) {
	$array = array();
	$date_format = self::getWPformats(self::$WP_DATE_FORMAT);
	$time_format = self::getWPformats(self::$WP_TIME_FORMAT);
	$ins = self::getRecurringInstances($webinar_id);
	if (count($ins['days']) < 1 || count($ins['times']) < 1) {
	    return $array;
	}

	foreach ($ins['days'] as $day) {
	    foreach ($ins['times'] as $time) {
        if($time != 'rightnow') {
		$time = $time + 0;
		$humanTime = date($time_format, $time);
		$humanDate = date($date_format, strtotime($day));
		array_push($array, array('day' => $day, 'time' => $time, 'datetime' => WebinarSysteemMetabox::getWeekDayArray($day) . ' ' . $humanTime, 'date' => $humanDate));
        }
        }
	}

	return $array;
    }
    
    /*
     * 
     * Return recurring JIT time integers
     * 
     */
    public static function getJITInstancesInTime($webinar_id){

	$array = array();
	$date_format = self::getWPformats(self::$WP_DATE_FORMAT);
	$time_format = self::getWPformats(self::$WP_TIME_FORMAT);
	$ins = self::getJustinTimeInstances($webinar_id);

	if (count($ins['days']) < 1 || count($ins['times']) < 1) {
	    return $array;
	}
	foreach ($ins['days'] as $day) {
	    foreach ($ins['times'] as $time) {
		$time = strtotime($time);
                $time = $time + 0;
		
		$humanTime = date($time_format, $time);
		$humanDate = date($date_format, strtotime($day));
		array_push($array, array('day' => $day, 'time' => $time, 'datetime' => WebinarSysteemMetabox::getWeekDayArray($day) . ' ' . $humanTime, 'date' => $humanDate));
	    }
	}
        return $array;
	}

    public static function ado() {
	global $array;
	return strcmp($array[$a]['db'], $array[$b]['db']);
    }

    public static function getWebinarTime($webinar_id, $attendee = NULL) {

	$is_recurring = self::isRecurring($webinar_id);
	$gener_time = get_post_meta($webinar_id, '_wswebinar_gener_time', true);
	$time_zone = get_post_meta($webinar_id, '_wswebinar_timezoneidentifier', true);
	$one_time_reg = get_post_meta($webinar_id, '_wswebinar_gener_time_occur', true);

	if (!$is_recurring) {
	    if (empty($time_zone)) {
		$time_zone = date_default_timezone_get();
	    }
	    if ($time_zone) {
		$dtStr = @date('Y-m-d H:i:s', $gener_time);
		$date = new DateTime($dtStr);
		@$date->setTimestamp($gener_time);
		$date->setTimezone(new DateTimeZone($time_zone));
		return $date->getTimestamp();
	    } else {
		return (int) $gener_time;
	    }
	} else if ($is_recurring && !empty($attendee)) {
	    if ($one_time_reg != "recur" || $one_time_reg != "jit") {
		return strtotime($attendee->exact_time);
	    }
	    $duration = WebinarSysteem::getWebinarDuration($webinar_id);
	    $last_time_instance = strtotime($attendee->exact_time);
	    if (WebinarSysteem::populateDateTime($webinar_id) <= $last_time_instance + $duration) {
		return $last_time_instance;
	    } else {
		return '';
	    }
	} else {
	    return (int) $gener_time;
	}
	return FALSE;
    }

    public function getWebinarTimezone($webinar_id) {
	
	$timeabbr = get_post_meta($webinar_id, '_wswebinar_timezoneidentifier', true);
	$wpoffset = get_option('gmt_offset');
	$gmt_offset = WebinarSysteem::formatTimezone(( $wpoffset > 0) ? '+' . $wpoffset : $wpoffset);
	$timeZone = ( (!empty($timeabbr)) ? WebinarSysteem::getTimezoneAbbreviation($timeabbr) : 'UTC ' . $gmt_offset );
	
	return $timeZone;
    }

    /*
     * Gets attendee registered time in webinar timezone.
     * @param  $webinar_id
     * @param  string $format
     * @return string formatted current time
     */

    public static function getTimezoneTime($webinar_id, $format = NULL) {
	$time_zone = get_post_meta($webinar_id, '_wswebinar_timezoneidentifier', true);
	if ($format == null)
	    $format = 'Y-m-d H:i:s';

	if (empty($time_zone))
	    return current_time(($format == null ? 'timestamp' : $format));
	if ($time_zone) {
	    try {
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone($time_zone));
		return $date->format($format);
	    } catch (Exception $e) {
		return false;
	    }
	}
    }

    public static function getWebinarDuration($webinar_id) {
	$_wswebinar_gener_duration = get_post_meta($webinar_id, '_wswebinar_gener_duration', true);
	if (empty($_wswebinar_gener_duration))
	    $_wswebinar_gener_duration = 3600;
	$_wswebinar_gener_duration = floatval($_wswebinar_gener_duration);
	return $_wswebinar_gener_duration;
    }

    public function registerPermissionSettings() {
	global $wp_roles;
	$roles = $wp_roles->get_names();
	foreach ($roles as $roleSlug => $roleName) {
	    register_setting('wswebinar_options', '_wswebinar_createwebinars_' . $roleSlug);
	    register_setting('wswebinar_options', '_wswebinar_managesubscribers_' . $roleSlug);
	    register_setting('wswebinar_options', '_wswebinar_accesscontrolbar_' . $roleSlug);
	    register_setting('wswebinar_options', '_wswebinar_managequestions_' . $roleSlug);
	    register_setting('wswebinar_options', '_wswebinar_managechatlogs_' . $roleSlug);
	    register_setting('wswebinar_options', '_wswebinar_webinarsettings_' . $roleSlug);
	}
    }

    public function setPermissions() {
	global $wp_roles;
	$roles = $wp_roles->get_names();
	foreach ($roles as $roleSlug => $roleName) {
	    $role = get_role($roleSlug);
	    $createWebinars = $roleSlug == 'administrator' ? 'on' : get_option('_wswebinar_createwebinars_' . $roleSlug);
	    $manageSubscribers = $roleSlug == 'administrator' ? 'on' : get_option('_wswebinar_managesubscribers_' . $roleSlug);
	    $accessControlbar = $roleSlug == 'administrator' ? 'on' : get_option('_wswebinar_accesscontrolbar_' . $roleSlug);
	    $manageQuestions = $roleSlug == 'administrator' ? 'on' : get_option('_wswebinar_managequestions_' . $roleSlug);
	    $manageChatlogs = $roleSlug == 'administrator' ? 'on' : get_option('_wswebinar_managechatlogs_' . $roleSlug);
	    $manageWebinarSettings = $roleSlug == 'administrator' ? 'on' : get_option('_wswebinar_webinarsettings_' . $roleSlug);
	    //Add caps
	    if($createWebinars=="on"){
	    	
	    	$role->add_cap('_wswebinar_createwebinars');
	    	$role->add_cap('edit_wswebinar');
	    	$role->add_cap('delete_wswebinar');		
	    	$role->add_cap('read_wswebinar');
	    	$role->add_cap('publish_wswebinars');
	    	$role->add_cap('edit_wswebinars');
	    	$role->add_cap('edit_others_wswebinars');
	    	$role->add_cap('read_private_wswebinars');
	    	$role->add_cap('delete_wswebinars');
	    	
		}
		else{
			
		    $role->remove_cap('_wswebinar_createwebinars');
	    	$role->remove_cap('edit_wswebinar');
	    	$role->remove_cap('delete_wswebinar');		
	    	$role->remove_cap('read_wswebinar');
	    	$role->remove_cap('publish_wswebinars');
	    	$role->remove_cap('edit_wswebinars');
	    	$role->remove_cap('edit_others_wswebinars');
	    	$role->remove_cap('read_private_wswebinars');
	    	$role->remove_cap('delete_wswebinars');
	    	
		}
	    
	    $manageSubscribers=="on" ? $role->add_cap('_wswebinar_managesubscribers') : $role->remove_cap('_wswebinar_managesubscribers');
	    $accessControlbar=="on" ? $role->add_cap('_wswebinar_accesscbar') : $role->remove_cap('_wswebinar_accesscbar');
	    $manageQuestions=="on" ? $role->add_cap('_wswebinar_managequestions') : $role->remove_cap('_wswebinar_managequestions');
	    $manageChatlogs=="on" ? $role->add_cap('_wswebinar_managechatlogs') : $role->remove_cap('_wswebinar_managechatlogs');
	    $manageWebinarSettings=="on" ? $role->add_cap('_wswebinar_webinarsettings') : $role->remove_cap('_wswebinar_webinarsettings');
	}
    }
    
    /* Remove admin menu links based on user role */
    public function remove_admin_menu_links(){
		$user = wp_get_current_user();
   		$user_role = $user->roles ? $user->roles[0] : false;
        if($user_role === 'wpws_webinar_moderator'){
			remove_menu_page('tools.php');
			remove_menu_page( 'edit.php' );
			remove_menu_page('options-general.php');
		}
		
		if(!current_user_can('_wswebinar_managesubscribers')){
			remove_menu_page('edit.php?post_type=wswebinars&page=wswbn-attendees');
		}
		
		if(!current_user_can('_wswebinar_managequestions')){
			remove_menu_page('edit.php?post_type=wswebinars&page=wswbn-questions');
		}
		
		if(!current_user_can('_wswebinar_managechatlogs')){
			remove_menu_page('edit.php?post_type=wswebinars&page=wswbn-questions');
		}
		
		if(!current_user_can('_wswebinar_webinarsettings')){
			remove_menu_page('edit.php?post_type=wswebinars&page=wswbn-options');
		}
		
	}
	


    public function liveControlBar() {
	global $is_live_page;
	$webinar_id = get_the_ID();
	$status = !empty($_GET['force_show']) ? $_GET['force_show'] : get_post_meta($webinar_id, '_wswebinar_gener_webinar_status', true);
	$page = ($status == 'live' || $status == 'liv') ? 'livep_' : 'replayp_';
	$show_chatbox = get_post_meta($webinar_id, '_wswebinar_' . $page . 'show_chatbox', true);
	$show_questionbox = get_post_meta($webinar_id, '_wswebinar_' . $page . 'askq_yn', true);
	$isMediaElementJs = !in_array(get_post_meta($webinar_id, '_wswebinar_' . $page . 'vidurl_type', true), array('image', 'vimeo', 'iframe', 'youtubelive'));
	$has_permission_to_show = (current_user_can('manage_options') || current_user_can('_wswebinar_accesscbar'));
	if (!isset($is_live_page) || !$is_live_page || !$has_permission_to_show)
	    return;
	?>

	<div id="webinar-actionbar">
	    <ul>
		<li>
		    <a href="#" id="livep-play-button" class="wbn-icon wbnicon-play <?php if (!$isMediaElementJs) { ?>disable-hover<?php } ?>"></a>
		</li>
	    </ul>
	    <?php
	    $CTA_action = get_post_meta($webinar_id, '_wswebinar_' . $page . 'call_action', true);
	    $isManualCTA = ($CTA_action == 'manual');
	    $show_cta_status = get_post_meta($webinar_id, '_wswebinar_' . $page . 'manual_show_cta', true);
	    $actionbox_status = get_post_meta($webinar_id, '_wswebinar_' . $page . 'show_actionbox', true);
	    $show_hostb = get_post_meta($webinar_id, '_wswebinar_' . $page . 'hostbox_yn', true);
	    $show_descb = get_post_meta($webinar_id, '_wswebinar_' . $page . 'webdes_yn', true);
	    $show_inctv = get_post_meta($webinar_id, '_wswebinar_' . $page . 'incentive_yn', true);
	    ?>
	    <ul class="webinar-admin-chatico">
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Host and Description Box', WebinarSysteem::$lang_slug); ?>">
		    <a style="padding-top: 10px;" href="#" id="show_multi_boxes" class="text-center fa fa-info <?php echo ($show_hostb == 'yes' | $show_descb == 'yes' ? 'message-center-newmsg' : ''); ?>" ></a>
		</li>
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Question Box', WebinarSysteem::$lang_slug); ?>">
		    <a href="#" id="webinar_show_questionbox" data-webinarid="<?php echo $webinar_id; ?>" class="icon fa fa-question <?php echo ($show_questionbox == 'yes' ? 'message-center-newmsg' : '' ); ?>" style="font-size: 18px; padding-top: 7px; margin-top: 0px;"></a>
		</li>
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Message Center', WebinarSysteem::$lang_slug); ?>">
		    <a href="#" class="icon webi-class-comments webinar-message-center"></a>
		    <ul id="wswebinar_private_que" style="display: none;"></ul>
		</li>
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Live Chatbox', WebinarSysteem::$lang_slug); ?>">
		    <a href="#" data-ajaxurl="<?php echo home_url(); ?>/wp-admin/admin-ajax.php" id="webinar_show_chatbox" data-webinarid="<?php echo $webinar_id; ?>" class="icon fa fa-comment <?php echo ($show_chatbox == 'yes' ? 'message-center-newmsg' : '' ); ?>" style="font-size: 15px; padding-top: 8px; margin-top: 0px;"></a>
		</li>
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Incentive Box', WebinarSysteem::$lang_slug); ?>">
		    <a href="#" class="glyphicon glyphicon-gift" id="gift_icon" style="padding-top: 9px;top: 0;"></a>
		</li>
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Action Box', WebinarSysteem::$lang_slug); ?>">
		    <a href="#" class="fa fa-tasks <?php echo ($actionbox_status == 'yes' ? 'message-center-newmsg' : '') ?>" id="action_box_handle" style="padding-top: 9px; top: 0;"></a>
		</li>
		<li class="tooltip-livep cusrsor-pointer" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Clear all Raised Hands', WebinarSysteem::$lang_slug); ?>">
		    <a href="#" id="adminbar-handraised" class="text-center center-block fa fa-hand-paper-o" style="padding: 8px 7px 0px 3px;"></a>
		</li>
		<?php
		if ($isManualCTA):
		    ?>
	    	<li class="tooltip-livep" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php _e('Call to action', WebinarSysteem::$lang_slug); ?>">
	    	    <a style="padding: 8px 7px 0px 3px;" href="javascript:void(0)" id="show_cta_action" class="text-center fa fa-bullhorn <?php echo ($show_cta_status == 'yes' ? 'show message-center-newmsg' : '') ?>" ></a>
	    	</li>
		<?php endif; ?>
	    </ul>
	    <ul class="right-column pull-right">
		<li>
		    <a href="#" class="webinar_live_viewers">
			<span id="webinar-live-viewers-icon"></span>
			<span id="webinar-live-viewers">0</span>
		    </a>
		    <ul id="attendee-online-list" >
		    </ul>
		</li>
		<li>
		    <a href="#" class="disable-hover">Status : <span class='status-text'><?php echo $this->getWebinarStatusText(get_the_ID()); ?></span></a>
		</li>
	    </ul>
	</div>
	<?php
    }

    public static function getTimezone() {
	$timezone;
	$xyz;
	$gmt_opt = get_option('gmt_offset');
	$hourint = (int) $gmt_opt;
	$xyz = ($hourint > 0 ? '+' : '');
	$float = $gmt_opt - intval($gmt_opt);
	if ($float == 0) {
	    $timezone = '00';
	} else if ($float == 0.5) {
	    $timezone = '30';
	} else if ($float == 0.75) {
	    $timezone = '45';
	} else {
	    $timezone = '00';
	}
	$timezone_string = get_option('timezone_string');
	return $xyz . $hourint . ':' . $timezone . (empty($timezone_string) ? '' : " ($timezone_string)");
    }

    public function sendChat() {
	global $wpdb;
	$time = self::populateDateTime($_POST['webinar_id']);
	$table_name = $wpdb->prefix . $this->db_tablename_chats;
	$content = strip_tags($_POST['message_co']);
	$content = str_replace('\\', '', $content);
	$num = $wpdb->insert(
		$table_name, array(
	    'webinar_id' => $_POST['webinar_id'],
	    'admin' => ($_POST['is_ifadmin'] == 'true' ? true : false),
	    'private' => ($_POST['pvt_chatmd'] == 'true' ? true : false),
	    'attendee_id' => $_POST['attendee_i'],
	    'content' => make_clickable($content),
	    'timestamp' => date("Y-m-d H:i:s", $time)
		)
	);
	if ($num == 1)
	    echo json_encode(array('status' => TRUE, 'timestamp' => date("Y-m-d H:i:s", WebinarSysteem::populateDateTime($_POST['webinar_id']))));
	else
	    echo json_encode(array('status' => FALSE));
	die();
    }

    public function setEnabledChats() {
	$webinar_id = $_POST['webinar_id'];
	$active = $_POST['active'];
	$page = $_POST['page_category'];
	if (!empty($webinar_id)) {
	    $meta_value = ($active == 'true' ? 'yes' : '');
	    update_post_meta($webinar_id, '_wswebinar_' . $page . 'show_chatbox', $meta_value);
	    $questionbox = get_post_meta($webinar_id, '_wswebinar_' . $page . 'askq_yn', true);
	}
	echo json_encode(array('show_chatbox' => ($active == 'true' ? 'true' : 'false'), 'show_questionbox' => ($questionbox == 'yes' ? 'true' : 'false')));
	wp_die();
    }

    public function setEnabledQuestions() {
	$webinar_id = $_POST['webinar_id'];
	$active = $_POST['active'];
	$page = $_POST['page_category'];
	if (!empty($webinar_id)) {
	    $meta_val = ($active == 'true' ? 'yes' : '');
	    update_post_meta($webinar_id, '_wswebinar_' . $page . 'askq_yn', $meta_val);
	    $chatbox = get_post_meta($webinar_id, '_wswebinar_' . $page . 'show_chatbox', true);
	}
	echo json_encode(array('show_chatbox' => ($chatbox == 'yes' ? 'true' : 'false'), 'show_questionbox' => ($active == 'true' ? 'true' : 'false')));
	wp_die();
    }

    public function getTimezoneIdentifiers() {
	$time_zones = timezone_identifiers_list();
	$time_to_use = 'now'; # just a dummy time
	$time_zone_abbreviations = array();
	foreach ($time_zones as $time_zone_id) {
	    try {
		$dateTime = new DateTime($time_to_use);
		$dateTime->setTimeZone(new DateTimeZone($time_zone_id));
		$abbreviation = $dateTime->format('T');
		$gmtoffset = $dateTime->format('P');
		if (!isset($time_zone_abbreviations[$abbreviation])) {
		    $time_zone_abbreviations[$time_zone_id] = $time_zone_id . ' - ' . $abbreviation;
		}
	    } catch (Exception $exc) {
		continue;
	    }
	}
	return $time_zone_abbreviations;
    }

    /**
     * Uplift current time to webinar's timezone.
     * 
     * @param integer $webinar_id
     * @return integer UNIX Timestamp
     */
    public static function populateDateTime($webinar_id) {
	$time_zone = get_post_meta($webinar_id, '_wswebinar_timezoneidentifier', true);
	if ($time_zone) {
	    try {
		$defTimeZone = date_default_timezone_get();
		$date = date_create(date('Y-m-d H:i:s'), timezone_open($defTimeZone));
		date_timezone_set($date, timezone_open($time_zone));
		$formattedDate = date_format($date, 'Y-m-d H:i:s');
		return strtotime($formattedDate);
	    } catch (Exception $e) {
		return current_time('timestamp');
	    }
	} else {
	    return current_time('timestamp');
	}
    }

    public static function formatTimezone($timeZone) {
	$sign = ($timeZone >= 0) ? '+' : '-';
	$timeZone = str_replace(array("+", "-"), array(" ", " "), $timeZone);
	$init = $timeZone * 60 * 60;
	$hours = floor($init / 3600);
	$minutes = floor(($init / 60) % 60);
	return $sign . $hours . (($minutes > 0) ? '.' . $minutes : '' );
    }

    /**
     * Get timezone abbreviation from name.
     * 
     * @param string $timezone_id Ex: "Asia/Colombo"
     * @return string Timezone abbr or FALSE on failure.
     */
    public static function getTimezoneAbbreviation($timezone_id) {
	if (!$timezone_id)
	    return FALSE;

	$time_zones = timezone_identifiers_list();

	foreach ($time_zones as $time_zone_id) {
	    if ($time_zone_id != $timezone_id)
		continue;

	    $dateTime = new DateTime();
	    $dateTime->setTimeZone(new DateTimeZone($timezone_id));
	    return strtoupper($dateTime->format('T'));
	}
    }

    private function getUserSession() {
	$email = (isset($_COOKIE['_wswebinar_registered_email']) ? $_COOKIE['_wswebinar_registered_email'] : '' );
	$registered = (isset($_COOKIE['_wswebinar_registered']) ? $_COOKIE['_wswebinar_registered'] : '');
	$reg_key = (isset($_COOKIE['_wswebinar_registered_key']) ? $_COOKIE['_wswebinar_registered_key'] : '');
	$rdm_reg_key = (isset($_COOKIE['_wswebinar_regrandom_key']) ? $_COOKIE['_wswebinar_regrandom_key'] : '');
	$camefromShortcode = (isset($_COOKIE['_wswebinar_camefromshortcode']) ? $_COOKIE['_wswebinar_camefromshortcode'] : '');
	return array('has_session' => (empty($email) && empty($reg_key) && empty($registered)), 'email' => $email, 'registerd' => $registered, 'reg_key' => $reg_key, 'random_key' => $rdm_reg_key, 'camefromshortcode' => $camefromShortcode);
    }

    private function isRegforWebinar($webinar_id, $email = NULL) {
	global $wpdb;
	$cookie_data = $this->getUserSession();
	$email = $cookie_data['email'];
	$table_subsc = $wpdb->prefix . $this->db_tablename_subscribers;
	$attendee = $wpdb->get_var($wpdb->prepare('SELECT * FROM ' . $table_subsc . ' WHERE webinar_id = %d AND email = %s', $webinar_id, $email));
	if ($attendee == NULL) {
	    return FALSE;
	} else {
	    return TRUE;
	}
    }

    public static function isWebinarPage() {
	return isset($_GET['post_type']) && $_GET['post_type'] == 'wswebinars';
    }

    function run_updates() {

	// retrieve our license key from the DB
	$license_key = trim(WebinarSystemUpdate::get_license());

	// setup the updater
	$edd_updater = new WebinarSystemUpdate(WSWEB_STORE_URL, WSWEB_FILE, array(
	    'version' => $this->plugin_version, // current version number
	    'license' => $license_key, // license key (used get_option above to retrieve from DB)
	    'item_name' => WSWEB_ITEM_NAME, // name of this plugin
	    'author' => 'Lucy Eind'  // author of this plugin
		)
	);
    }

    function sanitize_license($new) {
	$old = get_option('_wswebinar_licensekey');
	if ($old && $old != $new) {
	    delete_option('edd_sample_license_activations_left');
	}
	return $new;
    }

    public static function activate_license($force = false) {
	// listen for our activate button to be clicked
	if (!isset($_POST['wpws_edd_license_activate']))
	    return;

	// run a quick security check
	if (!$force && !check_admin_referer('wsweb_license_nonce', 'wsweb_active'))
	    return; // get out if we didn't click the Activate button

	$license = is_multisite() ? $_POST['wbn_network_lkey'] : $_POST['_wswebinar_licensekey'];

	// data to send in our API request
	$api_params = array(
	    'edd_action' => 'activate_license',
	    'license' => trim($license),
	    'item_name' => urlencode(WSWEB_ITEM_NAME), // the name of our product in EDD
	    'url' => !empty($_POST['wpws_edd_license_url']) ? $_POST['wpws_edd_license_url'] : home_url()
	);

	// Call the custom API.
	$response = wp_remote_post(WSWEB_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

	// make sure the response came back okay
	if (is_wp_error($response))
	    return false;

	// decode the license data
	$license_data = json_decode(wp_remote_retrieve_body($response));

	// $license_data->license will be either "valid" or "invalid"
	if (empty($license_data->success) || $license_data->success != true)
	    return;

	update_option('edd_sample_license_status', $license_data->license);
	update_option('edd_sample_license_activations_left', $license_data->activations_left);
	update_option('edd_sample_license_license_limit', $license_data->license_limit);
	update_option('edd_sample_license_exp_date', strtotime($license_data->expires));

	if (is_multisite())
	    WebinarSystemUpdate::save_license_on_sites($license_data->license_limit == "0");
	else
	    WebinarSysteemOptions::wbn_non_mpmu_license_save();
    }

    function deactivate_license() {
	// listen for our activate button to be clicked
	if (!isset($_REQUEST['edd_license_deactivate']))
	    return;

	// run a quick security check
	if (!isset($_GET['edd_license_deactivate']))
	    if (!check_admin_referer('wsweb_license_nonce', 'wsweb_deactive'))
		return; // get out if we didn't click the Activate button

	    $license = trim(WebinarSystemUpdate::get_license()); // retrieve the license from the database
	// data to send in our API request
	$api_params = array(
	    'edd_action' => 'deactivate_license',
	    'license' => $license,
	    'item_name' => urlencode(WSWEB_ITEM_NAME), // the name of our product in EDD
	);

	// Call the custom API.
	$response = wp_remote_post(WSWEB_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

	// make sure the response came back okay
	if (is_wp_error($response))
	    return false;

	// decode the license data
	$license_data = json_decode(wp_remote_retrieve_body($response));

	// $license_data->license will be either "deactivated" or "failed"
	if (empty($license_data) || $license_data->license != 'deactivated')
	    return;

	delete_option('_wswebinar_licensekey');
	delete_option('_wswebinar_licensekey_network');
	delete_option('edd_sample_license_status');
	delete_option('edd_sample_license_activations_left');
	delete_option('edd_sample_license_exp_date');
	if (is_multisite()) {
	    $blogs = WebinarSystemUpdate::get_site_list();
	    foreach ($blogs as $blog) {
		delete_blog_option($blog['blog_id'], '_wswebinar_licensekey');
		delete_blog_option($blog['blog_id'], '_wswebinar_invalid_key');
	    }
	}

	wp_redirect(remove_query_arg('edd_license_deactivate'));
	exit;
    }

    function registrationFormSubmit($original_template, $force_run = FALSE) {
	global $wp, $post;

	if (isset($wp->query_vars["post_type"]) && $wp->query_vars["post_type"] === $this->post_slug)
	    if (!$force_run)
		return $original_template;

	$right_now_registered = FALSE;
	$already_regd = FALSE;
	$newly_registered = FALSE;

	@$postId = isset($_POST['wbnid']) ? $_POST['wbnid'] : $post->ID;
	$url = get_permalink($postId);

	if (isset($_POST['webinarRegForm']) && $_POST['webinarRegForm'] == 'submit') {

	    if (isset($_COOKIE['_wswebinar_regrandom_key']) && $this->isThisBrowser($_COOKIE['_wswebinar_regrandom_key'], $postId)) {
		$already_regd = true;
	    } else {
		$already_regd = false;
	    }

	    $inputEmail = $_POST['inputemail'];
	    $inputName = (!empty($_POST['inputname']) ? $_POST['inputname'] : '');
	    $inputTab = $_POST['webinarTab'];
	    $inputDay = isset($_POST['inputday']) ? $_POST['inputday'] : '';
	    $inputTime = isset($_POST['inputtime']) ? $_POST['inputtime'] : '';
	    
	    $cameFromShortcode = 'no';
	    if(isset($_POST['redirectAfter'])){
	        //Check if Registration Shortcode url parameter is set to Webinar Thankyou page
			if($_POST['redirectAfter'] === $url ){
				$cameFromShortcode = 'yes';
			} else {
				$cameFromShortcode = 'no';
			}
		}
		
	    if ($this->checkUserAlreadyRegisteredForWebinar($postId, $inputEmail)) {
		$already_regd = true;
		if (!$force_run) {
		    return wp_redirect($url);
		}
	    } else {
		$rettt = $this->saveRegFormData($postId, $inputName, $inputEmail, $inputDay, $inputTime, $inputTab, $cameFromShortcode);
		$newly_registered = true;
		$right_now_registered = ($rettt['inputDay'] == 'rightnow');


		$_GET['_wswebinarsystem_newly_registered' . $postId] = true;
	    }
	} elseif ($this->checkUserForSavedSessions()) {
	    $cookie_data = $this->getUserSession();
	    $cameFromShortcode = $cookie_data['camefromshortcode'];
	    
	    if($cameFromShortcode === 'no'){
	        $cur_browser = $this->isThisBrowser($_COOKIE['_wswebinar_regrandom_key'], $postId);
	        $already_regd = $cur_browser;
	    } else {
	        $already_regd = FALSE;
		    $newly_registered = TRUE;
		    $is_updated = self::updateShortcodeCookie();
	    }
	}

	$_GET['_wswebinarsystem_already_registered' . $postId] = $already_regd;
	//Redirect if shortcode form has a url
	if (isset($_POST['redirectAfter']))
	    return wp_redirect($_POST['redirectAfter']);

	return $force_run ? array('already_regd' => $already_regd, 'new_registd' => $newly_registered, 'right_now_registered' => $right_now_registered) : $original_template;
    }

    private static function prepareRegFormCustomFields($postId) {
	$fields = json_decode(get_post_meta($postId, '_wswebinar_regp_custom_field_json', true));
	$data = array();
    
    if(!empty($fields)){
	foreach ($fields as $field) {
	    if ($field->type == 'checkbox') {
                $data[] = array('id' => $field->id, 'value' => empty($_POST["ws-{$field->id}"]) ? 'No' : 'Yes');
		continue;
            }else{
                $data[] = array('id' => $field->id, 'value' => $_POST["ws-{$field->id}"]);
            }
	}
    }
	return json_encode($data);
    }

    public function logoutWebinars() {
	$cookie = isset($_COOKIE['_wswebinar_regrandom_key']) ? $_COOKIE['_wswebinar_regrandom_key'] : 0;
	if ($this->isThisBrowser($cookie) == false) {
	    $this->clearUserSession();
	    $plugindir = dirname($this->_FILE_);
	    return $plugindir . '/' . 'includes/tmp-registration.php';
	}
    }

    public function postNotices() {
	$usermeta = get_user_meta(get_current_user_id(), '_wswebinar_postnotdismiss', true);
	if ($usermeta == 'yes') {
	    return;
	}
	$args = array(
	    'post_type' => 'wswebinars',
	    'post_status' => 'publish',
	    'posts_per_page' => -1,
	    'ignore_sticky_posts' => 1
	);

	$query_posts = get_posts($args);

	foreach ($query_posts as $post) {
	    setup_postdata($post);
	    $date_entered = get_post_meta($post->ID, '_wswebinar_gener_date', true);
	    if (empty($date_entered) && !$this->isRecurring($post->ID)):
		?>
		<div class="error wswebinar_adnotice wswebinar_adnotice_post">
		    <p>Please configure a time and date for your webinar <a href="<?php echo get_edit_post_link($post->ID); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(array('post' => $post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a> to work properly.</p>
		    <div class="closeIcon">
			<i class="wbn-icon wbnicon-close close_post_notification"></i>
		    </div>
		</div>
		<?php
	    endif;

	    wp_reset_postdata();
	    wp_reset_query();
	}
    }
    
    public function check_mysql_and_php_version() {
        if (!get_option('wpws-admin-notice')) {
            return;
        }

        if (WebinarSysteemRequirements::is_database_version_out_of_date()) {
            ?>
            <div class="error wswebinar_adnotice">
                <p><?php _e('Your MySQL version is out of date, and some functionalities of WP WebinarSystem won\'t work as expected. Please upgrade the MySQL version on your server to a minimum of MySQL 5.6.36 or higher.', WebinarSysteem::$lang_slug)?></p>
            </div>
            <?php
        }

        if (WebinarSysteemRequirements::is_php_version_out_of_date()) { ?>
            <div class="error wswebinar_adnotice">
                <p><?php _e('Your PHP version is out of date, and some functionalities of WP WebinarSystem won\'t work as expected. Please upgrade the PHP version on your server to a minimum of PHP 5.6 or higher.', WebinarSysteem::$lang_slug)?></p>
            </div>
            <?php
        }
	}
	
	public function wpws_plugin_notice_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		if (isset($_GET['wpws-db-ignore-notice'])) {
		
		add_user_meta($user_id, 'wpws_db_plugin_notice_ignore', 'true', true);
		
		} else if (isset($_GET['wpws-php-ignore-notice'])) {
		add_user_meta($user_id, 'wpws_php_plugin_notice_ignore', 'true', true);
		}
	}

    public function isRightnow($webinar_id) {
	if ($this->isRecurring($webinar_id)) {
	    $time_slots = get_post_meta($webinar_id, '_wswebinar_gener_rec_times', true);
	    if (!empty($time_slots)) {
		$slot_ar = json_decode($time_slots);
		return (in_array('rightnow', $slot_ar));
	    }
	}
	return false;
    }

    public function getNextRecurringTime($webinar_id) {
	$isRecurring = $this->isRecurring($webinar_id);
	$isRightnow = $this->isRightnow($webinar_id);
	$gener_time_occur_saved = get_post_meta($webinar_id, '_wswebinar_gener_time_occur', true);
	$value='';
	if ($isRecurring) {
	    if($gener_time_occur_saved == 'recur' && !$isRightnow){
	    $recurr_instances = $this->getRecurringInstances($webinar_id);
			$date_format = get_option('date_format');
    		$time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
	    	$date_time_instances_unix = array();
	    	$filtered_date_time = array();
    	
    		foreach($recurr_instances['days'] as $day){
    			$day_string = "next $day";
				if (strtolower(date('D')) == $day)
	 		    $day_string = "this $day";
	 		    $day = strtotime($day_string);
    			$date = date($date_format, strtotime('today ' . date("D", $day)));
				
				foreach($recurr_instances['times'] as $time){
					$time = date($time_format, $time);
					$date_time_instances_unix[] = strtotime($date ."". $time);
				}
			}
  			$cur_time = $this->populateDateTime($webinar_id);
 			
  			foreach($date_time_instances_unix as $key){
  			if($cur_time < $key){
				$filtered_date_time[] = $key;
			}
  			}
  			sort($filtered_date_time);
  			if($filtered_date_time){
  				$value = $filtered_date_time[0];
  			}
	 
    	return $value;
    	
		}
	   }
	}
    
    public function getNextJITRecurringTime($webinar_id){
		$jit_instances = $this->getJustinTimeInstances($webinar_id);
		$date_format = get_option('date_format');
		$time_format = WebinarSysteem::getWPformats(WebinarSysteem::$WP_TIME_FORMAT);
		$date_time_instances_unix = array();
		$filtered_date_time = array();
		$value = '';
		
		foreach($jit_instances['days'] as $day){
			$day_string = "next $day";
			
			if (strtolower(date('D')) == $day)
	 		    $day_string = "this $day";
	 		    $day = strtotime($day_string);
	 		
	 		$date = date($date_format, strtotime('today ' . date("D", $day)));
	 		
	 		foreach($jit_instances['times'] as $time){
				$time = strtotime($time);
				$time = date($time_format, $time);
				$date_time_instances_unix[] = strtotime($date ."". $time);
			}
		}
		
		$cur_time = $this->populateDateTime($webinar_id);
		
		foreach($date_time_instances_unix as $key){
  			if($cur_time < $key){
				$filtered_date_time[] = $key;
			}
  		}
  		
  		sort($filtered_date_time);
  		
  		if($filtered_date_time){
			$value = $filtered_date_time[0];
		}
		
		return $value;
	}

    public static $WP_DATE_FORMAT = 1, $WP_TIME_FORMAT = 2, $WP_DATE_TIME_FORMAT = 3;

    public static function getWPformats($WP_TIME_ANNOT) {

	$time_format = get_option('time_format');
	$date_format = get_option('date_format');

	if ($WP_TIME_ANNOT == self::$WP_DATE_FORMAT) {
	    return $date_format;
	} else if ($WP_TIME_ANNOT == self::$WP_TIME_FORMAT) {
	    return $time_format;
	} else if ($WP_TIME_ANNOT == self::$WP_DATE_TIME_FORMAT) {
	    return $date_format . ' ' . $time_format;
	} else {
	    throw new Exception("Invalid argument for getWPformats function in WebinarSysteem class.");
	}
    }

    /*
     * Return the login message.
     */

    function my_login_logo_url_title($messages) {
	return 'This Webinar is protected private webinar. Please login to continue.';
    }

    /*
     * Check if user can access webinar.
     * @Return bool yes or no
     */

    private static $ACCESS_EVERYONE = 'everyone',
	    $ACCESS_USER_ROLES = 'user_roles',
	    $ACCESS_MEMBER_LEVELS = 'member_levels',
	    $ACCESS_USER_IDS = 'user_ids';

    private static function can_access_webinar($webinar_id) {
	$webinar_access = get_post_meta($webinar_id, '_wswebinar_accesstab_parent', true);
	$isAuth = is_user_logged_in();
	$cur_user_id = get_current_user_id();

	$auth_reqset = array(self::$ACCESS_USER_ROLES, self::$ACCESS_MEMBER_LEVELS, self::$ACCESS_USER_IDS);
	if (in_array($webinar_access, $auth_reqset)) {
	    if (!$isAuth) {
		self::redirectAccessPages($webinar_id, false);
		exit();
	    }
	}
	switch ($webinar_access) {
	    case self::$ACCESS_EVERYONE:
		return true;
	    case self::$ACCESS_USER_ROLES:
		$current_user_roles_str = get_post_meta($webinar_id, '_wswebinar_selected_user_role', true);
		$user_roles_array = explode(',', $current_user_roles_str);
		foreach ($user_roles_array as $cap) {
		    if (current_user_can($cap))
			return true;
		}
	    case self::$ACCESS_USER_IDS:
		$selected_users = get_post_meta($webinar_id, '_wswebinar_filter_user_ids', true);
		$multi_usr_ar = explode(',', $selected_users);
		return in_array($cur_user_id, $multi_usr_ar);
	    case self::$ACCESS_MEMBER_LEVELS:
		$webinar_member_level_id = get_post_meta($webinar_id, '_wswebinar_selected_member_level', true);
		$user_mem_level = wc_memberships_get_user_membership($cur_user_id);
		$args = array(
		    'status' => array('active', 'complimentary', 'pending'),
		);
		$active_memberships = wc_memberships_get_user_memberships($cur_user_id, $args);
		if (!empty($active_memberships)) {
		    foreach ($active_memberships as $memship) {
			if ($memship->plan_id == $webinar_member_level_id) {
			    return true;
			}
		    }
		}
		return false;
	    default :
		return true;
	}
    }

    public static function redirectAccessPages($postId, $canAccess) {
	$redirect_page = get_post_meta($postId, '_wswebinar_ws_actab_redirect_page', true);
	$exp_array = explode("-", $redirect_page);
	$page_id = (is_array($exp_array) ? $exp_array[count($exp_array) - 1] : '0');
	$page_id = (!empty($page_id) ? str_replace(' ', '', $page_id) : '0');
	if (!$canAccess) {
	    if ($page_id != '0'):
		$url = get_permalink($page_id);
		wp_redirect($url);
	    else:
		wp_redirect(home_url());
	    endif;
	}
    }

    function webinar_exclude_plugins($plugins) {

	// We are not in Webinar Ajax
	if (!defined('DOING_AJAX') || !DOING_AJAX || !defined('DOING_WEBINAR_AJAX') || !DOING_WEBINAR_AJAX) {
	    return $plugins;
	}

	foreach ($plugins as $key => $plugin) {
	    if (false === strpos($plugin, 'wpwebinarsystem')) {
		unset($plugins[$key]);
	    }
	}
	return $plugins;
    }

    function sanitize_use_theme_style_default_values() {
	return empty($_POST['_wswebinar_enable_theme_styles']) ? 'off' : 'on';
    }

    public static function get_timezone_str_by_utc_offset($offset) {
	/*
	 * Required Offset : -5:30
	 */
	list($hours, $minutes) = explode(':', $offset);
	$seconds = $hours * 60 * 60 + $minutes * 60;
	$tz = timezone_name_from_abbr('', $seconds, 1);
	if ($tz === false) {
	    $tz = timezone_name_from_abbr('', $seconds, 0);
	}
	return $tz;
    }

    /*
     * Returns a random string.
     * @return String
     */

    public static function RandomString($length = 10) {
        $characters = '0123456789';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            @$randstring = $randstring . $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }

    /*
     * Returns unique browser or not
     * @returns boolean
     * @param {String} random code from cookie
     */

    private function isThisBrowser($rand_from_cookey, $post_id) {
	global $wpdb;
	$table = $wpdb->prefix . $this->db_tablename_subscribers;
	$row = $wpdb->get_row("SELECT * FROM $table WHERE random_key='$rand_from_cookey' and webinar_id='$post_id'");
	return !empty($row);
    }
    
    /*
     * Returns custom field name by id.
     */
    public static function getFieldName($post_id, $field_id){
        $namesOb = json_decode(get_post_meta($post_id, self::$lang_slug.'_regp_custom_field_json',true));
        foreach ($namesOb as $nameOb) {
            if($nameOb->id == $field_id){
                return $nameOb->labelValue;
            }
        }
        return "";
    }
    
    /*
     * Returns custom field value by id.
     */
    public static function getFieldValue($attendee_row, $field_id){
        $json = json_decode($attendee_row->custom_fields);
        if (!empty($json)) {
            foreach ($json as $values) {
                if ($values->id == $field_id) {
                    return $values->value;
                }
            }
        }
        return "";
    }
    
    /* Create new role webinar moderator */
    public function createRoles() {
	add_role('wpws_webinar_moderator', 'Webinar Moderator', array(
	    'read' => true,
	    'manage_options' => true,
	    'edit_others_posts' => true,
	    'delete_others_posts' => true,
	    'delete_private_posts' => true,
	    'edit_private_posts' => true,
	    'read_private_posts' => true,
	    'edit_published_posts' => true,
	    'publish_posts' => true,
	    'delete_published_posts' => true,
	    'edit_posts' => true,
	    'delete_posts' => true,
	    '_wswebinar_createwebinars' => true,
	    '_wswebinar_managesubscribers' => true,
	    '_wswebinar_managequestions' => true,
	    '_wswebinar_managechatlogs' => true,
	    '_wswebinar_webinarsettings' => true,
	    'publish_wswebinars' => true,
	    'edit_wswebinars' => true,
	    'edit_others_wswebinars' => true,
	    'read_private_wswebinars' => true,
	    'read_wswebinar' => true,
	    'edit_wswebinar' => true,
	    'delete_wswebinar' => true,
	    'delete_wswebinars' => true,
	    
	));
	$is_createwebinars_wpws_webinar_moderator = get_option('_wswebinar_createwebinars_wpws_webinar_moderator');
	$is_managesubscribers_wpws_webinar_moderator = get_option('_wswebinar_managesubscribers_wpws_webinar_moderator');
	$is_managequestions_wpws_webinar_moderator = get_option('_wswebinar_managequestions_wpws_webinar_moderator');
	$is_managechatlogs_wpws_webinar_moderator = get_option('_wswebinar_managechatlogs_wpws_webinar_moderator');
	$is_changesettings_wpws_webinar_moderator = get_option('_wswebinar_webinarsettings_wpws_webinar_moderator');
	$is_accesscontrolbar_wpws_webinar_moderator = get_option('_wswebinar_accesscontrolbar_wpws_webinar_moderator');
	
	if(!isset($is_createwebinars_wpws_webinar_moderator) || (isset($is_createwebinars_wpws_webinar_moderator) && $is_createwebinars_wpws_webinar_moderator !== "off"))
	update_option('_wswebinar_createwebinars_wpws_webinar_moderator', 'on');
	
	if(!isset($is_managesubscribers_wpws_webinar_moderator) || (isset($is_managesubscribers_wpws_webinar_moderator) && $is_managesubscribers_wpws_webinar_moderator !== "off"))
	update_option('_wswebinar_managesubscribers_wpws_webinar_moderator', 'on');
	
	if(!isset($is_managequestions_wpws_webinar_moderator) || (isset($is_managequestions_wpws_webinar_moderator) && $is_managequestions_wpws_webinar_moderator !== "off"))
	update_option('_wswebinar_managequestions_wpws_webinar_moderator', 'on');
	
	if(!isset($is_managechatlogs_wpws_webinar_moderator) || (isset($is_managechatlogs_wpws_webinar_moderator) && $is_managechatlogs_wpws_webinar_moderator !== "off"))
	update_option('_wswebinar_managechatlogs_wpws_webinar_moderator', 'on');
	
	if(!isset($is_changesettings_wpws_webinar_moderator) || (isset($is_changesettings_wpws_webinar_moderator) && $is_changesettings_wpws_webinar_moderator !== "off"))
	update_option('_wswebinar_webinarsettings_wpws_webinar_moderator', 'on');
	
	if(!isset($is_accesscontrolbar_wpws_webinar_moderator) || (isset($is_accesscontrolbar_wpws_webinar_moderator) && $is_accesscontrolbar_wpws_webinar_moderator !== "off"))
	update_option('_wswebinar_accesscontrolbar_wpws_webinar_moderator', 'on');
	
    }

    public function purgeRoles() {
	remove_role('wpws_webinar_moderator');
    }
    
    public function wpws_admin_bar_render() {
    	
	global $wp_admin_bar;
	if (!current_user_can('_wswebinar_createwebinars')){
			$wp_admin_bar->remove_menu('new-wswebinars');
		}
	
    }
    
    /* Allow Privileged roles to edit Webinar Settings */
    public function wswebinar_options_page_capability() {
		return 'read';
	}
}
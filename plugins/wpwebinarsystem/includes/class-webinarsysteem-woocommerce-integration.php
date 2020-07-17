<?php

/**
 * Description of WebinarSysteemWooCommerceIntegration
 * Integrate WooCommerce for paid webinars. Tickets aka Products will be sold.
 * 
 * @package  WebinarSysteem/WooCommerceIntegration
 * @author Thambaru Wijesekara <howdy@thambaru.com>
 */
 
define('ASSFURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
class WebinarSysteemWooCommerceIntegration { 
    /**
     * Checks if WooCommerce plugin exists
     * 
     * @return boolean
     */
   public static function isWCready() {
	return class_exists('WooCommerce');
    }

    /**
     * Checks if user enabled the integration
     * 
     * @return boolean
     */
    public static function isEnabled() {
	return get_option('_wswebinar_enable_woocommerce_integration') == 'on';
    }

    /**
     * Checks whether WooCommerce plugin exists and the user enabled the integration
     * 
     * @return boolean
     */
    public static function isReady() {
	return self::isWCready() && self::isEnabled();
    }

    /**
     * Shows an admin notice if WooCommerce is not ready but the integration
     * 
     * @return void
     */
    public static function noWCNotice() {
	if (self::isEnabled() && !self::isWCready()) {
	    ?>
	    <div class="error">
	        <p><?php printf(__('Please install/activate WooCommerce first to integrate with WebinarSystem. You can find WooCommerce <a href="%s" class="thickbox" aria-label="Download WooCommerce for WebinarSystem" data-title="WooCommerce">here</a>.', WebinarSysteem::$lang_slug), admin_url("plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true")); ?></p>
	    </div>
	    <?php
	}
    }

	/**
	* Adds custom fields to the product page for Recurring Webinar type
	* 
	* @return
	*/
 	public static function add_webinar_recurring_fields() {
		global $post;
		$wbnId = get_post_meta($post->ID, '_wpws_webinarID', true);
		$timeabbr = get_post_meta($wbnId, '_wswebinar_timezoneidentifier', true);
		$wpoffset = get_option('gmt_offset');
		$gmt_offset = WebinarSysteem::formatTimezone(( $wpoffset > 0) ? '+' . $wpoffset : $wpoffset);
		$timeZone = '(' . ( (!empty($timeabbr)) ? WebinarSysteem::getTimezoneAbbreviation($timeabbr) : 'UTC ' . $gmt_offset ) . ') ';

		$product = wc_get_product( $post->ID);
		$gener_time_occur_saved = get_post_meta($wbnId, '_wswebinar_gener_time_occur', true);

		if($gener_time_occur_saved == 'jit' || $gener_time_occur_saved == 'recur'){
    	echo '<h3>Select Webinar Date and Time</h3>';
		if($gener_time_occur_saved == 'recur'){
			include'templates/template-recurring-days-times-selects-boxes.php';
		} else if( $gener_time_occur_saved == 'jit'){
			include'templates/template-jit-days-times-selects-boxes.php';	
		}
		wp_enqueue_script('wpws-external', ASSFURL.'/js/webinarsystem-external.js', array('jquery',),'',false);
        wp_localize_script('wpws-external', 'wpexternal', array(
            'available_timeslots' => __('Loading available timeslots...', WebinarSysteem::$lang_slug),
            'ajaxurl' => admin_url('admin-ajax.php')
    	));
		?>
		<style>
		select[name="inputday"] {
		margin: 10px 0px;
		}
		select[name="inputtime"] {
		margin: 10px 0px;
		}
		</style>

		<?php
		}
	}

	/**
	* Validate Custom Webinar product fields 
	*
	* $passed_validation set to TRUE by default
	* @param undefined $product_id
	* 
	* @return
	*/
    public static function webinar_fields_validation($passed_validation, $product_id) { 

		$wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
		$is_recurring = webinarsysteem::isRecurring($wbnId);
		if($is_recurring)
		{
 			$already_in_cart = self::checkIfProductAlreadyAddedinCart($product_id);
 			if($already_in_cart)
 			{
 				$passed_validation = FALSE;
 				$product = wc_get_product( $product_id );
       			$error_message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'woocommerce' ), sprintf( __( 'You cannot add another &quot;%s&quot; to your cart.', 'woocommerce' ), $product->get_title() ) );
       			wc_add_notice( $error_message, 'error' );
 		}
 		else
 		{
 	  	if ( empty( $_REQUEST['inputday'] ))
 	  	{
   				$passed_validation = FALSE;
       			wc_add_notice( __( 'Please select a Day.', 'woocommerce' ), 'error' );
   		}
		
		if ( empty( $_REQUEST['inputtime'] ))
		{
			if(isset($_REQUEST['inputday'])) {
				if($_REQUEST['inputday'] !== 'rightnow') {
					
					$passed_validation = FALSE;
       				wc_add_notice( __( 'Please select a time.', 'woocommerce' ), 'error' );
         
        	}
    	} }
 		}

   	} 
    return $passed_validation;
	}
	
	/**
	* Add GDPR Opt-in field on checkout page
	* 
	* @return
	*/
	public static function customise_checkout_field() {
		$cart_items = WC()->cart->get_cart();
		$gdpr_optin = false;
		$gdpr_text = '';
		foreach( $cart_items as $cart_item ) {
			$product_id = $cart_item['product_id'];
			$wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
			if($wbnId){
				$regp_gdpr_optin_yn_value = get_post_meta($wbnId, '_wswebinar_regp_wc_gdpr_optin_yn', true);
				$showGDPROptin = ($regp_gdpr_optin_yn_value == "yes") ? true : false;
				if($showGDPROptin){
					$gdpr_optin = TRUE;
					$gdpr_text= get_post_meta($wbnId, '_wswebinar_regp_gdpr_optin_text', true);  
				}
				break;
			}
		}
		if($gdpr_optin){
			echo '<div id="customise_checkout_field">';
		woocommerce_form_field('wpws_gdpr_optin', array(
		'type' => 'checkbox',
		'class' => array(
			'input-checkbox form-row-wide'
		) ,
		'label' => $gdpr_text ,
		'required' => true,
		));
	echo '</div>';
		}
	}

	/**
	* Validate GDPR Opt-in field on checkout page
	* 
	* @return
	*/
	public static function customise_checkout_field_process() {
		$cart_items = WC()->cart->get_cart();
		$gdpr_optin = false;
		foreach( $cart_items as $cart_item ) {
			$product_id = $cart_item['product_id'];
			$wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
			if($wbnId){
				$regp_gdpr_optin_yn_value = get_post_meta($wbnId, '_wswebinar_regp_wc_gdpr_optin_yn', true);
				$showGDPROptin = ($regp_gdpr_optin_yn_value == "yes") ? true : false;
				if($showGDPROptin){
					$gdpr_optin = TRUE; 
				}
				break;
			}
		}
		if($gdpr_optin){
		if(!$_POST['wpws_gdpr_optin']){
			wc_add_notice(__('Please check the box to proceed further.') , 'error');
		}	
		}
	}


	public static function checkIfProductAlreadyAddedinCart($product_id)
	{
		foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
	
				if( $product_id == $_product->get_id() ) 
				{
				return true;
				}
	}
	return FALSE;
	}
	
	public static function save_webinar_fields( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['inputday'] ) ) {
        $cart_item_data[ 'inputday' ] = $_REQUEST['inputday'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    if( isset( $_REQUEST['inputtime'] ) ) {
        $cart_item_data[ 'inputtime' ] = $_REQUEST['inputtime'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    
    return $cart_item_data;
	}

	public static function custom_order_meta_handler($itemId, $item, $orderId) {
        $product_id = $item['product_id'];
	    $wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
	    $is_recurring = webinarsysteem::isRecurring($wbnId);
	    if($is_recurring) {
        wc_add_order_item_meta( $itemId, "inputday", $item->legacy_values['inputday'] );
		if($item->legacy_values['inputday'] != 'rightnow')
		{
                  wc_add_order_item_meta( $itemId, "inputtime", $item->legacy_values['inputtime'] );
		}
	   }
	}

    /**
     * Determines whether WC product should be created or updated and
     * call corresponding functions.
     * 
     * @param int $postId
     * @return void
     */
    public static function createOrUpdateTicket($postId) {
	if (!self::isWCready() || get_post_type($postId) !== 'wswebinars')
	    return;

	$productId = get_post_meta($postId, '_wswebinar_ticket_id', true);

	$productDetails = array(
	    'title' => esc_attr(get_post_meta($postId, '_wswebinar_ticket_title', true)),
	    'description' => esc_attr(get_post_meta($postId, '_wswebinar_ticket_description', true)),
	    'price' => esc_attr(get_post_meta($postId, '_wswebinar_ticket_price', true)),
	);

	if (empty($productId)) {
	    $ticketId = self::createTicket($postId, $productDetails);
	    update_post_meta($postId, '_wswebinar_ticket_id', $ticketId);
	} else {
	    self::updateTicket($postId, $productId, $productDetails);
	}
    }

    /**
     * Creates a WC product.
     * 
     * @param int $postId
     * @param array $productDetails
     * @return int $id
     */
    static function createTicket($postId, $productDetails) {
	$id = wp_insert_post(array(
	    'post_type' => 'product',
	    'post_title' => $productDetails['title'],
	    'post_content' => $productDetails['description'],
	    'post_status' => get_post_status($postId),
            'max_value' => 1,
	));
	update_post_meta($id, '_price', $productDetails['price']);
	update_post_meta($id, '_regular_price', $productDetails['price']);
	update_post_meta($id, '_wpws_webinarID', $postId);
	update_post_meta($id, '_sold_individually', "yes");
    wp_set_object_terms( $id, 'webinar', 'product_type' );
	return $id;
    }

    /**
     * Updates WC product
     * 
     * @param int $postId
     * @param int $productId
     * @param array $productDetails
     */
    static function updateTicket($postId, $productId, $productDetails) {
	wp_update_post(array(
	    'ID' => $productId,
	    'post_title' => $productDetails['title'],
	    'post_content' => $productDetails['description'],
	    'post_status' => get_post_status($postId)
	));
	update_post_meta($productId, '_price', $productDetails['price']);
	update_post_meta($productId, '_regular_price', $productDetails['price']);
	update_post_meta($productId, '_wpws_webinarID', $postId);
    update_post_meta($productId, '_sold_individually', "yes");
    wp_set_object_terms( $productId, 'webinar', 'product_type' );
    }

	public static function add_simple_webinar_product( $types ){
		$types[ 'webinar' ] = __( 'Webinar Product' );
		return $types;
	}

    /**
     * Sends an email with a link to register for the webinar
     * 
     * @param int $order_id
     * @return void
     */
    static function register_attendee_in_webinar($order_id) {
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item['product_id'];
            $product_details = wc_get_product($product_id);

            // This is a very nasty hack to make WP WebinarSystem work with
            // LearnPress Woo Payment Module. It looks like they don't correctly
            // set the product id even though the product is not a course
            // which breaks our activation code.

            if (!$product_details && isset($item['course_id'])) {
                $product_id = $item['course_id'];
                $product_details = wc_get_product($product_id);
            }

            $inputDay = wc_get_order_item_meta($item_id, "inputday", true);
            $inputTime = WC_get_order_item_meta($item_id, "inputtime", true);

            if(empty($inputDay)) {
                $inputDay = NULL;
            }

            if(empty($inputTime)) {
                $inputTime = NULL;
            }

            $wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
            $first_name = $order->get_billing_first_name();
            $billing_email = $order->get_billing_email();

            if (empty($wbnId))
                continue;

            // todo, check if we are already registered before registering again and sending emails

            $is_registered = WebinarSysteem::saveRegFormData($wbnId, $first_name, $billing_email,
                $inputDay, $inputTime, $inputTab = 'register');

            $mail = new WebinarSysteemMails();
            $mail->SendMailtoReaderOnWCOrderComplete($first_name, $billing_email, $wbnId);
        }
    }

    static function on_woo_order_status_completed($order_id) {
        WebinarSysteemWooCommerceIntegration::register_attendee_in_webinar($order_id);
    }

    static function on_woo_commerce_order_status_changed($order_id, $old_status, $new_status) {
        if ($new_status == 'completed') {
            WebinarSysteemWooCommerceIntegration::register_attendee_in_webinar($order_id);
        }
    }

    /**
     * Adds webinar id to WC product.
     * 
     * @return void
     */
    static function addWebinarIDField() {
	echo "<script>jQuery('.show_if_simple').addClass('show_if_webinar');
	</script>";
	woocommerce_wp_hidden_input(
		array(
		    'id' => '_wpws_webinarID',
		    'value' => ''
		)
	);
    }



    public static function generateUniqueMailURL() {
	return '?utm_source=' . rand(846554, 999999999) . '&cont=' . md5(rand()) . '&opt=' . md5(rand()) . '&mapdoi=' . md5(rand(100, 200)) . '&key=' . md5(rand(100, 300));
    }

    public static function generateUniquePurchasedURL() {
	return '?utm_source=' . md5(rand()) . '&unt=' . md5(rand()) . '&opt=' . rand(95127, 999999999) . '&mapdoi=' . md5(rand(100, 200)) . '&key=' . md5(rand(100, 300)) . '&auth=' . md5(rand());
    }

     /**
     * Hide custom fields in order confirmation mail.
     * 
     * @param integer $order_id
     * @param bool $is_admin_email
     */
    public static function hide_custom_fields_in_order_email( $order, $is_admin_email ) {
        $order = wc_get_order($order);
        foreach ($order->get_items() as $item_id => $item) {
	        $product_id = $item['product_id'];
            $wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
                if(!empty($wbnId)) {
                ?>
                <style>
                .wc-item-meta{
                    display:none;
                }
        </style> <?php }
        }
    }
    /**
     * Show webinar ticket details on WC ThankYou/OrderConfirmation page.
     * 
     * 
     * @param integer $order_id
     */
    static function joinTheWebinarOrderConfirmation($order_id) {
	$order = wc_get_order($order_id);

	$items = $order->get_items();

	foreach ($items as $item) {
	    $product_id = $item['product_id'];

	    $wbnId = get_post_meta($product_id, '_wpws_webinarID', true);

	    if (empty($wbnId))
		continue;
	    ?>
	    <style>
	        .wpwebinarsystem-join-webinar-wc-notice{
	    	margin: 15px 0px;
	    	border: 1px solid;
	    	overflow: auto;
	    	padding: 10px;
	        }
	        .wpwebinarsystem-join-webinar-wc-notice h4{
	    	margin: 0px;
	        }
	        .wpwebinarsystem-join-webinar-wc-notice p{
	    	margin: 10px;
	    	float: left;
	        }
            .wc-item-meta{
            display:none;
            }
	    </style>
	    <div class="wpwebinarsystem-join-webinar-wc-notice">
	        <h4><?php _e('Your webinar ticket', WebinarSysteem::$lang_slug) ?></h4>
	        <p><?php _e('You will receive your webinar ticket as soon as the payment for your order completes.', WebinarSysteem::$lang_slug); ?> </p>
	    </div>
	    <?php
	}
    }

    static function showTicketListOnDashboard() {
		$customer_orders = get_posts(array(
	    'meta_key' => '_customer_user',
	    'meta_value' => get_current_user_id(),
	    'post_type' => wc_get_order_types(),
	    'post_status' => array_keys(wc_get_order_statuses()),
	));?>
		<h2><?php _e('My Webinar Tickets', WebinarSysteem::$lang_slug) ?></h2> 
		<table class="shop_table shop_table_responsive my_account_orders">
        
	        <thead>
	    	<tr>
	    	    <th class="order-number"><span class="nobr">Order</span></th>
	    	    <th class="order-date"><span class="nobr">Webinar Date</span></th>
	    	    <th class="order-status"><span class="nobr">Webinar Time</span></th>
	    	    <th class="order-total"><span class="nobr">Join</span></th>
	    	</tr>
	        </thead> 
	        <tbody>
	<?php foreach ($customer_orders as $order) { //Iterate over orders
	    $order = wc_get_order($order->ID);
	    $items = $order->get_items();
		
		foreach ($items as $item) { //Iterate over brought products
			$product_id = $item['product_id'];

			$wbnId = get_post_meta($product_id, '_wpws_webinarID', true);
			if ( $order->get_status() != "completed")
			    continue;

			if (empty($wbnId))
			    continue;

			$attendee = WebinarSysteemAttendees::getAttendee($wbnId);
			$data_timer = WebinarSysteem::getWebinarTime($wbnId, $attendee);
			$date_date = empty($data_timer) ? 'N/A' : date_i18n(get_option('date_format'), $data_timer);
			$wb_time = empty($data_timer) ? 'N/A' : date_i18n(get_option('time_format'), $data_timer);
			?>

			<tr class="order">
			    <td class="order-number" data-title="Order">
				<a href="<?php echo esc_url($order->get_view_order_url()); ?>">
				    #<?php echo $order->get_order_number() ?>
				</a>
			    </td>
			    <td class="order-date" data-title="Webinar Date">
				<?php echo $date_date; ?>
			    </td>
			    <td class="order-status" data-title="Webinar Time">
				<?php echo $wb_time ?>
			    </td>
			    <td class="order-total" data-title="Join">
				<a href="<?php echo get_permalink($wbnId) . self::generateUniquePurchasedURL() ?>" class="button view">Join webinar</a>
			    </td>
			</tr>

			<?php
		    }
		    
	} ?>
		</tbody>
   </table>
    <style>
   	@media only screen and (max-width:320px) {
  	   .shop_table a {
            font-size:8px
        } 
        
        .shop_table > thead > tr > th {
            font-size: 9px !important;
        }
    }
    </style>
	<?php 
    }
}
?>

<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class WC_pdf_order_meta_box {

	    public function __construct() {
			
			$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );

	    	// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
	    	if ( extension_loaded('iconv') && extension_loaded('mbstring') ) {					
				
				// Add Invoice meta box to completed orders
				add_action( 'add_meta_boxes', array( $this,'invoice_details_admin_init' ), 10, 2 );

				add_action( 'admin_init' , array( $this,'admin_pdf_url_check') );

		    	// Add Create and Delete invoice options to WooCommerce Order Actions dropdown
		    	add_filter( 'woocommerce_order_actions', array( $this, 'pdf_invoice_woocommerce_order_actions' ) );

		    	// Delete Invoice per order
		    	add_action ( 'woocommerce_order_action_delete_invoice', array( $this, 'delete_invoice' ) );

		    	// Order Actions Meta Box
		    	add_action ( 'woocommerce_order_action_pdf_invoices_delete_invoice', array( $this, 'delete_invoice_per_order' ) );
		    	add_action ( 'woocommerce_order_action_pdf_invoices_create_invoice', array( $this, 'create_invoice_per_order' ) );
		    	add_action ( 'woocommerce_order_action_pdf_invoices_email_invoice',  array( $this, 'email_invoice_per_order' ) );

				// Add PDF Invoice Email
				add_filter( 'woocommerce_email_classes', array( $this, 'add_email_class' ) );
				add_filter( 'woocommerce_email_actions', array( $this, 'add_email' ) );
				add_action( 'pdf_invoice_send_emails', array( $this, 'trigger_email_action' ) );

				// Message when email has been sent
    			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ), 99 );

			}

		}

		/**
		 * Create Invoice MetaBox
		 */	
		function invoice_details_admin_init($post_type,$post) {
			if ( get_post_meta( $post->ID, '_invoice_number_display', TRUE ) ) {
					add_meta_box( 'woocommerce-invoice-details', __('Invoice Details', 'woocommerce-pdf-invoice'), array($this,'woocommerce_invoice_details_meta_box'), 'shop_order', 'side', 'high');
			}
		}
		
		/**
		 * Displays the invoice details meta box
		 * We include a download link, even if the order is not complete - let's the store owner view an invoice before the order is complete.
		 */
		function woocommerce_invoice_details_meta_box( $post ) {
			global $woocommerce;

			$data = get_post_custom( $post->id );
			?>
			<div class="invoice_details_group">
				<ul class="totals">
		
					<li class="left">
						<label><?php _e( 'Invoice Number:', 'woocommerce-pdf-invoice' ); ?></label>
						<?php if ( get_post_meta( $post->ID, '_invoice_number_display', TRUE ) ) 
								echo get_post_meta( $post->ID, '_invoice_number_display', TRUE ); ?>
					</li>
		
					<li class="right">
						<label><?php _e( 'Invoice Date:', 'woocommerce-pdf-invoice' ); ?></label>
						<?php 
						if ( get_post_meta( $post->ID, '_invoice_date', TRUE ) ) :
						
							$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
							$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];

							if ( !isset( $date_format ) || $date_format == '' ) :
								$date_format = "j F, Y";
							endif;

							$date = get_post_meta( $post->ID, '_invoice_date', TRUE );
							// Make sure the date is formated correctly
							$date_check = DateTime::createFromFormat( get_option( 'date_format' ), $date );
							if( $date_check ) {
								$date = $date_check->format( $date_format );
							}

							if( strtotime( $date ) ) {
								$date = date_i18n( $date_format, strtotime( $date ) );
							}

							echo $date;
							
						endif;

						?>
					</li>
	                
	                <li class="left">
						<a class="pdf_invoice_metabox_download_invoice" href="<?php echo $_SERVER['REQUEST_URI'] ?>&pdf_method=download&pdfid=<?php echo $post->ID ?>"><?php _e( 'Download Invoice', 'woocommerce-pdf-invoice' ); ?></a>
					</li>

				</ul>
				<div class="clear"></div>
			</div><?php
			
		}

		/**
		 * Check Admin URL for pdfaction
		 */
		function admin_pdf_url_check() {
			global $woocommerce;

			if ( is_admin() && isset( $_GET['pdfid'] ) ) {

				$orderid = stripslashes( $_GET['pdfid'] );
				$order   = new WC_Order($orderid);

				if( isset( $_GET['pdf_method']) && $_GET['pdf_method'] == 'download' ) {
					echo WC_send_pdf::get_woocommerce_invoice( $order , 'false' );
				}

			}

		}

	    /**
	     * [pdf_invoice_woocommerce_order_actions description]
	     * Add Create and Delete invoice options to the Order Actions dropdown.
	     * These options only show for admins
	     */
	    function pdf_invoice_woocommerce_order_actions( $orderactions ) {
	        global $post;

			$current_user = wp_get_current_user();

			// Only admins can do this!
			if( in_array('administrator', $current_user->roles) ) {

		        // If there is an invoice then show Delete option else show Create option
		        if ( get_post_meta( $post->ID, '_invoice_number', TRUE ) ) {
		        	$orderactions['pdf_invoices_email_invoice'] 	= 'Email PDF Invoice';
		        	$orderactions['pdf_invoices_delete_invoice'] 	= 'Delete Invoice';
		        } else {
		        	$orderactions['pdf_invoices_create_invoice'] 	= 'Create Invoice';
		        }

		    }

	        return $orderactions;
	    }

		/**
		 * [email_invoice_per_order description]
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		public static function email_invoice_per_order( $order ) {


			if( !is_null( $order) && is_object($order) ) {

				$order_id = $order->get_id();

				// Send the 'Resend Invoice', complete with PDF invoice!
				WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $order_id, $order );

				// Add order note
				$order->add_order_note( __( "Invoice emailed to customer manually.", 'woocommerce-pdf-invoice' ), false, true );

				// Change the post saved message.
				add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );

			}

		}

		/**
		 * [create_invoice_per_order description]
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		public static function create_invoice_per_order( $order ) {
			$order_id = $order->get_id();
			WC_pdf_functions::woocommerce_completed_order_create_invoice( $order_id );

			// Add order note
			$order->add_order_note( __( "Invoice created manually.", 'woocommerce-pdf-invoice' ), false, true );
		}

	    /**
	     * [delete_invoice_per_order description]
	     * @param  [type] $order [description]
	     * @return [type]        [description]
	     */
		public static function delete_invoice_per_order( $order ) {

			$ordernote 					= '';
			$order_id   				= $order->get_id();
			$invoice_meta 				= WC_pdf_functions::get_invoice_meta();
			$old_pdf_invoice_meta_items	= get_post_meta( $order_id, '_invoice_meta', TRUE );

			// Add an order note with the original infomation
			foreach( $old_pdf_invoice_meta_items as $key => $value ) {
				$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
			}

			// Delete the invoice meta
			foreach( $invoice_meta as $meta_key ) {
				delete_post_meta( $order_id, $meta_key );
			}

			WC_pdf_admin_functions::handle_next_invoice_number();

			// Add order note
			$order->add_order_note( __("Invoice deleted. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

		}

		/**
		 * [set_email_sent_message description]
		 * @param [type] $location [description]
		 */
		public static function set_email_sent_message( $location ) {
			return add_query_arg( 'message', 51, $location );
		}

		/**
		 * [post_updated_messages description]
		 * @param  [type] $messages [description]
		 * @return [type]           [description]
		 */
		public static function post_updated_messages( $messages ) {
			$messages['shop_order'][51] =  __( 'Order updated and PDF invoice emailed.', 'woocommerce-pdf-invoice' );
			return $messages;
		}

	    /**
	     * add_pdf_invoices_email
	     */
	    function add_email_class( $emails ) {
	    	$emails['PDF_Invoice_Customer_PDF_Invoice'] = include 'class-pdf-email-customer-invoice.php';
	    	return $emails;
	    }

	    function add_email( $emails ) {
	    	$emails[] = 'woocommerce_customer_pdf_invoice';
	    	return $emails;
	    }
	    
	    function trigger_email_action( $order_id ) {
	    	if ( isset( $order_id ) && !empty( $order_id ) ) {
	            WC_Emails::instance();
	            do_action( 'pdf_invoice_resend_invoice', $order_id );
	        }
	    }


	} // EOF WC_pdf_order_meta_box

	$GLOBALS['WC_pdf_order_meta_box'] = new WC_pdf_order_meta_box();

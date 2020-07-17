<?php

class WC_pdf_admin_functions {

    public function __construct() {

    	// Get PDF Invoice Options
    	$woocommerce_pdf_invoice_options = get_option('woocommerce_pdf_invoice_settings');
    	$this->id 	 = 'woocommerce_pdf_invoice';
    	$this->debug = false;

    	if( isset( $woocommerce_pdf_invoice_options["pdf_debug"] ) && $woocommerce_pdf_invoice_options["pdf_debug"] == "true" ) {
        	$this->debug = true;
        }

		// Add Invoice Number column to orders page in admin
		add_action( 'admin_init' , array( $this, 'pdf_manage_edit_shop_order_columns' ), 10, 2 );

		// Add Invoice Number to column
		add_action( 'manage_shop_order_posts_custom_column' , array( $this, 'invoice_number_admin_init') , 2 );

    	// Update Invoice Meta, only available if debugging is on
		if ( $this->debug == true ) {
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_edit_pdf_invoice_update_meta' ) );
    		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_update_invoice_meta' ), 10, 3 );
		}

		// Add PDF Invoice options to Bulk edit menu
    	add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_edit_pdf_invoice_options' ) );

    	// Delete Invoice, only available if debugging is on
		if ( $this->debug == true ) {
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_edit_pdf_invoice_delete_invoice' ) );
    		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_delete_invoice' ), 10, 3 );
		}

    	// Methods for Bulk edit menu
    	add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_create_invoice' ), 10, 3 );
    	add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_create_email_invoice' ), 10, 3 );
    	add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_email_invoice' ), 10, 3 );

    }

    /**
     * [pdf_manage_edit_shop_order_columns description]
     * @param  [type] $columns [description]
     * @return [type]          [description]
     */
	function pdf_manage_edit_shop_order_columns( $columns ) {
		add_filter( 'manage_edit-shop_order_columns', 'invoice_column_admin_init' );
	}

	/**
	 * [invoice_number_admin_init description]
	 * @param  [type] $column [description]
	 * @return [type]         [description]
	 */
	function invoice_number_admin_init( $column ) {
		global $post, $woocommerce, $the_order;

		if ( $column == 'pdf_invoice_num' ) {

			if ( get_post_meta( $post->ID, '_invoice_number_display', TRUE ) ) {

				$invoice_number = get_post_meta( $post->ID, '_invoice_number_display', TRUE );
				$invoice_date 	= get_post_meta( $post->ID, '_invoice_date', TRUE );
				$output 		=  '<a href="' . $_SERVER['REQUEST_URI'] . '&pdfid=' . $post->ID .'">' . $invoice_number . '<br />' . $invoice_date . '</a>';

				/**
				 * Filter the output
				 * $output: default HTML
				 * $invoice_number: stored '_invoice_number_display' from order meta
				 * $invoice_date: stored '_invoice_date' from order meta
				 * $post->ID: $order_id
				 */
				echo apply_filters( 'pdf_invoice_number_order_screen_output', $output, $invoice_number, $invoice_date, $post->ID );

			}

		}

	}

	/**
	 * [bulk_edit_pdf_invoice_update_meta description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
    public function bulk_edit_pdf_invoice_update_meta( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_update_invoice_meta'] 	= __( 'Update PDF Meta', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

	/**
	 * [bulk_update_invoice_meta description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
    public function bulk_edit_pdf_invoice_options( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_create_invoice'] 			= __( 'Create Invoice(s)', 'woocommerce-pdf-invoice' );
		$actions['pdf_create_email_invoice'] 	= __( 'Create and Email Invoice(s)', 'woocommerce-pdf-invoice' );
		$actions['pdf_email_invoice'] 			= __( 'Email Invoice(s)', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

	/**
	 * [bulk_update_invoice_meta description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
    public function bulk_edit_pdf_invoice_delete_invoice( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_delete_invoice'] 			= __( 'Delete Invoice(s)', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

    /**
     * [handle_update_invoice_meta description]
     * @param  [type] $redirect_to [description]
     * @param  [type] $action      [description]
     * @param  [type] $ids         [description]
     * @return [type]              [description]
     */
	public function handle_update_invoice_meta( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_update_invoice_meta' && $ids != NULL ) {

			require_once( 'class-pdf-send-pdf-class.php' );
			require_once( 'class-pdf-functions-class.php' );

			// Get PDF Invoice Options
			$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$ids 	 = array_map( 'absint', $ids );

			foreach ( $ids as $id ) {

				$order 	 = new WC_Order( $id );

				$old_pdf_invoice_meta_items		 = get_post_meta( $id, '_invoice_meta', TRUE );
				$ordernote 						 = '';

				$new_invoice_meta = array( 
					'invoice_created' 			=> isset( $old_pdf_invoice_meta_items['invoice_created'] ) ? $old_pdf_invoice_meta_items['invoice_created'] : '',
					'invoice_date' 				=> WC_pdf_admin_functions::handle_pdf_date( $id, $woocommerce_pdf_invoice_settings['pdf_date'], isset( $old_pdf_invoice_meta_items['invoice_date'] ) ? $old_pdf_invoice_meta_items['invoice_date'] : '' ),
					'invoice_number' 			=> isset( $old_pdf_invoice_meta_items['invoice_number'] ) ? $old_pdf_invoice_meta_items['invoice_number'] : '',
					'invoice_number_display' 	=> WC_pdf_functions::create_display_invoice_number( $id ),
					'pdf_company_name' 			=> $woocommerce_pdf_invoice_settings['pdf_company_name'],
					'pdf_company_information' 	=> nl2br( $woocommerce_pdf_invoice_settings['pdf_company_details'] ),
					'pdf_registered_name' 		=> $woocommerce_pdf_invoice_settings['pdf_registered_name'],
					'pdf_registered_address' 	=> $woocommerce_pdf_invoice_settings['pdf_registered_address'],
					'pdf_company_number' 		=> $woocommerce_pdf_invoice_settings['pdf_company_number'],
					'pdf_tax_number' 			=> $woocommerce_pdf_invoice_settings['pdf_tax_number'],
				);

				// Only update if the invoice meta has changed.
				if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) ) {

					// Update the invoice_meta
					update_post_meta( $id, '_invoice_meta', $new_invoice_meta );

					// Update the individual invoice meta
					foreach( $new_invoice_meta as $key => $value ) {
						update_post_meta( $id, '_'.$key, $value );
					}

					// Add an order note with the original infomation
					if( isset( $old_pdf_invoice_meta_items ) && is_array( $old_pdf_invoice_meta_items ) ) {

						foreach( $old_pdf_invoice_meta_items as $key => $value ) {
							$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
						}

						// Add order note
						$order->add_order_note( __("Invoice information changed. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

					} // if( isset( $old_pdf_invoice_meta_items ) && is_array( $old_pdf_invoice_meta_items ) )

				} // if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) )

			} // foreach ( $ids as $id ) {

		}

		return esc_url_raw( $redirect_to );

	}

	/**
	 * [handle_create_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_create_invoice( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_create_invoice' ) {

			require_once( 'class-pdf-send-pdf-class.php' );

			$ids = array_map( 'absint', $ids );

			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $id ) {

				if ( get_post_meta( $id, '_invoice_number', TRUE ) == '' ) {
					// Crreate the invoice
                    WC_pdf_functions::woocommerce_completed_order_create_invoice( $id );
                }

			}

		}

		return esc_url_raw( $redirect_to );

	}

	/**
	 * [handle_create_email_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_create_email_invoice( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_create_email_invoice' ) {

			require_once( 'class-pdf-send-pdf-class.php' );

			$ids = array_map( 'absint', $ids );

			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $id ) {

				if ( get_post_meta( $id, '_invoice_number', TRUE ) == '' ) {
					// Crreate the invoice
                    WC_pdf_functions::woocommerce_completed_order_create_invoice( $id );
                }
                
                // To stop the email being sent use the filter
                // add_filter( 'pdf_invoice_bulk_send_invoice', 'remove_pdf_invoice_bulk_send_invoice' );
                // function remove_pdf_invoice_bulk_send_invoice() {
                // 	return false;
                // }
                $send_email = apply_filters( 'pdf_invoice_bulk_send_invoice', true );

                if( $send_email ) {
                	$order = new WC_Order( $id );
					WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $id, $order );
				}

			}

		}

		return esc_url_raw( $redirect_to );

	}

	/**
	 * [handle_email_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_email_invoice( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_email_invoice' ) {

			require_once( 'class-pdf-send-pdf-class.php' );

			$ids = array_map( 'absint', $ids );

			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $id ) {

				if ( get_post_meta( $id, '_invoice_number', TRUE ) ) {
					$order = new WC_Order( $id );

                    // Send the 'Resend Invoice', complete with PDF invoice!
					WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $id, $order );
                }

			}

		}

		return esc_url_raw( $redirect_to );

	}

	/**
	 * [handle_delete_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_delete_invoice( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_delete_invoice.
		if ( $action === 'pdf_delete_invoice' ) {

			$ids = array_map( 'absint', $ids );

			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $id ) {

				$ordernote 					= '';
				$order 						= new WC_Order( $id );
				$invoice_meta 				= WC_pdf_functions::get_invoice_meta();
				$old_pdf_invoice_meta_items	= get_post_meta( $id, '_invoice_meta', TRUE );

				// Add an order note with the original infomation
				foreach( $old_pdf_invoice_meta_items as $key => $value ) {
					$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
				}

				// Delete the invoice meta
				foreach( $invoice_meta as $meta_key ) {
					delete_post_meta( $id, $meta_key );
				}

				// Add order note
				$order->add_order_note( __("Invoice deleted. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

			}

		}

		return esc_url_raw( $redirect_to );

	}

	/**
	 * [handle_pdf_date description]
	 * @param  [type] $order_id     [description]
	 * @param  [type] $usedate      [description]
	 * @param  [type] $invoice_date [description]
	 * @return [type]               [description]
	 */
	public static function handle_pdf_date( $order_id, $usedate, $invoice_date ) {
		global $woocommerce;
		
		$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
		$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];
		$date 		 = NULL;  

		require_once( 'class-pdf-send-pdf-class.php' );
		require_once( 'class-pdf-functions-class.php' );

		// Force a $date_format if one is not set
		if ( !isset( $date_format ) || $date_format == '' ) {
			$date_format = "j F, Y";
		}

		$order 	 = new WC_Order( $order_id );

		if ( $usedate == 'completed' ) {
			$order_status	= is_callable( array( $order, 'get_status' ) ) ? $order->get_status() : $order->order_status;
			if( $order_status == 'completed' ) {
				$date = WC_send_pdf::get_completed_date( $order_id );
			}

		} elseif ( $usedate == 'order' ) {
			$date = is_callable( array( $order, 'get_date_created' ) ) ? $order->get_date_created() : $order->order_date;
		}
		
		if ( $date ) {

			// Return a date in the format that matches the PDF Ivoice settings.
			return WC_pdf_admin_functions::format_pdf_date( $date) ;

		} else {
			// No changes to the date are being made or the order status is not completed but the settings say use the completed date
			// return the date already being used on the invoice.
			return $invoice_date;
		}

	}

	/**
	 * [format_pdf_date description]
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public static function format_pdf_date( $date = NULL ) {
		global $woocommerce;

		if( $date ) {

			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
			$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];

			// Make sure the date is formated correctly
			$date_check = DateTime::createFromFormat( get_option( 'date_format' ), $date );

			if( $date_check ) {
				$date = $date_check->format( $date_format );
			}

			if( strtotime( $date ) ) {
				$date = date_i18n( $date_format, strtotime( $date ) );
			}

		}

		// Return a date in the format that matches the PDF Ivoice settings.
		return $date;

	}

	/**
	 * [handle_next_invoice_number description]
	 * @return [type] [description]
	 */
	public static function handle_next_invoice_number() {
		global $wpdb;

		// Clear the cache
		$wpdb->flush();

		// Get PDF Invoice Options
		$woocommerce_pdf_invoice_settings= get_option('woocommerce_pdf_invoice_settings');

		// Starting Invoice Number stored in PDF Invoice settings
		$starting_invoice_number		 = isset( $woocommerce_pdf_invoice_settings['start_number'] ) ? $woocommerce_pdf_invoice_settings['start_number'] : 0;
		// Set the last invice number to 0 prior to checking the database for any invoice nmbers stored in orders.
		$last_invoice_number			 = 0;

		// Check if we have any invoice numbers stored in orders
		$_invoice_number_meta = $wpdb->get_row("SELECT * FROM $wpdb->postmeta 
								   WHERE meta_key = '_invoice_number' 
								   ORDER BY CAST(meta_value AS SIGNED) DESC
								   LIMIT 1;"
								);

		if ( $_invoice_number_meta != NULL ) {
			$last_invoice_number  = $_invoice_number_meta->meta_value;
		}

		$next_invoice_number = $last_invoice_number +1;

		if( $starting_invoice_number > $next_invoice_number ) {
			$next_invoice_number = $starting_invoice_number;
		}

		// Update WordPress Options table
		update_option( 'woocommerce_pdf_invoice_current_invoice', $next_invoice_number );
		// Update PDF Invoice Settings
		$woocommerce_pdf_invoice_settings['pdf_next_number'] = $next_invoice_number;
		update_option( 'woocommerce_pdf_invoice_settings', $woocommerce_pdf_invoice_settings );
	}

	/**
	 * [get_next_invoice_number description]
	 * @return [type] [description]
	 */
	public static function get_next_invoice_number() {
		// Get PDF Invoice Options
		$woocommerce_pdf_invoice_settings 	= get_option('woocommerce_pdf_invoice_settings');

		$start_number_settings 				= isset( $woocommerce_pdf_invoice_options['start_number'] ) ? $woocommerce_pdf_invoice_options['start_number'] : 0;
		$next_number_settings  				= isset( $woocommerce_pdf_invoice_options['pdf_next_number'] ) ? $woocommerce_pdf_invoice_options['pdf_next_number'] : 0;
		$current_invoice_number_options 	= ( null !== get_option( 'woocommerce_pdf_invoice_current_invoice' ) ) ? get_option( 'woocommerce_pdf_invoice_current_invoice' ) : 0;

		if( $next_number_settings > $current_invoice_number_options ) {
            $next_invoice = $next_number_settings;
        } else {
            $next_invoice = $current_invoice_number_options;
        }

        if( $start_number_settings > $next_invoice ) {
            $next_invoice = $start_number_settings;
        }

        return $next_invoice;	
	}



	/**
	 * [bulk_post_updated_messages description]
	 * @param  [type] $bulk_messages [description]
	 * @param  [type] $bulk_counts   [description]
	 * @return [type]                [description]
	 */
	public static function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		if( isset( $bulk_messages['shop_order']['invoiced'] ) ) {
			$bulk_messages['shop_order']['invoiced'] = _n( '%s invoice created.', '%s invoices created.', $bulk_counts['invoiced'], 'woocommerce' );
		}
		
		return $bulk_messages;
	}

}

$GLOBALS['WC_pdf_admin_functions'] = new WC_pdf_admin_functions();

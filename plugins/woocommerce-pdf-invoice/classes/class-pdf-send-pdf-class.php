<?php

		// include DomPDF autoloader
        require_once ( PDFPLUGINPATH . "lib/dompdf/autoload.inc.php" );

        // reference the Dompdf namespaces
		use WooCommercePDFInvoice\Dompdf;
		use WooCommercePDFInvoice\Options;

        class WC_send_pdf {

            public function __construct() {
            	$this->wc_version = get_option( 'woocommerce_version' );
				add_action( 'init', array( $this, 'init' ) );

				$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );

				// Add download link if required
				if( isset($woocommerce_pdf_invoice_settings['attachment_method']) && $woocommerce_pdf_invoice_settings['attachment_method'] != '0' ) {
					add_action( 'woocommerce_email_before_order_table', array( 'WC_send_pdf', 'pdf_download_link' ), 10, 4 );
				}

            }

            function init() {
            	/**
				 * Check the email being sent and attach a PDF if it's the right one
				 */
				add_filter( 'woocommerce_email_attachments' , array( $this, 'pdf_attachment' ) ,99, 3 );
            }

            /**
			 * Check the email being sent and attach a PDF if it's the right one
             */
		 	public static function pdf_attachment( $attachment = NULL, $id = NULL, $order = NULL ) {

		 		// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
            	if ( ! extension_loaded( 'iconv' ) || ! extension_loaded( 'mbstring' ) || ! $id || ! $order ) {
            		return $attachment;
            	}

            	$settings = get_option( 'woocommerce_pdf_invoice_settings' );

            	// Make the array for email ids
            	$email_ids = array();
            	if( isset( $settings['attach_multiple'] ) && $settings['attach_multiple'] !='' ) {
            		$email_ids = $settings['attach_multiple'];
            	}

            	// Make sure the completed order IDs are in there
            	$email_ids[] = 'pdf_customer_invoice';
            	$email_ids[] = 'customer_completed_order';
            	$email_ids[] = 'customer_completed_renewal_order';

            	// Make sure it's a unique array
            	$email_ids = array_unique( $email_ids );

            	// Add a filter for the array
            	$email_ids = apply_filters( 'pdf_invoice_email_ids', $email_ids, $order );

            	if ( !empty( $email_ids ) && in_array( $id, $email_ids ) ) {

            		// Create the PDF if required
					if( isset( $settings['attachment_method'] ) && $settings['attachment_method'] == '2' ) {

					} else {
						$pdf = WC_send_pdf::get_woocommerce_invoice( $order );

	            		// Apply a filter to modify the PDF if required
	            		$pdf = apply_filters( 'pdf_invoice_modify_attachment', $pdf, $id, $order );

	            		// Add the PDF to the attachments array
	            		$attachment[] = $pdf;
					}
	
				}

				return array_unique( $attachment );
				
		 	} // pdf_attachment

		 	/**
		 	 * [pdf_download_link description]
		 	 * @param  [type]  $order         [description]
		 	 * @param  boolean $sent_to_admin [description]
		 	 * @param  boolean $plain_text    [description]
		 	 * @param  string  $email         [description]
		 	 * @return [type]                 [description]
		 	 */
		 	public static function pdf_download_link(  $order, $sent_to_admin = false, $plain_text = false, $email = '' ) {

		 		$settings = get_option('woocommerce_pdf_invoice_settings');

		 		$order_id   	= $order->get_id();
		 		$download_url 	= site_url( '/?pdfid=' . $order_id . '&pdfnonce=' . wp_hash( $order->get_order_key(), 'nonce' ), 'https' );

		 		$download_link 	= '<a href="' . $download_url  . '">' . __( 'here', 'woocommerce-pdf-invoice' ) . '</a>';

		 		// Get text from settings
		 		$download_text 	= '<p><strong>' . $settings['invoice_download_url'] . '</strong></p>';

		 		// Replace placeholder with download URL
		 		$placeholder_replacement = str_replace( '[[PDFINVOICEDOWNLOADURL]]', $download_link, $download_text );

		 		echo apply_filters( 'pdf_invoice_download_invoice_link', $placeholder_replacement, $order, $order_id );

		 	}

			public static function get_woocommerce_invoice( $order = NULL, $stream = NULL ) {
				
				// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
				if ( ! extension_loaded( 'iconv' ) || ! extension_loaded( 'mbstring' ) || ! $order ) {
 					return array();
 				}

				$order_id   = $order->get_id();

				$pdf = new WC_send_pdf();
				
				$settings = get_option('woocommerce_pdf_invoice_settings');

				// Page Options
        		$papersize 				= self::get_paper_size( $settings, $order_id );
        		$paperorientation 		= self::get_paper_orientation( $settings, $order_id );
        		$isHtml5ParserEnabled 	= self::get_isHtml5ParserEnabled( $settings, $order_id );
				$customlogo				= ''; // No logo? No problem, we'll just use get_bloginfo('name')
				$footertext				= ''; // This is the legal stuff that you should be including everywhere!

				if( !isset($settings['enable_remote']) || $settings['enable_remote'] == 'false' ) {
					$pdfremoteimages = false;
				} else {
					$pdfremoteimages = true;
				}

				if( !isset($settings['enable_subsetting']) || $settings['enable_subsetting'] == 'true' ) {
					$fontsubsetting	= true;
				} else {
					$fontsubsetting	= false;
				}

				// Get the temp directory
				$pdftemp = WC_send_pdf::get_pdf_temp();

				// Get the filename
				$filename 	= WC_send_pdf::create_filename( $order_id, $settings );

				$messagetext  = '';
				$messagetext .= $pdf->get_woocommerce_invoice_content( $order_id );

				/**
				 * Debugging
				 */
		  		if( isset( $settings["pdf_debug"] ) && $settings["pdf_debug"] == "true" ) {
		  			// Load PDF Dbugging
		  			if( !class_exists( 'WC_pdf_debug') ) {
		  				include( 'class-pdf-debug.php' );
		  			}
		  			WC_pdf_debug::pdf_debug( $messagetext, 'WC_PDF_Invoice', __('PDF Invoice Body : ', 'woocommerce-pdf-invoice'), TRUE );
				} 

				// DOMPDF Options
				$options = new Options();
				$options->set([
						'isRemoteEnabled' 			=> $pdfremoteimages,
						'isHtml5ParserEnabled' 		=> $isHtml5ParserEnabled,
						'enable_font_subsetting'	=> $fontsubsetting,
						'tempDir'					=> $pdftemp,
						'logOutputFile'				=> $pdftemp . DIRECTORY_SEPARATOR . "log.htm"
				]);		
					
				if ( $stream && 
					( !isset($settings['pdf_termsid']) || $settings['pdf_termsid'] == 0 ) && 
					( !isset($settings['pdf_creation']) || $settings['pdf_creation'] == 'standard' )
				) {
					// Start the PDF Generator for the invoice

					ob_start();
					ob_clean();

					$dompdf = new DOMPDF();
					$dompdf->setOptions($options);
					$dompdf->load_html( $messagetext );
					$dompdf->set_paper( $papersize, $paperorientation );
					$dompdf->render();
						
					// Output the PDF for download
					return $dompdf->stream( $filename );
						
				} elseif ( 
					( isset($settings['pdf_termsid']) && $settings['pdf_termsid'] != 0 ) || 
					( isset($settings['pdf_creation']) && $settings['pdf_creation'] == 'file' )
				) {
					/**
					 * This section deals with sending / generating a PDF Invoice that will include a Terms and Conditions page
					 * Uses PDF Merge library
					 *
					 * REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
					 * You do not need to give a file path for browser, string, or download - just the name.
					 */
					
					// Add PDF extension 
					if (strpos($filename, '.pdf') === false) {
						$filename =  $filename . '.pdf';
					}

					$dompdf = new DOMPDF();
					$dompdf->setOptions($options);
					$dompdf->load_html( $messagetext );
					$dompdf->set_paper( $papersize, $paperorientation );
					$dompdf->render();
						
					$invattachments = $pdftemp . '/inv' . $filename;
						
					// Write the PDF to the TMP directory		
					file_put_contents( $invattachments, $dompdf->output() );
						
					ob_start();
					ob_clean();

					if ( !class_exists('PDFMerger') ) {
						include ( PDFPLUGINPATH . 'lib/PDFMerger/PDFMerger.php' );
					}

					if ( isset($settings['pdf_termsid']) && $settings['pdf_termsid'] != 0 ) {

						// Start the PDF Generator for the terms
						$dompdf = new Dompdf();
						$dompdf->setOptions($options);
						$dompdf->load_html( $pdf->get_woocommerce_invoice_terms( $settings['pdf_termsid'], $order_id ) );
						$dompdf->set_paper( $papersize, $paperorientation );
						$dompdf->render();
						
						$termsattachments = $pdftemp . '/terms-' . $filename;
						
						// Write the PDF to the TMP directory		
						file_put_contents( $termsattachments, $dompdf->output() );
					
						$pdf = new PDFMerger;
						
						if ( $stream ) {
							$pdf->addPDF( $invattachments, 'all' )
								->addPDF( $termsattachments, 'all' )
								->merge( 'download', $filename, $paperorientation );
								exit;
						} else {
							$pdf->addPDF( $invattachments, 'all' )
								->addPDF( $termsattachments, 'all' )
								->merge( 'file', $pdftemp . '/' . $filename, $paperorientation );
						}

					} else {
					
						$pdf = new PDFMerger;

						if ( $stream ) {
							$pdf->addPDF( $invattachments, 'all' )
								->merge( 'download', $filename, $paperorientation );
								exit;
						} else {
							$pdf->addPDF( $invattachments, 'all' )
								->merge( 'file', $pdftemp . '/' . $filename, $paperorientation );
						}

					}
						
					// Send the file name and location to the Email
					// return 	array( $invattachments, $termsattachments );
					return ( $pdftemp . '/' . $filename );
											
				} else {
					// Add PDF extension 
					if (strpos($filename, '.pdf') === false) {
						$filename =  $filename . '.pdf';
					}

					ob_start();
					ob_clean();

					$dompdf = new DOMPDF();
					$dompdf->setOptions($options);
					$dompdf->load_html( $messagetext );
					$dompdf->set_paper( $papersize, $paperorientation );
					$dompdf->render();
					
					$attachments = $pdftemp . '/' . $filename;
					
					// Write the PDF to the TMP directory		
					file_put_contents( $attachments, $dompdf->output() );
		
					// Send the file name and location to the Email
					return 	$attachments;
						
				}

			}

			/**
			 * Get the paper size. Default is A4
			 * @param  [Array] $woocommerce_pdf_invoice_settings 	[description]
			 * @param  [Int] $order_id                         		[description]
			 * @return [text]                                   	[description]
			 */
			private static function get_paper_size( $settings, $order_id ) {
				$size = isset( $settings['paper_size'] ) ? $settings['paper_size'] : "A4";
				$size = apply_filters( 'woocommerce_pdf_invoice_paper_size', $size, $order_id );
				return strtolower( $size );
			}

			/**
			 * Get the paper orientation. Default is Portrait
			 * @param  [Array] $woocommerce_pdf_invoice_settings 	[description]
			 * @param  [Int] $order_id                         		[description]
			 * @return [text]                                   	[description]
			 */
			private static function get_paper_orientation( $settings, $order_id ) {
				$orientation = isset( $settings['paper_orientation'] ) ? $settings['paper_orientation'] : "portrait";
				$orientation = apply_filters( 'woocommerce_pdf_invoice_paper_orientation', $orientation, $order_id );
				return strtolower( $orientation );
			}

			// isHtml5ParserEnabled
			private static function get_isHtml5ParserEnabled( $settings, $order_id ) {
				return apply_filters( 'woocommerce_pdf_invoice_isHtml5ParserEnabled', true );
			}

			/**
			 * Create the file name based on the settings
			 *
			 * Allowed variables
			 *
			 * companyname
			 * invoicedate
			 * invoicenumber
			 * month
			 * mon
			 * year
			 */
			private static function create_filename( $order_id, $woocommerce_pdf_invoice_options ) {

				$pdf = new WC_send_pdf();

				$replace 	= array( ' ', "/", "'",'"', "--" );
				$clean_up	= array( ',' );
				$filename	= $woocommerce_pdf_invoice_options['pdf_filename'];

				if ( $filename == '' ) {

					$filename	= get_bloginfo('name') . '-' . $order_id;

				} else {

					$invoice_date = $pdf->get_woocommerce_pdf_date( $order_id,'completed', true, 'invoice' );

					$filename	= str_replace( '{{company}}',	$woocommerce_pdf_invoice_options['pdf_company_name'] , $filename );
					$filename	= str_replace( '{{invoicedate}}', $invoice_date, $filename );
					$filename	= str_replace( '{{invoicenumber}}',	( $pdf->get_woocommerce_pdf_invoice_num( $order_id ) ? $pdf->get_woocommerce_pdf_invoice_num( $order_id ) : $order_id ) , $filename );
					$filename	= str_replace( '{{month}}',	date( 'F', strtotime( $invoice_date ) ) , $filename );
					$filename	= str_replace( '{{mon}}',	date( 'M', strtotime( $invoice_date ) ) , $filename );
					$filename	= str_replace( '{{year}}',	date( 'Y', strtotime( $invoice_date ) ) , $filename );
					
				}

				// Clean up the filename
				$filename	= str_replace( $replace, '-' , $filename );
				$filename	= str_replace( $clean_up, '' , $filename );

				// Filter the filename
				$filename 	= apply_filters( 'pdf_output_filename', $filename, $order_id );

				return $filename;

			}

			/** 
			 * Get pdf template
			 * 
			 * Put your customized template in 
			 * wp-content/themes/YOUR_THEME/pdf_templates/template.php
			 *
			 * Windows hosting fixes
			 */
			public static function get_pdf_template( $filename, $order_id ) {

				// Allow the filename to be modified
				$filename = apply_filters( 'woocommerce_pdf_invoice_filename', $filename, $order_id );

				$plugin_version     = str_replace('/classes/','/templates/',plugin_dir_path(__FILE__) ) . $filename;
				$plugin_version     = str_replace('\classes/','\templates\\',$plugin_version);

                $theme_version_file = get_stylesheet_directory() . '/pdf_templates/' . $filename;

				$pos = strpos( $plugin_version, ":\\" );
				if ( $pos === false ) {

					$pdftemplate 		= file_exists($theme_version_file) ? $theme_version_file : $plugin_version;

				} else {
					$theme_version_file = str_replace('/', '\\', $theme_version_file );
					$plugin_version		= str_replace('/', '\\', $plugin_version );
					$pdftemplate 		= file_exists($theme_version_file) ? $theme_version_file : $plugin_version;
					$pdftemplate		= str_replace('/', '\\', $pdftemplate );
				}

				return $pdftemplate;

			} // get_pdf_template

			/**
			 * Get the temp directory
			 */
		 	public static function get_pdf_temp() {

				// Set the temp directory
				$pdftemp = ( null != ini_get('upload_tmp_dir') || '' != ini_get('upload_tmp_dir') ) ? ini_get('upload_tmp_dir') : sys_get_temp_dir();

				$upload_dir =  wp_upload_dir();
	            $upload_dir =  $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
	            $upload_dir =  apply_filters( 'woocommerce_pdf_invoice_pdf_upload_dir', $upload_dir );

                if ( file_exists( $upload_dir . '/index.html' ) ) {
    				$pdftemp = $upload_dir;

    				// Windows hosting check
					$pos = strpos( $pdftemp, ":\\" );
					if ( $pos === false ) {

					} else {
    					$pdftemp = str_replace('/', '\\', $pdftemp );
					}

                }

                return $pdftemp;

		 	}

			/**
			 * [get_woocommerce_invoice_content description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			function get_woocommerce_invoice_content( $order_id ) {
				global $woocommerce;

				// WPML
				do_action( 'before_invoice_content', $order_id );

				$settings = get_option( 'woocommerce_pdf_invoice_settings' );
				
				if (!$order_id) return;	
				$order 			   = new WC_Order( $order_id );

				// Check if the order has an invoice
				$invoice_number_display = get_post_meta( $order_id, '_invoice_number_display', true );

				// Get the Invoice template ID
				$template_id = isset( $settings['pdf_invoice_template_id'] ) ? $settings['pdf_invoice_template_id'] : 0;

				// Just temporarily
				$template_id = 0;

                // Get the default invoice template ID
                // $default_invoice_template_id = (null !== get_option('woocommerce_pdf_invoice_main_template_id') ) ? get_option('woocommerce_pdf_invoice_main_template_id') : 0;
                // $template_id = (null !== $settings['pdf_invoice_template_id']) ? $settings['pdf_invoice_template_id'] : $default_invoice_template_id;

                // Allow the template to be filtered if required.
				$template_id = apply_filters( 'woocommerce_pdf_invoice_template_id', $template_id, $order );

				if( $template_id !== 0 ) {
					$post 	 = get_post( $template_id );
					$content = $post->post_content;
				} else {
					// Buffer
					ob_start();
		
					// load_template( $pdftemplate, false );
					require( WC_send_pdf::get_pdf_template( 'template.php', $order_id ) );
						
					// Get contents
					$content = ob_get_clean();
				}
				
				/**
				 * Notify when the PDF is about to be generated
				 * Added for Currency Switcher for WooCommerce
				 */
				do_action( 'woocommerce_pdf_invoice_before_pdf_content', $order );
		
				// REPLACE ALL TEMPLATE TAGS WITH REAL CONTENT
				$content = str_replace(	'[[PDFFONTFAMILY]]', 						self::get_fontfamily( $order_id, $settings ),								$content );
				$content = str_replace( '[[PDFCURRENCYSYMBOLFONT]]', 				self::get_currency_fontfamily( $order_id, $settings ),						$content );
				$content = str_replace(	'[[PDFLOGO]]', 								self::get_pdf_logo( $order_id, $settings ), 			 					$content );

				$content = str_replace(	'[[PDFCOMPANYNAME]]', 						self::get_invoice_companyname( $order_id, $settings ),						$content );
				$content = str_replace(	'[[PDFCOMPANYDETAILS]]', 					self::get_invoice_companydetails( $order_id, $settings ), 					$content );
				$content = str_replace(	'[[PDFREGISTEREDNAME]]', 					self::get_invoice_registeredname( $order_id, $settings ), 					$content );
				$content = str_replace(	'[[PDFREGISTEREDADDRESS]]', 				self::get_invoice_registeredaddress( $order_id, $settings ),				$content );
				$content = str_replace(	'[[PDFCOMPANYNUMBER]]', 					self::get_invoice_companynumber( $order_id, $settings ), 					$content );
				$content = str_replace(	'[[PDFTAXNUMBER]]', 						self::get_invoice_taxnumber( $order_id, $settings ), 						$content );


				$content = str_replace(	'[[PDFREGISTEREDNAME_SECTION]]', 			self::get_invoice_registeredname_section( $order_id, $settings ), 			$content );
				$content = str_replace(	'[[PDFREGISTEREDADDRESS_SECTION]]', 		self::get_invoice_registeredaddress_section( $order_id, $settings ),		$content );
				$content = str_replace(	'[[PDFCOMPANYNUMBER_SECTION]]', 			self::get_invoice_companynumber_section( $order_id, $settings ), 			$content );
				$content = str_replace(	'[[PDFTAXNUMBER_SECTION]]', 				self::get_invoice_taxnumber_section( $order_id, $settings ), 				$content );

				$content = str_replace(	'[[PDFINVOICENUMHEADING]]', 				self::get_pdf_template_invoice_number_text( $order ), 	 					$content );
				$content = str_replace(	'[[PDFINVOICENUM]]', 						self::get_invoice_display_invoice_num( $order_id ),							$content );

				$content = str_replace(	'[[PDFORDERENUMHEADING]]', 					self::get_pdf_template_order_number_text( $order ), 	 					$content );
				$content = str_replace(	'[[PDFORDERENUM]]', 						self::get_invoice_display_order_number( $order ), 							$content );

				$content = str_replace(	'[[PDFINVOICEDATEHEADING]]', 				self::get_pdf_template_invoice_date_text( $order ), 	 					$content );
				$content = str_replace(	'[[PDFINVOICEDATE]]', 						self::get_invoice_display_date( $order_id,'completed', false, 'invoice' ), 	$content );

				$content = str_replace(	'[[PDFORDERDATEHEADING]]', 					self::get_pdf_template_order_date_text( $order ), 	 						$content );
				$content = str_replace(	'[[PDFORDERDATE]]', 						self::get_invoice_display_date( $order_id,'ordered', false, 'order' ), 		$content );
				
				$content = str_replace(	'[[PDFINVOICE_BILLINGDETAILS_HEADING]]',	self::get_pdf_billing_details_heading( $order ), 	 						$content );
				$content = str_replace(	'[[PDFBILLINGADDRESS]]', 					self::get_invoice_billing_address( $order ),  								$content );
				$content = str_replace(	'[[PDFBILLINGTEL]]', 						self::get_invoice_billing_phone( $order_id ), 	  							$content );
				$content = str_replace(	'[[PDFBILLINGEMAIL]]', 						self::get_invoice_billing_email( $order_id ), 								$content );
				$content = str_replace(	'[[PDFBILLINGVATNUMBER]]', 					self::get_invoice_billing_vat_number( $order_id ), 							$content );

				$content = str_replace(	'[[PDFINVOICE_SHIPPINGDETAILS_HEADING]]',	self::get_pdf_shipping_details_heading( $order ), 	 						$content );
				$content = str_replace(	'[[PDFSHIPPINGADDRESS]]', 					self::get_invoice_shipping_address( $order ), 								$content );

				$content = str_replace(	'[[PDFINVOICE_PAYMETHOD_HEADING]]', 		self::get_template_payment_method_text( $order ), 	 						$content );
				$content = str_replace(	'[[PDFINVOICEPAYMENTMETHOD]]',				self::get_invoice_payment_method_title( $order_id ), 						$content );

				$content = str_replace(	'[[PDFINVOICE_SHIPMETHOD_HEADING]]', 		self::get_pdf_template_shipping_method_text( $order ), 	 					$content );
				$content = str_replace(	'[[PDFSHIPPINGMETHOD]]',					self::get_invoice_shipping_method_title( $order ), 							$content );

				$content = str_replace(	'[[ORDERINFOHEADER]]',						self::get_pdf_headers( $order_id ), 										$content );
				$content = str_replace(	'[[ORDERINFO]]', 							self::get_pdf_order_details( $order_id ), 	  								$content );
				$content = str_replace(	'[[PDFORDERNOTES]]', 						self::get_pdf_order_note( $order_id ), 	  									$content );

				$content = str_replace(	'[[PDFORDERSUBTOTAL]]', 					self::get_pdf_order_subtotal( $order_id ), 	  								$content );
				$content = str_replace(	'[[PDFORDERSHIPPING]]', 					self::get_pdf_order_shipping( $order_id ), 	  								$content );
				$content = str_replace(	'[[PDFORDERDISCOUNT]]', 					self::get_pdf_order_discount( $order_id ), 	  								$content );
				$content = str_replace(	'[[PDFORDERTAX]]', 							self::get_pdf_order_tax( $order_id ), 	  									$content );
				$content = str_replace(	'[[PDFORDERTOTAL]]', 						self::get_pdf_order_total( $order_id ), 	  								$content );
				$content = str_replace(	'[[PDFORDERTOTALS]]', 						self::get_pdf_order_totals( $order_id ), 	  								$content );

				$content = str_replace(	'[[PDFINVOICE_ORDERDETAILS_HEADING]]', 		self::get_pdf_order_details_heading( $order ), 	 							$content );

				$content = str_replace(	'[[PDFINVOICE_QTY_HEADING]]', 				self::get_pdf_qty_heading( $order ), 	 									$content );
				$content = str_replace(	'[[PDFINVOICE_PRODUCT_HEADING]]', 			self::get_pdf_product_heading( $order ),  									$content );
				$content = str_replace(	'[[PDFINVOICE_PRICEEX_HEADING]]', 			self::get_pdf_priceex_heading( $order ),  									$content );
				$content = str_replace(	'[[PDFINVOICE_TOTALEX_HEADING]]', 			self::get_pdf_totalex_heading( $order ),  									$content );
				$content = str_replace(	'[[PDFINVOICE_TAX_HEADING]]', 				self::get_pdf_tax_heading( $order ), 	 									$content );
				$content = str_replace(	'[[PDFINVOICE_PRICEINC_HEADING]]', 			self::get_pdf_priceinc_heading( $order ), 									$content );
				$content = str_replace(	'[[PDFINVOICE_TOTALINC_HEADING]]', 			self::get_pdf_totalinc_heading( $order ), 									$content );

				$content = str_replace(	'[[PDFINVOICE_REGISTEREDNAME_HEADING]]', 	self::get_pdf_template_registered_name_text( $order, $settings ), 			$content );
				$content = str_replace(	'[[PDFINVOICE_REGISTEREDOFFICE_HEADING]]', 	self::get_pdf_template_registered_office_text( $order, $settings ), 		$content );
				$content = str_replace(	'[[PDFINVOICE_COMPANYNUMBER_HEADING]]', 	self::get_pdf_template_company_number_text( $order, $settings ), 			$content );
				$content = str_replace(	'[[PDFINVOICE_VATNUMBER_HEADING]]', 		self::get_pdf_template_vat_number_text( $order, $settings ), 				$content );

				$content = str_replace(	'[[PDFBARCODES]]', 							self::get_barcode( $order_id ), 			 								$content );
				$content = str_replace(	'[[PDFBILLINGVATNUMBER]]', 					self::get_vat_number( $order_id ), 		 									$content );

				if( preg_match('/ORDERDETAILS(.*?)ENDORDERDETAILS/', $content, $match) == 1 ) {
					$template_order_details = WC_send_pdf::get_pdf_template_invoice_order_details( $order, $match );
					$content = preg_replace( '/ORDERDETAILS(.*?)ENDORDERDETAILS/', WC_send_pdf::get_pdf_template_invoice_order_details( $order, $match ), $content );
				}

				// Allow the content to be filtered
				$content = apply_filters( 'pdf_content_additional_content' , $content , $order_id );

				// WPML
				global $current_language;

				do_action( 'after_invoice_content', $current_language ); 
		
				return mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
			}

			/**
			 * [get_fontfamily description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_fontfamily( $order_id, $settings ) {
				$fontfamily = apply_filters( 'pdf_invoice_default_font_family', '"DejaVu Sans", "DejaVu Sans Mono", "DejaVu", sans-serif, monospace', $order_id );

				if( isset($settings['pdf_font']) && $settings['pdf_font'] != "" && $settings['pdf_font'] != "Default" ) {
					$fontfamily = '"' . $settings['pdf_font'] . '"';
				}

				return apply_filters( 'pdf_invoice_display_font_family', $fontfamily, $order_id );

			}

			/**
			 * [get_fontfamily description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_currency_fontfamily( $order_id, $settings ) {

				$currency_font_css = '';

				if( isset( $settings['pdf_currency_font'] ) && $settings['pdf_currency_font'] != "" && $settings['pdf_currency_font'] != "false" ) {

					/*
						Adding your own font family.
						add_filter('pdf_invoice_currency_symbol_font_family', 'custom_pdf_invoice_currency_symbol_font_family', $font, $order_id );
						function custom_pdf_invoice_currency_symbol_font_family( $font, $order_id ) {
							return "DejaVu Sans";
						}
					*/
					$font = apply_filters( 'pdf_invoice_currency_symbol_font_family', $settings['pdf_currency_font'], $order_id );

					$currency_font_css =    'span.woocommerce-Price-currencySymbol {
										      font-family: "' . $font . '";
										      font-size: 11px;
										    }';
				}

				/*
					Adding your own font from Google Fonts.
					add_filter('pdf_invoice_currency_symbol_font_css', 'custom_pdf_invoice_currency_symbol_font_css', $font_css, $order_id );
					function custom_pdf_invoice_currency_symbol_font_css( $font_css, $order_id ) {
						return "@import url('https://fonts.googleapis.com/css?family=Roboto&display=swap');
							    span.woocommerce-Price-currencySymbol {
							      font-family: 'Roboto';
							      font-size: 11px;
							      font-weight: normal;
							    }";
					}
				*/
				return apply_filters( 'pdf_invoice_currency_symbol_font_css', $currency_font_css, $order_id );

			}

			/**
			 * [get_pdf_company_field description]
			 * @param  {int} $order_id  [WooCommerce Order ID]
			 * @return {text}          [field required from order/settings]
			 */
			public static function get_pdf_logo( $order_id, $settings ) {

				// Get the logo
				$pdflogo = $settings['logo_file'];

				if ( $pdflogo ) :

					// Replace the URL with the file structure
					// Required whn the Remote Logo option is set to "no"
					$pdflogo = str_replace( site_url(), ABSPATH, $pdflogo );


					$logo = '<img src="' . $pdflogo . '" alt="' . get_bloginfo('name') . '" />';				
				else :
					$logo = '<h1>' . get_bloginfo('name') . '</h1>';	
				endif;

				return apply_filters( 'pdf_invoice_display_logo', $logo, $order_id );

			}

			/**
			 * [get_invoice_companyname description]
			 * @param  [type] $order_id                         [description]
			 * @param  [type] $woocommerce_pdf_invoice_settings [description]
			 * @return [type]                                   [description]
			 */
			public static function get_invoice_companyname( $order_id, $settings ) {

				$pdfcompanyname = get_post_meta( $order_id,'_pdf_company_name',TRUE );

				if ( !isset( $pdfcompanyname ) || $pdfcompanyname == '' ) {
					$pdfcompanyname    = __( $settings['pdf_company_name'], 'woocommerce-pdf-invoice' );
				}

				return apply_filters( 'pdf_invoice_display_companyname', $pdfcompanyname, $order_id );
			}

			/**
			 * [get_invoice_companydetails description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 */
			public static function get_invoice_companydetails( $order_id, $settings ) {

				$pdf_company_details = nl2br( get_post_meta( $order_id,'_pdf_company_details',TRUE ) );

				if ( !isset( $pdf_company_detailspdf_company_details ) || $pdf_company_details == '' ) {
					$pdf_company_details    = nl2br( __( $settings['pdf_company_details'], 'woocommerce-pdf-invoice' ) );
				}

				return apply_filters( 'pdf_invoice_display_companydetails', $pdf_company_details, $order_id );
			}

			/**
			 * [get_invoice_registeredname description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 */
			public static function get_invoice_registeredname( $order_id, $settings ) {

				$pdf_registered_name = get_post_meta( $order_id,'_pdf_registered_name',TRUE );

				if ( !isset( $pdf_registered_name ) || $pdf_registered_name == '' ) {
					$pdf_registered_name    = __( $settings['pdf_registered_name'], 'woocommerce-pdf-invoice' );
				}

				return apply_filters( 'pdf_invoice_display_registeredname', $pdf_registered_name, $order_id );
			}

			/**
			 * [get_invoice_registeredaddress description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 */
			public static function get_invoice_registeredaddress( $order_id, $settings ) {

				$pdf_registered_address = get_post_meta( $order_id,'_pdf_registered_address',TRUE );

				if ( !isset( $pdf_registered_address ) || $pdf_registered_address == '' ) {
					$pdf_registered_address    = __( $settings['pdf_registered_address'], 'woocommerce-pdf-invoice' );
				}

				return apply_filters( 'pdf_invoice_display_registered_address', $pdf_registered_address, $order_id );
			}

			/**
			 * [get_invoice_companynumber description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 */
			public static function get_invoice_companynumber( $order_id, $settings ) {

				$pdf_company_number = get_post_meta( $order_id,'_pdf_company_number',TRUE );

				if ( !isset( $pdf_company_number ) || $pdf_company_number == '' ) {
					$pdf_company_number    = __( $settings['pdf_company_number'], 'woocommerce-pdf-invoice' );
				}

				return apply_filters( 'pdf_invoice_display_company_number', $pdf_company_number, $order_id );
			}

			/**
			 * [get_invoice_taxnumber description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 */
			public static function get_invoice_taxnumber( $order_id, $settings ) {

				$pdf_tax_number = get_post_meta( $order_id,'_pdf_tax_number',TRUE );

				if ( !isset( $pdf_tax_number ) || $pdf_tax_number == '' ) {
					$pdf_tax_number    = __( $settings['pdf_tax_number'], 'woocommerce-pdf-invoice' );
				}

				return apply_filters( 'pdf_invoice_display_tax_number', $pdf_tax_number, $order_id );
			}

			/**
			 * [get_invoice_registeredname description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 *
			 * <?php echo apply_filters( 'pdf_template_registered_name_text', __( 'Registered Name : ', 'woocommerce-pdf-invoice' ) ); ?>[[PDFREGISTEREDNAME]] 
			 */
			public static function get_invoice_registeredname_section( $order_id, $settings ) {

				$title = apply_filters( 'pdf_template_registered_name_text', __( 'Registered Name : ', 'woocommerce-pdf-invoice' ) );

				$pdf_registered_name = get_post_meta( $order_id,'_pdf_registered_name',TRUE );

				if ( !isset( $pdf_registered_name ) || $pdf_registered_name == '' ) {
					$pdf_registered_name    = __( $settings['pdf_registered_name'], 'woocommerce-pdf-invoice' );
				}

				$pdf_registered_name = apply_filters( 'pdf_invoice_display_registeredname', $pdf_registered_name, $order_id );

				if ( isset( $pdf_registered_name ) && $pdf_registered_name != '' ) { 
					return $title . $pdf_registered_name;
				} else {
					return '';
				}
			}

			/**
			 * [get_invoice_registeredaddress description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 *
			 * <?php echo apply_filters( 'pdf_template_registered_office_text', __( 'Registered Office : ', 'woocommerce-pdf-invoice' ) ); ?>[[PDFREGISTEREDADDRESS]]
			 */
			public static function get_invoice_registeredaddress_section( $order_id, $settings ) {

				$title = apply_filters( 'pdf_template_registered_office_text', __( 'Registered Office : ', 'woocommerce-pdf-invoice' ) );

				$pdf_registered_address = get_post_meta( $order_id,'_pdf_registered_address',TRUE );

				if ( !isset( $pdf_registered_address ) || $pdf_registered_address == '' ) {
					$pdf_registered_address    = __( $settings['pdf_registered_address'], 'woocommerce-pdf-invoice' );
				}

				$pdf_registered_address = apply_filters( 'pdf_invoice_display_registered_address', $pdf_registered_address, $order_id );

				if ( isset( $pdf_registered_address ) && $pdf_registered_address != '' ) { 
					return $title . $pdf_registered_address;
				} else {
					return '';
				}
			}

			/**
			 * [get_invoice_companynumber description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 *
			 * <?php echo apply_filters( 'pdf_template_company_number_text', __( 'Company Number : ', 'woocommerce-pdf-invoice' ) ); ?>[[PDFCOMPANYNUMBER]]
			 */
			public static function get_invoice_companynumber_section( $order_id, $settings ) {

				$title = apply_filters( 'pdf_template_company_number_text', __( 'Company Number : ', 'woocommerce-pdf-invoice' ) );

				$pdf_company_number = get_post_meta( $order_id,'_pdf_company_number',TRUE );

				if ( !isset( $pdf_company_number ) || $pdf_company_number == '' ) {
					$pdf_company_number    = __( $settings['pdf_company_number'], 'woocommerce-pdf-invoice' );
				}

				$pdf_company_number = apply_filters( 'pdf_invoice_display_company_number', $pdf_company_number, $order_id );

				if ( isset( $pdf_company_number ) && $pdf_company_number != '' ) { 
					return $title . $pdf_company_number;
				} else {
					return '';
				}
			}

			/**
			 * [get_invoice_taxnumber description]
			 * @param  [type] $order_id                        [description]
			 * @param  [type] $woocommerce_pdf_invoice_options [description]
			 * @return [type]                                  [description]
			 *
			 * <?php echo apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number : ', 'woocommerce-pdf-invoice' ) ); ?>[[PDFTAXNUMBER]]
			 */
			public static function get_invoice_taxnumber_section( $order_id, $settings ) {

				$title = apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number : ', 'woocommerce-pdf-invoice' ) );

				$pdf_tax_number = get_post_meta( $order_id,'_pdf_tax_number',TRUE );

				if ( !isset( $pdf_tax_number ) || $pdf_tax_number == '' ) {
					$pdf_tax_number    = __( $settings['pdf_tax_number'], 'woocommerce-pdf-invoice' );
				}

				$pdf_tax_number = apply_filters( 'pdf_invoice_display_tax_number', $pdf_tax_number, $order_id );

				if ( isset( $pdf_tax_number ) && $pdf_tax_number != '' ) { 
					return $title . $pdf_tax_number;
				} else {
					return '';
				}
			}

			/**
			 * Get the Invoice Number
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_display_invoice_num( $order_id ) {
				if ( $order_id ) :
					$invnum = esc_html( get_post_meta( $order_id, '_invoice_number_display', true ) );
				else :
					$invnum = ''; 
				endif;

				return apply_filters( 'pdf_display_invoice_number', $invnum, $order_id );
			}

			/**
			 * [get_display_order_number description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_invoice_display_order_number( $order ) {
				// Get order id
				$order_id = $order->get_id();
				
				// Look for the Sequential Order Numbers Pro / Sequential Order Numbers order number and use it if it's there
				$output_order_num = $order->get_order_number();

				// Load plugin.php if required
				if( !is_admin() ) {
					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				// Sequential Order Numbers
				if ( get_post_meta( $order_id,'_order_number',TRUE ) && class_exists( 'WC_Seq_Order_Number' ) ) :
					$output_order_num = get_post_meta( $order_id,'_order_number',TRUE );
				endif;

				// Sequential Order Numbers Pro
				if ( get_post_meta( $order_id,'_order_number_formatted',TRUE ) && class_exists( 'WC_Seq_Order_Number_Pro' ) ) :
					$output_order_num = get_post_meta( $order_id,'_order_number_formatted',TRUE );
				endif;

				return apply_filters( 'pdf_invoice_display_order_number', $output_order_num, $order ); 

			}

			/** 
			 * Get the invoice date
			 * @param  [type] $order_id [description]
			 * @param  [type] $usedate  [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_display_date( $order_id, $usedate, $sendsomething = false, $display_date = 'invoice' ) {
				global $woocommerce;

				$order 	 						 = new WC_Order( $order_id );
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				$date_format 					 = $woocommerce_pdf_invoice_options['pdf_date_format'];

				// Invoice Date : Use Invoice date from order meta if available
				if( get_post_meta( $order_id, '_invoice_date', TRUE ) && $display_date == 'invoice' ) {
					return apply_filters( 'pdf_display_invoice_date', get_post_meta( $order_id, '_invoice_date', TRUE ), $order_id, $usedate, $sendsomething, $display_date );
				}

				// Order Date
				if( $order->get_date_created() && $display_date == 'order' ) {
					return apply_filters( 'pdf_display_invoice_date', self::get_formatted_date( $order->get_date_created(), $date_format ), $order_id, $usedate, $sendsomething, $display_date );
				}

				// No date stored
				$date = NULL;

				// Force a $date_format if one is not set
				if ( !isset( $date_format ) || $date_format == '' ) {
					$date_format = "j F, Y";
				}

				if ( $usedate == 'completed' && $order->get_status() == 'completed' ) {
					// Order completed data
					$date = WC_send_pdf::get_completed_date( $order_id );
				} else {
					// Order placed date
					$date = $order->get_date_created();
				}

				// In some cases $date will be empty so we might want to send the order date
				if ( $sendsomething && !$date ) {
					$date = $order->get_date_created();
				}
				
				// Format the date
				if ( $date ) {
					// Return a date in the format that matches the PDF Ivoice settings.
					return apply_filters( 'pdf_display_invoice_date', self::get_formatted_date( $date, $date_format ), $order_id, $usedate, $sendsomething, $display_date );
				}

				// Nothing to return
				return apply_filters( 'pdf_display_invoice_date', '', $order_id, $usedate, $sendsomething, $display_date );
		
			}

			/**
			 * [get_invoice_billing_address description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_invoice_billing_address( $order ) {
				return apply_filters( 'pdf_invoice_billing_address', $order->get_formatted_billing_address(), $order );
			}

			/**
			 * [get_invoice_billing_phone description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_billing_phone( $order_id ) {
				return apply_filters( 'pdf_invoice_billing_phone', get_post_meta( $order_id,'_billing_phone',TRUE ), $order_id );
			}

			/**
			 * [get_invoice_billing_email description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_billing_email( $order_id ) {
				return apply_filters( 'pdf_invoice_billing_email', get_post_meta( $order_id,'_billing_email',TRUE ), $order_id );
			}

			/**
			 * [get_invoice_billing_vat_number description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_billing_vat_number( $order_id ) {

				$billing_vat_number = '';

				// Support for EU VAT Number Extension
				if ( get_post_meta( $order_id,'VAT Number',TRUE ) ) {
					$billing_vat_number = __( 'VAT Number : ', 'woocommerce-pdf-invoice' ) . get_post_meta( $order_id,'VAT Number',TRUE );
				} elseif ( get_post_meta( $order_id,'vat_number',TRUE ) ) {	
					$billing_vat_number = __( 'VAT Number : ', 'woocommerce-pdf-invoice' ) . get_post_meta( $order_id,'vat_number',TRUE );
				}

				return apply_filters( 'pdf_invoice_billing_email', $billing_vat_number, $order_id );
			}

			/**
			 * [get_shipping_address description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_invoice_shipping_address( $order ) {
				return apply_filters( 'pdf_invoice_shipping_address', $order->get_formatted_shipping_address(), $order );
			}

			/**
			 * [get_invoice_payment_method_title description]
			 * Add PO Number from order meta
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_payment_method_title( $order_id ) {
				$order 	 				= new WC_Order( $order_id );
				$payment_method 		= get_post_meta( $order_id, '_payment_method', true );
				$payment_method_title 	= ucwords( $order->get_payment_method_title() );
				
				if ( $payment_method == 'woocommerce_gateway_purchase_order' ) {
					$payment_method_title .= null !== get_post_meta( $order_id, '_po_number', true ) ? ' ' . get_post_meta( $order_id, '_po_number', true ) : '';
				}

				return apply_filters( 'pdf_invoice_payment_method_title', $payment_method_title, $order_id );
			}

			/**
			 * [get_invoice_shipping_method_title description]
			 * Add PO Number from order meta
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_invoice_shipping_method_title( $order ) {
				return apply_filters( 'pdf_invoice_shipping_method_title', ucwords( $order->get_shipping_method() ), $order );
			}

			/**
			 * [get_pdf_headers description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_pdf_headers( $order_id ) {

				$headers =  '<table class="shop_table orderdetails" width="100%">' . 
							'<thead>' .
							'<tr><th colspan="7" align="left"><h2>' . esc_html__('Order Details', 'woocommerce-pdf-invoice') . '</h2></th></tr>' .
							'<tr>' .
							'<th width="5%" valign="top" align="right">'  . esc_html__( 'Qty', 'woocommerce-pdf-invoice' ) 		. '</th>' .						
							'<th width="50%" valign="top" align="left">'  . esc_html__( 'Product', 'woocommerce-pdf-invoice' ) 	. '</th>' .
							'<th width="9%" valign="top" align="right">'  . esc_html__( 'Price Ex', 'woocommerce-pdf-invoice' ) 	. '</th>' .
							'<th width="9%" valign="top" align="right">'  . esc_html__( 'Total Ex.', 'woocommerce-pdf-invoice' ) 	. '</th>' .
							'<th width="7%" valign="top" align="right">'  . esc_html__( 'Tax', 'woocommerce-pdf-invoice' ) 		. '</th>' .
							'<th width="10%" valign="top" align="right">' . esc_html__( 'Price Inc', 'woocommerce-pdf-invoice' ) 	. '</th>' .
							'<th width="10%" valign="top" align="right">' . esc_html__( 'Total Inc', 'woocommerce-pdf-invoice' ) 	. '</th>' .
							'</tr>' .
							'</thead>' .
							'</table>';

				$headers = apply_filters( 'pdf_template_table_headings', $headers, $order_id );

				return $headers;
				
			}

			/**
			 * Get the PDF order details in a table
			 * @param  [type] $order_id 
			 * @return [type]           
			 */
			function get_pdf_order_details( $order_id ) {
				global $woocommerce;

				$order 	 		= new WC_Order( $order_id );
				$order_currency = $order->get_currency();

				$item_loop 		= 0;

				$row_class 		= '';
				$cell_class 	= '';

				$row_even_class = apply_filters( 'pdf_invoices_pdf_table_row_even_class', 'pdf_table_row_even', $order_id );
				$row_odd_class 	= apply_filters( 'pdf_invoices_pdf_table_row_odd_class', 'pdf_table_row_odd', $order_id );
				$cell_even_class= apply_filters( 'pdf_invoices_pdf_table_cell_even_class', 'pdf_table_cell_even', $order_id );
				$cell_odd_class = apply_filters( 'pdf_invoices_pdf_table_cell_odd_class', 'pdf_table_cell_odd', $order_id );
							
				$pdflines  = '<table width="100%" class="shop_table ordercontent">';
				$pdflines .= '<tbody>';

				if ( sizeof( $order->get_items() ) > 0 ) {

					foreach ( $order->get_items() as $item ) {

						if ( $item['quantity'] ) {
							
							$line = '';
							$item_loop++;

							if( $item_loop % 2 == 0 ){ 
						        $row_class 		= $row_even_class;
						        $cell_class 	= $cell_even_class;  
						    } else { 
						        $row_class 		= $row_odd_class; 
						        $cell_class 	= $cell_odd_class;
						    }

							$_product 	= $order->get_product_from_item( $item );
							$item_name 	= $item['name'];
							$item_id 	= $item->get_id();

							$meta_display = '';
							foreach ( $item->get_formatted_meta_data() as $meta_key => $meta ) {
								$meta_display .= '<br /><small>(' . $meta->display_key . ':' . wp_kses_post( strip_tags( $meta->display_value ) ) . ')</small>';
			 				}

			 				// Add Booking details
			 				if ( class_exists( 'WC_Booking_Data_Store' ) ) {
								$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );

								if ( $booking_ids ) {
									foreach ( $booking_ids as $booking_id ) {

										$booking = new WC_Booking( $booking_id );

										$product  = $booking->get_product();
										$resource = $booking->get_resource();
										$label    = $product && is_callable( array( $product, 'get_resource_label' ) ) && $product->get_resource_label() ? $product->get_resource_label() : __( 'Type', 'woocommerce-bookings' );

										if ( strtotime( 'midnight', $booking->get_start() ) === strtotime( 'midnight', $booking->get_end() ) ) {
											$booking_date = sprintf( '%1$s', $booking->get_start_date() );
										} else {
											$booking_date = sprintf( '%1$s / %2$s', $booking->get_start_date(), $booking->get_end_date() );
										}

										$meta_display .= '<br /><small>' . esc_html( sprintf( __( 'Booking ID : %d', 'woocommerce-pdf-invoice' ), $booking_id ) ) . '</small>';
										$meta_display .= '<br /><small>' . esc_html( sprintf( __( 'Booking Date : %s', 'woocommerce-pdf-invoice' ), apply_filters( 'wc_bookings_summary_list_date', $booking_date, $booking->get_start(), $booking->get_end() ) ) ) . '</small>';

										if ( $resource ) :
											$meta_display .= '<br /><small>' . esc_html( sprintf( __( '%s: %s', 'woocommerce-bookings' ), $label, $resource->get_name() ) ) . '</small>';
										endif;

										if ( $product->has_persons() ) {
											if ( $product->has_person_types() ) {
												$person_types  = $product->get_person_types();
												$person_counts = $booking->get_person_counts();

												if ( ! empty( $person_types ) && is_array( $person_types ) ) {
													foreach ( $person_types as $person_type ) {

														if ( empty( $person_counts[ $person_type->get_id() ] ) ) {
															continue;
														}

														$meta_display .= '<br /><small>' . esc_html( sprintf( '%s: %d', $person_type->get_name(), $person_counts[ $person_type->get_id() ] ) ) . '</small>';

													}
												}
											} else {

												$meta_display .= '<br /><small>Persons : ' . esc_html( sprintf( __( '%d Persons', 'woocommerce-bookings' ), array_sum( $booking->get_person_counts() ) ) ) . '</small>';

											}
										}

									}

								}

			 				} // Add Booking details
 
							if ( $meta_display ) {

								$meta_output	 = apply_filters( 'pdf_invoice_meta_output', $meta_display );
								$item_name 		.= $meta_output;

 							}

 							/**
 							 * Allow additional info to be added to the $item_name
							 *
 							 * add_filter( 'pdf_invoice_item_name', 'add_product_description_pdf_invoice_item_name', 10, 4 );
 							 * 
 							 * function add_product_description_pdf_invoice_item_name( $item_name, $item, $product, $order ) {
 							 * 	
 							 *	// Use $product->get_id() if you want to get the post id for the product.
 							 * 	$item_name .= '<p>' . $product->get_description() . '</p>';
 							 * 	return $item_name;
	 						 * 
 							 * }
 							 */
 							$item_name = apply_filters( 'pdf_invoice_item_name', $item_name, $item, $_product, $order );

							$line .= 	'<tr class="pdf_table_row '  . $row_class  . '">' .
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="5%" align="right">' . $item['quantity'] . ' x</td>' .
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="50%">' .  stripslashes( $item_name ) . '</td>' .
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="9%" align="right">'  .  wc_price( $item['total'] / $item['qty'], array( 'currency' => $order_currency ) ) . '</td>' .							
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="9%" align="right">'  .  wc_price( $item['total'], array( 'currency' => $order_currency ) ) . '</td>' .	
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="7%" align="right">'  .  wc_price( $item['total_tax'] / $item['qty'], array( 'currency' => $order_currency ) ). '</td>' .			
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="10%" align="right">' .  wc_price( ( $item['total'] + $item['total_tax'] ) / $item['qty'], array( 'currency' => $order_currency ) ). '</td>' .
										'<td class="pdf_table_cell ' . $cell_class . '" valign="top" width="10%" align="right">' .  wc_price( $item['total'] + $item['total_tax'], array( 'currency' => $order_currency ) ). '</td>' .
										'</tr>';
							
							$pdflines .= $line;
						}
					}
			
				} // if ( sizeof( $order->get_items() ) > 0 ) {

				$pdflines .=	'</tbody>';
				$pdflines .=	'</table>';
				
				$pdf = apply_filters( 'pdf_template_line_output', $pdflines, $order_id );
				return $pdf;
			}

			/**
			 * [get_pdf_template_invoice_order_details description]
			 *
			 * identifier : column width : column title
			 *
			 * ORDERDETAILS 
			 * 	quantity:5:Qty:, 
			 * 	product:50:Description:, 
			 * 	priceex:9:Price Ex:, 
			 * 	totalex:9:Total Ex:, 
			 * 	tax:7:Tax:, 
			 * 	priceinc:10:Price Inc:, 
			 * 	totalinc:10:Total Inc: 
			 * ENDORDERDETAILS
			 * 
			 * @param  [type] $order [description]
			 * @param  [type] $match [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_invoice_order_details( $order, $match ) {

				$order_currency = $order->get_currency();
			    $fields 		= explode( ',', $match[1] );
			    $odd_even 		= 0;

			    // Table Headings
				$return = '<thead class="pdforderdetails_headings">
							<tr>';
						    foreach( $fields AS $field ) {
				    			$output = explode( ':', $field );
				    			$return .= '<th align="left" class="pdforderdetails_heading" width="'.$output[1].'%">' . ucwords( $output[2] ) . '</th>';
				    		}		
				$return .= '</tr>
							</thead>';

				// Order items
				if ( sizeof( $order->get_items() ) > 0 ) {

					foreach ( $order->get_items() as $item ) {

						// counter
						$odd_even++;

						if ($odd_even % 2 == 0) {
  							$odd_even_row_class 	= 'pdforderdetails_row_even';
  							$odd_even_cell_class 	= 'pdforderdetails_cell_even';
						} else {
							$odd_even_row_class 	= 'pdforderdetails_row_odd';
							$odd_even_cell_class 	= 'pdforderdetails_cell_odd';
						}

						$return .= '<tr class="pdforderdetails_row ' .$odd_even_row_class. '">';
						    foreach( $fields AS $field ) {
				    			$output 	= explode( ':', $field );
				    			$width 		= strtolower( trim($output[1]) );
				    			$identifier = strtolower( trim($output[0]) );

				    			$return .= '<td align="left" width="'.$output[1].'%" class="pdforderdetails_cell ' .$odd_even_cell_class. '">';

								switch ( $identifier ) {
									case "counter":
								        $return .= $odd_even;
								        break;
								    case "quantity":
								        $return .= $item['quantity'];
								        break;
								    case "product":
								        $return .= WC_send_pdf::get_pdf_template_invoice_order_item_name( $item['name'], $item, $order, strtolower(trim($output[0])) );
								        break;
								    case "priceex":
								        $return .= wc_price( $item['total'] / $item['qty'], array( 'currency' => $order_currency ) );
								        break;
								    case "totalex":
								        $return .= wc_price( $item['total'], array( 'currency' => $order_currency ) );
								        break;
								    case "tax":
								        $return .= wc_price( $item['total_tax'] / $item['qty'], array( 'currency' => $order_currency ) );
								        break;
								    case "priceinc":
								        $return .= wc_price( ( $item['total'] + $item['total_tax'] ) / $item['qty'], array( 'currency' => $order_currency ) );
								        break;
								    case "totalinc":
								        $return .= wc_price( $item['total'] + $item['total_tax'], array( 'currency' => $order_currency ) );
								        break;
								    default:
								        $return .= WC_send_pdf::get_pdf_template_invoice_order_item_custom( $identifier, $item, $order );
								}

								$return .= '</td>';

				    		}		
							
							$return .= '</tr>';

					} // foreach ( $order->get_items() as $item ) {

				} // if ( sizeof( $order->get_items() ) > 0 ) {

				return $return;

			}

			/**
			 * [get_pdf_template_invoice_order_item_name description]
			 * @param  [type] $item_name [description]
			 * @param  [type] $item      [description]
			 * @param  [type] $order     [description]
			 * @param  [type] $output    [description]
			 * @return [type]            [description]
			 */
			public static function get_pdf_template_invoice_order_item_name( $item_name, $item, $order, $output ) {

				// Check WC version - changes for WC 3.0.0
				$pre_wc_30 		= version_compare( WC_VERSION, '3.0', '<' );

				$_product 	= $order->get_product_from_item( $item );
				$item_name 	= $item['name'];
				$item_id 	= $pre_wc_30 ? $item['variation_id'] : $item->get_id();

				$meta_display = '';
				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$item_meta  = new WC_Order_Item_Meta( $item );
					$meta_display = $item_meta->display( true, true );
					$meta_display = $meta_display ? ( ' ( ' . $meta_display . ' )' ) : '';
				} else {
					foreach ( $item->get_formatted_meta_data() as $meta_key => $meta ) {
						$meta_display .= '<br /><small>(' . $meta->display_key . ':' . wp_kses_post( strip_tags( $meta->display_value ) ) . ')</small>';
					}
				}

				// Add Booking details
				if ( class_exists( 'WC_Booking_Data_Store' ) ) {
					$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );

					if ( $booking_ids ) {
						foreach ( $booking_ids as $booking_id ) {

							$booking = new WC_Booking( $booking_id );

							$product  = $booking->get_product();
							$resource = $booking->get_resource();
							$label    = $product && is_callable( array( $product, 'get_resource_label' ) ) && $product->get_resource_label() ? $product->get_resource_label() : __( 'Type', 'woocommerce-bookings' );

							if ( strtotime( 'midnight', $booking->get_start() ) === strtotime( 'midnight', $booking->get_end() ) ) {
								$booking_date = sprintf( '%1$s', $booking->get_start_date() );
							} else {
								$booking_date = sprintf( '%1$s / %2$s', $booking->get_start_date(), $booking->get_end_date() );
							}

							$meta_display .= '<br /><small>' . esc_html( sprintf( __( 'Booking ID : %d', 'woocommerce-pdf-invoice' ), $booking_id ) ) . '</small>';
							$meta_display .= '<br /><small>' . esc_html( sprintf( __( 'Booking Date : %s', 'woocommerce-pdf-invoice' ), apply_filters( 'wc_bookings_summary_list_date', $booking_date, $booking->get_start(), $booking->get_end() ) ) ) . '</small>';

							if ( $resource ) :
								$meta_display .= '<br /><small>' . esc_html( sprintf( __( '%s: %s', 'woocommerce-bookings' ), $label, $resource->get_name() ) ) . '</small>';
							endif;

							if ( $product->has_persons() ) {
								if ( $product->has_person_types() ) {
									$person_types  = $product->get_person_types();
									$person_counts = $booking->get_person_counts();

									if ( ! empty( $person_types ) && is_array( $person_types ) ) {
										foreach ( $person_types as $person_type ) {

											if ( empty( $person_counts[ $person_type->get_id() ] ) ) {
												continue;
											}

											$meta_display .= '<br /><small>' . esc_html( sprintf( '%s: %d', $person_type->get_name(), $person_counts[ $person_type->get_id() ] ) ) . '</small>';

										}
									}
								} else {

									$meta_display .= '<br /><small>Persons : ' . esc_html( sprintf( __( '%d Persons', 'woocommerce-bookings' ), array_sum( $booking->get_person_counts() ) ) ) . '</small>';

								}
							}

						}

					}

				} // Add Booking details

				if ( $meta_display ) {

					$meta_output	 = apply_filters( 'pdf_invoice_meta_output', $meta_display );
					$item_name 		.= $meta_output;

				}

				/**
				 * Allow additional info to be added to the $item_name
				 *
				 * add_filter( 'pdf_invoice_item_name', 'add_product_description_pdf_invoice_item_name', 10, 4 );
				 * 
				 * function add_product_description_pdf_invoice_item_name( $item_name, $item, $product, $order ) {
				 * 	
				 *	// Use $product->get_id() if you want to get the post id for the product.
				 * 	$item_name .= '<p>' . $product->get_description() . '</p>';
				 * 	return $item_name;
				 * 
				 * }
				 */
				$item_name = apply_filters( 'pdf_invoice_item_name', $item_name, $item, $_product, $order );

				return $item_name;

			}

			/**
			 * [get_pdf_template_invoice_order_item_custom description]
			 * @param  [type] $identifier [description]
			 * @param  [type] $item       [description]
			 * @param  [type] $order      [description]
			 * @return [type]             [description]
			 */
			public static function get_pdf_template_invoice_order_item_custom( $identifier, $item, $order ) {
				// Item : 
				// get_type
				// get_product_id
				// get_variation_id
				// get_quantity
				// get_tax_class
				// get_subtotal
				// get_subtotal_tax
				// get_total
				// get_total_tax
				// get_taxes
				 
				// product : 
				// get_type
				// get_name
				// get_slug
				// get_date_created
				// get_date_modified
				// get_status
				// get_featured
				// get_catalog_visibility
				// get_description
				// get_short_description
				// get_sku
				// get_price
				// get_regular_price
				// get_sale_price
				// get_date_on_sale_from
				// get_date_on_sale_to
				// get_total_sales
				// get_tax_status
				// get_tax_class
				// get_manage_stock
				// get_stock_quantity
				// get_stock_status
				// get_backorders
				// get_low_stock_amount
				// get_sold_individually
				// get_weight
				// get_length
				// get_width
				// get_height
				// get_dimensions
				// get_upsell_ids
				// get_cross_sell_ids
				// get_parent_id
				// get_reviews_allowed
				// get_purchase_note
				// get_attributes
				// get_default_attributes
				// get_menu_order
				// get_post_password
				// get_category_ids
				// get_tag_ids
				// get_virtual
				// get_gallery_image_ids
				// get_shipping_class_id
				// get_downloads
				// get_download_expiry
				// get_downloadable
				// get_download_limit
				// get_image_id
				// get_rating_counts
				// get_average_rating
				// get_review_count
				// get_image
				// get_shipping_class
				// get_attribute
				// get_rating_count
				// get_file
				// get_file_download_path
				// get_price_suffix
				// get_availability
				// get_availability_text
				// get_availability_class

				$custom 	= '';
				$product 	= $order->get_product_from_item( $item );

				// WC_pdf_debug::pdf_debug( $identifier, 'WC_PDF_Invoice', __('Identifier : ', 'woocommerce-pdf-invoice'), FALSE );
				// WC_pdf_debug::pdf_debug( $item, 'WC_PDF_Invoice', __('Item : ', 'woocommerce-pdf-invoice'), FALSE );
				// WC_pdf_debug::pdf_debug( $order, 'WC_PDF_Invoice', __('Order : ', 'woocommerce-pdf-invoice'), FALSE );
				// WC_pdf_debug::pdf_debug( $product, 'WC_PDF_Invoice', __('Product : ', 'woocommerce-pdf-invoice'), FALSE );

				if( is_callable( array( $item, 'get_'.$identifier ) ) ) {
					$method = 'get_'.$identifier;
					$custom = $item->$method();
				} elseif( is_callable( array( $product, 'get_'.$identifier ) ) ) {
					$method = 'get_'.$identifier;
					$custom = $product->$method();
				} elseif( is_callable( array( $order, 'get_'.$identifier ) ) ) {
					$method = 'get_'.$identifier;
					$custom = $order->$method();
				} else {
					$custom = apply_filters( 'get_pdf_template_invoice_order_item_custom_filter', $custom, $identifier, $item, $product, $order );
				}

				return $custom;
			}

			/**
			 * Get the Invoice Number
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			function get_woocommerce_pdf_invoice_num( $order_id ) {
				global $woocommerce;
		
				if ( $order_id ) :
					$invnum = esc_html( get_post_meta( $order_id, '_invoice_number_display', true ) );
				else :
					$invnum = ''; 
				endif;

				return $invnum;
			}
	
			/** 
			 * Get the invoice date
			 * @param  [type] $order_id [description]
			 * @param  [type] $usedate  [description]
			 * @return [type]           [description]
			 */
			public static function get_woocommerce_pdf_date( $order_id, $usedate, $sendsomething = false, $display_date = 'invoice' ) {
				global $woocommerce;

				$order 	 						 = new WC_Order( $order_id );
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				$date_format 					 = $woocommerce_pdf_invoice_options['pdf_date_format'];

				// Invoice Date : Use Invoice date from order meta if available
				if( get_post_meta( $order_id, '_invoice_date', TRUE ) && $display_date == 'invoice' ) {
					return get_post_meta( $order_id, '_invoice_date', TRUE );
				}

				// Order Date
				if( $order->get_date_created() && $display_date == 'order' ) {
					return self::get_formatted_date( $order->get_date_created(), $date_format );
				}

				// No date stored
				$date = NULL;

				// Force a $date_format if one is not set
				if ( !isset( $date_format ) || $date_format == '' ) {
					$date_format = "j F, Y";
				}

				if ( $usedate == 'completed' && $order->get_status() == 'completed' ) {
					// Order completed data
					$date = WC_send_pdf::get_completed_date( $order_id );
				} else {
					// Order placed date
					$date = $order->get_date_created();
				}

				// In some cases $date will be empty so we might want to send the order date
				if ( $sendsomething && !$date ) {
					$date = $order->get_date_created();
				}
				
				// Format the date
				if ( $date ) {
					// Return a date in the format that matches the PDF Ivoice settings.
					return self::get_formatted_date( $date, $date_format );
				}

				// Nothing to return
				return '';
		
			}

			// Get the date the order was completed if _invoice_date was not set at the time the invoice number was created
			public static function get_completed_date( $order_id ) {

				$date = '';

				// Use _date_completed from order meta
				$date = get_post_meta( $order_id, '_completed_date', true );

				// if _date_completed is empty then use this as a backup
				if( !isset( $date ) || $date == '' ) {

					if( get_post_meta($order_id, '_invoice_meta', TRUE) && get_post_meta($order_id, '_invoice_meta', TRUE) != '' ) {

						$invoice_meta = get_post_meta($order_id, '_invoice_meta', TRUE);
						$date 		  = $invoice_meta['invoice_created'];

					} else {
						global $wpdb;

						$invoice_number = get_post_meta( $order_id, '_invoice_number_display', TRUE );

						$invoice = $wpdb->get_row( "SELECT * FROM $wpdb->comments 
													WHERE comment_post_id = $order_id 
													AND comment_content LIKE '% $invoice_number %' 
													AND comment_type = 'order_note'
													LIMIT 1;"
												);
									

						$date  = $invoice->comment_date;
					}

				}

				return $date;
			}

			public static function get_formatted_date( $date, $date_format ) {

				// Make sure the date is formated correctly
				$date_check = DateTime::createFromFormat( get_option( 'date_format' ), $date );

				if( $date_check ) {
					$date = $date_check->format( $date_format );
				}

				if( strtotime( $date ) ) {
					$date = date_i18n( $date_format, strtotime( $date ) );
				}

				return $date;
			}

			/**
			 * Get the order notes for the template
			 */			
			function get_pdf_order_note( $order_id ) {
				global $woocommerce;
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				
				if (!$order_id) return;	
				$order 			= new WC_Order( $order_id );
				// WooCommerce 3.0 compatibility 
        		$customer_note  = is_callable( array( $order, 'get_customer_note' ) ) ? $order->get_customer_note() : $order->customer_note;

				$output = '';
				
				if( $customer_note ) {
					$output = '<h3>' . __('Note:', 'woocommerce-pdf-invoice') . '</h3>' . wpautop( wptexturize( $customer_note ) );
					$output = apply_filters( 'pdf_template_order_notes' , $output, $order_id );
				}
				return $output;
					
			}
			
			/**
			 * Get the order subtotal for the template
			 */
			function get_pdf_order_subtotal( $order_id ) {
				global $woocommerce;
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				
				if (!$order_id) return;	
				$order = new WC_Order( $order_id );
				$output = '';

				$output = 	'<tr>' .
							'<td align="right">' .
							'<strong>' . __('Subtotal', 'woocommerce-pdf-invoice') . '</strong></td>' .
							'<td align="right"><strong>' . $order->get_subtotal_to_display() . '</strong></td>' .
							'</tr>' ;
				$output = apply_filters( 'pdf_template_order_subtotal' , $output, $order_id );
				return $output;
			}
			
			/**
			 * Get the order shipping total for the template
			 */
			function get_pdf_order_shipping( $order_id ) {
				global $woocommerce;
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				
				if (!$order_id) return;	
				$order = new WC_Order( $order_id );
				$output = '';
				
				$output = 	'<tr>' .
							'<td align="right">' .
							'<strong>' . __('Shipping', 'woocommerce-pdf-invoice') . '</strong></td>' .
							'<td align="right"><strong>' . $order->get_shipping_to_display() . '</strong></td>' .
							'</tr>' ;
				
				$output = apply_filters( 'pdf_template_order_shipping' , $output, $order_id );
				return $output;
			}

			/**
			 * Show coupons used
			 */
			function pdf_coupons_used( $order_id ) {
				global $woocommerce;

				if (!$order_id) return;	
				$order = new WC_Order( $order_id );

				$output = '';

				$used_coupons = is_callable( array( $order, 'get_coupon_codes' ) ) ? $order->get_coupon_codes() : $order->get_used_coupons();

				if( $used_coupons ) {
					
					$coupons_count = count( $used_coupons );
					
					$i = 1;
					$coupons_list = '';
					foreach( $used_coupons as $coupon) {
						
						$coupons_list .= $coupon;
						if( $i < $coupons_count )
							$coupons_list .= ', ';
						
						$i++;
					}

					$output .= '<br /><strong>' . __('Coupons used', 'woocommerce-pdf-invoice') . ' (' . $coupons_count . ') :</strong>' . $coupons_list;
				
				} // endif get_used_coupons

				$output = apply_filters( 'pdf_template_order_coupons' , $output, $order_id );

				return $output;

			}
			
			/**
			 * Get the order discount for the template
			 */
			function get_pdf_order_discount( $order_id ) {
				global $woocommerce;
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				
				if (!$order_id) return;	
				$order = new WC_Order( $order_id );

				$order_discount = $order->get_total_discount();

				$output 	= '';
				$negative 	= apply_filters( 'get_pdf_order_discount_negative', '-', $order );
				$coupons  	= apply_filters( 'get_pdf_order_discount_coupons_used', '<strong>' . esc_html__('Discount:', 'woocommerce-pdf-invoice') . '</strong>' . $this->pdf_coupons_used( $order_id ) . '</td>', $order );

				if ( $order_discount > 0 ) {
					$output .=  '<tr>' .
								'<td align="right" valign="top">' .
								$coupons .
								'<td align="right" valign="top"><strong>' . $negative . wc_price( $order_discount ). '</strong></td>' .
								'</tr>' ;
				}
				
				$output = apply_filters( 'pdf_template_order_discount' , $output, $order_id );
				return $output;
			}
			
			/**
			 * Get the tax for the template
			 */
			function get_pdf_order_tax( $order_id ) {
				global $woocommerce;
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				
				if (!$order_id) return;	
				$order = new WC_Order( $order_id );

				// Check WC version - changes for WC 3.0.0
				$pre_wc_30 		= version_compare( WC_VERSION, '3.0', '<' );

				$output = '';

				if ( $order->get_total_tax()>0 ) {

					$tax_items = $order->get_tax_totals();
				
					if ( count( $tax_items ) > 1 ) {

						foreach ( $tax_items as $tax_item ) {
							$tax_item_amount = $pre_wc_30 ? woocommerce_price( $tax_item->amount ) : wc_price( $tax_item->amount );
							$output .=  '<tr>' .
										'<td align="right">' . esc_html( $tax_item->label ) . '</td>' .
										'<td align="right">' . $tax_item_amount . '</td>' .
										'</tr>' ;
						}

						$total_tax = $pre_wc_30 ? woocommerce_price( $order->get_total_tax() ) : wc_price( $order->get_total_tax() );

						$output .=  '<tr>' .
									'<td align="right">' . __('Total Tax', 'woocommerce-pdf-invoice') . '</td>' .
									'<td align="right">' . $total_tax . '</td>' .
									'</tr>' ;

					} else {

						foreach ( $tax_items as $tax_item ) {

							$tax_item_amount = $pre_wc_30 ? woocommerce_price( $tax_item->amount ) : wc_price( $tax_item->amount );
							$output .=  '<tr>' .
										'<td align="right">' . esc_html( $tax_item->label ) . '</td>' .
										'<td align="right">' . $tax_item_amount . '</td>' .
										'</tr>' ;
						}

					}


				}

				$output = apply_filters( 'pdf_template_order_tax' , $output, $order_id );
				return $output;

			}
			
			/**
			 * [get_pdf_order_total description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			function get_pdf_order_total( $order_id ) {
				global $woocommerce;
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

				if (!$order_id) return;	
				$order = new WC_Order( $order_id );

				// Check WC version - changes for WC 3.0.0
				$pre_wc_30 		= version_compare( WC_VERSION, '3.0', '<' );
				$order_total = $pre_wc_30 ? woocommerce_price( $order->order_total ) : wc_price( $order->get_total() );

				$output =  	'<tr>' .
							'<td align="right">' .
							'<strong>' . __('Grand Total', 'woocommerce-pdf-invoice') . '</strong></td>' .
							'<td align="right"><strong>' . $order_total . '</strong></td>' .
							'</tr>' ;
				$output = apply_filters( 'pdf_template_order_total' , $output, $order_id );
				return $output;
			}

			/**
			 * [get_pdf_order_totals description]
			 * New for Version 1.3.0, replaces several functions with one looped function
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			function get_pdf_order_totals( $order_id ) {
				global $woocommerce;

				if (!$order_id) return;	
				$order = new WC_Order( $order_id );

				// Check WC version - changes for WC 3.0.0
				$pre_wc_30 		= version_compare( WC_VERSION, '3.0', '<' );
				$order_currency = $order->get_currency();

				$order_item_totals = $order->get_order_item_totals();
 
				unset( $order_item_totals['payment_method'] );

				$output = '';

				foreach ( $order_item_totals as $order_item_total ) {

					$output .=  '<tr>' .
								'<td align="right">' .
								'<strong>' . $order_item_total['label'] . '</strong></td>' .
								'<td align="right"><strong>' . $order_item_total['value'] . '</strong></td>' .
								'</tr>' ;

				}

				if( $order->get_total_refunded() > 0 ) {

					$output .=  '<tr>' .
								'<td align="right">' .
								'<strong>Amount Refunded:</strong></td>' .
								'<td align="right"><strong>' . wc_price( $order->get_total_refunded(), array( 'currency' => $order_currency ) ) . '</strong></td>' .
								'</tr>' ;
								
				}

				$output = apply_filters( 'pdf_template_order_totals' , $output, $order_id );
				return $output;

			}

			/**
			 * [get_barcode description]
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_barcode( $order_id ) {

				$barcode_output = '';

				$barcode_text 	= get_post_meta( $order_id, '_barcode_text', TRUE );
				$barcode_data 	= get_post_meta( $order_id, '_barcode_image', TRUE );

				$show_barcode 	= apply_filters( 'pdf_template_show_barcode', TRUE );

				if( $show_barcode ) {

					// Get barcode image
					if( isset( $barcode_data ) && $barcode_data != '' ) {

						// Get image data from barcode string
						list( $settings, $string ) = explode( ',', $barcode_data );
						list( $img_type, $method ) = explode( ';', substr( $settings, 5 ) );

						// Get image extensoin
						$img_ext = str_replace( 'image/', '', $img_type );

						// Decode barcode image
						$barcode = base64_decode( $string );

						// Output image and die
						$barcode_image = '<img src="data:image/' . $img_type . ';base64,' . base64_encode( $barcode ) . '"/>';

					}

					// Build ouput for barcode
					$barcode_output = '<div class="barcode">';

					if( isset( $barcode_image ) && $barcode_image != '' ) {
						$barcode_output .= $barcode_image;
					}

					if( isset( $barcode_text ) && $barcode_text != '' ) {
						$barcode_output .= '<br />' . $barcode_text;
					}

					$barcode_output .= '</div>';
				}

				return $barcode_output;
				
			}

			/**
			 * [get_vat_number description]
			 * Support for EU VAT Number Extension
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			public static function get_vat_number( $order_id ) {

				$vat_number = '';

				if ( get_post_meta( $order_id,'VAT Number',TRUE ) ) {
					$vat_number = get_post_meta( $order_id,'VAT Number',TRUE );
				} elseif ( get_post_meta( $order_id,'vat_number',TRUE ) ) {	
					$vat_number = get_post_meta( $order_id,'vat_number',TRUE );
				}

				return $vat_number;
			}

			/**
			 * [get_pdf_template_invoice_number_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_invoice_number_text( $order ) {
				return apply_filters( 'pdf_template_invoice_number_text', esc_html__( 'Invoice No.', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_template_order_number_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_order_number_text( $order ) {
				return apply_filters( 'pdf_template_order_number_text', esc_html__( 'Order No.', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_template_invoice_date_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_invoice_date_text( $order ) {
				return apply_filters( 'pdf_template_invoice_date_text', esc_html__( 'Invoice Date', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_template_order_date_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_order_date_text( $order ) {
				return apply_filters( 'pdf_template_order_date_text', esc_html__( 'Order Date', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_billing_details_heading description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_billing_details_heading( $order ) {
				return apply_filters( 'pdf_template_billing_details_text', esc_html__('Billing Details', 'woocommerce-pdf-invoice'), $order );
			}

			/**
			 * [get_pdf_shipping_details_heading description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_shipping_details_heading( $order ) {
				$return = apply_filters( 'pdf_template_shipping_details_text', esc_html__('Shipping Details', 'woocommerce-pdf-invoice'), $order );
				
				$get_invoice_shipping_address = self::get_invoice_shipping_address( $order );

				if( !isset( $get_invoice_shipping_address ) || $get_invoice_shipping_address == '' ) {
					$return = '';
				}
				return $return;
			}

			/**
			 * [get_template_payment_method_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_template_payment_method_text( $order ) {
				return apply_filters( 'pdf_template_payment_method_text', __( 'Payment Method', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_template_shipping_method_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_shipping_method_text( $order ) {
				$return = apply_filters( 'pdf_template_shipping_method_text', __( 'Shipping Method', 'woocommerce-pdf-invoice' ), $order );
				
				$get_invoice_shipping_method_title = self::get_invoice_shipping_method_title( $order );

				if( !isset( $get_invoice_shipping_method_title ) || $get_invoice_shipping_method_title == '' ) {
					$return = '';
				}
				return $return;
			}

			/**
			 * [get_pdf_order_details_heading description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_order_details_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_order_details_heading', esc_html__('Order Details', 'woocommerce-pdf-invoice'), $order );
			}

		 	/**
		 	 * [get_pdf_qty_heading description]
		 	 * @param  [type] $order [description]
		 	 * @return [type]        [description]
		 	 */
			public static function get_pdf_qty_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_qty_heading', esc_html__( 'Qty', 'woocommerce-pdf-invoice' ), $order );
			}

		 	/**
		 	 * [get_pdf_product_heading description]
		 	 * @param  [type] $order [description]
		 	 * @return [type]        [description]
		 	 */
			public static function get_pdf_product_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_product_heading', esc_html__( 'Product', 'woocommerce-pdf-invoice' ), $order );
			}

		 	/**
		 	 * [get_pdf_priceex_heading description]
		 	 * @param  [type] $order [description]
		 	 * @return [type]        [description]
		 	 */
			public static function get_pdf_priceex_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_priceex_heading', esc_html__( 'Price Ex.', 'woocommerce-pdf-invoice' ), $order );
			}

		 	/**
		 	 * [get_pdf_totalex_heading description]
		 	 * @param  [type] $order [description]
		 	 * @return [type]        [description]
		 	 */
			public static function get_pdf_totalex_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_totalex_heading', esc_html__( 'Total Ex.', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_tax_heading description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_tax_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_tax_heading', esc_html__( 'Tax', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_priceinc_heading description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_priceinc_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_priceinc_heading', esc_html__( 'Price Inc', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_totalinc_heading description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_totalinc_heading( $order ) {
				return apply_filters( 'woocommerce_pdf_invoice_totalinc_heading', esc_html__( 'Total Inc', 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_custom_heading description]
			 * @param  [type] $order [description]
			 * @param  [type] $text  [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_custom_heading( $order, $text ) {
				return apply_filters( 'woocommerce_pdf_invoice_' . strtolower( str_replace( ' ', '_', $text ) ) . '_heading', esc_html__( $text, 'woocommerce-pdf-invoice' ), $order );
			}

			/**
			 * [get_pdf_template_registered_name_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_registered_name_text( $order, $settings ) {
				$return = apply_filters( 'pdf_template_registered_name_text', esc_html__( 'Registered Name ', 'woocommerce-pdf-invoice' ), $order );

				$get_invoice_registeredname = self::get_invoice_registeredname( $order->get_id(), $settings );

				if( !isset( $get_invoice_registeredname ) || $get_invoice_registeredname = '' ) {
					$return = '';
				}

				return $return;
			}

			/**
			 * [get_pdf_template_registered_office_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_registered_office_text( $order, $settings ) {
				$return = apply_filters( 'pdf_template_registered_office_text', esc_html__( 'Registered Office ', 'woocommerce-pdf-invoice' ), $order );

				$get_invoice_registeredaddress = self::get_invoice_registeredaddress( $order->get_id(), $settings );

				if( !isset( $get_invoice_registeredaddress ) || $get_invoice_registeredaddress = '' ) {
					$return = '';
				}

				return $return;
			}

			/**
			 * [get_pdf_template_company_number_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_company_number_text( $order, $settings ) {
				$return = apply_filters( 'pdf_template_company_number_text', __( 'Company Number ', 'woocommerce-pdf-invoice' ), $order );

				$get_invoice_companynumber = self::get_invoice_companynumber( $order->get_id(), $settings );

				if( !isset( $get_invoice_companynumber ) || $get_invoice_companynumber = '' ) {
					$return = '';
				}

				return $return; 
			}

			/**
			 * [get_pdf_template_vat_number_text description]
			 * @param  [type] $order [description]
			 * @return [type]        [description]
			 */
			public static function get_pdf_template_vat_number_text( $order, $settings ) {
				$return = apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number ', 'woocommerce-pdf-invoice' ), $order );

				$get_invoice_taxnumber = self::get_invoice_taxnumber( $order->get_id(), $settings );

				if( !isset( $get_invoice_taxnumber ) || $get_invoice_taxnumber = '' ) {
					$return = '';
				}

				return $return;
			}

			/**
			 * [get_woocommerce_invoice_terms description]
			 * @param  integer $page_id [description]
			 * @return [type]           [description]
			 */
			function get_woocommerce_invoice_terms( $page_id = 0, $order_id = 0 ) {
				global $woocommerce;
				$settings = get_option( 'woocommerce_pdf_invoice_settings' );

				/**
				 * Filter the $page_id for reasons
				 */
				$page_id = apply_filters( 'pdf_invoice_terms_page_id', $page_id, $order_id );
								
				if ( $page_id == 0 ) 
					return;
				
				/** 
				 * Get terms template
				 * 
				 * Put your customized template in 
				 * wp-content/themes/YOUR_THEME/pdf_templates/terms-template.php
				 */
				$termstemplate 	= $this->get_pdf_template( 'terms-template.php', $order_id );
				
				// Buffer
				ob_start();
				
				require( $termstemplate );
	
				// Get contents
				$content = ob_get_clean();

				$id		 = $page_id; 
				$post 	 = get_post( $id );  
				
				$content = str_replace(	'[[TERMSTITLE]]', 						$post->post_title,  													$content );
				$content = str_replace(	'[[TERMS]]', 							$post->post_content,													$content );

				$content = str_replace(	'[[PDFREGISTEREDNAME]]', 				self::get_invoice_registeredname( $order_id, $settings ), 				$content );
				$content = str_replace(	'[[PDFREGISTEREDADDRESS]]', 			self::get_invoice_registeredaddress( $order_id, $settings ),			$content );
				$content = str_replace(	'[[PDFCOMPANYNUMBER]]', 				self::get_invoice_companynumber( $order_id, $settings ), 				$content );
				$content = str_replace(	'[[PDFTAXNUMBER]]', 					self::get_invoice_taxnumber( $order_id, $settings ), 					$content );

				$content = str_replace(	'[[PDFREGISTEREDNAME_SECTION]]', 		self::get_invoice_registeredname_section( $order_id, $settings ), 		$content );
				$content = str_replace(	'[[PDFREGISTEREDADDRESS_SECTION]]', 	self::get_invoice_registeredaddress_section( $order_id, $settings ),	$content );
				$content = str_replace(	'[[PDFCOMPANYNUMBER_SECTION]]', 		self::get_invoice_companynumber_section( $order_id, $settings ), 		$content );
				$content = str_replace(	'[[PDFTAXNUMBER_SECTION]]', 			self::get_invoice_taxnumber_section( $order_id, $settings ), 			$content );
				
				return $content;	
			}

			/** 
			 * Get pdf template
			 * 
			 * Put your customized template in 
			 * wp-content/themes/YOUR_THEME/pdf_templates/template.php
			 *
			 * Windows hosting fixes
			 */
/*			function get_pdf_template( $filename, $order_id = NULL ) {

				// Allow the filename to be modified
				$filename = apply_filters( 'woocommerce_pdf_invoice_filename', $filename, $order_id );

				$plugin_version     = str_replace('/classes/','/templates/',plugin_dir_path(__FILE__) ) . $filename;
				$plugin_version     = str_replace('\classes/','\templates\\',$plugin_version);

                $theme_version_file = get_stylesheet_directory() . '/pdf_templates/' . $filename;

				$pos = strpos( $plugin_version, ":\\" );
				if ( $pos === false ) {

					$pdftemplate 		= file_exists($theme_version_file) ? $theme_version_file : $plugin_version;

				} else {
					$theme_version_file = str_replace('/', '\\', $theme_version_file );
					$plugin_version		= str_replace('/', '\\', $plugin_version );
					$pdftemplate 		= file_exists($theme_version_file) ? $theme_version_file : $plugin_version;
					$pdftemplate		= str_replace('/', '\\', $pdftemplate );
				}

				return $pdftemplate;

			} // get_pdf_template
*/
			/**
			 * Get the tem directory
			 */
/*		 	public static function get_pdf_temp() {

				// Set the temp directory
				$pdftemp = sys_get_temp_dir();

				$upload_dir =  wp_upload_dir();
	            $upload_dir =  $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
	            $upload_dir =  apply_filters( 'woocommerce_pdf_invoice_pdf_upload_dir', $upload_dir );

                if ( file_exists( $upload_dir . '/index.html' ) ) {
    				$pdftemp = $upload_dir;

    				// Windows hosting check
					$pos = strpos( $pdftemp, ":\\" );
					if ( $pos === false ) {

					} else {
    					$pdftemp = str_replace('/', '\\', $pdftemp );
					}

                }

                return $pdftemp;

		 	}
*/
			 /**
			  * Send a test PDF from the PDF Debugging settings
			  */
			public static function send_test_pdf() {
				 
				ob_start();

				include( PDFPLUGINPATH . "templates/pdftest.php" );
					
				$dompdf = new DOMPDF();
				$dompdf->load_html( $messagetext );
				$dompdf->set_paper( 'a4', 'portrait' );
				$dompdf->render();
						
				$attachments = WC_send_pdf::get_pdf_temp() . '/testpdf.pdf';
					
				ob_clean();
				// Write the PDF to the TMP directory		
				file_put_contents( $attachments, $dompdf->output() );
					
				$emailsubject 	= __( 'Test Email with PDF Attachment', 'woocommerce-pdf-invoice' );
				$emailbody 		= __( 'A PDF should be attached to this email to confirm that the PDF is being created and attached correctly', 'woocommerce-pdf-invoice' );
					
				wp_mail( sanitize_email( $_POST['pdfemailtest-emailaddress'] ), $emailsubject , $emailbody , $headers='', $attachments );
				 
			}

        }

    	$GLOBALS['WC_send_pdf'] = new WC_send_pdf();
<?php
/**
 * @package WordPress
 * @subpackage BuddyBoss Media
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BPCP_Pro_WP_User_Export_GDPR' ) ) {

	class BPCP_Pro_WP_User_Export_GDPR {

		/**
		 * Constructor method.
		 */
		function __construct( $args = array() ) {

			add_filter(
				'wp_privacy_personal_data_exporters',
				array( $this, 'register_exporter' ),
				10
			);

			add_filter(
				'wp_privacy_personal_data_erasers',
				array( $this, 'erase_exporter' ),
				10
			);

		}

		function register_exporter( $exporters ) {
			global $bp;
			if ( ! empty( $bp->portfolio_pro ) ) {
				$exporters[ 'bp-portfolio-pro-' . $bp->portfolio_pro->wip_post_type_slug ] = array(
					'exporter_friendly_name' => __( 'BP Portfolio Pro WIP', 'bp-portfolio-pro' ),
					'callback'               => array( $this, 'wips_exporter' ),
				);
				$exporters[ 'bp-portfolio-pro-' . $bp->portfolio_pro->collections_post_type_slug ] = array(
					'exporter_friendly_name' => __( 'BP Portfolio Pro Collections', 'bp-portfolio-pro' ),
					'callback'               => array( $this, 'collections_exporter' ),
				);
			}
			return $exporters;
		}

		function erase_exporter( $erasers ) {
			global $bp;
			if ( ! empty( $bp->portfolio_pro ) ) {
				$erasers['bp-portfolio-pro-' . $bp->portfolio_pro->wip_post_type_slug] = array(
					'eraser_friendly_name' => __( 'BP Portfolio Pro WIP', 'bp-portfolio-pro' ),
					'callback'             => array( $this, 'wips_eraser' ),
				);
				$erasers['bp-portfolio-pro-' . $bp->portfolio_pro->collections_post_type_slug] = array(
					'eraser_friendly_name' => __( 'BP Portfolio Pro Collections', 'bp-portfolio-pro' ),
					'callback'             => array( $this, 'collections_eraser' ),
				);
			}
			return $erasers;
		}

		function wips_exporter( $email_address, $page = 1 ) {
			$per_page = 500; // Limit us to avoid timing out
			$page = (int) $page;

			$export_items = array();

			$user = get_user_by( 'email' , $email_address );
			if ( false === $user ) {
				return array(
					'data' => $export_items,
					'done' => true,
				);
			}

			$wips_details = $this->get_wips( $user, $page, $per_page );
			$total = isset( $wips_details['total'] ) ? $wips_details['total'] : 0;
			$wips = isset( $wips_details['wips'] ) ? $wips_details['wips'] : array();

			if ( $total > 0 ) {
				foreach( $wips as $wip ) {
					$item_id = "bpcp-pro-wip-{$wip->ID}";

					$group_id = 'bpcp-pro-wips';

					$group_label = __( 'BP Pro Portfolio WIPs', 'bp-portfolio-pro' );

					$permalink = get_permalink( $wip->ID );

					// Plugins can add as many items in the item data array as they want
					$data = array(
						array(
							'name'  => __( 'WIP Author', 'bp-portfolio-pro' ),
							'value' => $user->display_name
						),
						array(
							'name'  => __( 'WIP Author Email', 'bp-portfolio-pro' ),
							'value' => $user->user_email
						),
						array(
							'name'  => __( 'WIP Title', 'bp-portfolio-pro' ),
							'value' => $wip->post_title
						),
						array(
							'name'  => __( 'WIP Content', 'bp-portfolio-pro' ),
							'value' => $wip->post_content
						),
						array(
							'name'  => __( 'WIP Date', 'bp-portfolio-pro' ),
							'value' => $wip->post_date
						),
						array(
							'name'  => __( 'WIP URL', 'bp-portfolio-pro' ),
							'value' => $permalink
						),
					);

					$export_items[] = array(
						'group_id'    => $group_id,
						'group_label' => $group_label,
						'item_id'     => $item_id,
						'data'        => $data,
					);
				}
			}

			$offset = ( $page - 1 ) * $per_page;

			// Tell core if we have more comments to work on still
			$done = $total < $offset;
			return array(
				'data' => $export_items,
				'done' => $done,
			);
		}

		function get_wips( $user, $page, $per_page ) {
			global $bp;
			$pp_args   = array(
				'post_type'      => $bp->portfolio_pro->wip_post_type_slug,
				'author'         => $user->ID,
				'posts_per_page' => $per_page,
				'paged'          => $page
			);
			$the_query = new WP_Query( $pp_args );
			if ( $the_query->have_posts() ) {
				return array( 'wips' => $the_query->posts, 'total' => $the_query->post_count );
			}
			return false;
		}

		function collections_exporter( $email_address, $page = 1 ) {
			$per_page = 500; // Limit us to avoid timing out
			$page = (int) $page;

			$export_items = array();

			$user = get_user_by( 'email' , $email_address );
			if ( false === $user ) {
				return array(
					'data' => $export_items,
					'done' => true,
				);
			}

			$collections_details = $this->get_collections( $user, $page, $per_page );
			$total = isset( $collections_details['total'] ) ? $collections_details['total'] : 0;
			$collections = isset( $collections_details['collections'] ) ? $collections_details['collections'] : array();

			if ( $total > 0 ) {
				foreach( $collections as $collection ) {
					$item_id = "bpcp-pro-collection-{$collection->ID}";

					$group_id = 'bpcp-pro-collection';

					$group_label = __( 'BP Pro Portfolio Collections', 'bp-portfolio-pro' );

					$permalink = get_permalink( $collection->ID );

					// Plugins can add as many items in the item data array as they want
					$data = array(
						array(
							'name'  => __( 'Collection Author', 'bp-portfolio-pro' ),
							'value' => $user->display_name
						),
						array(
							'name'  => __( 'Collection Author Email', 'bp-portfolio-pro' ),
							'value' => $user->user_email
						),
						array(
							'name'  => __( 'Collection Title', 'bp-portfolio-pro' ),
							'value' => $collection->post_title
						),
						array(
							'name'  => __( 'Collection Content', 'bp-portfolio-pro' ),
							'value' => $collection->post_content
						),
						array(
							'name'  => __( 'Collection Date', 'bp-portfolio-pro' ),
							'value' => $collection->post_date
						),
						array(
							'name'  => __( 'Collection URL', 'bp-portfolio-pro' ),
							'value' => $permalink
						),
					);

					$export_items[] = array(
						'group_id'    => $group_id,
						'group_label' => $group_label,
						'item_id'     => $item_id,
						'data'        => $data,
					);
				}
			}

			$offset = ( $page - 1 ) * $per_page;

			// Tell core if we have more comments to work on still
			$done = $total < $offset;
			return array(
				'data' => $export_items,
				'done' => $done,
			);
		}

		function get_collections( $user, $page, $per_page ) {
			global $bp;
			$pp_args   = array(
				'post_type'      => $bp->portfolio_pro->collections_post_type_slug,
				'author'         => $user->ID,
				'posts_per_page' => $per_page,
				'paged'          => $page
			);
			$the_query = new WP_Query( $pp_args );
			if ( $the_query->have_posts() ) {
				return array( 'collections' => $the_query->posts, 'total' => $the_query->post_count );
			}
			return false;
		}


		function wips_eraser( $email_address, $page = 1 ) {
			$per_page = 500; // Limit us to avoid timing out
			$page = (int) $page;

			$user = get_user_by( 'email' , $email_address );
			if ( false === $user ) {
				return array(
					'items_removed'  => false,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			}

			$items_removed  = false;
			$items_retained = false;
			$messages    = array();

			$items = $this->get_wips( $user, $page, $per_page );

			if ( ! $items ) {
				return array(
					'items_removed'  => false,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			}

			$total	 = isset( $items['total'] ) ? $items['total'] : 0;
			$paged_wips	 = ! empty( $items['wips'] ) ? $items['wips'] : array();

			if ( $total ) {
				foreach ( (array) $paged_wips as $wip ) {
					$attachments = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => $wip->ID,
					) );

					if ( $attachments ) {
						foreach ( $attachments as $attachment ) {
							wp_delete_post( $attachment->ID, true );
						}
					}
					$this->delete_component_likes( $wip->ID, $user->ID );
					wp_delete_post( $wip->ID, true );
					$items_removed = true;
				}
			}

			$offset = ( $page - 1 ) * $per_page;

			// Tell core if we have more comments to work on still
			$done = $total < $offset;

			return array(
				'items_removed'  => $items_removed,
				'items_retained' => $items_retained,
				'messages'       => $messages,
				'done'           => $done,
			);
		}

		function collections_eraser( $email_address, $page = 1 ) {
			$per_page = 500; // Limit us to avoid timing out
			$page = (int) $page;

			$user = get_user_by( 'email' , $email_address );
			if ( false === $user ) {
				return array(
					'items_removed'  => false,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			}

			$items_removed  = false;
			$items_retained = false;
			$messages    = array();

			$items = $this->get_collections( $user, $page, $per_page );

			if ( ! $items ) {
				return array(
					'items_removed'  => false,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			}

			$total	 = isset( $items['total'] ) ? $items['total'] : 0;
			$paged_collections	 = ! empty( $items['collections'] ) ? $items['collections'] : array();

			if ( $total ) {
				foreach ( (array) $paged_collections as $collection ) {
					$attachments = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => $collection->ID,
					) );

					if ( $attachments ) {
						foreach ( $attachments as $attachment ) {
							wp_delete_post( $attachment->ID, true );
						}
					}
					$this->delete_collection_meta( $collection->ID );
					$this->delete_component_likes( $collection->ID, $user->ID );
					wp_delete_post( $collection->ID, true );
					$items_removed = true;
				}
			}

			$offset = ( $page - 1 ) * $per_page;

			// Tell core if we have more comments to work on still
			$done = $total < $offset;

			return array(
				'items_removed'  => $items_removed,
				'items_retained' => $items_retained,
				'messages'       => $messages,
				'done'           => $done,
			);
		}

		function delete_collection_meta( $collection_id ){
			global $wpdb;
			if ( ! $wpdb->query("DELETE FROM ".$wpdb->prefix."bpcp_collection_meta WHERE collection_id = {$collection_id}") ) {
				return false;
			}
			return true;
		}

		function delete_component_likes( $id, $user_id ){
			global $wpdb;
			if ( ! $wpdb->query("DELETE FROM ".$wpdb->prefix."bpcp_components_like WHERE post_id = {$id} AND user_id = {$user_id}") ) {
				return false;
			}
			return true;
		}

	}

	new BPCP_Pro_WP_User_Export_GDPR();

}
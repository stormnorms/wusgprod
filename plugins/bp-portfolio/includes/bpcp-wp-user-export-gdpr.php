<?php
/**
 * @package WordPress
 * @subpackage BuddyBoss Media
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BPCP_WP_User_Export_GDPR' ) ) {

	class BPCP_WP_User_Export_GDPR {

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
			$exporters['bp-portfolio-bb_project'] = array(
				'exporter_friendly_name' => __( 'BP Portfolio Projects', 'bp-portfolio' ),
				'callback' => array( $this, 'projects_exporter' ),
			);
			return $exporters;
		}

		function erase_exporter( $erasers ) {
			$erasers['bp-portfolio-bb_project'] = array(
				'eraser_friendly_name' => __( 'BP Portfolio Projects', 'bp-portfolio' ),
				'callback'             => array( $this, 'projects_eraser' ),
			);
			return $erasers;
		}

		function projects_exporter( $email_address, $page = 1 ) {
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

			$projects_details = $this->get_projects( $user, $page, $per_page );
			$total = isset( $projects_details['total'] ) ? $projects_details['total'] : 0;
			$projects = isset( $projects_details['projects'] ) ? $projects_details['projects'] : array();

			if ( $total > 0 ) {
				foreach( $projects as $project ) {
					$item_id = "bpcp-project-{$project->ID}";

					$group_id = 'bpcp-projects';

					$group_label = __( 'BP Portfolio Projects', 'bp-portfolio' );

					$permalink = get_permalink( $project->ID );

					// Plugins can add as many items in the item data array as they want
					$data = array(
						array(
							'name'  => __( 'Project Author', 'bp-portfolio' ),
							'value' => $user->display_name
						),
						array(
							'name'  => __( 'Project Author Email', 'bp-portfolio' ),
							'value' => $user->user_email
						),
						array(
							'name'  => __( 'Project Title', 'bp-portfolio' ),
							'value' => $project->post_title
						),
						array(
							'name'  => __( 'Project Content', 'bp-portfolio' ),
							'value' => $project->post_content
						),
						array(
							'name'  => __( 'Project Date', 'bp-portfolio' ),
							'value' => $project->post_date
						),
						array(
							'name'  => __( 'Project URL', 'bp-portfolio' ),
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

		function get_projects( $user, $page, $per_page ) {
			$pp_args   = array(
				'post_type'      => 'bb_project',
				'author'         => $user->ID,
				'posts_per_page' => $per_page,
				'paged'          => $page
			);
			$the_query = new WP_Query( $pp_args );
			if ( $the_query->have_posts() ) {
				return array( 'projects' => $the_query->posts, 'total' => $the_query->post_count );
			}
			return false;
		}


		function projects_eraser( $email_address, $page = 1 ) {
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

			$items = $this->get_projects( $user, $page, $per_page );

			if ( ! $items ) {
				return array(
					'items_removed'  => false,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			}

			$total	 = isset( $items['total'] ) ? $items['total'] : 0;
			$paged_projects	 = ! empty( $items['projects'] ) ? $items['projects'] : array();

			if ( $total ) {
				foreach ( (array) $paged_projects as $project ) {
					$attachments = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => $project->ID,
					) );

					if ( $attachments ) {
						foreach ( $attachments as $attachment ) {
							wp_delete_post( $attachment->ID, true );
						}
					}
					wp_delete_post( $project->ID, true );
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

	}

	new BPCP_WP_User_Export_GDPR();

}
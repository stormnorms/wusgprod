<?php
// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists('PP_Field_Location') ) {
	class PP_Field_Location {

		private $gapikey = 'Paste Your Key Here';
		/*
		private $gapikey is Deprecated.
		For BuddyPress >  Go to wp-admin > Settings > BuddyPress > Options. Under 'Profile Settings', find 'Google Maps API key', enter your key and Save
		For BuddyBoss Platform > Go to wp-admin > Settings > BuddyBoss > Integrations > PhiloPress. Find 'Google Maps API key', enter your key and Save
		*/

		function __construct () {

			$check_for_gapikey = bp_get_option( 'pp_gapikey' );
			if ( $check_for_gapikey != false )
				$this->gapikey = $check_for_gapikey;

			add_action( 'wp_enqueue_scripts',       			array( $this, 'pp_loc_enqueue') );
			add_action( 'admin_enqueue_scripts',    			array( $this, 'pp_loc_enqueue_admin') );
			add_action( 'xprofile_data_after_save',     		array( $this, 'pp_loc_xprofile_data_after_save') );
			add_action( 'xprofile_data_after_delete',   		array( $this, 'pp_loc_xprofile_data_after_delete') );
			add_action( 'xprofile_field_after_save',    		array( $this, 'pp_loc_xprofile_field_after_save') );
			add_action( 'xprofile_field_after_delete',   		array( $this, 'pp_loc_xprofile_field_after_delete'), 99, 2 );
			add_filter( 'bp_xprofile_get_field_types',          array( $this, 'pp_loc_get_field_types'), 10, 1 );
			add_filter( 'xprofile_get_field_data',              array( $this, 'pp_loc_get_field_data'), 10, 2 );
			add_filter( 'bp_get_the_profile_field_value',       array( $this, 'pp_loc_get_field_value'), 10, 3 );
			add_filter( 'xprofile_field_options_before_save',   array( $this, 'pp_loc_field_options_before_save'), 20, 2 );
			add_filter( 'bp_signup_usermeta',       			array( $this, 'pp_loc_signup_usermeta'), 15, 1 );
			add_action( 'bp_core_activated_user',   			array( $this, 'pp_loc_activated_user'), 15, 3 );
			add_action( 'bp_core_signup_user',      			array( $this, 'pp_loc_signup_user'), 15, 5 );
			add_action( 'bp_signup_validate',       			array( $this, 'pp_loc_signup_validate') );
		}
		function pp_loc_enqueue() {
			if ( bp_is_user_profile_edit() || bp_is_register_page() ) {
				$this->pp_loc_scripts_styles();
			} elseif ( bp_is_members_directory() && PP_BPS ) {
				$this->pp_loc_scripts_styles();
			} elseif ( bp_is_members_directory() && PP_BOSS ) {
				$this->pp_loc_scripts_styles();
			}
		}
		function pp_loc_enqueue_admin( $hook ) {
			if ($hook != 'users_page_bp-profile-edit' ) {
				return;
			}
			$this->pp_loc_scripts_styles();
		}
		function pp_loc_scripts_styles() {
			wp_register_script( 'google-places-api',  '//maps.googleapis.com/maps/api/js?key=' . $this->gapikey . '&libraries=places', array( 'jquery' ), false );
			wp_print_scripts( 'google-places-api' );

		}
		function pp_loc_get_field_types( $fields ) {
			$new_fields = array(
				'location'  => 'PP_Field_Type_Location',
			);
			$fields = array_merge($fields, $new_fields);
			return $fields;
		}
		function pp_loc_get_field_data( $value, $field_id ) {
			$field = new BP_XProfile_Field( $field_id );
			if ( $field->type == 'location' ) {
				$value_to_return = strip_tags( $value );
				if ( $value_to_return !== '' ) {
					$value = apply_filters('pp_loc_show_field_data', $value, $field_id);
				} else {
					$value  = $value_to_return;
				}

				if ( $value == 'a:0:{}' ) {

					$value = 'id: ' . $id;
				}
			}
			return $value;
		}
		function pp_loc_get_field_value( $value='', $type='', $id='' ) {
			if ( $type == 'location' ) {
				$value_to_return = strip_tags( $value );
				if ( $value_to_return !== '' ) {
					$value = apply_filters('pp_loc_show_field_value', $value, $type, $id);
				} else {
					$value  = $value_to_return;
				}

				if ( $value == 'a:0:{}' ) {

					$value = 'id: ' . $id;
				}
			}
			return $value;
		}
		function pp_loc_signup_validate() {
			global $bp;
			if ( bp_is_active( 'xprofile' ) ) {
				if ( isset( $_POST['signup_profile_field_ids'] ) && !empty( $_POST['signup_profile_field_ids'] ) ) {
					$profile_field_ids = explode(',', $_POST['signup_profile_field_ids']);
					foreach ($profile_field_ids as $field_id) {
						$field = new BP_XProfile_Field( $field_id );
						if ($field->type == 'location' && $field->is_required) {
							if (isset($_POST['field_' . $field_id]) && empty( $_POST['field_' . $field_id] ) ) {
								$bp->signup->errors['field_' . $field_id] = __( 'This is a required field', 'buddypress' );
							}
						}
					}
				}
			}
		}
		function pp_loc_signup_usermeta( $meta ) {
			if ( isset( $meta['profile_field_ids'] ) && !empty( $meta['profile_field_ids'] ) ) {
				$profile_field_ids = explode(',', $meta['profile_field_ids']);
				foreach ( $profile_field_ids as $field_id ) {
					$field = new BP_XProfile_Field( $field_id );
					if ($field->type == 'location' ) {
						if (isset($_POST['field_' . $field_id]) && ! empty( $_POST['field_' . $field_id] ) ) {
							if ( ! empty( $_POST['pp_'.$field_id.'_geocode'] ) ) {
								$geocode =  sanitize_text_field( $_POST['pp_'.$field_id.'_geocode'] );
								$meta['geocode_' . $field_id] = $geocode;
							}
						}
					}
				}
			}
			return $meta;
		}
		function pp_loc_signup_user( $user_id, $user_login, $user_password, $user_email, $usermeta ) {
			if ( ! is_multisite() ) {
				if ( $user_id ) {
					if ( bp_is_active( 'xprofile' ) ) {
						if ( isset( $_POST['signup_profile_field_ids'] ) && !empty( $_POST['signup_profile_field_ids'] ) ) {
							$profile_field_ids = explode(',', $_POST['signup_profile_field_ids']);
							foreach ($profile_field_ids as $field_id) {
								$field = new BP_XProfile_Field( $field_id );
								if ($field->type == 'location' ) {
									if (isset($_POST['field_' . $field_id]) && ! empty( $_POST['field_' . $field_id] ) ) {
										if ( ! empty( $_POST['pp_'.$field_id.'_geocode'] ) ) {
											// the $user_id var passed by the hook is just a bool, so we need to get the int
											global $wpdb;
											$uid = $wpdb->get_var( "SELECT ID FROM $wpdb->users WHERE user_login = '$user_login'" );
											$geocode =  sanitize_text_field( $_POST['pp_'.$field_id.'_geocode'] );
											update_user_meta( $uid, 'geocode_' . $field_id, $geocode );
										}
									}
								}
							}
						}
					}
				}
			}
		}
		function pp_loc_activated_user( $user_id, $key, $user ) {
			if ( is_multisite() ) {
				if ( isset( $user['meta']['profile_field_ids'] ) ) {
					$profile_field_ids = explode(',', $user['meta']['profile_field_ids'] );
					foreach ( $profile_field_ids as $field_id ) {
						$field = new BP_XProfile_Field( $field_id );
						if ($field->type == 'location' ) {
							if ( isset( $user['meta']['geocode_' . $field_id] ) && ! empty( $user['meta']['geocode_' . $field_id] ) ) {
								update_user_meta( $user_id, 'geocode_' . $field_id, $user['meta']['geocode_' . $field_id] );
							}
						}
					}
				}
			}
		}
		function pp_loc_xprofile_data_after_save( $data ) {

			$field = new BP_XProfile_Field( $data->field_id );
			if ( $field->type == 'location' ) {

				if ( $data->value == 'a:0:{}' ) {

					xprofile_delete_field_data( $data->field_id, $data->user_id );

					delete_user_meta( $data->user_id, 'geocode_' . $data->field_id );

				} elseif ( ! empty( $_POST['pp_'.$data->field_id.'_geocode'] ) ) {

					$geocode =  sanitize_text_field( $_POST['pp_'.$data->field_id.'_geocode'] );

					update_user_meta( $data->user_id, 'geocode_' . $data->field_id, $geocode );
				}
			}
		}
		function pp_loc_xprofile_data_after_delete( $obj ) {
			delete_user_meta( $obj->user_id, 'geocode_' . $obj->field_id );
		}
		function pp_loc_xprofile_field_after_save( $obj ){

			if ( $obj->type == 'location' ) {
				if ( isset( $_POST['location_option'] ) ) { //&& $_POST['location_option'][1] == '1' ) {
					bp_xprofile_update_meta( $obj->id, 'data', 'geocode', $_POST['location_option'][1] );
				}
				//else
				//	bp_xprofile_delete_meta( $obj->id, 'data', 'geocode' );
			}
		}
		function pp_loc_xprofile_field_after_delete( $data, $delete ) {
			global $wpdb;

			$bp  = buddypress();
			$geocode_id = 'geocode_' . $data->id;
			$id = $data->id;

			// delete the geocode
			$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => $geocode_id ), array( '%s' ) );

			// delete from xprofile_meta > object_id
			$sql = $wpdb->prepare( "DELETE FROM {$bp->profile->table_name_meta} WHERE object_id = %d", $id );
			$wpdb->query( $sql );

			// delete from xprofile_data > field_id
			$sql = $wpdb->prepare( "DELETE FROM {$bp->profile->table_name_data} WHERE field_id = %d", $id );
			$wpdb->query( $sql );

		}
		function pp_loc_field_options_before_save( $post_option,  $type ) {
			if ( $type == 'location' )  {
				$post_option = '';
			}
			return $post_option;
		}
	}
}


function pp_loc_initiate(){

	require_once( PP_LOC_DIR . '/inc/class-pp-field-type-location.php' );

	if (class_exists('PP_Field_Location'))  {
		new PP_Field_Location();
	}
}
add_action('bp_init','pp_loc_initiate');

<?php
### Function: Calculate Post Views
add_action( 'wp_head', 'bpcp_pro_process_postviews' );
function bpcp_pro_process_postviews() {
	global $user_ID, $post;
	if( is_int( $post ) ) {
		$post = get_post( $post );
	}
	if( !wp_is_post_revision( $post ) ) {
		if( is_single() || is_page() ) {
			$id = intval( $post->ID );
			$views_options = get_option( 'bpcp_pro_views_options' );
			if ( !$post_views = get_post_meta( $post->ID, 'bpcp_pro_views', true ) ) {
				$post_views = 0;
			}
			$should_count = false;
			switch( intval( $views_options['count'] ) ) {
				case 0:
					$should_count = true;
					break;
				case 1:
					if(empty( $_COOKIE[USER_COOKIE] ) && intval( $user_ID ) === 0) {
						$should_count = true;
					}
					break;
				case 2:
					if( intval( $user_ID ) > 0 ) {
						$should_count = true;
					}
					break;
			}
			if( intval( $views_options['exclude_bots'] ) === 1 ) {
				$bots = array
				(
					  'Google Bot' => 'googlebot'
					, 'Google Bot' => 'google'
					, 'MSN' => 'msnbot'
					, 'Alex' => 'ia_archiver'
					, 'Lycos' => 'lycos'
					, 'Ask Jeeves' => 'jeeves'
					, 'Altavista' => 'scooter'
					, 'AllTheWeb' => 'fast-webcrawler'
					, 'Inktomi' => 'slurp@inktomi'
					, 'Turnitin.com' => 'turnitinbot'
					, 'Technorati' => 'technorati'
					, 'Yahoo' => 'yahoo'
					, 'Findexa' => 'findexa'
					, 'NextLinks' => 'findlinks'
					, 'Gais' => 'gaisbo'
					, 'WiseNut' => 'zyborg'
					, 'WhoisSource' => 'surveybot'
					, 'Bloglines' => 'bloglines'
					, 'BlogSearch' => 'blogsearch'
					, 'PubSub' => 'pubsub'
					, 'Syndic8' => 'syndic8'
					, 'RadioUserland' => 'userland'
					, 'Gigabot' => 'gigabot'
					, 'Become.com' => 'become.com'
					, 'Baidu' => 'baiduspider'
					, 'so.com' => '360spider'
					, 'Sogou' => 'spider'
					, 'soso.com' => 'sosospider'
					, 'Yandex' => 'yandex'
				);
				$useragent = $_SERVER['HTTP_USER_AGENT'];
				foreach ( $bots as $name => $lookfor ) {
					if ( stristr( $useragent, $lookfor ) !== false ) {
						$should_count = false;
						break;
					}
				}
			}
			// Bail out if user view their own Project or WIP
			if ( is_user_logged_in() && $user_ID == $post->post_author ) {
                $should_count = false;
            }
			if( $should_count && ( ( isset( $views_options['use_ajax'] ) && intval( $views_options['use_ajax'] ) === 0 ) || ( !defined( 'WP_CACHE' ) || !WP_CACHE ) ) ) {
				update_post_meta( $id, 'bpcp_pro_views', ( $post_views + 1 ) );
				do_action( 'postviews_increment_views', ( $post_views + 1 ) );
			}
		}
	}
}


### Function: Calculate Post Views With WP_CACHE Enabled
add_action('wp_enqueue_scripts', 'bpcp_pro_wp_postview_cache_count_enqueue');
function bpcp_pro_wp_postview_cache_count_enqueue() {
	global $user_ID, $post;

	if( !defined( 'WP_CACHE' ) || !WP_CACHE )
		return;

	$views_options = get_option( 'bpcp_pro_views_options' );

	if( isset( $views_options['use_ajax'] ) && intval( $views_options['use_ajax'] ) === 0 )
		return;

	if ( !wp_is_post_revision( $post ) && ( is_single() || is_page() ) ) {
		$should_count = false;
		switch( intval( $views_options['count'] ) ) {
			case 0:
				$should_count = true;
				break;
			case 1:
				if ( empty( $_COOKIE[USER_COOKIE] ) && intval( $user_ID ) === 0) {
					$should_count = true;
				}
				break;
			case 2:
				if ( intval( $user_ID ) > 0 ) {
					$should_count = true;
				}
				break;
		}
		if ( $should_count ) {
			wp_enqueue_script( 'wp-postviews-cache', plugins_url( 'postviews-cache.js', __FILE__ ), array( 'jquery' ), '1.68', true );
			wp_localize_script( 'wp-postviews-cache', 'viewsCacheL10n', array( 'admin_ajax_url' => admin_url( 'admin-ajax.php' ), 'post_id' => intval( $post->ID ) ) );
		}
	}
}


### Function: Determine If Post Views Should Be Displayed (By: David Potter)
function bpcp_pro_should_views_be_displayed($views_options = null) {
	if ($views_options == null) {
		$views_options = get_option('bpcp_pro_views_options');
	}
	$display_option = 0;
	if (is_home()) {
		if (array_key_exists('display_home', $views_options)) {
			$display_option = $views_options['display_home'];
		}
	} elseif (is_single()) {
		if (array_key_exists('display_single', $views_options)) {
			$display_option = $views_options['display_single'];
		}
	} elseif (is_page()) {
		if (array_key_exists('display_page', $views_options)) {
			$display_option = $views_options['display_page'];
		}
	} elseif (is_archive()) {
		if (array_key_exists('display_archive', $views_options)) {
			$display_option = $views_options['display_archive'];
		}
	} elseif (is_search()) {
		if (array_key_exists('display_search', $views_options)) {
			$display_option = $views_options['display_search'];
		}
	} else {
		if (array_key_exists('display_other', $views_options)) {
			$display_option = $views_options['display_other'];
		}
	}
	return (($display_option == 0) || (($display_option == 1) && is_user_logged_in()));
}


### Function: Display The Post Views
function bpcp_pro_the_views($display = true, $prefix = '', $postfix = '', $always = false) {
	$post_views = intval( get_post_meta( get_the_ID(), 'bpcp_pro_views', true ) );
	$views_options = get_option('bpcp_pro_views_options');
	if ($always || bpcp_pro_should_views_be_displayed($views_options)) {
		$output = $prefix.str_replace( array( '%VIEW_COUNT%', '%VIEW_COUNT_ROUNDED%' ), array( number_format_i18n( $post_views ), bpcp_pro_postviews_round_number( $post_views) ), stripslashes( $views_options['template'] ) ).$postfix;
		if($display) {
			echo apply_filters('bpcp_pro_the_views', $output);
		} else {
			return apply_filters('bpcp_pro_the_views', $output);
		}
	}
	elseif (!$display) {
		return '';
	}
}

### Function: Display The Post Views by id
function bpcp_pro_the_views_by_id($pid, $display = true, $prefix = '', $postfix = '', $always = false) {
	$post_views = intval( get_post_meta( $pid, 'bpcp_pro_views', true ) );
	$views_options = get_option('bpcp_pro_views_options');
	if ($always || bpcp_pro_should_views_be_displayed($views_options)) {
		$output = $prefix.str_replace( array( '%VIEW_COUNT%', '%VIEW_COUNT_ROUNDED%' ), array( number_format_i18n( $post_views ), bpcp_pro_postviews_round_number( $post_views) ), stripslashes( $views_options['template'] ) ).$postfix;
		if($display) {
			echo apply_filters('bpcp_pro_the_views', $output);
		} else {
			return apply_filters('bpcp_pro_the_views', $output);
		}
	}
	elseif (!$display) {
		return '';
	}
}


### Function: Modify Default WordPress Listing To Make It Sorted By Post Views
function bpcp_pro_views_fields($content) {
	global $wpdb;
	$content .= ", ($wpdb->postmeta.meta_value+0) AS bpcp_pro_views";
	return $content;
}
function bpcp_pro_views_join($content) {
	global $wpdb;
	$content .= " LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID";
	return $content;
}
function bpcp_pro_views_where($content) {
	global $wpdb;
	$content .= " AND $wpdb->postmeta.meta_key = 'bpcp_pro_views'";
	return $content;
}
function bpcp_pro_views_orderby($content) {
	$orderby = trim(addslashes(get_query_var('v_orderby')));
	if(empty($orderby) || ($orderby != 'asc' && $orderby != 'desc')) {
		$orderby = 'desc';
	}
	$content = " bpcp_pro_views $orderby";
	return $content;
}


### Function: Add Views Custom Fields
add_action('publish_post', 'bpcp_pro_add_views_fields');
add_action('publish_page', 'bpcp_pro_add_views_fields');
function bpcp_pro_add_views_fields($post_ID) {
	global $wpdb;
	if(!wp_is_post_revision($post_ID)) {
		add_post_meta($post_ID, 'bpcp_pro_views', 0, true);
	}
}


### Function: Delete Views Custom Fields
add_action('delete_post', 'bpcp_pro_delete_views_fields');
function bpcp_pro_delete_views_fields($post_ID) {
	global $wpdb;
	if(!wp_is_post_revision($post_ID)) {
		delete_post_meta($post_ID, 'bpcp_pro_views');
	}
}


### Function: Views Public Variables
add_filter('query_vars', 'bpcp_pro_views_variables');
function bpcp_pro_views_variables($public_query_vars) {
	$public_query_vars[] = 'v_sortby';
	$public_query_vars[] = 'v_orderby';
	return $public_query_vars;
}


### Function: Sort Views Posts
add_action('pre_get_posts', 'bpcp_pro_views_sorting');
function bpcp_pro_views_sorting($local_wp_query) {
	if($local_wp_query->get('v_sortby') == 'bpcp_pro_views') {
		add_filter('posts_fields', 'bpcp_pro_views_fields');
		add_filter('posts_join', 'bpcp_pro_views_join');
		add_filter('posts_where', 'bpcp_pro_views_where');
		add_filter('posts_orderby', 'bpcp_pro_views_orderby');
	} else {
		remove_filter('posts_fields', 'bpcp_pro_views_fields');
		remove_filter('posts_join', 'bpcp_pro_views_join');
		remove_filter('posts_where', 'bpcp_pro_views_where');
		remove_filter('posts_orderby', 'bpcp_pro_views_orderby');
	}
}


### Function: Increment Post Views
add_action( 'wp_ajax_postviews', 'bpcp_pro_increment_views' );
add_action( 'wp_ajax_nopriv_postviews', 'bpcp_pro_increment_views' );
function bpcp_pro_increment_views() {
	if( empty( $_GET['postviews_id'] ) )
		return;

	if( !defined( 'WP_CACHE' ) || !WP_CACHE )
		return;

	$views_options = get_option( 'bpcp_pro_views_options' );

	if( isset( $views_options['use_ajax'] ) && intval( $views_options['use_ajax'] ) === 0 )
		return;

	$post_id = intval( $_GET['postviews_id'] );
	if( $post_id > 0 ) {
		$post_views = get_post_custom( $post_id );
		$post_views = intval( $post_views['bpcp_pro_views'][0] );
		update_post_meta( $post_id, 'bpcp_pro_views', ( $post_views + 1 ) );
		do_action( 'postviews_increment_views_ajax', ( $post_views + 1 ) );
		echo ( $post_views + 1 );
		exit();
	}
}


### Function: Round Numbers To K (Thousand), M (Million) or B (Billion)
function bpcp_pro_postviews_round_number( $number, $min_value = 1000, $decimal = 1 ) {
	if( $number < $min_value ) {
		return number_format_i18n( $number );
	}
	$alphabets = array( 1000000000 => 'B', 1000000 => 'M', 1000 => 'K' );
	foreach( $alphabets as $key => $value )
		if( $number >= $key ) {
			return round( $number / $key, $decimal ) . '' . $value;
		}
}


### Function: Parse View Options
function bpcp_pro_views_options_parse($key) {
	return !empty($_POST[$key]) ? $_POST[$key] : null;
}
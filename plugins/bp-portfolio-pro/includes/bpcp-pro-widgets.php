<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * All widgets are here
 */
function bpcp_pro_register_widgets() {
    $projects_enable = bp_portfolio()->setting( 'bpcp-projects-enable' );
    $wip_enable = bp_portfolio_pro()->setting( 'bpcp-wip-enable' );
    $collections_enable = bp_portfolio_pro()->setting( 'bpcp-collections-enable' );

    if($projects_enable) { register_widget('BPCP_Pro_Projects_Widget'); }
    if($wip_enable) { register_widget('BPCP_Pro_WIP_Widget'); }
    if($collections_enable) { register_widget('BPCP_Pro_Collections_Widget'); }
}
add_action( 'widgets_init', 'bpcp_pro_register_widgets' );

/**
 * Projects Widget.
 */
class BPCP_Pro_Projects_Widget extends WP_Widget {

    /**
     * Constructor method.
     */
    function __construct() {
        $widget_ops = array(
            'description' => __( 'A dynamic list of newest and popular projects', 'bp-portfolio-pro' ),
            'classname' => 'widget_bpcp_pro_projects_widget buddypress widget',
        );
        parent::__construct( false, $name = _x( '(BP Portfolio) Projects', 'widget name', 'bp-portfolio-pro' ), $widget_ops );
    }

    /**
     * Display the Projects widget.
     *
     * @see WP_Widget::widget() for description of parameters.
     *
     * @param array $args Widget arguments.
     * @param array $instance Widget settings, as saved by the user.
     */
    function widget( $args, $instance ) {

        extract( $args );

        if ( !$instance['project_default'] )
            $instance['project_default'] = 'newest';

        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $before_widget;

        echo $before_title
            . $title
            . $after_title;
        ?>

        <?php
        switch($instance['project_default']){
            case 'newest': $order_by = 'date'; break;
            case 'popular': $order_by = 'like'; break;
            default: $order_by = 'date';
        };

        $paged = bpcp_paged_query_var();
        $meta_query = bpcp_project_meta_args('project_visibility', true);

        if($order_by != 'like'){
            $gp_args = array(
                'post_type' => 'bb_project',
                'meta_query' => array($meta_query),
                'orderby' => $order_by,
                'order' => 'DESC',
                'posts_per_page' => $instance['max_projects'],
                'paged' => $paged
            );
            add_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
            $the_query = new WP_Query($gp_args);
            remove_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
        }else{
            $gp_args = array(
                'post_type' => 'bb_project',
                'meta_query' => array($meta_query),
                'posts_per_page' => $instance['max_projects'],
                'paged' => $paged
            );
            add_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
            $the_query = new WP_Query($gp_args);
            remove_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
        }

        if ($the_query->have_posts()): ?>
            <div class="item-options" id="projects-list-options">
                <a href="javascript:void(0)" id="newest-projects" <?php if ( $instance['project_default'] == 'newest' ) : ?>class="selected"<?php endif; ?>><?php _e( 'Newest', 'bp-portfolio-pro' ) ?></a>
                | <a href="javascript:void(0)" id="popular-projects" <?php if ( $instance['project_default'] == 'popular' ) : ?>class="selected"<?php endif; ?>><?php _e( 'Popular', 'bp-portfolio-pro' ) ?></a>
            </div>

            <ul id="projects-list" class="item-list">
                <?php while ($the_query->have_posts()):
                    $the_query->the_post();
                    $default_img_url = 'http://via.placeholder.com/50x50/CBE5E5/FFFFFF?text=Project';
                    $get_project_thumb = bpcp_featured_image_detail(get_the_ID(), 'popular-thumbnail', $default_img_url);
                    $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
                    ?>
                    <li class="vcard">
                        <div class="item-avatar">
                            <a href="<?php the_permalink();?>" title="<?php the_title();?>"><img class="avatar" src="<?php echo $project_thumb_url;?>" alt="<?php the_title();?>" /></a>
                        </div>

                        <div class="item">
                            <div class="item-title fn"><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></div>
                            <div class="item-meta">
								<span class="activity">
								<?php
                                if ( 'newest' == $instance['project_default'] )
                                    echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago';
                                if ( 'popular' == $instance['project_default'] )
                                    echo strtolower( bpcp_pro_like_count_text() );
                                ?>
								</span>
                            </div>
                        </div>
                    </li>

                <?php endwhile; ?>
            </ul>
            <?php wp_nonce_field( 'bp_core_widget_projects', '_wpnonce-projects', false ); ?>
            <input type="hidden" name="projects_widget_max" id="projects_widget_max" value="<?php echo esc_attr( $instance['max_projects'] ); ?>" />

        <?php else: ?>

            <div class="widget-error">
                <?php _e('Sorry, there were no projects found.', 'bp-portfolio-pro') ?>
            </div>

        <?php endif; ?>

        <?php echo $after_widget; ?>
    <?php
    }

    /**
     * Update the Projects widget options.
     *
     * @param array $new_instance The new instance options.
     * @param array $old_instance The old instance options.
     * @return array $instance The parsed options to be saved.
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] 	    = strip_tags( $new_instance['title'] );
        $instance['max_projects']    = strip_tags( $new_instance['max_projects'] );
        $instance['project_default'] = strip_tags( $new_instance['project_default'] );

        return $instance;
    }

    /**
     * Output the Projects widget options form.
     *
     * @param $instance Settings for this widget.
     */
    function form( $instance ) {
        $defaults = array(
            'title' 	 => __( 'Projects', 'bp-portfolio-pro' ),
            'max_projects' 	 => 5,
            'project_default' => 'newest',
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        $title 		= strip_tags( $instance['title'] );
        $max_projects 	= strip_tags( $instance['max_projects'] );
        $project_default = strip_tags( $instance['project_default'] );
        ?>

        <p><label for="bp-core-widget-title"><?php _e('Title:', 'bp-portfolio-pro'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

        <p><label for="bp-core-widget-projects-max"><?php _e('Max projects to show:', 'bp-portfolio-pro'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_projects' ); ?>" name="<?php echo $this->get_field_name( 'max_projects' ); ?>" type="text" value="<?php echo esc_attr( $max_projects ); ?>" style="width: 30%" /></label></p>

        <p>
            <label for="bp-core-widget-groups-default"><?php _e('Default projects to show:', 'bp-portfolio-pro'); ?>
                <select name="<?php echo $this->get_field_name( 'project_default' ) ?>">
                    <option value="newest" <?php if ( $project_default == 'newest' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Newest', 'bp-portfolio-pro' ) ?></option>
                    <option value="popular"  <?php if ( $project_default == 'popular' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Popular', 'bp-portfolio-pro' ) ?></option>
                </select>
            </label>
        </p>

    <?php
    }
}


/**
 * WIP Widget.
 */
class BPCP_Pro_WIP_Widget extends WP_Widget {

    /**
     * Constructor method.
     */
    function __construct() {
        $widget_ops = array(
            'description' => __( 'A dynamic list of Work in Progress', 'bp-portfolio-pro' ),
            'classname' => 'widget_bpcp_pro_wip_widget buddypress widget',
        );
        parent::__construct( false, $name = _x( '(BP Portfolio) Works in Progress', 'widget name', 'bp-portfolio-pro' ), $widget_ops );
    }

    /**
     * Display the WIP widget.
     *
     * @see WP_Widget::widget() for description of parameters.
     *
     * @param array $args Widget arguments.
     * @param array $instance Widget settings, as saved by the user.
     */
    function widget( $args, $instance ) {

        extract( $args );

        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $before_widget;

        echo $before_title
            . $title
            . $after_title;
        ?>

        <?php
        $paged = bpcp_paged_query_var();
        $meta_query = bpcp_project_meta_args('wip_visibility', true);
        $wip_ids = bpcp_pro_wip_by_latest_revision();

        $gwip_args = array(
            'post_type' => 'bb_wip',
            'post__in' => $wip_ids,
            'meta_query' => array($meta_query),
            'posts_per_page' => $instance['max_wip'],
            'orderby' => 'post__in',
            'paged' => $paged
        );

        $the_query = new WP_Query($gwip_args);

        if ($the_query->have_posts()): ?>
            <ul id="wips-list" class="item-list">
                <?php while ($the_query->have_posts()):
                    $the_query->the_post();
                    $default_img_url = 'http://via.placeholder.com/50x50/CBE5E5/FFFFFF?text=WIP';
                    $current_revision = bpcp_pro_current_revision(get_the_ID());
                    $post_id = !empty($current_revision->ID) ? $current_revision->ID : get_the_ID();
                    $get_wip_thumb = bpcp_pro_latest_attachment($post_id, 'popular-thumbnail');
                    $wip_thumb_url = ( isset($get_wip_thumb['src']) && !empty($get_wip_thumb['src']) ) ? $get_wip_thumb['src'] : $default_img_url;
                    $current_revision_num = bpcp_pro_current_revision_num(get_the_ID());
                    ?>
                    <li class="vcard">
                        <div class="item-avatar">
                            <a href="<?php echo get_permalink($post_id);?>" title="<?php echo get_the_title($post_id);?>"><img class="avatar" src="<?php echo $wip_thumb_url;?>" alt="<?php echo get_the_title($post_id);?>" /></a>
                        </div>

                        <div class="item">
                            <div class="item-title fn"><a href="<?php echo get_permalink($post_id);?>" title="<?php echo get_the_title($post_id);?>"><?php echo get_the_title($post_id);?></a></div>
                            <div class="item-meta">
								<span class="activity">
								<?php echo $current_revision_num == 1 ? $current_revision_num.' revision' : $current_revision_num.' revisions'; ?>
								</span>
                            </div>
                        </div>
                    </li>

                <?php endwhile; ?>
            </ul>

        <?php else: ?>

            <div class="widget-error">
                <?php _e('Sorry, there were no works in progress found.', 'bp-portfolio-pro') ?>
            </div>

        <?php endif; ?>

        <?php echo $after_widget; ?>
    <?php
    }

    /**
     * Update the WIP widget options.
     *
     * @param array $new_instance The new instance options.
     * @param array $old_instance The old instance options.
     * @return array $instance The parsed options to be saved.
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] 	      = strip_tags( $new_instance['title'] );
        $instance['max_wip'] = strip_tags( $new_instance['max_wip'] );
        return $instance;
    }

    /**
     * Output the WIP widget options form.
     *
     * @param $instance Settings for this widget.
     */
    function form( $instance ) {
        $defaults = array(
            'title' 	 => __( 'Works in Progress', 'bp-portfolio-pro' ),
            'max_wip' 	 => 5,
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        $title 		= strip_tags( $instance['title'] );
        $max_wip 	= strip_tags( $instance['max_wip'] );
        ?>

        <p><label for="bp-core-widget-title"><?php _e('Title:', 'bp-portfolio-pro'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

        <p><label for="bp-core-widget-wip-max"><?php _e('Max WIP to show:', 'bp-portfolio-pro'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_wip' ); ?>" name="<?php echo $this->get_field_name( 'max_wip' ); ?>" type="text" value="<?php echo esc_attr( $max_wip ); ?>" style="width: 30%" /></label></p>

    <?php
    }
}


/**
 * Collections Widget.
 */
class BPCP_Pro_Collections_Widget extends WP_Widget {

    /**
     * Constructor method.
     */
    function __construct() {
        $widget_ops = array(
            'description' => __( 'A dynamic list of updated and popular collections', 'bp-portfolio-pro' ),
            'classname' => 'widget_bpcp_pro_collections_widget buddypress widget',
        );
        parent::__construct( false, $name = _x( '(BP Portfolio) Collections', 'widget name', 'bp-portfolio-pro' ), $widget_ops );
    }

    /**
     * Display the Collections widget.
     *
     * @see WP_Widget::widget() for description of parameters.
     *
     * @param array $args Widget arguments.
     * @param array $instance Widget settings, as saved by the user.
     */
    function widget( $args, $instance ) {

        extract( $args );

        if ( !$instance['collection_default'] )
            $instance['collection_default'] = 'updated';

        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $before_widget;

        echo $before_title
            . $title
            . $after_title;
        ?>

        <?php
        switch($instance['collection_default']){
            case 'updated': $order_by = 'date'; break;
            case 'popular': $order_by = 'follower'; break;
            default: $order_by = 'date';
        };

        $meta_query = bpcp_project_meta_args('collection_visibility');
        $paged = bpcp_paged_query_var();
        $collection_ids = array();
        $collection_ids = bpcp_pro_most_followed_collection_ids();

        if($order_by != 'follower'){
            $pp_args = array(
                'post_type' => 'bb_collection',
                'meta_query' => array($meta_query),
                'orderby' => $order_by,
                'order' => 'DESC',
                'posts_per_page' => $instance['max_collections'],
                'paged' => $paged
            );
        }else{
            $pp_args = array(
                'post_type' => 'bb_collection',
                'meta_query' => array($meta_query),
                'post__in' => $collection_ids,
                'posts_per_page' => $instance['max_collections'],
                'orderby' => 'post__in',
                'paged' => $paged
            );
        }

        $the_query = new WP_Query($pp_args);

        if ($the_query->have_posts()): ?>
            <div class="item-options" id="collections-list-options">
                <a href="javascript:void(0)" id="updated-collections" <?php if ( $instance['collection_default'] == 'updated' ) : ?>class="selected"<?php endif; ?>><?php _e( 'Updated', 'bp-portfolio-pro' ) ?></a>
                | <a href="javascript:void(0)" id="popular-collections" <?php if ( $instance['collection_default'] == 'popular' ) : ?>class="selected"<?php endif; ?>><?php _e( 'Popular', 'bp-portfolio-pro' ) ?></a>
            </div>

            <ul id="collections-list" class="item-list">
                <?php while ($the_query->have_posts()):
                    $the_query->the_post();
                    $default_img_url = 'http://via.placeholder.com/50x50/CBE5E5/FFFFFF?text=Collection';
                    $collection_thumb_url = '';
                    $project_ids = get_post_meta(get_the_ID(), 'project_ids', true);
                    if( is_array($project_ids) && !empty($project_ids) ){
                        $project_ids = array_reverse($project_ids);
                    }
                    $project_ids = $project_ids == '' ? array() : $project_ids;
                    if(count($project_ids) > 0){
                        if( isset($project_ids[0]) && !empty($project_ids[0]) ){
                            $collection_thumb_url = bpcp_featured_image_detail($project_ids[0], 'popular-thumbnail', $default_img_url);
                            if(!empty($collection_thumb_url['src'])){
                                $collection_thumb_url =  !empty($collection_thumb_url['src']) ? $collection_thumb_url['src'] : $default_img_url;
                            }
                        }
                    }else{
                        $collection_thumb_url = $default_img_url;
                    }
                    ?>
                    <li class="vcard">

                        <div class="item-avatar">
                            <a href="<?php the_permalink();?>" title="<?php the_title();?>"><img class="avatar" src="<?php echo $collection_thumb_url;?>" alt="<?php the_title();?>" /></a>
                        </div>

                        <div class="item">
                            <div class="item-title fn"><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></div>
                            <div class="item-meta">
								<span class="activity">
								<?php
                                if ( 'updated' == $instance['collection_default'] ){
                                    $updated_time = human_time_diff( get_the_time('U'), current_time('timestamp') );
                                    _e( sprintf('updated %s ago', $updated_time), 'bp-portfolio-pro' );
                                }
                                if ( 'popular' == $instance['collection_default'] ){
                                    $followers = bpcp_pro_collection_follower_check(get_the_ID());
                                    $followers_text = $followers == 1 ? __('follower', 'bp-portfolio-pro') : __('followers', 'bp-portfolio-pro');
                                    echo $followers. ' '. $followers_text;
                                }
                                ?>
								</span>
                            </div>
                        </div>
                    </li>

                <?php endwhile; ?>
            </ul>
            <?php wp_nonce_field( 'bpcp_pro_widget_collections', '_wpnonce-collections', false ); ?>
            <input type="hidden" name="collections_widget_max" id="collections_widget_max" value="<?php echo esc_attr( $instance['max_collections'] ); ?>" />

        <?php else: ?>

            <div class="widget-error">
                <?php _e('Sorry, there were no collections found.', 'bp-portfolio-pro') ?>
            </div>

        <?php endif; ?>

        <?php echo $after_widget; ?>
    <?php
    }

    /**
     * Update the Collections widget options.
     *
     * @param array $new_instance The new instance options.
     * @param array $old_instance The old instance options.
     * @return array $instance The parsed options to be saved.
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] 	    = strip_tags( $new_instance['title'] );
        $instance['max_collections']    = strip_tags( $new_instance['max_collections'] );
        $instance['collection_default'] = strip_tags( $new_instance['collection_default'] );

        return $instance;
    }

    /**
     * Output the Collections widget options form.
     *
     * @param $instance Settings for this widget.
     */
    function form( $instance ) {
        $defaults = array(
            'title' 	 => __( 'Collections', 'bp-portfolio-pro' ),
            'max_collections' 	 => 5,
            'collections_default' => 'updated',
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        $title 		= strip_tags( $instance['title'] );
        $max_collections 	= strip_tags( $instance['max_collections'] );
        $collection_default = strip_tags( $instance['collection_default'] );
        ?>

        <p><label for="bp-core-widget-title"><?php _e('Title:', 'bp-portfolio-pro'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

        <p><label for="bp-core-widget-collections-max"><?php _e('Max collections to show:', 'bp-portfolio-pro'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_collections' ); ?>" name="<?php echo $this->get_field_name( 'max_collections' ); ?>" type="text" value="<?php echo esc_attr( $max_collections ); ?>" style="width: 30%" /></label></p>

        <p>
            <label for="bp-core-widget-groups-default"><?php _e('Default collections to show:', 'bp-portfolio-pro'); ?>
                <select name="<?php echo $this->get_field_name( 'collection_default' ) ?>">
                    <option value="updated" <?php if ( $collection_default == 'updated' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Updated', 'bp-portfolio-pro' ) ?></option>
                    <option value="popular"  <?php if ( $collection_default == 'popular' ) : ?>selected="selected"<?php endif; ?>><?php _e( 'Popular', 'bp-portfolio-pro' ) ?></option>
                </select>
            </label>
        </p>

    <?php
    }
}


/**
 * AJAX request handler for Projects widgets.
 */
function bpcp_pro_ajax_widget_projects() {

    check_ajax_referer( 'bp_core_widget_projects' );

    switch($_POST['filter']){
        case 'newest-projects': $order_by = 'date'; break;
        case 'popular-projects': $order_by = 'like'; break;
        default: $order_by = 'like';
    };
    $paged = bpcp_paged_query_var();
    $meta_query = bpcp_project_meta_args('project_visibility', true);
    if($order_by != 'like'){
        $gp_args = array(
            'post_type' => 'bb_project',
            'meta_query' => array($meta_query),
            'orderby' => $order_by,
            'order' => 'DESC',
            'posts_per_page' => $_POST['max-projects'],
            'paged' => $paged
        );
        add_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
        $the_query = new WP_Query($gp_args);
        remove_filter( 'posts_where', 'bpcp_project_visibility_filter', 10, 2 );
    }else{
        $gp_args = array(
            'post_type' => 'bb_project',
            'meta_query' => array($meta_query),
            'posts_per_page' => $_POST['max-projects'],
            'paged' => $paged
        );
        add_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
        $the_query = new WP_Query($gp_args);
        remove_filter( 'posts_clauses', 'projects_filter_by_user_choice', 10, 2 );
    }

    if ($the_query->have_posts()): ?>
        <?php echo '0[[SPLIT]]'; // return valid result. TODO: remove this. ?>
        <?php while ($the_query->have_posts()):
            $the_query->the_post();
            $default_img_url = 'http://via.placeholder.com/50x50/CBE5E5/FFFFFF?text=Project';
            $get_project_thumb = bpcp_featured_image_detail(get_the_ID(), 'popular-thumbnail', $default_img_url);
            $project_thumb_url = ( isset($get_project_thumb['src']) && !empty($get_project_thumb['src']) ) ? $get_project_thumb['src'] : '';
            ?>
            <li class="vcard">
                <div class="item-avatar">
                    <a href="<?php the_permalink();?>" title="<?php the_title();?>"><img class="avatar" src="<?php echo $project_thumb_url;?>" alt="<?php the_title();?>" /></a>
                </div>

                <div class="item">
                    <div class="item-title fn"><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></div>
                    <?php if ( 'date' == $order_by ) : ?>
                        <div class="item-meta"><span class="activity"><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></span></div>
                    <?php elseif ( 'like' == $order_by ) : ?>
                        <div class="item-meta"><span class="activity"><?php echo strtolower( bpcp_pro_like_count_text() ); ?></span></div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>

    <?php else: ?>
        <?php echo "-1[[SPLIT]]<li>"; ?>
        <?php _e( 'There were no projects found, please try another filter.', 'bp-portfolio-pro' ) ?>
        <?php echo "</li>"; ?>
    <?php endif;
}

/**
 * AJAX request handler for collections widgets.
 */
function bpcp_pro_ajax_widget_collections() {

    check_ajax_referer( 'bpcp_pro_widget_collections' );

    switch($_POST['filter']){
        case 'updated-collections': $order_by = 'date'; break;
        case 'popular-collections': $order_by = 'follower'; break;
        default: $order_by = 'date';
    };

    $meta_query = bpcp_project_meta_args('collection_visibility');
    $paged = bpcp_paged_query_var();
    $collection_ids = array();
    $collection_ids = bpcp_pro_most_followed_collection_ids();

    if($order_by != 'follower'){
        $pp_args = array(
            'post_type' => 'bb_collection',
            'meta_query' => array($meta_query),
            'orderby' => $order_by,
            'order' => 'DESC',
            'posts_per_page' => $_POST['max-collections'],
            'paged' => $paged
        );
    }else{
        $pp_args = array(
            'post_type' => 'bb_collection',
            'meta_query' => array($meta_query),
            'post__in' => $collection_ids,
            'posts_per_page' => $_POST['max-collections'],
            'orderby' => 'post__in',
            'paged' => $paged
        );
    }

    $the_query = new WP_Query($pp_args);

    if ($the_query->have_posts()): ?>
        <?php echo '0[[SPLIT]]'; // return valid result. TODO: remove this. ?>
        <?php while ($the_query->have_posts()):
            $the_query->the_post();
            $default_img_url = 'http://via.placeholder.com/50x50/CBE5E5/FFFFFF?text=Collection';
            $collection_thumb_url = '';
            $project_ids = get_post_meta(get_the_ID(), 'project_ids', true);
            if( is_array($project_ids) && !empty($project_ids) ){
                $project_ids = array_reverse($project_ids);
            }
            $project_ids = $project_ids == '' ? array() : $project_ids;
            if(count($project_ids) > 0){
                if( isset($project_ids[0]) && !empty($project_ids[0]) ){
                    $collection_thumb_url = bpcp_featured_image_detail($project_ids[0], 'popular-thumbnail', $default_img_url);
                    if(!empty($collection_thumb_url['src'])){
                        $collection_thumb_url =  !empty($collection_thumb_url['src']) ? $collection_thumb_url['src'] : $default_img_url;
                    }
                }
            }else{
                $collection_thumb_url = $default_img_url;
            }
            ?>
            <li class="vcard">
                <div class="item-avatar">
                    <a href="<?php the_permalink();?>" title="<?php the_title();?>"><img class="avatar" src="<?php echo $collection_thumb_url;?>" alt="<?php the_title();?>" /></a>
                </div>

                <div class="item">
                    <div class="item-title fn"><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></div>
                    <?php if ( 'date' == $order_by ) : ?>
                        <div class="item-meta">
                            <span class="activity">
                                <?php
                                $updated_time = human_time_diff( get_the_time('U'), current_time('timestamp') );
                                _e( sprintf('updated %s ago', $updated_time), 'bp-portfolio-pro' );
                                ?>
                            </span>
                        </div>
                    <?php elseif ( 'follower' == $order_by ) : ?>
                        <div class="item-meta">
                            <span class="activity">
                                <?php
                                $followers = bpcp_pro_collection_follower_check(get_the_ID());
                                $followers_text = $followers == 1 ? __('follower', 'bp-portfolio-pro') : __('followers', 'bp-portfolio-pro');
                                echo $followers. ' '. $followers_text;
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>

    <?php else: ?>
        <?php echo "-1[[SPLIT]]<li>"; ?>
        <?php _e( 'There were no collections found, please try another filter.', 'bp-portfolio-pro' ) ?>
        <?php echo "</li>"; ?>
    <?php endif;
}

add_action( 'wp_ajax_widget_bpcp_pro_projects', 'bpcp_pro_ajax_widget_projects' );
add_action( 'wp_ajax_nopriv_widget_bpcp_pro_projects', 'bpcp_pro_ajax_widget_projects' );

add_action( 'wp_ajax_widget_bpcp_pro_collections', 'bpcp_pro_ajax_widget_collections' );
add_action( 'wp_ajax_nopriv_widget_bpcp_pro_collections', 'bpcp_pro_ajax_widget_collections' );

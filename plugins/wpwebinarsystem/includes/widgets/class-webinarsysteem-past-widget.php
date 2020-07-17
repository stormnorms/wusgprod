<?php

class WebinarSysteemPastWebinars extends WP_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'WebinarSysteemPostWebinars',
            'description' => 'Show the Past Webinars.',
        );
        parent::__construct('WebinarSysteemPastWebinars', 'WP WebinarSystem - Past webinars', $widget_ops);
    }

    public function widget($args, $instance) {
        ?>
        <form method="post">
            <div class="widget">
                <h2 class="widget-title"><?php echo (empty($instance['wswebinar_past_widget_title']) ? 'Past Webinars' : $instance['wswebinar_past_widget_title']) ?></h2>
                <?php
                $show_count = (empty($instance['past_widget_post_count']) | ($instance['past_widget_post_count'] == '0') ? 100 : $instance['past_widget_post_count']);
                $date_format = get_option('date_format');
                $time_format = get_option('time_format');
                $format = $date_format.' '.$time_format;
                $count = 0;
                $args = array(
                    'post_type' => 'wswebinars',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'ignore_sticky_posts' => 1
                );
                $query_post = new WP_Query($args);
                $webinar_systeem = new WebinarSysteem();
                if ($query_post->have_posts()) {
                    while ($query_post->have_posts()) {
                        if ($show_count > $count) {
                            $query_post->the_post();
                            
                            $webinar_time = $webinar_systeem->getWebinarTime(get_the_ID());
                            $webinar_timezone = $webinar_systeem->getWebinarTimezone(get_the_ID());
                            $isRecurring = $webinar_systeem->isRecurring(get_the_ID());
                            $isRightnow = $webinar_systeem->isRightnow(get_the_ID());
                            $cur_time = $webinar_systeem->populateDateTime(get_the_ID());
                            $closed_webinar = get_post_meta(get_the_ID(),'_wswebinar_gener_webinar_status',true) == 'clo';
                            
                            if($cur_time > $webinar_time & !$isRightnow & !$isRecurring | $closed_webinar){
                            ?>
                            <p><?php the_title(); echo ' '.($webinar_time != 0 ? date($format,$webinar_time) : '') . ' (' . $webinar_timezone . ')'; ?> </p> 
                            <?php
                            $count++;
                            }
                        } else {
                            break;
                        }
                        wp_reset_query();
                    }
                }
                if($count == 0){
                    echo ('<p>No past webinars.</p>');
                }
                ?>
            </div>
        </form>
        <?php
    }

    public function form($instance) {
        ?>
        <div id="wswebinar_widget_past_admin">
            <p>
                <label for="past_widget_title">Widget Title:</label> 
                <input class="widefat" id="past_widget_title" name="<?php echo $this->get_field_name('wswebinar_past_widget_title'); ?>" type="text" value="<?php echo (empty($instance['wswebinar_past_widget_title']) ? 'Past Webinars' : $instance['wswebinar_past_widget_title']); ?>">
            </p>

            <p>
                <label for="past_widget_post_count">Webinar Limit:</label> 
                <input class="widefat" id="upcomin_widget_title" name="<?php echo $this->get_field_name('past_widget_post_count'); ?>" type="number" max="100" min="0" value="<?php echo (empty($instance['past_widget_post_count']) ? '0' : $instance['past_widget_post_count']); ?>">
            </p>

        </div>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['wswebinar_past_widget_title'] = $new_instance['wswebinar_past_widget_title'];
        $instance['past_widget_post_count'] = $new_instance['past_widget_post_count'];
        return $instance;
    }

}

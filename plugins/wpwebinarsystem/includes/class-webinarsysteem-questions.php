<?php

class WebinarSysteemQuestions extends WebinarSysteem {

    function __construct() {
        $this->setAttributes();
    }

    /*
     * 
     * Questions view page.
     * 
     */

    public function showPage() {
        $webs = $this->getWebinarList();
        $webinar_id = @$webs[0]->ID;
        if (!empty($_GET['webinar_id']))
            $webinar_id = (int) $_GET['webinar_id'];
        ?>
        <div class="wrap wswebinarwrap">
            <div class="wswebinarLogo">
                <img src="<?php echo plugins_url('images/WebinarSysteem-logo.png', __FILE__); ?>" />
            </div>
			<div style="clear: both"></div>
                <h2><?php _e('WP WebinarSystem Questions', WebinarSysteem::$lang_slug); ?></h2>
                <p><?php _e('Select webinar to view questions for active webinars', WebinarSysteem::$lang_slug); ?></p>
            <div class="tablenav top">
                <div class="alignleft">
                    <form method="get">
                        <input type="hidden" name="post_type" value="wswebinars">
                        <input type="hidden" name="page" value="wswbn-questions">
                        <select name="webinar_id">
                            <?php
                            if (!empty($webinar_id) && $webinar_id > 0) {
                                foreach ($webs as $web):
                                    echo '<option value="' . $web->ID . '"' . ($webinar_id == $web->ID ? "selected" : "") . '>' . $web->post_title . '</option>';
                                endforeach;
                            }
                            ?>
                        </select>
                        <input class="button" type="submit" value="Select">
                    </form>
                    <?php //echo var_dump();  ?>
                </div>
            </div>
                <div class="chatlog_control">
                    <button data-clear="all" class="quelog_dashboard green">Clear Questions</button>
                    <button data-clear="selected" class="quelog_dashboard blue">Delete Question Message</button>
                    <img class="chatlog_loader" src="<?php echo plugins_url('includes/images/loading_small.GIF', WSWEB_FILE); ?>">
                    <div class="webinar_clear_fix"></div>
                </div>
            <table class="wp-list-table widefat fixed posts">
                <thead>
                    <?php
                    echo $header__s = '<tr><th class="questions-row selecbox-th"><input type="checkbox" class="select_chats" data-select="all"></th><th class="column-title wsquestionid">#</th><th class="column-title wsquestionname">'.__('Name',  WebinarSysteem::$lang_slug).'</th><th class="column-title">'.__('Question',  WebinarSysteem::$lang_slug).'</th><th class="column-title wsquestiontime">'.__('Time',  WebinarSysteem::$lang_slug).'</th></tr>';
                    ?>
                </thead>
                <tfoot>
                    <?php echo $header__s; ?>
                </tfoot>
                <tbody id="loadQuestions">
                    <?php
                    if (!empty($webinar_id) && $webinar_id > 0) {
                        $res = $this->getQuestionsFromDb($webinar_id);
                        echo $res['string'];
                        $loadedQues = $res['last_id'];
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" id="loadedQues" value="<?php echo $loadedQues; ?>">
            <input type="hidden" id="webinar_id" value="<?php echo $webinar_id; ?>">
        </div>
        <script>
            REFRESH_QUESTIONS = false;
            jQuery(document).ready(function() {
                setInterval(function() {
                    checkBottonsClicked();
                        if(!REFRESH_QUESTIONS){
                            var datas = {action: 'retrieveQuestions', webinar_id: jQuery('#webinar_id').val(), last: jQuery('#loadedQues').val()};
                            jQuery.ajax({type: 'POST', data: datas, url: wpws_ajaxurl, dataType: 'json'
                            }).done(function(data) {
                                if (data.status) {
                                    jQuery('#loadQuestions').html(" ");
                                    jQuery('#loadQuestions').prepend(jQuery('' + data.text));
                                    jQuery('#loadedQues').val(data.id);
                                }
                            });
                        }
                }, 5000);
            });
            function checkBottonsClicked(){
                var checked = false;
                jQuery('.questions-row').each(function (){
                    checked = jQuery(this).prop('checked');
                    if(checked){
                        REFRESH_QUESTIONS = true;
                    }
                });
                return REFRESH_QUESTIONS;
            }
            jQuery(document).on('click','.questions-row',function (){
                REFRESH_QUESTIONS = jQuery(this).prop('checked');
            });
            jQuery(document).on('click','.quelog_dashboard',function (){
                var que_ids = [];
                var att_val = jQuery(this).attr('data-clear');
                var webinar_id = jQuery('[name="webinar_id"]').val();
                if(confirm("<?php echo _e("Are you sure you want to clear selected questions?",WebinarSysteem::$lang_slug); ?>")){
                    jQuery('.chatlog_loader').css('display','inline');
                    jQuery('.questions-row').each(function (){
                        var checked = jQuery(this).prop('checked');
                        if(checked){
                            que_ids.push(jQuery(this).attr("data-queid"));
                        }
                    });
                    var datas = {action: 'deleteQuestions', webinar_id: webinar_id, question_ids: que_ids, delete_type : att_val};
                    jQuery.ajax({type: 'POST', data: datas, url: wpws_ajaxurl, dataType: 'json'
                    }).done(function(data) {
                        if(data.status){
                            if(data.type == 'selected'){
                                jQuery(data.ids).each(function (selected_id){
                                    jQuery('tr[data-queid="'+data.ids[selected_id]+'"]').remove();
                                });
                            }else{
                                jQuery("#loadQuestions").html("");
                            }
                            jQuery('.chatlog_loader').css('display','none');
                            uncheckAll();
                        }
                    });
                }
            });
            function uncheckAll(){
                jQuery('.select_chats').each(function (){
                    jQuery(this).prop('checked',false);
                });
            }
            jQuery(document).on('click','.select_chats',function (){
                jQuery('.select_chats[data-select="one"]').each(function (){
                    var chat_id = jQuery(this).attr('data-queid');
                    if(jQuery(this).prop('checked')){
                        jQuery('tr[data-queid="' + chat_id + '"]').addClass('selected_table_row'); 
                    }else{
                        jQuery('tr[data-queid="' + chat_id + '"]').removeClass('selected_table_row'); 
                    }
                });
            });
        </script>
        <?php
    }

    /*
     * 
     * Handles the Ajax request of the questions page.
     * 
     */

    public function retrieveQuestions() {
        $webinar_id = (int) $_POST['webinar_id'];
        $last_id = $_POST['last'];
        $ret = $this->getQuestionsFromDb($webinar_id, $last_id);
        $status = false;
        if (count($ret['num_of_rows']) > 0) {
            $status = true;
        }
        echo json_encode(array('status' => $status, 'text' => $ret['string'], 'id' => $ret['last_id']));
        die();
    }

    /*
     * 
     * Create the <tr> elements for the questions page.
     * 
     */

    public function getQuestionsFromDb($webinar_id, $last_id = NULL) {
        global $wpdb;
        $questions_bucket = array();
        
        $table = $wpdb->prefix . $this->db_tablename_questions;
        $query = "SELECT * FROM $table WHERE webinar_id = $webinar_id ORDER BY id DESC";
        $savedQues = $wpdb->get_results($query);
        $ret = '';
        //$ret.= '<span>';
        
        $chat_table = $wpdb->prefix . $this->db_tablename_chats;
        $chat_query = "SELECT * FROM $chat_table WHERE webinar_id=$webinar_id AND private=1";
        $chat_ques  = $wpdb->get_results($chat_query);
        
        foreach ($savedQues as $que):
            array_push($questions_bucket, array(
                'que_id' => $que->id,
                'que_at_email' => $que->email,
                'que_at_name' => $que->name,
                'que_text' => $que->question,
                'que_datetime' => strtotime($que->time),
                'que_box' => TRUE
            ));
        endforeach;
        
        foreach ($chat_ques as $que) {
            $attendee = WebinarSysteemAttendees::getAttendeeByID($que->attendee_id);
            array_push($questions_bucket, array(
                'que_id' => $que->id,
                'que_at_email' => $attendee->email,
                'que_at_name' => $attendee->name,
                'que_text' => $que->content,
                'que_datetime' => strtotime($que->timestamp),
                'que_box' => FALSE
            ));
        }
        
        $times_arr = array();
        foreach ($questions_bucket as $row) {
            array_push($times_arr, $row['que_datetime']);
        }
        array_multisort($times_arr, SORT_ASC, $questions_bucket);
        
        foreach ($questions_bucket as $question) {
            $ret.= '<tr data-queid="'.$question['que_id'].'">';
            $ret.= '<td><input data-queid="'.$question['que_id'].'" type="checkbox" class="questions-row select_chats" data-select="one"> </td>';
            $ret.= "<td class='wsquestionid'>".$question['que_id']."</td>";
            $ret.= "<td class='wsquestionname'><a href='mailto:".$question['que_at_email']."' target='_blank'>".$question['que_at_name']."</a></td>";
            $ret.= "<td class='wsquestion'>".$question['que_text']."</td>";
            $ret.= "<td class='wsquestiontime'>" . date("Y/m/d H:i A", $question['que_datetime']) . "</td>";
            $ret.= '</tr>';
        }
        
        $lastid = 0;
        if (!empty($savedQues[0]->id)) {
            $lastid = $savedQues[0]->id;
        } elseif (!empty($last_id)) {
            $lastid = $last_id;
        }
        //$ret.= '</span>';
        return array('string' => $ret, 'last_id' => $lastid, 'num_of_rows' => count($savedQues));
    }
    
    public function getChatsFromDB($webinar_id) {
        global $wpdb;
        $table = $wpdb->prefix . $this->db_tablename_chats;
        $query = "SELECT * FROM $table WHERE webinar_id = $webinar_id AND private='0' ORDER BY id ASC";
        $chats = $wpdb->get_results($query);
        $webinarsys_attendees = new WebinarSysteemAttendees();
        $big_bag = array();
        foreach ($chats as $chat) {
            array_push($big_bag, array(
                'chat_id' => $chat->id,
                'admin' => $chat->admin,
                'private' => $chat->private,
                'attendee_id' => $chat->attendee_id,
                'attendee' => $webinarsys_attendees->getAttendeeByID($chat->attendee_id),
                'chat_content' => $chat->content,
                'chat_timestp' => $chat->timestamp
            ));   
        }
        return array('chats' => $big_bag, 'chats_count' => count($big_bag));
    }

    public function getWebinarList() {
        $args = array(
            'orderby' => 'post_date',
            'order' => 'DESC',
            //'meta_key'         => '',
            //'meta_value'       => '',
            'post_type' => 'wswebinars',
            'post_status' => 'publish',
            'suppress_filters' => true,
            'posts_per_page' => -1);

        $webs = get_posts($args);
        return $webs;
    }

}

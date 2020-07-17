<?php

/*
 * Webinar chatlogs page.
 */

class WebinarSysteemChatlogs extends WebinarSysteem {
    public function __construct() {
        $this->setAttributes();
    }
    
    public function showPage(){
        $wbns_questions = new WebinarSysteemQuestions();
        $webs = $wbns_questions->getWebinarList();
        $webinar_id = @$webs[0]->ID;
        if (!empty($_GET['webinar_id']))
            $webinar_id = (int) $_GET['webinar_id'];
        $chats = $wbns_questions->getChatsFromDB($webinar_id);
        ?>
        
        <div class="wrap wswebinarwrap">
            <div class="wswebinarLogo">
                <img src="<?php echo plugins_url('images/WebinarSysteem-logo.png', __FILE__); ?>" />
            </div>
			<div style="clear: both"></div>
                <h2><?php _e('WP WebinarSystem Chatlogs', WebinarSysteem::$lang_slug); ?></h2>
                <p><?php _e('Select webinar to view chatlogs for active webinars', WebinarSysteem::$lang_slug); ?></p>
                
                <div class="tablenav top">
                <div class="alignleft">
                    <form method="get">
                        <input type="hidden" name="post_type" value="wswebinars">
                        <input type="hidden" name="page" value="wswbn-chatlogs">
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
                        <?php if ($chats['chats_count'] > 0): ?>
                            <div class="chatlog_control">
                                <button data-clear="all" class="chatlog_dashboard green">Clear Chat</button>
                                <button data-clear="selected" class="chatlog_dashboard blue">Delete Message</button>
                                <img class="chatlog_loader" src="<?php echo plugins_url('includes/images/loading_small.GIF', WSWEB_FILE); ?>">
                                <div class="webinar_clear_fix"></div>
                            </div>
                            <div class="webinar_chatlog">
                                <table class="wp-list-table widefat fixed posts">
                                    <thead>
                                        <tr>
                                            <th class="selecbox-th"><input type="checkbox" class="select_chats" data-select="all"></th>
                                            <th class="column-title chatid-col">#</th>
                                            <th class="column-title chatname-col">Name</th>
                                            <th class="column-title">Message</th>
                                            <th class="column-title chattime-col">Time</th>
                                        </tr>                
                                    </thead>
                                    <tbody id="wswebinar_chatlog">
                                        <?php foreach ($chats['chats'] as $chat): ?>
                                            <tr data-chatid="<?php echo $chat['chat_id']; ?>" >
                                                <td>
                                                    <input type="checkbox" class="select_chats" data-select="one" data-chatid="<?php echo $chat['chat_id']; ?>">
                                                </td>
                                                <td><?php echo $chat['chat_id']; ?></td>
                                                <td><?php echo "<a class='chtlog_mailto' href='mailto:".$chat['attendee']->email."' target='_blank'>".$chat['attendee']->name."</a>"; ?></td>
                                                <td><?php echo make_clickable($chat['chat_content']); ?></td>
                                                <td><?php echo $chat['chat_timestp']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>

                            <div class="ws_chatlog_empty">
                                <h1><?php _e('Chatlog is empty for this webinar', WebinarSysteem::$lang_slug); ?></h1>
                            </div>

                        <?php endif; ?>
                </div>
<script>
    var CLEAR_ALL_CHAT_CONF = '<?php _e('Clear all the chat messages for this webinar?',  WebinarSysteem::$lang_slug); ?>';
    var CLEAR_SEL_CHAT_CONF = '<?php _e('Are you sure to delete selected chat messages?',  WebinarSysteem::$lang_slug); ?>';
</script>
                <?php
            }
}

/* global wswebinarsysteemMJP */

PROCESSING_REQUEST = false;
// Reduce requests.
HOLD_SCROLLING = false;
POLLING_COUNT = 0;
USER_REGISTER_CODE = 0;
REG_SECRET_COOKIE_NAME = '_wswebinar_regrandom_key';

jQuery(document).ready(function () {
    if (typeof theWebinarId !== 'undefined') {
        USER_REGISTER_CODE = getCookie(REG_SECRET_COOKIE_NAME);
        transferLivepData();
    }
});

var NORMAL_FETCH_INTERVAL = 5000;
var LONGER_FETCH_INTERVAL = 60000;
var fetchRequestInterval = 0;
var fetchIntervalId = 0;

setFetchIntervalId(NORMAL_FETCH_INTERVAL);

function setFetchIntervalId(requestInterval) {

    USER_REGISTER_CODE = getCookie(REG_SECRET_COOKIE_NAME);

    // If the request timer interval is not changed then do nothing
    if (requestInterval == fetchRequestInterval) {
        return;
    }

    if (fetchIntervalId !== 'undefined') {
        clearInterval(fetchIntervalId);
    }

    // Set the global var to the new value
    fetchRequestInterval = requestInterval;

    fetchIntervalId = setInterval(function () {
        if (typeof theWebinarId !== 'undefined') {
            transferLivepData();
        }
    }, fetchRequestInterval);
}

var meow = 0;
function transferLivepData() {
    var data_ob = {
        action: 'transferLivepData',
        webinar_id: theWebinarId,
        webinar_st: theWebinarstatus,
        page_state: pageCategory,
        user_code: USER_REGISTER_CODE
    };
    jQuery.ajax({
        url: wpwebinarsystem.ajaxurl,
        data: data_ob,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            if (!PROCESSING_REQUEST) {
                // Get online count
                jQuery('#webinar-live-viewers').html(response.data.online_attendees.count);
                populateAttendeeNameList(response.data.online_attendees.attendees, response.data.online_attendees.raisehandset);
                // End of get online count

                // Incentive status.
                incentiveStatusChange(response.data.incentive_status.isShow);
                // End of incentive status;

                //CTA button status
                setCTAStatus(response.data.CTA_status);
                //End of CTA button status

                // Set description and hostbox status.
                setHostAndDescBox(response.data.hostdesc_status);
                // End of Set description and hostbox status.

                // Set actionbox status.
                setActionboxStatus(response.data.actionbox_status);
                // End of set actionbox status

                //Set block sizes.
                setColumnSizes();

                //Checks user session.
                checkUserSession(response.data.user_session);
            }
            // Fetch chats
            showChats(response.data.chats);
            // Endof fetch chats

            POLLING_COUNT++;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Errors handled.
        }
    });
}

var COUNT = 0;
function setColumnSizes() {
    //Check if left boxes are showing.
    var showing_lbox = false;
    jQuery('.left-box').each(function () {
        var visibility = jQuery(this).is(':visible');
        if (visibility) {
            showing_lbox = true;
        }
    });
    var short_col_clases = "col-lg-7 col-sm-6 col-xs-12 box-column";
    var long_col_classes = "col-lg-12 col-sm-12 col-xs-12 box-column";
    var cur_col_elem = jQuery('.box-column');

    if (showing_lbox) {
        cur_col_elem.removeClass();
        cur_col_elem.addClass(short_col_clases);
    } else {
        cur_col_elem.removeClass();
        cur_col_elem.addClass(long_col_classes);
    }
}

jQuery(document).on('click', '#gift_icon', function (event) {
    PROCESSING_REQUEST = true;
    event.preventDefault();
    startAnimation(jQuery(this).attr('id'));
    update_incentive();
});

function setActionboxStatus(isShow) {
    if (isShow) {
        jQuery('.raise-hand-box').show();
    } else {
        jQuery('.raise-hand-box').hide();
    }
}

function setHostAndDescBox(isShow) {
    if (isShow) {
        jQuery('#host_box').addClass('show');
        jQuery('#description_box').addClass('show');
        jQuery('#cuspage_host_box').show();
        jQuery('#show_multi_boxes').addClass('message-center-newmsg');
    } else {
        jQuery('#host_box').removeClass('show');
        jQuery('#description_box').removeClass('show');
        jQuery('#host_box').hide();
        jQuery('#description_box').hide();
        jQuery('#cuspage_host_box').hide();
        jQuery('#show_multi_boxes').removeClass('message-center-newmsg');
    }
}

function setCTAStatus(isShow) {
    if (isShow) {
        // Show CTA element
        jQuery('.cta-view').addClass('show');
        jQuery('#show_cta_action').addClass('message-center-newmsg');
        if (jQuery('.cta-view').hasClass('show')) {
            jQuery('.cta-view').fadeIn();
        }
    } else {
        // Hide CTA element
        jQuery('.cta-view').removeClass('show');
        jQuery('#show_cta_action').removeClass('message-center-newmsg');
        if (!jQuery('.cta-view').hasClass('show')) {
            jQuery('.cta-view').fadeOut();
        }
    }
}

function incentiveStatusChange(isShow) {
    if (isShow === true) {
        jQuery('#show_incentive').show();
        jQuery('#gift_icon').css('color', '#ff002c');
        jQuery('#data_show_incentive').val('');
    } else {
        jQuery('#show_incentive').hide();
        jQuery('#gift_icon').css('color', ' #4c4c4c');
        jQuery('#data_show_incentive').val('yes');
    }
}

function update_incentive() {
    setColumnSizes();
    jQuery.ajax({
        type: 'POST',
        url: wpwebinarsystem.ajaxurl,
        data: {
            'action': 'updateIncentive',
            'post_id': theWebinarId,
            'status': theWebinarstatus
        },
        success: function (data, textStatus, jqXHR) {
            stopAnimation();
            var data_show_incentive = jQuery('#data_show_incentive').val();
            incentiveStatusChange(data_show_incentive == 'yes');
            PROCESSING_REQUEST = false;
        }
    });
}

function populateAttendeeNameList(data, handRaises) {
    //data-attendee
    var list_li = '';
    var raisedHandsCount = 0;
    for (x = 0; x < data.length; x++) {
        var raisedHand = handRaises[x];
        var handIcon = (raisedHand ? "<i class='fa fa-hand-paper-o pull-right hand-raised'></i>" : "<i class='fa fa-hand-paper-o pull-right '></i>");
        raisedHandsCount = (raisedHand ? ++raisedHandsCount : raisedHandsCount);
        if (raisedHandsCount > 0) {
            jQuery('#adminbar-handraised').addClass('hand-raised-admin');
        } else {
            jQuery('#adminbar-handraised').removeClass('hand-raised-admin');
        }

        var curAttendee = jQuery('.raise-hand-lg').attr('data-attendee');
        if (curAttendee == data[x].id) {
            if (data[x].high_five == 1) {
                jQuery('.raise-hand-lg').addClass('hand-raised');
            } else {
                jQuery('.raise-hand-lg').removeClass('hand-raised');
            }
        }
        var name = data[x].name;
        name = (name.length > 16 ? name = name.substring(0, 14) + "..." : name);

        list_li += '<li><a href="#">' + name + '</a>' + handIcon + '</li>';
        jQuery('#attendee-online-list').html(list_li);
    }
}
/*
 * Send chat
 */
SHOW_TIMESTAMPS = false;
IS_ADMIN = false;
CUR_ATTENDEE = false;
QUESTIONS_ARRAY = new Array();
OLD_QUESTION_SIZE = 0;
jQuery(document).on('keypress', '[name="webinar-chat-content"]', function (e) {
    if (e.which == 13) {
        jQuery('.webinar-chat-push').trigger('click');
    }
});

jQuery(document).on('click', '.webinar-chat-push', function () {
    var webinar_id = jQuery(this).attr('data-webinarid');
    var attendee_name = jQuery(this).attr('data-attendeename');
    var attendee_id = jQuery(this).attr('data-attendeeid');
    var message = jQuery('[name="webinar-chat-content"]').val();
    message = message.replace(/<\/?[^>]+(>|$)/g, "");
    var admin = jQuery(this).attr('data-isadmin');
    var pvt_chat = (jQuery('[name="wsweb_private_chat"]').prop('checked') == true ? 'true' : 'false');
    if (message.length > 1) {
        jQuery('[name="webinar-chat-content"]').prop('disabled', true);
        var dataSet = {
            action: 'sendChat',
            webinar_id: webinar_id,
            attendee_n: attendee_name,
            attendee_i: attendee_id,
            message_co: message,
            is_ifadmin: admin,
            pvt_chatmd: pvt_chat
        };
        jQuery.ajax({
            type: 'POST',
            data: dataSet,
            url: wpwebinarsystem.ajaxurl,
            dataType: 'json',
            success: function (data) {
                var timestamp = data.timestamp;
                jQuery('[name="webinar-chat-content"]').val('');
                jQuery('[name="webinar-chat-content"]').prop('disabled', false);
                jQuery('[name="webinar-chat-content"]').focus();
                if (pvt_chat === 'false') {
                    populateMyChat(attendee_name, message, timestamp);
                } else {
                    var elem = populateAdminMessage(data.attendee_id, timestamp);
                    var chat_box = jQuery('.weninar-chat-showbox');
                    chat_box.html(chat_box.html() + elem);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                jQuery('[name="webinar-chat-content"]').prop('disabled', false);
                // Exceptions handled!
            }
        });
    }
});

jQuery(document).ready(function () {
    var timestmp = jQuery('.webinar-chat-push').attr('data-show-timestamp');
    SHOW_TIMESTAMPS = (timestmp == 'true' ? true : false);
    IS_ADMIN = (jQuery('.webinar-chat-push').attr('data-isadmin') == 'true' ? true : false);
    CUR_ATTENDEE = jQuery('.webinar-chat-push').attr('data-attendeeid');
});

function populateMyChat(name, content, timestamp) {
    timestamp = (timestamp == null ? getFormattedDate() : timestamp);
    var chat_box = jQuery('.weninar-chat-showbox');
    var element = "<span class='webinar-my-chat'><b>" + (SHOW_TIMESTAMPS ? timestamp : '') + " " + name + " :</b> " + content + "</span><br>";
    chat_box.html(chat_box.html() + element);
    chat_box.scrollTop(chat_box[0].scrollHeight);
}

function populateAdminMessage(attendee_id, timestamp) {
    if (attendee_id == CUR_ATTENDEE) {
        timestamp = (timestamp == null ? getFormattedDate() : timestamp);
        var element = "<span class='webinar-admin-message'><b>" + (SHOW_TIMESTAMPS ? timestamp : '') + " System Bot :</b> You sent a message to Webinar host.</span><br>";
        return element;
    } else {
        return '';
    }
}

function showChats(data) {
    QUESTIONS_ARRAY = new Array();
    ARRAY_PUSH_COUNT = 0;
    var my_id = jQuery('.webinar-chat-push').attr('data-attendeeid');
    var chat_box = jQuery('.weninar-chat-showbox');
    var big_bag = "";
    var count = 0;
    var admin_messages_count = 0;
    var dataset = data.chats;
    setBoxEnabled(data.show_chatbox, data.show_questionbox);
    getQuestions(data.questions);
    var isModerator = jQuery('.webinar-chat-push').attr('data-isadmin') == 'true';
    jQuery(dataset).each(function () {
        var row = dataset[count];
        var closeBtnContent = "<span data-chatid='" + row.id + "' class='chat_delete fa fa-close'></span>";
        var rowCName = (row.name == null ? '' : row.name);
        var name = rowCName.split(' ')[0];
        var attendee = dataset[count].attendee_id;
        if (row.private == 0) {
            var isAdmin = row.admin;
            if (attendee == my_id) {
                var content = row.content;
                big_bag = big_bag + "<div data-chatrow-id='" + row.id + "' class='chat-parent' ><span data-message='" + row.id + "' class='webinar-my-chat'><b>" + (SHOW_TIMESTAMPS ? row.timestamp : '') + " Me :</b> " + content + "</span>" + (isModerator ? closeBtnContent : "") + "</div><br>";
            } else {
                big_bag = big_bag + "<div data-chatrow-id='" + row.id + "' class='chat-parent' ><span data-message='" + row.id + "' class='webinar-other-chat " + (isAdmin == 1 ? "webinar-moderator" : "") + " '><b>" + (SHOW_TIMESTAMPS ? row.timestamp : '') + " " + name + " " + (isAdmin == 1 ? "(moderator)" : "") + " :</b> " + row.content + "</span>" + (isModerator ? closeBtnContent : "") + "</div><br>";
            }
        } else {
            if (IS_ADMIN) {
                var style_class = (admin_messages_count % 2 === 0 ? 'webinar_privcht_light' : 'webinar_privcht_dark');
                pushtoArray(style_class, name, row.content, row.timestamp);
                admin_messages_count++;
            } else {
                big_bag = big_bag + populateAdminMessage(attendee, row.timestamp);
            }
        }
        count++;
    });
    chat_box.html(big_bag);
    setQuestions();

    if (!HOLD_SCROLLING)
        chat_box.scrollTop(chat_box[0].scrollHeight);
}
jQuery(document).on('mouseenter', '.weninar-chat-showbox', function () {
    HOLD_SCROLLING = true;
});
jQuery(document).on('mouseleave', '.weninar-chat-showbox', function () {
    HOLD_SCROLLING = false;
});

jQuery(document).on('mouseenter', '.chat-parent', function (event) {
    var msg = jQuery(this).attr('data-chatrow-id');
    jQuery('.chat_delete[data-chatid="' + msg + '"]').fadeIn();
});

jQuery(document).on('mouseleave', '.chat-parent', function () {
    var msg = jQuery(this).attr('data-chatrow-id');
    jQuery('.chat_delete[data-chatid="' + msg + '"]').fadeOut();
});

function getFormattedDate() {
    var date = new Date();
    var day = date.getDate();
    day = (day < 10 ? "0" + day : day);
    var str = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + day + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    return str;
}

jQuery('[name="wsweb_private_chat"]').bootstrapSwitch();
jQuery(document).ready(function () {
    setTimeout(function () {
        jQuery('.bootstrap-switch-wrapper').addClass('webinar-bswitch');
    }, 200);
});

function getQuestions(questions) {
    var q_count = 0;
    jQuery(questions).each(function () {
        var que_row = questions[q_count];
        var name = que_row.name.split(' ')[0];
        var style_class = (q_count % 2 === 0 ? 'webinar_privcht_light' : 'webinar_privcht_dark');
        pushtoArray(style_class, name, que_row.question, que_row.time);
        var child_element = "<div class='" + style_class + "'><span><b>" + name + "</b> : " + que_row.question + "</span><br></div>";
        q_count++;
    });
}

function setQuestions() {
    QUESTIONS_ARRAY.sort(function (a, b) {
        var keyA = new Date(a.timestamp),
                keyB = new Date(b.timestamp);
        // Compare the 2 dates
        if (keyA < keyB)
            return -1;
        if (keyA > keyB)
            return 1;
        return 0;
    });
    var arr_length = QUESTIONS_ARRAY.length - 1;

    var message_box = "";
    for (var count = arr_length; count >= 0; count--) {
        var set = QUESTIONS_ARRAY[count];
        message_box = message_box + "<div class='" + set.style + "'><span><b>" + set.name + "</b> : " + set.question + "</span><br></div>";
    }

    if (arr_length == -1) {
        message_box = message_box + "<div id='webinar_no_messages' class='webinar_privcht_system'><span><b>System Bot</b> : No messages to show</span><br></div>";
    } else {
        jQuery('#webinar_no_messages').remove();
    }

    var list_element = jQuery('#wswebinar_private_que');
    list_element.html(message_box);


    if (POLLING_COUNT > 1 && OLD_QUESTION_SIZE < ARRAY_PUSH_COUNT) {
        jQuery('.webinar-message-center').addClass('message-center-newmsg');
    }
    OLD_QUESTION_SIZE = QUESTIONS_ARRAY.length;
    return true;
}

jQuery(document).on('click', '#adminbar-handraised', function (event) {
    event.preventDefault();
});

jQuery(document).on('click', '.webinar-message-center', function (event) {
    event.preventDefault();
    jQuery('#wswebinar_private_que').toggleClass('display-block');
    jQuery(this).removeClass('message-center-newmsg');
    jQuery(this).toggleClass('message-center-active');
});

jQuery(document).on('click', '#webinar_show_chatbox', function (event) {
    PROCESSING_REQUEST = true;
    event.preventDefault();
    jQuery(this).toggleClass('message-center-newmsg');
    var ajaxurl = jQuery(this).attr('data-ajaxurl');
    var has_elem = jQuery("#webinar_show_chatbox").length;
    var webinar_id = jQuery(this).attr('data-webinarid');
    startAnimation(jQuery(this).attr('id'));
    if (has_elem == 1) {
        var active = jQuery(this).hasClass('message-center-newmsg');
        jQuery.ajax({
            type: 'POST',
            data: {active: active, action: 'setEnabledChats', webinar_id: webinar_id, page_category: pageCategory},
            dataType: 'json',
            url: wpwebinarsystem.ajaxurl,
            success: function (data) {
                setBoxEnabled(data.show_chatbox, data.show_questionbox);
                stopAnimation();
                PROCESSING_REQUEST = false;
                // It's Done
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Exceptions handled!
                PROCESSING_REQUEST = false;
            }
        });
    }
});

function setBoxEnabled(chatbox, questionbox) {
    if (questionbox == 'true') {
        // When questionbox active
        jQuery('#webinar_quesbox_tabhead').fadeIn('fast');
        jQuery('#webinar_questionbox').fadeIn('fast');
    } else {
        jQuery('#webinar_quesbox_tabhead').fadeOut('fast');
        jQuery('#webinar_questionbox').fadeOut('fast');
    }

    if (chatbox == 'true') {
        jQuery('#webinar_chatbox_tabhead').fadeIn('fast');
        jQuery('#webinar_chatbox').fadeIn('fast');
    } else {
        jQuery('#webinar_chatbox_tabhead').fadeOut('fast');
        jQuery('#webinar_chatbox').fadeOut('fast');
    }
    if (chatbox == 'true' && questionbox != 'true') {
        // When chatbox enabled and questionbox desabled.
        jQuery('#webinar_chatbox_tabhead a').trigger('click');
        jQuery('#webinar_chatbox').removeClass('hide');
    } else if (chatbox != 'true' && questionbox == 'true') {
        // When chatbox desabled and questionbox enabled.
        jQuery('#webinar_chatbox').addClass('hide');
        jQuery('#webinar_quesbox_tabhead a').trigger('click');
        jQuery('#webinar_quesbox_tabhead').addClass('active');
        jQuery('#webinar_quesbox_tabhead').fadeIn();
    }

    if (chatbox == 'false') {
        jQuery('#webinar_chatbox').removeClass('show');
    }

    return true;
}

jQuery(document).on('click', '.webinar_live_viewers', function (event) {
    event.preventDefault();
});

jQuery(document).on('click', '#webinar_show_questionbox', function (event) {
    PROCESSING_REQUEST = true;
    event.preventDefault();
    jQuery(this).toggleClass('message-center-newmsg');
    var ajaxurl = jQuery('#webinar_show_chatbox').attr('data-ajaxurl');
    var webinar_id = jQuery(this).attr('data-webinarid');
    var active = jQuery(this).hasClass('message-center-newmsg');
    startAnimation(jQuery(this).attr('id'));
    jQuery.ajax({
        type: 'POST',
        data: {active: active, action: 'setEnabledQuestions', webinar_id: webinar_id, page_category: pageCategory},
        dataType: 'json',
        url: wpwebinarsystem.ajaxurl,
        success: function (data) {
            // It's Done
            setBoxEnabled(data.show_chatbox, data.show_questionbox);
            stopAnimation();
            PROCESSING_REQUEST = false;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Exceptions handled!
            PROCESSING_REQUEST = false;
        }
    });
});



var theSaveQuestionButton;
var theSaveQuestionButtonVal;

jQuery(document).on('click', '#saveQuestion', function (e) {
    e.preventDefault();
    var ques_name = jQuery('#que_name').val();
    var ques_email = jQuery('#que_email').val();
    var quest = jQuery('#addQuestion').val();
    if (ques_email.length < 3 || !validateEmail(ques_email) || ques_name.length < 1 || quest.length < 1) {
        alert(questionFormerror);
        return false;
    }


    var data = {'action': 'saveQuestionAjax', 'question': quest, 'name': jQuery('#que_name').val(),
        'email': jQuery('#que_email').val(), 'webinar_id': theWebinarId};
    theSaveQuestionButton = jQuery(this);
    theSaveQuestionButtonVal = theSaveQuestionButton.val();
    jQuery(this).val(questionWait);
    jQuery(this).attr('disabled', 'disabled');

    jQuery.ajax({data: data, url: wpwebinarsystem.ajaxurl, dataType: 'jsonp', jsonp: 'callback', jsonpCallback: "jsonpCallback"
    }).done(function (retrievedData) {

        jQuery('#myQuestions').show();
        theSaveQuestionButton.val(theSaveQuestionButtonVal);
        theSaveQuestionButton.removeAttr('disabled');
        jQuery('#addQuestion').val('');

    });
    //e.preventDefault();
});

function jsonpCallback(retrievedData){
  addQuestionToPage("" + retrievedData.question, "" + retrievedData.time);
}

function addQuestionToPage(question, time) {
    jQuery('#ques_load').prepend(jQuery('<p class="myquestion"><span>' + time + '</span>' + question + '</p>').hide().fadeIn(2000));
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
/*
 * Raise hand
 */
jQuery(document).on('click', '.raise-hand-box', function () {
    jQuery('#action_hand').hide();
    jQuery('.actionbox-loader').fadeIn();
    jQuery.ajax({
        url: wpwebinarsystem.ajaxurl,
        data: {action: 'raiseHand', webinar_id: theWebinarId},
        dataType: 'json',
        type: 'POST',
    }).done(function (response) {
        jQuery('#action_hand').fadeIn();
        jQuery('.actionbox-loader').hide();
        jQuery('.raise-hand-lg').toggleClass('hand-raised');
    });
});

jQuery(document).on('click', '#adminbar-handraised', function () {
    startAnimation(jQuery(this).attr('id'));
    jQuery.ajax({
        url: wpwebinarsystem.ajaxurl,
        data: {action: 'unraiseHands', webinar_id: theWebinarId},
        dataType: 'json',
        type: 'POST',
    }).done(function (response) {
        jQuery('#adminbar-handraised').removeClass('hand-raised-admin');
        jQuery('.raise-hand-lg').removeClass('hand-raised');
        stopAnimation();
    });
});
jQuery('[name="wsweb_private_chat"]').bootstrapSwitch();

/*
 * Show manually the CTA box.
 */
jQuery(document).on('click', '#show_cta_action', function () {
    PROCESSING_REQUEST = true;
    setColumnSizes();
    startAnimation(jQuery(this).attr('id'));
    jQuery('.cta-view').toggleClass('show-cta');
    var CTA_status = (jQuery('.cta-view').hasClass('show-cta') ? 'yes' : 'no');

    jQuery.ajax({
        url: wpwebinarsystem.ajaxurl,
        data: {action: 'showCTA', webinar_id: theWebinarId, cta_status: CTA_status, webinar_status: pageCategory},
        dataType: 'json',
        type: 'POST',
        success: function (data, textStatus, jqXHR) {
            jQuery('#show_cta_action').toggleClass('message-center-newmsg');
            if (!jQuery('.cta-view').hasClass('show')) {
                jQuery('.cta-view').fadeOut();
            }
            setCTAStatus(data.showStatus);
            stopAnimation();
            PROCESSING_REQUEST = false;
        }
    });
});

/*
 * Show/Hide multiple boxes in live or replay page.
 */

jQuery(document).on('click', '#show_multi_boxes', function (event) {
    event.preventDefault();
    PROCESSING_REQUEST = true;
    jQuery('#show_multi_boxes').toggleClass('message-center-newmsg');
    var isShow = jQuery('#show_multi_boxes').hasClass('message-center-newmsg');
    startAnimation(jQuery(this).attr('id'));

    setColumnSizes();
    var updateBoxes = (isShow ? 'yes' : 'no');
    jQuery.ajax({
        url: wpwebinarsystem.ajaxurl,
        data: {action: 'hostdescBoxes', webinar_id: theWebinarId, box_status: updateBoxes, webinar_status: pageCategory},
        type: 'POST',
        success: function (data, textStatus, jqXHR) {

            if (isShow) {
                jQuery('#host_box').addClass('show');
                jQuery('#description_box').addClass('show');
                jQuery('#cuspage_host_box').show();
            } else {
                jQuery('#host_box').removeClass('show');
                jQuery('#description_box').removeClass('show');

                jQuery('#host_box').hide();
                jQuery('#description_box').hide();

                jQuery('#cuspage_host_box').hide();
            }
            setColumnSizes();
            stopAnimation();
            PROCESSING_REQUEST = false;
        }
    });
});

jQuery(document).on('click', '#livep-play-button', function (e) {
    e.preventDefault();
    wswebinarsysteemMJP[jQuery(this).hasClass('wbnicon-play') ? 'play' : 'pause']();
});

jQuery(document).on('click', '#action_box_handle', function (event) {
    PROCESSING_REQUEST = true;
    event.preventDefault();
    startAnimation(jQuery(this).attr('id'));
    jQuery(this).toggleClass('message-center-newmsg');
    var isShow = jQuery(this).hasClass('message-center-newmsg');
    var actionBox = (isShow ? 'yes' : 'no');
    setColumnSizes();
    jQuery.ajax({
        url: wpwebinarsystem.ajaxurl,
        data: {action: 'actionBoxStatus', webinar_id: theWebinarId, box_status: actionBox, webinar_status: pageCategory},
        dataType: 'json',
        type: 'POST',
        success: function (data, textStatus, jqXHR) {

            var isShow = jQuery('#action_box_handle').hasClass('message-center-newmsg');
            if (isShow) {
                jQuery('.raise-hand-box').show();
            } else {
                jQuery('.raise-hand-box').hide();
            }

            setColumnSizes();
            stopAnimation();

            PROCESSING_REQUEST = false;
        }
    });
});

jQuery(document).on('click', '.webinar-my-chat a', function (event) {
    event.preventDefault();
    window.open(jQuery(this).attr('href'), '_blank');
});

jQuery(document).on('click', '.chat_delete', function (event) {
    event.preventDefault();
    var chat_id = jQuery(this).attr('data-chatid');
    jQuery('span[data-message="' + chat_id + '"]').css('text-decoration', 'line-through');
    var messages_array = [chat_id];
    jQuery.ajax({
        url: ajaxurl,
        data: {action: 'deleteChats', messages: messages_array},
        dataType: 'json',
        type: 'POST'
    }).done(function (data) {
        jQuery('span[data-message="' + chat_id + '"]').remove();
        jQuery('.chat_delete[data-chatid="' + chat_id + '"]').remove();
    });
});

function startAnimation(anchorID) {
    var classes_to_remove = [];

    jQuery("#" + anchorID).removeClass(function (index, classNames) {
        var current_classes = classNames.split(" ");
        jQuery.each(current_classes, function (index, class_name) {
            if (!class_name.indexOf('fa') | !class_name.indexOf('glyphicon')) {
                classes_to_remove.push(class_name);
            }
        });
    });
    var animImg = "<img id='adminbar_loader' data-iconclass='" + classes_to_remove.join(" ") + "' class='loading_img_adminbar' data-parent='" + anchorID + "' src='" + loadingImg + "'>";
    var anchorElement = jQuery("#" + anchorID);
    anchorElement.html(animImg);

    jQuery("#" + anchorID).removeClass(classes_to_remove.join(" "));
    return classes_to_remove.join(" ");
}
function stopAnimation() {
    var parent = jQuery('#adminbar_loader').attr('data-parent');
    var clases = jQuery('#adminbar_loader').attr('data-iconclass');
    jQuery('#adminbar_loader').remove();
    jQuery('#' + parent).addClass(clases);
}

jQuery(function () {
    jQuery('[data-toggle="tooltip"]').tooltip();
});

jQuery(document).on('click', '.cta-button', function (event) {
    event.preventDefault();
});

jQuery(document).on('click', '#cta_action_btn_openurl', function (event) {
    event.preventDefault();
    var URL = jQuery(this).attr('href');
    if (URL != '#') {
        window.open(URL);
    }
});

/*
 * Check user session.
 * Redirect page to login page if session expired.
 */

function checkUserSession(status) {
    if (status == false) {
        // Remove all cookies
        var url = window.location.href;
        location.href = url + "?logout=true";
        console.log(url);
    }
}

/*
 * Get cookie by name
 * @returns {String} cookie_value 
 * @param {String} cookie_name 
 */
function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2)
        return parts.pop().split(";").shift();
}

/*
  * Get the input times on the basis of day selected.
  * @return input times array.
  */
  jQuery(document).on('change', 'select[name="inputday"]', function () {
	
	jQuery('#inputtime').empty();
	jQuery('#inputtime').append(jQuery('<option></option>').html(wpws.available_timeslots));
	    	
		var input_day = jQuery(this).val();
		if (input_day == 'rightnow') {
		    jQuery('select[name="inputtime"]').slideUp();
		} 
		else {
		jQuery('select[name="inputtime"]').slideDown();
		jQuery.ajax({
			'type': 'GET',
			'url' : wpwebinarsystem.ajaxurl,
			data: {
				action: 'getInputTimes',
				input_day: input_day
			},
			success: function(result){
				jQuery('#inputtime').empty();
				options = '';
				 var data = JSON.parse(result);
				 for(var i in data)
				 {
				if(data[i].value == 'no'){
                 options += '<option value="" disabled="disabled" selected="selected">' + data[i].label +'</option>';
				 break;
                } else if(data[i].value == 'default'){
					options += '<option disabled="disabled" selected="selected" value="'+ data[i].value +'">'+ data[i].label +'</option>';
				}
                 else{
				 options += '<option value="'+ data[i].value +'">'+ data[i].label +'</option>';
				 }}
				  jQuery('#inputtime').html(options);
			},
			error: function(exception){
				//alert(exception);
			}
			
		});
			
		}
		
	
});
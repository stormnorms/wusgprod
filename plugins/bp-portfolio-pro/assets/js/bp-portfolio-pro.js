// BP Portfolio Pro main js

jQuery(document).ready(function () {
    
    /* Media Library */
    jQuery(document).on('click', '#upload_image_lib', function (e) {
        var id = jQuery(this).attr('id');
        jQuery.magnificPopup.open({
            items: {
                src: '<div class="white-popup" style="height: 348px;"><iframe id="'+ id +'_frame" class="photo-dialog" src="'+ajaxurl+'?action=upload_portofolio_user_media_lib&pid='+bpcp_object.bpcp_project_id+'" width="100%" /></div>',
                type: 'inline'
            }
        });
    });
    

    /* File Dialog Core */
    function bpcp_media_uploader($btn,func,showcaption,type) {

        if (typeof type == 'undefined') {
            type = 'photo';
        }

        jQuery($btn).click(function(e){
            var id = jQuery(this).attr('id');
            jQuery.magnificPopup.open({
                items: {
                    src: '<div class="white-popup upload-media" style="height: 450px;"><iframe id="'+ id +'_frame" class="photo-dialog" src="'+ajaxurl+'?action=upload_bpcp_photos_dialog&caption='+showcaption+'&type='+type+'" width="100%" /></div>',
                    type: 'inline'
                }
            });
        });

        jQuery($btn).click(function(){
            window.bpcp_media_uploader_callback = func;
        });


    }

    window.bpcp_photo_dialog_callback = function(data) {
        jQuery.magnificPopup.close();
        window.bpcp_media_uploader_callback(data);

    }

    jQuery('.bpcp-popup-image').magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function(item) {
              return item.el.attr('title');
            }
        }
    });

    jQuery('.bpcp_pro_wip_single_thumb img').click(function(){
        var src = $(this).attr('data-src');
        $.magnificPopup.open({
            items: {
              src: src
            },
            type: 'image',
        });
    });

    /* File Dialog Core */

    // all about magnific popup
    jQuery('.bpcp-modal').magnificPopup({
        type: 'inline',
        preloader: false,
        callbacks: {
            open: function() {
                if(jQuery('.video_error_message').length){
                    jQuery('.video_error_message').html('');
                }
            }
        }
    });
    jQuery(document).on('click', '.bpcp-modal-cancel', function (e) {
        e.preventDefault();
        jQuery.magnificPopup.close();
    });

    // project filtering profile page
    jQuery('.bpcp-pro-tab-title-filter #projects-order-by').live('change', function(){
        var selector = jQuery(this);
        var projects_filter_by = selector.val();
        var data = {
            action: 'bpcpproajax',
            task: 'projects_order_filtering',
            projects_filter_by: projects_filter_by
        };
        jQuery.post(ajaxurl, data, function (response) {
            jQuery('.bb-display-projects .bb-project-items').html(response);
        });
        return false;
    });

    // project filtering global page
    /*jQuery('.projects-order-filter #projects-order-by').live('change', function(){
        var selector = jQuery(this);
        var projects_filter_by = selector.val();
        var data = {
            action: 'bpcpproajax',
            task: 'projects_order_filtering_global',
            projects_filter_by: projects_filter_by
        };
        jQuery.post(ajaxurl, data, function (response) {
            jQuery('.bpcp-grid-wrapper #portfolio-activity').html(response);
        });
        return false;
    });*/
    jQuery('select.auto_submit_pform').live('change', function(){
        jQuery(this).closest('form').submit();
    });

    // revision thumbs photo switch
    jQuery('.revision-thumbs li').on('click', function(){
        var selector = jQuery(this),
            revision_id = jQuery(this).find('a').attr('data-revid'),
            post_id = jQuery(this).find('a').attr('data-postid'),
            editWIP = $('.edit-wip'),
            editLink = editWIP.attr('href');

        if ( 'undefined' != typeof editLink ) {
            var newEditLink = editLink.replace(/current_wip_id=\d+/, 'current_wip_id='+revision_id);
            editWIP.attr('href', newEditLink );
        }

        var data = {
            action: 'bpcpproajax',
            task: 'revision_switch',
            revision_id: revision_id
        };
        jQuery('.bpcp_pro_wip_single_thumb p').addClass('loading').append('<span class="loader-overlay"><span class="throbber-loader"></span></span>');
        jQuery.post(ajaxurl, data, function (response) {
            var response = jQuery.parseJSON(response);
            selector.parent().find('li').removeClass('active');
            selector.addClass('active');
            jQuery('.bpcp_pro_wip_single_thumb img')
                .attr('src', response.thumb_url)
                .attr('data-src', response.thumb_url);
            jQuery('input[name=target_wip]').val(revision_id);
            jQuery('.bpcp_pro_wip_single_thumb p').removeClass('loading');
			jQuery('.wip-views-count span').html(response.wip_views);
            jQuery('.loader-overlay').remove();
            jQuery('#comments').after('<div id="comments-placeholder"></div>');
            jQuery('#comments').remove();
            var html = jQuery(response.comments);
            html.find('#reply-title').html(bpcp_object.wip_comment_heading);
            jQuery('#comments-placeholder').html(html);
            jQuery('#comments-placeholder').attr('id', 'comments');
        });
        return false;
    });

    // components like
    jQuery('.bpcp-components-like #like_this_post').live('click', function(){
        var selector = jQuery(this);
        var get_userid = selector.attr('data-userid');
        var get_postid = selector.attr('data-postid');
        var get_posttype = selector.attr('data-posttype');
        var data = {
            action: 'bpcpproajax',
            task: 'bpcp_components_like',
            postid: get_postid,
            userid: get_userid,
            post_type: get_posttype
        };
        jQuery.post(ajaxurl, data, function (response) {
            var response = jQuery.parseJSON(response);
            if(response.status != '' && response.status == 'success'){
                selector.html('<b>'+bpcp_object.appreciated_text+'</b>');
                selector.attr('id', 'unlike_this_post');
                var current_appreciations = parseInt(jQuery('.bpcp-components-like .appreciation_count span').html());
                jQuery('.bpcp-components-like .appreciation_count span').html(current_appreciations+1);
            }
        });
        return false;
    });

    // components unlike
    jQuery('.bpcp-components-like #unlike_this_post').live('click', function(){
        var selector = jQuery(this);
        var get_userid = selector.attr('data-userid');
        var get_postid = selector.attr('data-postid');
		var get_posttype = selector.attr('data-posttype');
        var data = {
            action: 'bpcpproajax',
            task: 'bpcp_components_unlike',
            postid: get_postid,
            userid: get_userid,
            post_type: get_posttype,

        };
        jQuery.post(ajaxurl, data, function (response) {
            var response = jQuery.parseJSON(response);
            if(response.status != '' && response.status == 'success'){
                selector.html('<b>'+bpcp_object.appreciate_this_text+'</b>');
                selector.attr('id', 'like_this_post');
                var current_appreciations = parseInt(jQuery('.bpcp-components-like .appreciation_count span').html());
                jQuery('.bpcp-components-like .appreciation_count span').html(current_appreciations-1);
            }
        });
        return false;
    });

	//hide sidebar if no collections
	var existing_coll = jQuery( '.bpcp_collections_list :input' );
	if ( jQuery( existing_coll ).length == '0' ) {
		jQuery( '#add_collection .bpcp-right' ).hide();
	}

    // add collection from project single page
    jQuery('.add-collection-details .project-single-btn #collection_add').on('click', function(){
        var selector = jQuery(this);
        selector.attr("disabled", true);
        var collection_description_field = selector.parent().parent().find('#collection_description');
        var collection_description = collection_description_field.val();
        var collection_title_field = selector.parent().parent().find('#collection_title');
        var collection_title = collection_title_field.val();
        var collection_visibility = selector.parent().parent().find('#collection_visibility').val();
        var collection_add_project_field = selector.parent().parent().find('#collection_add_project');
        var collection_project_id = collection_add_project_field.is(':checked') ? collection_add_project_field.val() : '';
        var data = {
            action: 'bpcpproajax',
            task: 'collection_add',
            collection_description: collection_description,
            collection_title: collection_title,
            collection_visibility: collection_visibility,
            collection_project_id: collection_project_id
        };
        jQuery.post(ajaxurl, data, function (response) {
                selector.parent().append(response);
                setTimeout(function(){ selector.attr("disabled", false); location.reload(); }, 3000);
        });
        return false;
    });

	// jQuey function for the project suggestion
	jQuery.fn.projectsAutocomplete = function() {
        return this.each(function() {
            var projectInput = jQuery(this);
            projectInput.autocomplete({
                source: ajaxurl+'?action=bpcpproajax&task=search_projects',
                minLength: 3,
                select: function (event, ui) {
                    var projectList = projectInput.parent().prev();
                    var projectDiv = projectList.find('.project-'+ui.item.value);
                    if ( projectDiv.length == 0 ) {
                        projectList[0].innerHTML += '<div class="project-'+ui.item.value+'">' +
                                                    '<input type="hidden" name="choosen_project[]" value="'+ui.item.value+'" />' +
                                                    '<span><a class="delete-project-btn" title="Remove">x</a>'+ui.item.label+'</span>' +
                                                    '</div>';
                    }

                    projectInput.autocomplete('close').val('');

                    return false;
                }
            });
        });
    };

	// init project autocomplete
	jQuery('.bpcp_projects_autocomplete').projectsAutocomplete();

    // display project autocomplete input
    jQuery('#edit_collection').on('click', '.bpcp-add-project-btn', function (event) {
        var addButton =  jQuery(this);
        addButton.hide();
        addButton.next().show();
    });

	// remove project from the collection list
	jQuery(document).on('click', '.delete-project-btn', function () {
        jQuery(this).parents('div[class^=project-]').remove();
    });

    // edit collection popup
    jQuery('.edit_collection').on('click', function () {
        var collection_id = jQuery(this).attr('data-collection_id');

        jQuery.magnificPopup.open({
            items: {
                src: '#edit_collection',
                type: 'inline'
            },
            callbacks: {
                beforeOpen: function () {
                    var form = jQuery('.edit-collection-details');

                    form.toggleClass('loading');

                    jQuery.ajax({
                        method: 'GET',
                        url: ajaxurl,
                        data: { action: 'bpcpproajax', task: 'get_collection_data', 'collection_id' : collection_id },
                        success: function (response) {
                            jQuery('#collection_title').val(response.title);
                            jQuery('#collection_description').val(response.description);
                            jQuery('#collection_visibility').val(response.visibility);
                            jQuery('#target_collection').val(collection_id);
                            jQuery('.bpcp_projects_list').html(response.projects_list_html);
                            form.toggleClass('loading');
                        }
                    });
                },
                close: function () {
                    var addButton =  jQuery('#edit_collection .bpcp-add-project-btn');
                    addButton.show();
                    addButton.next().hide();
                }
            }
        });
    });

    // adding project as  a collection item
    jQuery('.add-collection-details #collection_save').on('click', function(){
        var selector = jQuery(this);
        selector.attr("disabled", true);
        var current_project_id = selector.parent().find('#current_project_id').val();
        var chosen_collection = selector.parent().parent().find('.bpcp_collections_list input[type=checkbox]:checked').map(function() {return this.value;}).get().join(',');
        var not_chosen_collection = selector.parent().parent().find('.bpcp_collections_list input[type=checkbox]').not(':checked').map(function() {return this.value;}).get().join(',');
        var data = {
            action: 'bpcpproajax',
            task: 'add_project_as_collection_item',
            current_project_id: current_project_id,
            chosen_collection: chosen_collection,
            not_chosen_collection: not_chosen_collection
        };
        jQuery.post(ajaxurl, data, function (response) {
            selector.after(response);
            setTimeout(function(){ selector.attr("disabled", false); location.reload(); }, 3000);
        });
    });


    // adding video embed url to a project as custom field
    jQuery('.add-video-details #video_add').on('click', function(){
        var selector = jQuery(this);
        var current_project_id = selector.parent().parent().find('#current_project_id').val();
        var video_embed = selector.parent().parent().find('#video_embed_url'),
            video_embed_url = video_embed.val();

		if ( video_embed_url == '' ) {
			jQuery('.video_error_message').html(bpcp_object.video_embed_empty_msg);
			return false;
		}

        var data = {
            action: 'bpcpproajax',
            task: 'embed_video_url_to_project',
            current_project_id: current_project_id,
            video_embed_url: video_embed_url
        };
        jQuery.post(ajaxurl, data, function (response) {
            var response = jQuery.parseJSON(response);
            if(response.video_id != '' && response.video_output != ''){
                var prepare_html = '<ul id="'+response.video_id+'" class="bpcp-li-left"><li class="project_item">' + response.video_output + '</li>';
                prepare_html += '<li><input type="hidden" name="embeded_url" value="'+video_embed_url+'" />' +
                    '<label for="attachment_caption">'+bpcp_object.caption_input_label+'</label>' +
                    '<input type="hidden" name="video_item[vid][]" value="'+response.video_id+'" />' +
                    '<input type="text" name="video_item[vcaption][]" class="attachment-caption" value="" />' +
                    '<a href="#" class="delete-video">'+bpcp_object.delete_btn+'</a></li></ul>';
                jQuery('.uploaded-images').append(prepare_html);
                jQuery('.bpcp-buttons .bpcp-media-upload-title').html(bpcp_object.upload_another_image);
                jQuery('video,audio').mediaelementplayer();
                jQuery.magnificPopup.close();
                video_embed.val('');
                bpcp_project_sorting();
                // for updating sort list of project items
                bpcp_project_sorting_update();
            }else{
                jQuery('.video_error_message').html(bpcp_object.video_embed_disable_msg);
            }
        });
    });

    // remove embeded video url from project custom field
    jQuery('a.delete-video').live('click', function (e) {
        e.preventDefault();
        var selector = jQuery(this);
        var embeded_url = selector.parent().find('input[name="embeded_url"]').val();
        var video_ID = selector.parent().parent().attr('id');
        var current_project_id = jQuery('.add-video-details #current_project_id').val();
        var data = {
            action: 'bpcpproajax',
            task: 'remove_embeded_video_url_from_project',
            current_project_id: current_project_id,
            video_embed_url: embeded_url,
            video_ID: video_ID
        };
        jQuery.post(ajaxurl, data, function (response) {
            // for updating sort list of project items
            bpcp_project_sorting_update();
        });
        selector.parent().parent().hide(0, function(){
            jQuery(this).remove();
            // now change button text based on condition
            if(jQuery('.uploaded-images').find('ul').length == 0){
                jQuery('.bpcp-media-upload-title').html(bpcp_object.image_upload_text);
            }
        });
    });

    // set project attachment caption - photo & song
    jQuery('.uploaded-images .caption-save').live('click', function(){
        var selector = jQuery(this);
        var attachment_ID = selector.parent().find('input[name="atachment_id"]').val();
        var caption_value = selector.parent().find('input[name="attachment_caption"]').val();
        var data = {
            action: 'bpcpproajax',
            task: 'set_project_attachment_caption',
            attachment_ID: attachment_ID,
            caption_value: caption_value
        };
        jQuery.post(ajaxurl, data, function (response) {
            var response = jQuery.parseJSON(response);
            jQuery.magnificPopup.open({
                items: {
                    src: '<div class="bbcp-white-popup-block"><h4>' + response.message + '</h4></div>',
                    type: 'inline'
                }
            });
        });
    });

    // set project attachment caption - video
    jQuery('.uploaded-images .video-caption-save').live('click', function(){
        var selector = jQuery(this);
        var current_project_id = jQuery('.add-video-details #current_project_id').val();
        var video_ID = selector.parent().parent().attr('id');
        var caption_value = selector.parent().find('input[name="attachment_caption"]').val();
        var data = {
            action: 'bpcpproajax',
            task: 'set_project_video_attachment_caption',
            current_project_id: current_project_id,
            video_ID: video_ID,
            caption_value: caption_value
        };
        jQuery.post(ajaxurl, data, function (response) {
            var response = jQuery.parseJSON(response);
            jQuery.magnificPopup.open({
                items: {
                    src: '<div class="bbcp-white-popup-block"><h4>' + response.message + '</h4></div>',
                    type: 'inline'
                }
            });
        });
    });

    // subcomponent uploaded items sorting
    var bpcp_project_sorting = function(){
        jQuery('.uploaded-images').sortable({
            'items': 'ul',
            'axis': 'y',
            cursor: "move",
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
                var sort_data = jQuery(this).sortable('serialize');
                var current_project_id = jQuery('.add-video-details #current_project_id').val();
                var data = {
                    action: 'bpcpproajax',
                    task: 'project_items_sorting',
                    current_project_id: current_project_id,
                    sort_data: sort_data
                };
                jQuery.post(ajaxurl, data, function (response) {
                    // do whatever you want to do
                });
            }
        });

    }
    bpcp_project_sorting();

    var bpcp_project_sorting_update = function(){
        var sort_data = jQuery('.uploaded-images').sortable('serialize');
        var current_project_id = jQuery('.add-video-details #current_project_id').val();
        var data = {
            action: 'bpcpproajax',
            task: 'project_items_sorting',
            current_project_id: current_project_id,
            sort_data: sort_data
        };
        jQuery.post(ajaxurl, data, function (response) {
            // do whatever you want to do
        });
    }

    // form required field validation
    jQuery('.add-project-details #create_project').on('click', function() {
        jQuery('.add-project-details').find('small.error').remove();
        var valid1 = validation_empty('.add-project-details #project_title', bpcp_object.project_title_missing);
        var valid2 = true;
        if( bpcp_object.project_category_required && jQuery('.bp-category-multiselect').length){
            valid2 = validation_empty('.add-project-details #project-category', bpcp_object.project_category_missing);
        }

        if (valid1 === false || valid2===false) {
            return false;
        } else {
            return true;
        }
    });
    jQuery('.add-project-details #project_finish').on('click', function() {
        jQuery('.add-project-details').find('small.error').remove();
        var valid1 = validation_empty('.add-project-details #project_visibility', bpcp_object.project_visibility_missing);
        if (valid1 === false) {
            return false;
        } else {
            return true;
        }
    });
    jQuery('.add-wip-details #wip_finish').on('click', function() {
        jQuery('.add-wip-details').find('small.error').remove();
        var valid1 = validation_empty('.add-wip-details #wip_title', bpcp_object.wip_title_missing);
        var valid2 = true;
		var is_revision = jQuery(this).parents().find('.bbp-is-revision');
        if( bpcp_object.wip_category_required && jQuery('.bp-category-multiselect').length ){
            valid2 = validation_empty('.add-wip-details #wip-category', bpcp_object.wip_category_missing);
        }

        if ((valid1 === false || valid2 === false) && !(is_revision.length > 0) ) {
            return false;
        } else {
            return true;
        }
    });
    jQuery('.add-collection-details #collection_add').on('click', function() {
        jQuery('.add-collection-details').find('small.error').remove();
        var valid1 = validation_empty('.add-collection-details #collection_title', bpcp_object.collection_title_missing);
        if (valid1 === false) {
            return false;
        } else {
            return true;
        }
    });


    // upload image
    bpcp_media_uploader(jQuery("#upload_image"),function(data){

        var prepare_html = '<ul id="attach-'+data.id+'" class="bpcp-li-left"><li class="project_item">';

        prepare_html += '<img class="display-image" src="'+data.large[0]+'"/></li>';

        prepare_html += '<li><input class="chosen_file" type="hidden" name="chosen_file[]" value="'+data.id+'" />' +
            '<input type="hidden" name="atachment_id" value="'+data.id+'" />' +
            '<label for="attachment_caption">'+bpcp_object.caption_input_label+'</label>' +
            '<input type="hidden" name="photo_item[pid][]" value="'+data.id+'" />' +
            '<input type="text" name="photo_item[pcaption][]" class="attachment-caption" value="'+data.caption+'" />' +
            '<a href="#" class="delete-image">'+bpcp_object.delete_btn+'</a>' +
            '</li></ul>';
        jQuery('.uploaded-images').append(prepare_html);
        jQuery('.uploaded-images').parent().find('.bpcp-media-upload-title').html(bpcp_object.upload_another_image);

    },'1');

    // upload song

    bpcp_media_uploader(jQuery("#upload_song"),function(data){

        var prepare_html = '<ul id="attach-'+data.id+'" class="bpcp-li-left"><li class="project_item">';

        prepare_html += '<!--[if lt IE 9]><script>document.createElement(\'audio\');</script><![endif]--><audio class="wp-audio-shortcode" preload="none" controls="controls"><source type="audio/mpeg" src="'+data.url+'?_=1" /><a href="'+data.url+'">'+data.url+'</a></audio></li>';

        prepare_html += '<li><input class="chosen_file" type="hidden" name="chosen_file[]" value="'+data.id+'" />' +
            '<input type="hidden" name="atachment_id" value="'+data.id+'" />' +
            '<label for="attachment_caption">'+bpcp_object.caption_input_label+'</label>' +
            '<input type="hidden" name="photo_item[pid][]" value="'+data.id+'" />' +
            '<input type="text" name="photo_item[pcaption][]" class="attachment-caption" value="'+data.caption+'" />' +
            '<a href="#" class="delete-image">'+bpcp_object.delete_btn+'</a>' +
            '</li></ul>';
        jQuery('.uploaded-images').append(prepare_html);
        jQuery('.uploaded-images').parent().find('.bpcp-media-upload-title').html(bpcp_object.upload_another_image);

    },'1','song');


    // upload image from lib
    bpcp_media_uploader(jQuery("#upload_image_lib"),function(data){
        jQuery('#display_cover_image').attr("src",data.large[0]);
        jQuery(".chosen-cover").val(data.id);
        
    },0);

    //cover upload

    bpcp_media_uploader(jQuery("#upload_cover"),function(data){
        jQuery('#display_cover_image').attr("src",data.large[0]);
        jQuery(".chosen-cover").val(data.id);

    },0);


    //wip upload

    bpcp_media_uploader(jQuery("#upload_wip_image"),function(data){

        var prepare_html = '<ul class="bpcp-li-left"><li>';
        prepare_html += '<img class="display-image" src="'+data.large[0]+'"/></li>';
        prepare_html += '<li><input class="chosen_file" type="hidden" name="chosen_file" value="'+data.id+'" />' +
        '<input type="hidden" name="atachment_id" value="'+data.id+'" />' +
		'<a href="#" class="delete-image">'+bpcp_object.delete_btn+'</a>' +
        '</li></ul>';
        jQuery('.uploaded-images').append(prepare_html);
		jQuery('.bpcp-buttons #upload_wip_image').hide();

    },0);

    // remove image
    jQuery('a.delete-image').live('click', function (e) {
        e.preventDefault();
        // remove attachment by ajax
        var current_project_id = jQuery('.add-video-details #current_project_id').val();
        var get_attachment_ID = jQuery(this).parent().find('input[name="atachment_id"]').val();
        var data = {
            action: 'bbpajax',
            task: 'remove_project_attachment',
            attachment_ID: get_attachment_ID
        };
        jQuery.post(ajaxurl, data, function (response) {
        });

        jQuery(this).parent().parent().remove();

		jQuery('.bpcp-buttons #upload_wip_image').show();

        // now change button text based on condition
        if(jQuery('.uploaded-images').find('ul').length == 0){
            jQuery('.bpcp-media-upload-title').html(bpcp_object.image_upload_text);
        }

        // for updating sort list of project items
        bpcp_project_sorting_update();
    });

    // projects widget
    function project_widget_response(a) {
        a = a.substr(0, a.length - 1), a = a.split("[[SPLIT]]"), "-1" !== a[0] ? jQuery(".widget ul#projects-list").fadeOut(200, function() {
            jQuery(".widget ul#projects-list").html(a[1]), jQuery(".widget ul#projects-list").fadeIn(200)
        }) : jQuery(".widget ul#projects-list").fadeOut(200, function() {
            var b = "<p>" + a[1] + "</p>";
            jQuery(".widget ul#projects-list").html(b), jQuery(".widget ul#projects-list").fadeIn(200)
        })
    }
    jQuery(document).ready(function() {
        jQuery(".widget div#projects-list-options a").on("click", function() {
            var a = this;
            return jQuery(a).addClass("loading"), jQuery(".widget div#projects-list-options a").removeClass("selected"), jQuery(this).addClass("selected"), jQuery.post(ajaxurl, {
                action: "widget_bpcp_pro_projects",
                cookie: encodeURIComponent(document.cookie),
                _wpnonce: jQuery("input#_wpnonce-projects").val(),
                "max-projects": jQuery("input#projects_widget_max").val(),
                filter: jQuery(this).attr("id")
            }, function(b) {
                jQuery(a).removeClass("loading"), project_widget_response(b)
            }), !1
        })
    });

    // collections widget
    function collection_widget_response(a) {
        a = a.substr(0, a.length - 1), a = a.split("[[SPLIT]]"), "-1" !== a[0] ? jQuery(".widget ul#collections-list").fadeOut(200, function() {
            jQuery(".widget ul#collections-list").html(a[1]), jQuery(".widget ul#collections-list").fadeIn(200)
        }) : jQuery(".widget ul#collections-list").fadeOut(200, function() {
            var b = "<p>" + a[1] + "</p>";
            jQuery(".widget ul#collections-list").html(b), jQuery(".widget ul#collections-list").fadeIn(200)
        })
    }
    jQuery(document).ready(function() {
        jQuery(".widget div#collections-list-options a").on("click", function() {
            var a = this;
            return jQuery(a).addClass("loading"), jQuery(".widget div#collections-list-options a").removeClass("selected"), jQuery(this).addClass("selected"), jQuery.post(ajaxurl, {
                action: "widget_bpcp_pro_collections",
                cookie: encodeURIComponent(document.cookie),
                _wpnonce: jQuery("input#_wpnonce-collections").val(),
                "max-collections": jQuery("input#collections_widget_max").val(),
                filter: jQuery(this).attr("id")
            }, function(b) {
                jQuery(a).removeClass("loading"), collection_widget_response(b)
            }), !1
        })
    });

    // get query string values by name
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }


    // empty check/validation
    function validation_empty(selector, message) {
        jQuery(selector).removeClass('error');
        if (jQuery(selector).val() == '' || jQuery(selector).val() == null ) {
            jQuery(selector).addClass('error');
            jQuery(selector).after('<small class="error">' + message + '</small>');
            return false;
        }
        else {
            return true;
        }
    }

    // email validation
    function validateEmail(selector, message) {
        jQuery(selector).removeClass('error');
        var email = jQuery(selector).val();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if (!emailReg.test(email) || jQuery(selector).val() == '') {
            jQuery(selector).addClass('error');
            jQuery(selector).after('<small class="error">' + message + '</small>');
            return false;
        } else {
            return true;
        }
    }

    //for adding new tags
    if(jQuery('#project_tags').length) {
        jQuery( '#project_tags' ).selectize( {
            plugins: [ 'remove_button' ],
            delimiter: ',',
            persist: false,
            create: function ( input ) {
                return {
                    value: input,
                    text: input
                };
            }
        } );
    }

    //for adding new tags
    if(jQuery('#wip_tags').length) {
        jQuery( '#wip_tags' ).selectize( {
            plugins: [ 'remove_button' ],
            delimiter: ',',
            persist: false,
            create: function ( input ) {
                return {
                    value: input,
                    text: input
                };
            }
        } );
    }

    // multiselect
    if(jQuery('.bp-category-multiselect').length) {
        jQuery('.bp-category-multiselect').select2({
          placeholder: bpcp_object.multiselect_placeholder
        });
    }

    // filter work
    jQuery('.bp-work-filter').change(function() {
        window.location.href = $(this).val();
    });

});





/*
    Elfsight PDF Embed
    Version: 1.0.1
    Release date: Thu Aug 15 2019

    https://elfsight.com

    Copyright (c) 2019 Elfsight, LLC. ALL RIGHTS RESERVED
*/

!function(e,t){"use strict";var i,a,n,s,o,r,d,l,g,p,c,f=!1;function h(){this.pages=[]}h.prototype={constructor:h,add:function(t,i){if(!t||!i||!e.isFunction(i))return!1;this.pages[t]=i()||{}},get:function(e){return this.pages[e]||!1},show:function(t,i){var r,g=this,p=this.get(t);return!!p&&("init"in p&&e.isFunction(p.init)&&(r=p.init(i)),e.when(r).then(function(){d.hasClass("elfsight-admin-other-products-hidden-permanently")||d.toggleClass("elfsight-admin-other-products-hidden","widgets"!==t),a.removeClass("elfsight-admin-loading"),s.removeClass("active"),s.filter("[data-elfsight-admin-page="+t+"]").addClass("active"),l.length&&l.removeClass("elfsight-admin-page-active"),l=n.filter("[data-elfsight-admin-page-id="+t+"]"),o.css("min-height",l.outerHeight()),l.addClass("elfsight-admin-page-animation elfsight-admin-page-active"),setTimeout(function(){l.removeClass("elfsight-admin-page-animation"),"function"==typeof g.onPageChanged&&g.onPageChanged(t)},200)}),p)}};var m=new h;function u(){this.popups=[]}t.elfsightAdminPagesController=m,u.prototype={constructor:u,add:function(t,i){if(!t||!i||!e.isFunction(i))return!1;this.popups[t]=i()||{}},get:function(e){return this.popups[e]||!1},hide:function(e){(g=r.filter("[data-elfsight-admin-popup-id="+e+"]")).removeClass("elfsight-admin-popup-active")},show:function(t,i){var a,n=this.get(t);return!!n&&("init"in n&&e.isFunction(n.init)&&(a=n.init(i)),e.when(a).then(function(){g.length&&g.removeClass("elfsight-admin-popup-active"),(g=r.filter("[data-elfsight-admin-popup-id="+t+"]")).addClass("elfsight-admin-popup-animation elfsight-admin-popup-active"),setTimeout(function(){g.removeClass("elfsight-admin-popup-animation")},200)}),n)}};var v=new u;e(function(){i=e(".elfsight-admin"),a=e(".elfsight-admin-main"),n=e("[data-elfsight-admin-page-id]"),s=e("[data-elfsight-admin-page]"),o=e(".elfsight-admin-pages-container"),r=e("[data-elfsight-admin-popup-id]"),d=e(".elfsight-admin-other-products"),l=e(),g=e(),p=a.attr("data-elfsight-admin-slug"),c=a.attr("data-elfsight-admin-user")?JSON.parse(a.attr("data-elfsight-admin-user")):{},m.add("welcome",e.noop),m.add("widgets",function(){var t=[],i="true"===a.attr("data-elfsight-admin-widgets-clogged"),n=e(".elfsight-admin-page-widgets"),s=e(".elfsight-admin-page-widgets-list",n),o=e(".elfsight-admin-template-widgets-list-item",n),r=e(".elfsight-admin-template-widgets-list-empty",n);s.on("click",".elfsight-admin-page-widgets-list-item-actions-remove",function(t){var i=e(this),a=i.attr("data-widget-id");i.closest(".elfsight-admin-page-widgets-list-item").addClass("elfsight-admin-page-widgets-list-item-removed"),u("remove",{id:a},"post",!0),t.preventDefault()}),s.on("click",".elfsight-admin-page-widgets-list-item-actions-restore a",function(t){var i=e(this),a=i.attr("data-widget-id");i.closest(".elfsight-admin-page-widgets-list-item").removeClass("elfsight-admin-page-widgets-list-item-removed"),u("restore",{id:a},"post",!0),t.preventDefault()}),s.tablesorter({cssAsc:"elfsight-admin-page-widgets-list-sort-asc",cssDesc:"elfsight-admin-page-widgets-list-sort-desc",cssHeader:"elfsight-admin-page-widgets-list-sort",headers:{0:{},1:{},2:{},3:{sorter:!1}}});var d=function(){var i=e("tbody",s).empty();e.isArray(t)&&t.length?(e.each(t,function(t,a){var n=e(o.html()),s="["+p.split("-").join("_")+' id="'+a.id+'"]';n.find(".elfsight-admin-page-widgets-list-item-name a").attr("href","#/edit-widget/"+a.id+"/").text(a.name);var r=new Date(1e3*(a.time_updated||a.time_created));n.find(".elfsight-admin-page-widgets-list-item-date").text(function(e){e instanceof Date||(e=new Date(Date.parse(e)));return["January","February","March","April","May","June","July","August","September","October","November","December"][e.getMonth()]+" "+e.getDate()+", "+e.getFullYear()}(r)),n.find(".elfsight-admin-page-widgets-list-item-shortcode-hidden").text(s);var d=n.find(".elfsight-admin-page-widgets-list-item-shortcode-input").val(s),l=n.find(".elfsight-admin-page-widgets-list-item-shortcode-copy-trigger").attr("data-clipboard-text",s),g=new ClipboardJS(l.get(0));g.on("success",function(){l.addClass("elfsight-admin-page-widgets-list-item-shortcode-copy-trigger-copied").find("span").text("Copied"),setTimeout(function(){l.removeClass("elfsight-admin-page-widgets-list-item-shortcode-copy-trigger-copied").find("span").text("Copy")},5e3)}),g.on("error",function(){var e=n.find(".elfsight-admin-page-widgets-list-item-shortcode-copy-error").show();d.select(),setTimeout(function(){e.hide()},5e3)}),n.find(".elfsight-admin-page-widgets-list-item-shortcode-value").text(s),n.find(".elfsight-admin-page-widgets-list-item-actions-edit").attr("href","#/edit-widget/"+a.id+"/"),n.find(".elfsight-admin-page-widgets-list-item-actions-duplicate").attr("href","#/edit-widget/"+a.id+"/duplicate/"),n.find(".elfsight-admin-page-widgets-list-item-actions-remove, .elfsight-admin-page-widgets-list-item-actions-restore a").attr("data-widget-id",a.id),n.appendTo(i)}),s.trigger("update")):e(r.html()).appendTo(i)};return{init:function(a,n){return u("list").then(function(a){if(a.status){if(t=a.data,!(i||e.isArray(t)&&t.length))return m.show("welcome"),e.Deferred().reject(a).promise();d(),f=!0}})}}}),m.add("edit-widget",function(){var i,a,n,s=e(".elfsight-admin-page-edit-widget"),o=e(".elfsight-admin-page-edit-widget-form-submit",s),r=e(".elfsight-admin-page-edit-widget-form-apply",s),d=e(".elfsight-admin-page-edit-widget-unsaved",s),l=e(".elfsight-admin-page-edit-widget-form-cancel",s),g=e(".elfsight-admin-page-edit-widget-name-input",s),p="elfsight-admin-page-edit-widget-form-editor",h=p+"-clone",w=e("."+h,s).parent(),C=e("."+h,s),S=!1,_=JSON.parse(C.attr("data-elfsight-admin-editor-settings")),x=JSON.parse(C.attr("data-elfsight-admin-editor-preferences")),y=JSON.parse(C.attr("data-elfsight-admin-editor-preview-url")),b=C.attr("data-elfsight-admin-editor-observer-url")||null;b&&(b=JSON.parse(b));var j=function(e){e,d.toggleClass("elfsight-admin-page-edit-widget-unsaved-visible",e),e?t.addEventListener("beforeunload",k):t.removeEventListener("beforeunload",k)},k=function(e){e.preventDefault(),e.returnValue="Widget has unsaved changed"},D=function(t){var n=g.val(),s={};a.getData()?s=a.getData():i&&(s=i.options);var o=i?"update":"add",r={name:n||"Untitled",options:encodeURIComponent(JSON.stringify(s))};i&&(r.id=i.id),u(o,r,"post").done(function(a){a.status&&(a.id&&(i={id:a.id},f=!0,v.popups.rating.open(!0,3e4)),e.isFunction(t)&&t())})};return o.on("click",function(){e("html, body").animate({scrollTop:0}),D(function(){j(!1),hasher.setHash("widgets/")})}),r.on("click",function(){D(),j(!1)}),l.on("click",function(){hasher.setHash("widgets/"),j(!1)}),{init:function(t,o){var r=function(){g.val(i?i.name:""),n&&n.remove(),(n=C.clone().removeClass(h).addClass(p)).appendTo(w),angular.module("elfsightEditor",["elfsightAppsEditor"]).controller("AppController",["$elfsightAppsEditor","$scope","$rootScope","$timeout",function(t,s,o,r){a=t,o.user=c,S=!1;var d={parent:n,previewUrl:y,observerUrl:b||void 0,settings:e.extend(!0,{},_),preferences:x,onChange:function(e){S?j(!0):S=!0}};i&&(d.widget={data:i.options}),t.init(d)}]),n.attr("ng-controller","AppController as app"),angular.bootstrap(n,["elfsightEditor"])};if(t&&t.id)return u("list",{id:t.id}).then(function(a){if(a.status){if(!a.data.length)return m.show("error",{message:"There is no widget with id "+t.id+"."}),e.Deferred().reject(a).promise();i=a.data[0],f=!0,r(),j(!1),s.toggleClass("elfsight-admin-page-edit-widget-new",!!t.duplicate),t.duplicate&&(i=null)}},function(){i=null});i=null,r(),s.addClass("elfsight-admin-page-edit-widget-new")}}}),m.add("support",e.noop),m.add("preferences",function(){var t=e(".elfsight-admin-page-preferences-form"),i=t.attr("data-nonce"),a=function(t,a){var n={action:p.split("-").join("_")+"_update_preferences",nonce:i},s=a.find(".elfsight-admin-page-preferences-option-save");s.addClass("elfsight-admin-page-preferences-option-save-loading"),e.post(ajaxurl,e.extend(n,t),null,"json").done(function(e){var t=a.find(".elfsight-admin-page-preferences-option-save-success"),i=a.find(".elfsight-admin-page-preferences-option-save-error");s.removeClass("elfsight-admin-page-preferences-option-save-loading"),e.success?(i.text(""),t.addClass("elfsight-admin-page-preferences-option-save-success-visible"),setTimeout(function(){t.removeClass("elfsight-admin-page-preferences-option-save-success-visible")},2e3)):e.error&&i.text(e.error)})},n=function(e,t){var i=e.getSession().getScreenLength()*e.renderer.lineHeight+e.renderer.scrollBar.getWidth();t.height(i.toString()+"px"),e.resize()},s=ace.edit("elfsightPreferencesSnippetCSS");s.setOption("useWorker",!1),s.setTheme("ace/theme/monokai"),s.getSession().setMode("ace/mode/css"),s.commands.addCommand({name:"save",bindKey:{win:"Ctrl-S",mac:"Command-S"},exec:function(){var t=e(".elfsight-admin-page-preferences-option-css");a({preferences_custom_css:s.getSession().doc.getValue()},t)}}),n(s,e("#elfsightPreferencesSnippetCSS")),s.getSession().on("change",function(){n(s,e("#elfsightPreferencesSnippetCSS"))});var o=ace.edit("elfsightPreferencesSnippetJS");o.setOption("useWorker",!1),o.setTheme("ace/theme/monokai"),o.getSession().setMode("ace/mode/javascript"),o.commands.addCommand({name:"save",bindKey:{win:"Ctrl-S",mac:"Command-S"},exec:function(){var t=e(".elfsight-admin-page-preferences-option-js");a({preferences_custom_js:o.getSession().doc.getValue()},t)}}),n(o,e("#elfsightPreferencesSnippetJS")),o.getSession().on("change",function(){n(o,e("#elfsightPreferencesSnippetJS"))}),t.find(".elfsight-admin-page-preferences-option-save").click(function(t){t.preventDefault();var i=e(this),n=i.closest(".elfsight-admin-page-preferences-option"),r=i.closest(".elfsight-admin-page-preferences-option-input-container").find('input[type="text"]'),d={};r.each(function(t,i){d[e(i).attr("name")]=e(i).val()}),n.is(".elfsight-admin-page-preferences-option-css")&&(d.preferences_custom_css=s.getSession().doc.getValue()),n.is(".elfsight-admin-page-preferences-option-js")&&(d.preferences_custom_js=o.getSession().doc.getValue()),a(d,n)}),t.find('[name="preferences_force_script_add"]').change(function(){var t=e(this),i=t.closest(".elfsight-admin-page-preferences-option");a({preferences_force_script_add:t.is(":checked")?"on":"off"},i)}),t.find('[name="preferences_access_role"]').change(function(){var t=e(this),i=t.closest(".elfsight-admin-page-preferences-option");a({access_role:t.val()},i)}),t.find('[name="preferences_auto_upgrade"]').change(function(){var t=e(this),i=t.closest(".elfsight-admin-page-preferences-option");a({preferences_auto_upgrade:t.is(":checked")?"on":"off"},i)})}),m.add("activation",function(){var t=e(".elfsight-admin-page-activation-form"),a=e(".elfsight-admin-page-activation-form-purchase-code-input",t),n=e(".elfsight-admin-page-activation-form-activated-input",t),s=e(".elfsight-admin-page-activation-form-supported-until-input",t),o=e(".elfsight-admin-page-activation-form-host-input",t),r=e(".elfsight-admin-page-activation-form-submit",t),d=e(".elfsight-admin-page-activation-form-deactivation",t),l=e(".elfsight-admin-page-activation-form-deactivation-button",t),g=e(".elfsight-admin-page-activation-form-deactivation-confirm-no",t),c=e(".elfsight-admin-page-activation-form-deactivation-confirm-yes",t),f=e(".elfsight-admin-page-activation-form-message-success",t),h=e(".elfsight-admin-page-activation-form-message-error",t),m=e(".elfsight-admin-page-activation-form-message-fail",t),u=e(".elfsight-admin-page-activation-faq"),v=e(".elfsight-admin-page-activation-faq-list-item",u),w=e(".elfsight-admin-page-support-ticket-iframe"),C=w.attr("src"),S=t.attr("data-activation-url"),_=t.attr("data-activation-version"),x=function(i,n,s){e.post(ajaxurl,{action:p.split("-").join("_")+"_update_activation_data",purchase_code:i,supported_until:s||0,activated:n,nonce:t.attr("data-nonce")}),a.prop("readonly",n)};r.click(function(t){t.preventDefault(),t.stopPropagation();var r=a.val(),d=n.val(),l=o.val();e.ajax({url:S,dataType:"jsonp",data:{action:"purchase_code",slug:p+"-cc",host:l,purchase_code:r,version:_}}).done(function(e){e.verification?(d=!!e.verification.valid,n.val(d),s.val(e.verification.supported_until||0),e.verification.valid?(i.removeClass("elfsight-admin-activation-invalid").addClass("elfsight-admin-activation-activated"),h.hide(),m.hide(),f.show(),C=C.replace(/purchase_code=(.*)#/,"purchase_code="+r+"#"),w.attr("src",C)):(i.removeClass("elfsight-admin-activation-activated").toggleClass("elfsight-admin-activation-invalid",!!r),f.hide(),m.hide(),h.text(e.verification.error).show())):d=!1,x(r,d,e.verification.supported_until)}).fail(function(){i.removeClass("elfsight-admin-activation-activated").addClass("elfsight-admin-activation-invalid"),n.val(!1),f.hide(),h.hide(),m.show(),x(r,!1)})}),l.click(function(e){e.preventDefault(),e.stopPropagation(),d.addClass("elfsight-admin-page-activation-form-deactivation-confirm-visible")}),g.click(function(e){e.preventDefault(),e.stopPropagation(),d.removeClass("elfsight-admin-page-activation-form-deactivation-confirm-visible")}),c.click(function(t){t.preventDefault(),t.stopPropagation(),d.removeClass("elfsight-admin-page-activation-form-deactivation-confirm-visible");var r=a.val(),l=(n.val(),o.val());e.ajax({url:S,dataType:"jsonp",data:{action:"deactivate",slug:p+"-cc",host:l,purchase_code:r,version:_}}).done(function(e){a.val(""),n.val("false"),s.val(0),i.removeClass("elfsight-admin-activation-activated"),f.hide(),m.hide(),h.hide(),x("",!1)})}),u.find(".elfsight-admin-page-activation-faq-list-item-question").click(function(){var t=e(this).closest(".elfsight-admin-page-activation-faq-list-item");v.not(t).removeClass("elfsight-admin-page-activation-faq-list-item-active"),t.toggleClass("elfsight-admin-page-activation-faq-list-item-active")})}),m.add("error",function(){var t=e(".elfsight-admin-page-error");return{init:function(i){i&&i.message&&e(".elfsight-admin-page-error-message",t).text(i.message)}}}),v.add("rating",function(){var t=e(".elfsight-admin-header-rating"),i=t.find("input[name=rating-header]"),a=e(".elfsight-admin-popup-rating"),n=a.find("form"),s=a.find("input[name=rating-popup]"),o=a.find(".elfsight-admin-popup-textarea"),r=a.find(".elfsight-admin-popup-text"),d=a.find(".elfsight-admin-popup-footer-button-ok"),l=a.find(".elfsight-admin-popup-footer-button-close"),g=localStorage.getItem("popupRatingShowed")?localStorage.getItem("popupRatingShowed"):Math.floor(Date.now()/1e3),c=parseInt(g)+86400<Math.floor(Date.now()/1e3);const m=(e,t=1e3)=>{setTimeout(function(){if(a.length&&!a.hasClass("elfsight-admin-popup-sent")){const t=!~hasher.getHash().indexOf("edit-widget")&&!~hasher.getHash().indexOf("add-widget");(!e||e&&c&&t&&f)&&v.show("rating"),localStorage.setItem("popupRatingShowed",Math.floor(Date.now()/1e3))}},t)};setTimeout(()=>{f&&t&&(m(!0,3e4),t.slideDown())},5e3),i.on("change",function(){var t=parseInt(e(this).val());m(!1,0),setTimeout(function(){s.filter('[value="'+t+'"]').prop("checked",!0),u(t)},400),e(this).prop("checked",!1)});const u=e=>{d.removeClass("elfsight-admin-popup-footer-button-hide"),o.toggleClass("elfsight-admin-popup-textarea-hide",5===e),r.toggleClass("elfsight-admin-popup-text-hide",e<5)};return s.on("change",function(){u(parseInt(e(this).val()))}),d.on("click",function(i){i.preventDefault();var s=parseInt(n.find('input[name="rating-popup"]:checked').val()),r=n.find("textarea").val(),g={action:p.split("-").join("_")+"_rating_send",nonce:n.attr("data-nonce"),value:s,comment:r};5===s&&h(e(i.target).attr("href")),s<5&&""===r?o.toggleClass("elfsight-admin-popup-textarea-error",!0):(o.toggleClass("elfsight-admin-popup-textarea-error",!1),e.post(ajaxurl,g).then(function(){a.addClass("elfsight-admin-popup-sent"),l.text("OK"),d.addClass("elfsight-admin-popup-footer-button-hide"),t.slideUp(),localStorage.removeItem("popupRatingShowed")}))}),l.on("click",function(){v.hide("rating"),d.addClass("elfsight-admin-popup-footer-button-hide"),o.addClass("elfsight-admin-popup-textarea-hide"),r.addClass("elfsight-admin-popup-text-hide"),s.prop("checked",!1)}),{init:function(e,t){return!0},open:m}});var h=function(e,i){var a=940,n=700,s=["width="+a,"height="+n,"menubar=no","toolbar=no","resizable=yes","scrollbars=yes","left="+(t.screen.availLeft+t.screen.availWidth/2-a/2),"top="+(t.screen.availTop+t.screen.availHeight/2-n/2)];t.open(e,i,s.join(","))},u=function(t,i,n,s){n="post"===n?"post":"get";var o=e.extend({},{action:p.split("-").join("_")+"_widgets_api",endpoint:t},i);return e.ajax({url:ajaxurl,data:o,dataType:"json",type:n,beforeSend:function(){s||a.addClass("elfsight-admin-loading")}}).always(function(){s||a.removeClass("elfsight-admin-loading")}).then(function(t){return t.status?t:(m.show("error",{message:"An error occurred during your request process. Please, try again."}),e.Deferred().reject(t).promise())},function(e){return m.show("error",{message:"An error occurred during your request process. Please, try again."}),e})};if(t.crossroads&&t.hasher){crossroads.addRoute("/add-widget/",function(){m.show("edit-widget")}),crossroads.addRoute("/edit-widget/{id}/",function(e){m.show("edit-widget",{id:e})}),crossroads.addRoute("/edit-widget/{id}/duplicate/",function(e){m.show("edit-widget",{id:e,duplicate:!0})}),crossroads.addRoute("/{page}/",function(e){e&&-1===e.indexOf("!")&&(m.show(e)||m.show("error",{message:"The requested page was not found."}))});var w=function(e,t){crossroads.parse(e)};hasher.initialized.add(w),hasher.changed.add(w),hasher.init(),hasher.getHash()||hasher.setHash("widgets/")}})}(jQuery,window);
!function(e){var t={};function n(i){if(t[i])return t[i].exports;var o=t[i]={i:i,l:!1,exports:{}};return e[i].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,i){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(i,o,function(t){return e[t]}.bind(null,o));return i},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s="./assets/admin/settings.js")}({"./assets/admin/settings.js":
/*!**********************************!*\
  !*** ./assets/admin/settings.js ***!
  \**********************************/
/*! no static exports found */function(e,t){var n;window.storeabill=window.storeabill||{},window.storeabill.admin=window.storeabill.admin||{},n=jQuery,window.storeabill.admin.settings={params:{},timeout:null,init:function(){storeabill.admin.settings.params=storeabill_admin_settings_params,n(document).on("click",".sab-oauth-disconnect-button",this.onSyncHandlerDisconnect).on("click",".sab-oauth-refresh-button, .sab-oauth-button",this.maybeShowCode).on("change keydown paste input",".sab-oauth-wrapper .authorization-code input[type=text]",this.onChangeCode).on("click",".sab-oauth-submit-code",this.onSubmitCode).on("change",".sab-input-unblock input[type=checkbox]",this.onEnableEditMode).on("change keydown paste input",".sab-number-preview-trigger",this.onPreviewDocumentNumber)},onPreviewDocumentNumber:function(){var e=storeabill.admin.settings,t=n(".sab-number-preview");clearTimeout(e.timeout),e.timeout=setTimeout((function(){t.addClass("loading");var i={last_number:n(".sab-number-preview-last-number").val(),document_type:t.data("document-type"),number_min_size:n(".sab-number-preview-number-min-size").val(),number_format:n(".sab-number-preview-number-format").val(),security:e.params.preview_number_nonce,action:"storeabill_admin_preview_formatted_document_number"};n.ajax({type:"POST",url:e.params.ajax_url,data:i,success:function(e){t.removeClass("loading"),e.success&&t.find(".sab-number").html(e.preview)},error:function(e){},dataType:"json"})}),500)},onEnableEditMode:function(){var e=n(this).parents(".sab-input-unblock-wrapper");return n(this).is(":checked")?e.find("input.sab-input-to-unblock").prop("disabled",!1):e.find("input.sab-input-to-unblock").prop("disabled",!0),!1},onChangeCode:function(){n(this).val().length>0?n(this).parents(".authorization-code").find(".sab-oauth-submit-code").show():n(this).parents(".authorization-code").find(".sab-oauth-submit-code").hide()},onSubmitCode:function(e){return e.preventDefault(),n(this).parents("form").find("[type=submit]").trigger("click"),!1},onSyncHandlerDisconnect:function(e){var t=storeabill.admin.settings,i=n(this),o=i.parents(".sab-oauth-connected").find("input.sab-oauth-disconnect-input");e.preventDefault(),window.confirm(t.params.i18n_oauth_disconnect_notice)&&(o.val("yes"),i.parents("form").find("[type=submit]").trigger("click"))},maybeShowCode:function(e){storeabill.admin.settings;var t=n(this).parents(".sab-oauth-wrapper");t.find(".authorization-code").length>0&&t.find(".authorization-code").show()}},n(document).ready((function(){storeabill.admin.settings.init()}))}});
!function(e){var t={};function a(n){if(t[n])return t[n].exports;var r=t[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,a),r.l=!0,r.exports}a.m=e,a.c=t,a.d=function(e,t,n){a.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.t=function(e,t){if(1&t&&(e=a(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(a.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)a.d(n,r,function(t){return e[t]}.bind(null,r));return n},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,"a",t),t},a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.p="",a(a.s="./assets/admin/bulk-actions.js")}({"./assets/admin/bulk-actions.js":
/*!**************************************!*\
  !*** ./assets/admin/bulk-actions.js ***!
  \**************************************/
/*! no static exports found */function(e,t){var a;window.storeabill=window.storeabill||{},window.storeabill.admin=window.storeabill.admin||{},a=jQuery,window.storeabill.admin.bulk_actions={params:{},init:function(){var e=storeabill.admin.bulk_actions;e.params=storeabill_admin_bulk_actions_params,a(document).on("click","#doaction, #doaction2",e.onBulkSubmit)},getCurrentSort:function(e){var t="desc",a=e,n=storeabill.admin.bulk_actions.getForm().find("table.wp-list-table thead").find("th[class*='"+a+"']").first();return n.length>0&&n.hasClass("asc")&&(t="asc"),t},onBulkSubmit:function(){var e=storeabill.admin.bulk_actions,t=a(this).parents(".bulkactions").find("select[name^=action]").val(),n=a(this).parents("#posts-filter").length>0?a(this).parents("#posts-filter"):a(this).parents("#wc-orders-filter"),r=e.params.hasOwnProperty("object_type")?e.params.object_type:n.find("input[name="+e.params.object_input_type_name+"]").val(),s=[];if(e.getForm().find('input[name="'+e.getInputIdName()+'[]"]:checked').each((function(){s.push(a(this).val())})),e.params.bulk_actions.hasOwnProperty(t)&&s.length>0){var i=e.params.bulk_actions[t];return"desc"===e.getCurrentSort(i.id_order_by_column)&&i.parse_ids_ascending&&(s=s.reverse()),a(".sab-bulk-action-wrapper").find(".bulk-title").text(i.title),a(".sab-bulk-action-wrapper").addClass("processing"),a(".sab-bulk-action-wrapper").parents(".tablenav").addClass("sab-bulk-action-running"),e.getForm().find(".bulkactions button").prop("disabled",!0).addClass("disabled"),e.handleBulkAction(t,1,s,r),!1}},getInputIdName:function(){var e=storeabill.admin.bulk_actions;return a('input[name="'+e.params.table_type+'[]"]').length>0?e.params.table_type:"id"},getForm:function(){var e=storeabill.admin.bulk_actions;return a('input[name="'+e.getInputIdName()+'[]"]:checked').parents("form")},handleBulkAction:function(e,t,n,r){var s=storeabill.admin.bulk_actions,i=s.params.bulk_actions[e];a.ajax({type:"POST",url:s.params.ajax_url,data:{action:"storeabill_admin_handle_bulk_action",bulk_action:e,step:t,type:r,reference_type:s.params.hasOwnProperty("reference_type")?s.params.reference_type:"",ids:n,security:i.nonce},dataType:"json",success:function(t){t.success?"done"===t.step?(a(".sab-bulk-action-wrapper").find(".sab-bulk-progress").val(t.percentage),window.location=t.url,setTimeout((function(){a(".sab-bulk-action-wrapper").removeClass("processing"),a(".sab-bulk-action-wrapper").parents(".tablenav").removeClass("sab-bulk-action-running"),s.getForm().find(".bulkactions button").prop("disabled",!1).removeClass("disabled")}),2e3)):(a(".sab-bulk-action-wrapper").find(".sab-bulk-progress").val(t.percentage),s.handleBulkAction(e,parseInt(t.step,10),t.ids,t.type)):(a(".sab-bulk-notice-wrapper").find(".notice").remove(),a(".sab-bulk-action-wrapper").removeClass("processing"),a(".sab-bulk-action-wrapper").parents(".tablenav").removeClass("sab-bulk-action-running"),s.getForm().find(".bulkactions button").prop("disabled",!1).removeClass("disabled"),t.hasOwnProperty("messages")&&a.each(t.messages,(function(e,t){a(".sab-bulk-notice-wrapper").append('<div class="notice is-dismissible notice-error"><p>'+t+'</p><button type="button" class="notice-dismiss"></button></div>')})))}}).fail((function(e){window.console.log(e)}))}},a(document).ready((function(){storeabill.admin.bulk_actions.init()}))}});
!function(){var e,t;window.germanized=window.germanized||{},e=jQuery,(t=window.germanized).multistep_checkout_payment_compatibility={params:{},init:function(){this.params=wc_gzdp_multistep_checkout_payment_compatibility_params,e(document).on("click",".next-step-button",this.onClickNextStep).on("click",".prev-step-button",this.onClickPrevStep).on("refresh",".step-wrapper",this.onClickNextStep),e(document.body).on("wc_gzdp_step_changed",this.onStepChanged).on("updated_checkout",this.onUpdateCheckout),e("form.checkout").on("checkout_place_order",this.onCheckoutPlaceOrderEvent)},onCheckoutPlaceOrderEvent:function(){var c=t.multistep_checkout_payment_compatibility;"payment"===e(".step-wrapper-active").data("id")&&c.needsPaymentStepBlock()&&e(document.body).on("checkout_error",(function(){"payment"===e(".step-wrapper-active").data("id")&&!t.multistep_checkout.checkoutHasErrors()&&c.needsPaymentStepBlock()&&(e(".woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message").remove(),e(".step-order").trigger("change",e(".step-order")))}))},needsPaymentStepBlock:function(){var c=t.multistep_checkout_payment_compatibility,a=c.getCurrentPaymentMethod();return!1!==a&&-1!==e.inArray(a,c.params.extended_gateways)},getCurrentPaymentMethod:function(){t.multistep_checkout_payment_compatibility;var c=e("form.checkout").find("input[name=payment_method]:checked");return c.length>0&&c.val()},isActivated:function(){var c=t.multistep_checkout_payment_compatibility,a=c.getCurrentPaymentMethod(),o=Boolean(Number(c.params.force_enable));return!1!==a&&(-1!==e.inArray(a,c.params.gateways)||"placeholder"===a||!0===o)},maybeAddPlaceholderCheckbox:function(c){var a=t.multistep_checkout_payment_compatibility,o=(e(".step-wrapper-active"),e("form.checkout"));o.find(".wc-gzdp-cc-terms-placeholder").length>0&&o.find(".wc-gzdp-cc-terms-placeholder").remove(),a.isActivated()&&("payment"===c?o.prepend('<input class="wc-gzdp-cc-terms-placeholder" type="checkbox" name="terms" value="1" style="display: none" checked />'):"order"===c&&o.find(".wc-gzdp-cc-terms-placeholder").remove())},onUpdateCheckout:function(){var c=t.multistep_checkout_payment_compatibility,a=e(".step-wrapper-active"),o=a.data("id"),n=e("form.checkout");c.maybeInitPaymentPlaceholders(o),c.isActivated()&&"address"===o&&(n.find(".wc-gzdp-payment-method-placeholder").prop("checked",!0),n.find(".wc-gzdp-payment-method-placeholder").trigger("click")),c.maybeAddPlaceholderCheckbox(a.data("id"))},onStepChanged:function(){var c=t.multistep_checkout_payment_compatibility,a=e(".step-wrapper-active").data("id");c.maybeInitPaymentPlaceholders(a),c.maybeAddPlaceholderCheckbox(a)},maybeInitPaymentPlaceholders:function(c){var a=t.multistep_checkout_payment_compatibility,o=e("input[name=payment_method]:not(.wc-gzdp-payment-method-placeholder):checked"),n=e("form.checkout");if(a.isActivated())if("address"===c){var i=o.length>0?o.attr("id"):"";0===n.find(".wc-gzdp-payment-method-placeholder").length?n.append('<input type="radio" style="display: none;" name="payment_method" data-current="'+i+'" id="payment_method_wc_gzdp_payment_method_placeholder" class="wc-gzdp-payment-method-placeholder" value="placeholder" checked="checked" />'):i&&void 0!==i&&n.find(".wc-gzdp-payment-method-placeholder").data("current",i)}else if(n.find(".wc-gzdp-payment-method-placeholder").length>0){var d=n.find(".wc-gzdp-payment-method-placeholder").data("current");d&&void 0!==d&&n.find("input#"+d).length>0&&(n.find("input#"+d).prop("checked",!0),n.find("input#"+d).trigger("click")),n.find(".wc-gzdp-payment-method-placeholder").remove()}},onClickPrevStep:function(){var c=t.multistep_checkout_payment_compatibility,a=e(this).data("href");c.maybeInitPaymentPlaceholders(a),c.maybeAddPlaceholderCheckbox(a)},onClickNextStep:function(c){var a=e(".step-wrapper-active"),o=t.multistep_checkout_payment_compatibility,n=a.data("id");o.maybeInitPaymentPlaceholders(n),o.maybeAddPlaceholderCheckbox(n),o.isActivated()&&"payment"===n&&(e("#place_order").trigger("click"),c.preventDefault())}},e(document).ready((function(){t.multistep_checkout_payment_compatibility.init()})),((window.germanizedPro=window.germanizedPro||{}).static=window.germanizedPro.static||{})["checkout-multistep-payment-compatibility"]={}}();
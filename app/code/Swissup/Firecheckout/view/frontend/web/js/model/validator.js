define([
    'jquery',
    'ko',
    'uiRegistry',
    'Swissup_Firecheckout/js/model/layout',
    'Swissup_Checkout/js/scroll-to-error',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/translate'
], function (
    $,
    ko,
    registry,
    layout,
    scrollToError,
    quote,
    customer,
    paymentValidators,
    $t
) {
    'use strict';

    return {
        /**
         * Validate firecheckout form
         */
        validate: function () {
            var isShippingSelected = layout.isMultistep() || this.validateShippingRadios(),
                isPaymentSelected  = this.validatePayment(),
                isAddressValid     = layout.isMultistep() || this.validateShippingAddress(),
                event = $.Event('fc:validate', {
                    valid: true
                });

            $('body').trigger(event);

            // try to scroll to third-party message
            setTimeout(scrollToError, 100);

            return event.valid &&
                isShippingSelected &&
                isPaymentSelected &&
                isAddressValid;
        },

        /**
         * @deprecated
         */
        scrollToError: function () {
            scrollToError();
        },

        /**
         * @return {Boolean}
         */
        validateShippingAddress: function () {
            if (quote.isVirtual() || !$('.form-shipping-address:visible').length) {
                return true;
            }

            return registry
                .get('checkout.steps.shipping-step.shippingAddress')
                .validateShippingInformation();
        },

        /**
         * Check is shipping radio is selected
         */
        validateShippingRadios: function () {
            var el = $('#co-shipping-method-form');

            if (!el.length) {
                return true;
            }

            this.removeNotice(el);

            if (!quote.shippingMethod() || typeof quote.shippingMethod() !== 'object') {
                this.addNotice(el, $t('Please specify a shipping method.'));

                return false;
            }

            return true;
        },

        /**
         * Check is payment is valid
         */
        validatePayment: function () {
            var el = $('#co-payment-form .payment-methods'),
                payment = quote.paymentMethod(),
                paymentComponent,
                form;

            if (!el.length) {
                return true;
            }

            this.removeNotice(el);

            if (!payment || typeof payment !== 'object') {
                this.addNotice(
                    el,
                    $t('Please specify a payment method.'),
                    el.find('.step-title')
                );

                return false;
            }

            paymentComponent = registry.get(
                'checkout.steps.billing-step.payment.payments-list.' + payment.method
            );

            if ((paymentComponent && !paymentComponent.validate()) ||
                !paymentValidators.validate()) {

                return false;
            }

            form = $('.payment-method._active form');

            if (form.length && form.validation && !form.validation().valid()) {
                return false;
            }

            return true;
        },

        /**
         * Add notice message at the top of the element
         *
         * @param {jQuery} el
         * @param {String} msg
         */
        addNotice: function (el, msg, after) {
            var method = 'prepend';

            if (after) {
                el = after;
                method = 'after';
            }

            el[method](
                '<div class="firecheckout-msg message notice"><span>' +
                    msg +
                '</span></div>'
            );
        },

        /**
         * Remove notice label
         *
         * @param {jQuery} el
         */
        removeNotice: function (el) {
            $('.firecheckout-msg', el).remove();
        }
    };
});

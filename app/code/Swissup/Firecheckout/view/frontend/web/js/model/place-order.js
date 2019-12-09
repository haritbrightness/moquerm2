define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Swissup_Firecheckout/js/model/layout',
    'Swissup_Firecheckout/js/model/validator',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/payment-service'
], function (
    $,
    quote,
    layout,
    validator,
    setShippingInformationAction,
    paymentService
) {
    'use strict';

    /**
     * @param  {Object} result
     * @return {Object}
     */
    function submitShippingInformationCallback(result) {
        $('body').trigger($.Event('fc:placeOrderSetShippingInformationAfter', {
            response: result
        }));

        delete paymentService.doNotUpdate;

        return result;
    }

    return {
        /**
         * Place Order method
         */
        placeOrder: function () {
            var event;

            if (!validator.validate()) {
                return false;
            }

            event = $.Event('fc:placeOrderBefore', {
                cancel: false
            });
            $('body').trigger(event);

            // allow to interrupt the process
            if (event.cancel) {
                return;
            }

            if (layout.isMultistep()) {
                this._place();
            } else {
                $.when(this.submitShippingInformation()).done(this._place);
            }
        },

        /**
         * Click hidden "Place Order" button in payment section
         */
        _place: function () {
            $(
                [
                    '.actions-toolbar:not([style="display: none;"])',
                    '.action.checkout:not([style="display: none;"])'
                ].join(' '),
                '.payment-method._active'
            ).click();

            // try to call button click method directly, without .click() emulation

            $('body').trigger('fc:placeOrderAfter');
        },

        /**
         * @return {Deferred|Boolean}
         */
        submitShippingInformation: function () {
            if (!quote.isVirtual() && quote.shippingMethod()) {
                paymentService.doNotUpdate = true;

                $('body').trigger($.Event('fc:placeOrderSetShippingInformationBefore'));

                return setShippingInformationAction()
                    .then(
                        submitShippingInformationCallback,
                        submitShippingInformationCallback
                    );
            }

            return true;
        }
    };
});

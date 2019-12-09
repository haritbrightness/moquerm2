define(
    [
        'jquery',
        'mage/storage',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/get-payment-information',
        'Swissup_CheckoutCart/js/action/update-summary-heading',
        'Swissup_Checkout/js/action/update-shipping-rates'
    ],
    function (
        $,
        storage,
        customerData,
        resourceUrlManager,
        errorProcessor,
        fullScreenLoader,
        getPaymentInformationAction,
        updateSummaryHeadingAction,
        updateShippingRatesAction
    ) {
        'use strict';

        return function(quote, params) {
            fullScreenLoader.startLoader();

            return storage.post(
                resourceUrlManager.getUrlForUpdateCartItems(quote),
                JSON.stringify(params)
            ).done(
                function(response) {
                    // reload shipping methods
                    updateShippingRatesAction();

                    // reload payment methods and totals
                    var deferred = $.Deferred();
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function() {
                        // update summary block heading quantity
                        var totalQty = quote.totals().items_qty;
                        updateSummaryHeadingAction(totalQty);

                        // invalidate shopping cart
                        customerData.invalidate(['cart']);

                        fullScreenLoader.stopLoader();
                    });
                }
            ).fail(
                function(response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);

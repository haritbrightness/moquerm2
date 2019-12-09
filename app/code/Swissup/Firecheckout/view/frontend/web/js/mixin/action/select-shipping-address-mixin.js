define([
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Swissup_Firecheckout/js/action/set-shipping-address'
], function (_, wrapper, quote, setShippingAddressAction) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }

        /**
         * Reload payment methods when sensitive shipping fields are updated.
         */
        return wrapper.wrap(target, function (originalAction, shippingAddress) {
            var result = originalAction(shippingAddress),
                keysToCompare = ['postcode', 'countryId', 'region', 'regionId'],
                previousValues = false,
                currentValues = false;

            if (quote.firecheckout && quote.firecheckout.memo.shippingAddress) {
                previousValues = _.pick(quote.firecheckout.memo.shippingAddress, keysToCompare);
                currentValues = _.pick(shippingAddress, keysToCompare);
            }

            // save shipping address to get updated payment methods
            if (-1 !== checkoutConfig.swissup.firecheckout.dependencies.payment.indexOf('address')) {
                if (previousValues && !_.isEqual(currentValues, previousValues)) {
                    setShippingAddressAction();
                }
            }

            return result;
        });
    };
});

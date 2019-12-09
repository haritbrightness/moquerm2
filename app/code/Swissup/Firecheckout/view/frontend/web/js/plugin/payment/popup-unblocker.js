define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function ($, _, quote) {
    'use strict';

    var config,
        currentPopup;

    /**
     * Opens popup for selected payment method if needed
     */
    function open() {
        var payment = quote.paymentMethod(),
            settings,
            width, height,
            left, top;

        if (!payment) {
            return;
        }

        settings = _.extend({}, _.find(config, function (item) {
            return item.paymentCode === payment.method;
        }));

        if (!settings || !settings.windowName) {
            return;
        }

        if (settings.center && settings.windowFeatures) {
            width = settings.windowFeatures.match(/width=(\d+)/);
            height = settings.windowFeatures.match(/height=(\d+)/);

            if (width[1] && height[1]) {
                left = parseInt(window.outerWidth / 2 - width[1] / 2, 10);
                top = parseInt(window.outerHeight / 2 - height[1] / 2, 10);

                settings.windowFeatures += ',left=' + left + ',top=' + top;
            }
        }

        currentPopup = window.open('', settings.windowName, settings.windowFeatures);
        currentPopup.document.write($.mage.__('Please wait'));
    }

    /**
     * Closes opened popup, if shipping wasn't saved
     *
     * @param {jQuery.Event} e
     */
    function closeIfFailed(e) {
        var isSuccess = true;

        if (e.response && e.response.status) {
            isSuccess = e.response.status >= 200 &&
                e.response.status < 300 ||
                e.response.status === 304;
        }

        if (!isSuccess && currentPopup) {
            currentPopup.close();
        }
    }

    /**
     * Closes opened popup, if it doesn't loaded yet.
     */
    function closeIfNotLoaded() {
        var href;

        if (!currentPopup) {
            return;
        }

        try {
            href = currentPopup.location.href;
        } catch (e) {
            href = 'loaded';
        }

        if (!href || href === 'about:blank') {
            currentPopup.close();
        }
    }

    return {
        /**
         * @param {Object} settings
         */
        init: function (settings) {
            if (!settings) {
                return;
            }

            config = settings;

            $('body').on('fc:placeOrderSetShippingInformationBefore', open);
            $('body').on('fc:placeOrderSetShippingInformationAfter', closeIfFailed);
            $('body').on('fc:placeOrderAfter', function () {
                setTimeout(closeIfNotLoaded, 2000);
            });
        }
    };
});

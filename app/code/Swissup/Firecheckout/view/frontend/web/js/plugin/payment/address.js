define([
    'Magento_Ui/js/lib/view/utils/async',
    'Magento_Checkout/js/model/quote',
    'Swissup_Firecheckout/js/utils/move'
], function ($, quote, move) {
    'use strict';

    var settings = {
            el: '.checkout-billing-address',
            title: '',
            position: 0
        },
        position = {
            'before-payment-methods': {
                el: '.opc-payment',
                method: 'before'
            },
            'before-shipping-address': {
                el: '.checkout-shipping-address',
                method: 'prepend'
            },
            'after-shipping-address': {
                el: '#checkout-step-shipping',
                method: 'after',
                disabled: quote.isVirtual()
            }
        };

    return {
        /**
         * Plugin initialization
         */
        init: function (config, positionRules) {
            settings = $.extend({}, settings, config);
            position = $.extend({}, position, positionRules);

            this.addTitle();
            this.move();
        },

        /**
         * Add title above billign address
         */
        addTitle: function () {
            if (!settings.title) {
                return;
            }

            $.async(settings.el, function (el) {
                if ($(el).closest('.payment-method-content').length) {
                    return;
                }

                $(el).prepend('<div class="step-title">' + settings.title + '</div>');
            });
        },

        /**
         * Move billing address as configured
         */
        move: function () {
            var rule = position[settings.position];

            if (!rule || rule.disabled) {
                return;
            }

            move(settings.el)[rule.method](rule.el);
        }
    };
});

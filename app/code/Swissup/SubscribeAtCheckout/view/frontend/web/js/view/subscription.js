define([
    'Magento_Ui/js/lib/view/utils/async',
    'Magento_Ui/js/form/element/boolean'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Swissup_SubscribeAtCheckout/form/element/subscription'
        },

        /**
         * Initialize component
         */
        initialize: function () {
            var self = this;

            this._super();

            // Move subscription to the login section for better compatiblity
            // with store-pickup modules that hide shipping section
            $.async('#customer-email-fieldset', function (emailFieldset) {
                $.async('#' + self.uid, function (subscription) {
                    $(emailFieldset).append($(subscription).closest('.subscription').get(0));
                });
            });
        }
    });
});

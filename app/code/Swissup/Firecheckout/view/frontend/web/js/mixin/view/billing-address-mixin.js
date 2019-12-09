define([], function () {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        updateTriggered = false;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }

        return target.extend({
            /**
             * Mark updateTriggered flag. Used in updateAddresses method.
             */
            updateAddress: function () {
                updateTriggered = true;
                this._super();
            },

            /**
             * Don't save billing address when it has invalid fields.
             *
             * Fixes ajax reload when address is rendered outside of
             * payment method container.
             */
            updateAddresses: function () {
                if (updateTriggered) {
                    updateTriggered = false;

                    if (this.source.get('params.invalid')) {
                        return;
                    }
                }

                this._super();
            }
        });
    };
});

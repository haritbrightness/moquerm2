define([
    'jquery',
    'mage/utils/wrapper',
    'Swissup_Firecheckout/js/model/storage-sequence'
], function ($, wrapper, sequence) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    /**
     * This mixin is written to cover all third-party payment modules.
     * No one uses standard actions like Magento_Checkout/js/action/place-order
     * or Magento_Checkout/js/action/set-payment-information.
     *
     * Each third-party method 'invents' its own submit logic, that is why
     * we wrote such a global mixin.
     *
     * @param  {Object} target
     * @return {Object}
     */
    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }

        /**
         * Runs sequence before and after original action
         *
         * @param  {Function} o
         * @param  {String} url
         */
        function _proxy(o, url) {
            var args = arguments,
                wrappedOriginal,
                wrappedAfter;

            if (sequence.has(url)) {
                /**
                 * Wrapped original function to use in `then` chain
                 * @return {Promise}
                 */
                wrappedOriginal = function () {
                    return o.apply(
                        target,
                        Array.prototype.slice.call(args, 1)
                    );
                };

                /**
                 * Wrapped sequence.run to use in `then` chain
                 * @param  {Mixed} result
                 * @return {Promise}
                 */
                wrappedAfter = function (result) {
                    sequence.run(url, 'after', result);

                    return result;
                };

                return $.when.apply($, sequence.run(url, 'before'))
                    .then(wrappedOriginal, wrappedOriginal)
                    .then(wrappedAfter, wrappedAfter);
            }

            return o.apply(
                target,
                Array.prototype.slice.call(args, 1)
            );
        }

        // Wrap all methods into _proxy call
        target.get    = wrapper.wrap(target.get, _proxy);
        target.post   = wrapper.wrap(target.post, _proxy);
        target.put    = wrapper.wrap(target.put, _proxy);
        target.delete = wrapper.wrap(target.delete, _proxy);

        return target;
    };
});

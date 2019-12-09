define([
    'jquery',
    'mage/url',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'Magento_Checkout/js/model/quote',
    'Swissup_CheckoutCart/js/action/update-cart',
    'Swissup_CheckoutCart/js/action/remove-item'
], function ($, urlBuilder, $t, modalConfirm, quote, updateCartAction, removeItemAction) {
    'use strict';

    var checkoutConfig = window.checkoutConfig.swissup.CheckoutCart;

    return function (target) {
        if (!checkoutConfig.enabled) {
            return target;
        }

        //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
        return target.extend({
            /**
             * @param {Object} item
             */
            incQty: function (item) {
                this.applyQty(item.item_id, item.qty + 1);
            },

            /**
             * @param  {Object} item
             */
            decQty: function (item) {
                if (item.qty - 1 === 0) {
                    this.removeItem(item);
                } else {
                    this.applyQty(item.item_id, item.qty - 1);
                }
            },

            /**
             * @param  {Object} item
             * @param  {Object} event
             */
            newQty: function (item, event) {
                var quoteItem = this.getQuoteItemById(item.item_id);

                if (item.qty == 0) { // eslint-disable-line eqeqeq
                    this.removeItem(item, event);
                } else if (this.isValidQty(quoteItem.qty, item.qty)) {
                    this.applyQty(item.item_id, item.qty);
                } else {
                    item.qty = quoteItem.qty;
                    $(event.target).val(item.qty);
                }
            },

            /**
             * @param {Number} itemId
             * @param {Number} qty
             */
            applyQty: function (itemId, qty) {
                var params = {
                    cartItem: {
                        item_id: itemId,
                        qty: qty,
                        quote_id: quote.getQuoteId()
                    }
                };

                this.getQuoteItemById(itemId).qty = qty;

                updateCartAction(quote, params);
            },

            /**
             * @param {Object} item
             * @param {Object} event
             */
            removeItem: function (item, event) {
                var quoteItem = this.getQuoteItemById(item.item_id);

                modalConfirm({
                    content: $t('Are you sure you want to remove this item?'),
                    actions: {
                        /**
                         * Remove item from cart
                         */
                        confirm: function () {
                            removeItemAction(quote, item.item_id);
                        },

                        /**
                         * Cancel action
                         */
                        cancel: function () {
                            if (event) {
                                item.qty = quoteItem.qty;
                                $(event.target).val(item.qty);
                            }
                        }
                    }
                });
            },

            /**
             * @param  {Number} origin
             * @param  {Number} changed
             * @return {Boolean}
             */
            isValidQty: function (origin, changed) {
                return origin != changed && // eslint-disable-line eqeqeq
                    changed.length > 0 &&
                    changed - 0 == changed && // eslint-disable-line eqeqeq
                    changed - 0 > 0;
            },

            /**
             * @param  {Number} itemId
             * @return {Object}
             */
            getQuoteItemById: function (itemId) {
                return $.grep(quote.getItems(), function (item) {
                    return item.item_id == itemId; // eslint-disable-line eqeqeq
                })[0];
            },

            /**
             * @param  {Number} itemId
             * @return {Boolean}
             */
            productLinkEnabled: function (itemId) {
                var quoteItem = this.getQuoteItemById(itemId);

                return quoteItem.product.request_path &&
                    checkoutConfig.productLinkEnabled;
            },

            /**
             * @param  {Number} itemId
             * @return {String}
             */
            getProductHref: function (itemId) {
                var quoteItem = this.getQuoteItemById(itemId);

                return urlBuilder.build(quoteItem.product.request_path);
            }
        });
        //jscs:enable requireCamelCaseOrUpperCaseIdentifiers
    };
});

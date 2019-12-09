define([
    'Magento_Ui/js/lib/view/utils/async',
    'underscore'
], function ($, _) {
    'use strict';

    /**
     * @param  {String|Element}   selector1
     * @param  {String|Element}   selector2
     * @param  {Function} callback
     */
    function wait(selector1, selector2, callback) {
        if (typeof selector1 === 'string') {
            $.async(selector1, function (element1) {
                wait(element1, selector2, callback);
            });
        } else if (typeof selector2 === 'string') {
            $.async(selector2, function (element2) {
                callback(selector1, element2);
            });
        } else {
            callback(selector1, selector2);
        }
    }

    /**
     * @param {Element} source
     * @param {Element} destination
     * @param {Number} sortOrder
     * @param {String} method
     */
    function insert(source, destination, sortOrder, method) {
        var key = 'fc-moved-' + method,
            data = $(destination).data(key) || [],
            item = {
                el: source,
                sortOrder: _.isNumber(sortOrder) ? sortOrder : 100
            },
            index = _.sortedIndex(data, item, 'sortOrder');

        if (index > 0) {
            $(data[index - 1].el).after(source);
        } else if (data.length) {
            $(data[0].el).before(source);
        } else {
            $(destination)[method](source);
        }

        data.splice(index, 0, item);
        $(destination).data(key, data);
    }

    /**
     * @param  {String|Element} source
     * @return {Object}
     */
    return function (source) {
        return {
            /**
             * @param {String|Element} destination
             * @param {Number} sortOrder
             */
            before: function (destination, sortOrder) {
                wait(source, destination, function (sourceEl, destinationEl) {
                    insert(sourceEl, destinationEl, sortOrder, 'before');
                });
            },

            /**
             * @param {String|Element} destination
             * @param {Number} sortOrder
             */
            after: function (destination, sortOrder) {
                wait(source, destination, function (sourceEl, destinationEl) {
                    insert(sourceEl, destinationEl, sortOrder, 'after');
                });
            },

            /**
             * @param {String|Element} destination
             * @param {Number} sortOrder
             */
            prepend: function (destination, sortOrder) {
                wait(source, destination, function (sourceEl, destinationEl) {
                    insert(sourceEl, destinationEl, sortOrder, 'prepend');
                });
            },

            /**
             * @param {String|Element} destination
             * @param {Number} sortOrder
             */
            append: function (destination, sortOrder) {
                wait(source, destination, function (sourceEl, destinationEl) {
                    insert(sourceEl, destinationEl, sortOrder, 'append');
                });
            }
        };
    };
});

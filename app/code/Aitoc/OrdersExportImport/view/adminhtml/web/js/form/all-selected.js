/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/single-checkbox',
    'jquery'
], function (Element, $) {
    'use strict';

    return Element.extend({
        defaults : {
            collection: null
        },
        onCheckedChanged: function (newChecked) {
            this._super();
            
            var self = this;
            var scope = this.source.get('data.invoices');
            var $collection = $("[data-index='" + this.collection + "']").find('fieldset');
            
            if (newChecked) {
                $.each($collection, function (index, value) {
                    if (typeof($(value).data("index")) != 'undefined') {
                        var list = [];
                        $.each($(value).find('input'), function (key, record) {
                            $(record).attr("checked", "checked");
                            list.push($(record).val());
                        });
                        self.source.data[$(value).data("index")] = list;
                    }
                });
            } else {
                $.each($collection, function (index, value) {
                    if (typeof($(value).data("index")) != 'undefined') {
                        $.each($(value).find('input'), function (key, record) {
                            $(record).removeAttr("checked");
                        });
                        self.source.data[$(value).data("index")] = [];
                    }
                });
            }
        }
    });
});

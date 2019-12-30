/**
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/checkbox-set',
    'mageUtils',
    'jquery'
], function (Element, utils, $) {
    'use strict';

    return Element.extend({
        defaults: {
            collection: null,
            isAllSelected: false
        },
        
        /**
         * @inheritdoc
         */
        getSubCollection: function () {
            let items = $("[data-index='" + this.collection + "']").find('fieldset input:checkbox');
            return items;
        },

        /**
         * @inheritdoc
         */
        massToggle: function () {
            var checked = $( '#' + this.collection + '-toggler' ).prop('checked');
            
            if (checked) {
                var allVals = [];
                this.getSubCollection().each(function() {
                    allVals.push($(this).val());
                });
                this.value(allVals); 
            } else {
                this.clear();
            }
    
            return true;
        },

        /**
         * @inheritdoc
         */
        initLinks: function () {
            var res = this._super();
            var self = this;
            self.options.each(function(item) {
                if (self.value().indexOf(item.value) === undefined) {
                    self.isAllSelected = false;
                }
            });
            self.isAllSelected = true;
            return res;
        }
    });
});

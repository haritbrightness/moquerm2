/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/components/fieldset',
], function (Element) {
    'use strict';

    return Element.extend({
        defaults        : {
            valuesForOptions : [],
            imports          : {
                toggleVisibility: 'ordersexportimport_profile_form.ordersexportimport_profile_form.automatic.exporttype:value'
            },
            openOnShow       : true,
            inverseVisibility: false
        },
        toggleVisibility: function (selected) {
            this.isShown = selected in this.valuesForOptions;
            this.visible(this.inverseVisibility ? this.isShown : !this.isShown);
            if (this.openOnShow) {
                this.opened(this.inverseVisibility ? this.isShown : !this.isShown);
            }
        }
    });
});
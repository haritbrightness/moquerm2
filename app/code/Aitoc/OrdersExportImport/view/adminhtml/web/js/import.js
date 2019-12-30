/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiRegistry',
    'jquery'
], function (registry, $) {
    'use strict';

    return {
       getData: function () {
           var newData = 0;
           registry.async('ordersexportimport_import_edit.import_edit_data_source')(function (provider) {
               provider.trigger('data.validate');
               var valid = $(".admin__field-error").size();
               if (!valid) {
                   newData = provider.get().data;
               }
       });
           return newData;
    },
    isShow: function (number) {
        if (number) {
            $("#save").show();
        } else {
            $("#save").hide();
        }
    }
    }
});

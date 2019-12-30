/**
 * Copyright © Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/checkbox-set',
    'jquery',
    'underscore'
], function (Element, $, _) {
    'use strict';

    return Element.extend({
        defaults: {
            oldOptions: [],
        },
        setInitialValue: function () {
            this._super();
            this.oldOptions = this.value().toArray();

            return this;
        },
        hasChanged: function () {
            var value = this._super();
            var diff = _.difference(this.oldOptions, this.value().toArray());
            var index = this.index;
            var self = this;
            var inputs = {};
            $("fieldset[data-index='" + index + "'] input").each(function () {
                inputs[$(this).val()] = {
                    id: $(this).attr("id"),
                    mark: $(this).attr("mark"),
                    "checked": $(this).attr("checked")
                };
            });
            var mark = 0;
            var values = [];
            $.each(inputs, function (key, value) {
                if (value.checked == 'checked' && value.mark == 1) {
                    mark = 1;
                }
            });
            if (diff.size() == 0) {
                if (mark == 0 && inputs[index] != undefined) {
                    $("fieldset[data-index='" + index + "'] input#" + inputs[index].id).trigger("click");
                }
            } else {
                $.each(diff, function (key, item) {
                    if (item == index || mark == 0) {
                        var temp = self.value().toArray();
                        for (var key in temp) {
                            if (inputs[temp[key]].checked == 'checked') {
                                $("fieldset[data-index='" + index + "'] input#" + inputs[temp[key]].id).trigger("click");
                                break;
                            }
                        }
                    }
                });
            }
            this.oldOptions = this.value().toArray();
            return value;
        }
    });
});

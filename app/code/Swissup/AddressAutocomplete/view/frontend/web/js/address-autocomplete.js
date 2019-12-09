define([
    'Magento_Ui/js/lib/view/utils/async',
    'underscore',
    'uiComponent',
    'Swissup_AddressAutocomplete/js/google-maps',
    'Magento_Customer/js/customer-data'
], function (
    $,
    _,
    Component,
    maps,
    customerData
) {
    'use strict';

    var countryData,
        config,
        authFailed = false;

    countryData = customerData.get('directory-data');

    if (_.isEmpty(countryData())) {
        customerData.reload(['directory-data'], false);
    }

    config = window.checkoutConfig.swissup.AddressAutocomplete;

    /**
     *
     * @param  {Object} el
     * @return {Object}
     */
    function getAutocomplete(el) {
        return el.addressAutocomplete;
    }

    /**
     * Find region_id by it's code, or name
     *
     * @param  {Object} address
     * @return {mixed}
     */
    function findRegionId(address) {
        var id,
            regions,
            regionCode = address.region_code,
            regionName = address.region,
            countryCode = address.country_id;

        if (countryData()[countryCode] &&
            countryData()[countryCode].regions
        ) {
            regions = countryData()[countryCode].regions;
            // 1. search by codes
            for (id in regions) {
                if (regions[id].code === regionCode) {

                    return id;
                }
            }
            // 2. search by name
            for (id in regions) {
                if (regions[id].name === regionName) {

                    return id;
                }
            }
        }

        return false;
    }

    /**
     *
     * @param  {String} name
     * @param  {Int} value
     * @param  {Object} place
     * @return {String}
     */
    function extractFieldValueFromPlace(name, value, place) {
        var i = 0,
        field;

        while ((field = place.address_components[i])) {
            if (field.types[0] === name) {
                return field[value];
            }
            i++;
        }

        return '';
    }

    /**
     *
     * @param  {Object} place @see autocomplete.getPlace()
     * @return {Object}
     */
    function extractAddress(place) {
        var mapping, address, i, re, fields, field, value, fieldValue;

        if (!place || !place.address_components) {

            return false;
        }

        mapping = {
            'country_id': '{{country.short_name}}',
            'street1': '{{route.long_name}}',
            'street2': '',
            'city': '{{postal_town.long_name|locality.long_name|sublocality_level_1.long_name}}',
            'postcode': '{{postal_code.short_name}}',
            'region': '{{administrative_area_level_1.long_name}}',
            'region_id': '',
            'region_code': '{{administrative_area_level_1.short_name}}'
        };

        if (config.streetNumberPlacement === 'line1_start') {
            mapping.street1 = '{{street_number.short_name}} {{route.long_name}}';
        } else if (config.streetNumberPlacement === 'line1_end') {
            mapping.street1 = '{{route.long_name}} {{street_number.short_name}}';
        } else if (config.streetNumberPlacement === 'line2') {
            mapping.street2 = '{{street_number.short_name}}';
        } else if (config.streetNumberPlacement.indexOf('custom_attributes[') === 0) {
            mapping[config.streetNumberPlacement] = '{{street_number.short_name}}';
        }

        address = {};

        for (i in mapping) {
            if (!mapping[i].length) {
                address[i] = '';

                continue;
            }

            address[i] = [];
            re = /\{\{(.+?)\}\}/g;

            while ((fields = re.exec(mapping[i]))) {
                _.find(fields[1].split('|'), function (string) {
                    field = string.split('.')[0];
                    value = string.split('.')[1];

                    fieldValue = extractFieldValueFromPlace(field, value, place);

                    if (fieldValue) {
                        address[i].push(fieldValue);

                        return true;
                    }
                });
            }
            address[i] = address[i].join(' ');
        }

        address.street = [address.street1, address.street2];
        address.region_id = findRegionId(address);

        return address;
    }

    /**
     *
     * @param {Object} address
     * @param {jQuery} form
     */
    function setAddress(address, form) {
        var mapping = {
            'street1': 'street[0]',
            'street2': 'street[1]'
        };

        _.each(address, function (value, key) {
            var selector = mapping[key] ? mapping[key] : key,
                el = $('[name="' + selector + '"]', form);

            if (!el.length || typeof address[key] == 'undefined') {
                return;
            }

            el.val(address[key]);
            el.trigger('change');
        });
    }

    /**
     * @param  {Element} el
     */
    function placeChangedHandler(el) {
        var form, address;

        // 1. Match parent container
        form = $(el).closest('.address');

        if (!form.length) {
            return;
        }

        // 2. Extract address from google place
        address = extractAddress(getAutocomplete(el).getPlace());

        if (!address) {
            return;
        }

        // 3. Fill the fields inside parent container
        setAddress(address, form);
    }

    return Component.extend({
        /**
         * Component initializing
         */
        initialize: function () {
            this._super();

            if (!config.enable) {
                return;
            }

            maps.auth().fail(this.failure.bind(this));
            maps.init(config.apiKey, config.locale)
                .done(this.success.bind(this));
        },

        /**
         * Revert field attributes if API key is invalid
         */
        failure: function () {
            var self = this;

            authFailed = true;

            _.each(self.selectors, function (selector) {
                $(selector).each(function () {
                    self.destroyAutocomplete(this);
                });
            });
        },

        /**
         * Method to run after maps API are loaded
         */
        success: function () {
            var self = this;

            _.each(self.selectors, function (selector) {
                $.async(selector, function (el) {
                    // Give some time to initialize field placeholder
                    // at firecheckout
                    setTimeout(function () {
                        self.initAutocomplete(el);
                    }, 100);
                });
            });
        },

        /**
         * Initialize address autocomplete on the element
         * @param {Element} el
         */
        initAutocomplete: function (el) {
            var self = this,
                autocomplete;

            if (el.addressAutocomplete || authFailed) {
                return;
            }

            $(el).data('old-placeholder', $(el).attr('placeholder') || '');
            $(el).data('old-style', $(el).attr('style') || '');

            autocomplete = new google.maps.places.Autocomplete(el, {
                types: ['address'],
                componentRestrictions: {
                    country: config.country
                }
            });
            autocomplete.addListener(
                'place_changed',
                placeChangedHandler.bind(self, el)
            );

            el.addressAutocomplete = autocomplete;
        },

        /**
         * @param {Element} el
         */
        destroyAutocomplete: function (el) {
            if (!el.addressAutocomplete) {
                return;
            }

            google.maps.event.clearInstanceListeners(el.addressAutocomplete);

            $(el).attr('placeholder', $(el).data('old-placeholder'));
            $(el).attr('style', $(el).data('old-style'));
            $(el).removeProp('disabled');
        }
    });
});

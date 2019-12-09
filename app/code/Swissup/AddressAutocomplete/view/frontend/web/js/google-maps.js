define([
    'jquery'
], function ($) {
    'use strict';

    var maps = $.Deferred(),
        auth = $.Deferred(),
        url = 'https://maps.googleapis.com/maps/api/js?',
        oldGmAuthFailure;

    window.swissupMapsLoaded = function () {
        maps.resolve(google.maps);
    };

    if (window.gm_authFailure)  {
        oldGmAuthFailure = window.gm_authFailure;
        /**
         * Callback method for google API's
         */
        window.gm_authFailure = function () {
            authFailure();
            Array.prototype.unshift.call(arguments);
            oldGmAuthFailure.apply(this, arguments);
        };
    } else {
        window.gm_authFailure = authFailure;
    }

    function authFailure () {
        auth.reject();
    }

    return {
        /**
         * Load API and call swissupMapsLoaded method
         *
         * @param  {String} apiKey
         * @param  {String} locale
         * @return {Promise}
         */
        init: function (apiKey, locale) {
            var params = [
                'key=' + apiKey,
                'language=' + locale,
                'libraries=places',
                'callback=swissupMapsLoaded'
            ].join('&');

            if (!apiKey) {
                maps.reject();
            }

            require([url + params], function () {}, function () {
                maps.reject();
            });

            return maps.promise();
        },

        /**
         * Returs a Promise that will never resolved, but can be failed
         *
         * @return {Promise}
         */
        auth: function () {
            return auth.promise();
        }
    };
});
